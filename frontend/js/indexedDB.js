// IndexedDB para almacenamiento local de favoritos
class FavoritesDB {
    constructor() {
        this.dbName = 'cineflix';
        this.dbVersion = 1;
        this.storeName = 'favorites';
        this.db = null;
        this.initDB();
    }

    // Inicializar la base de datos
    initDB() {
        return new Promise((resolve, reject) => {
            if (!window.indexedDB) {
                console.error("Su navegador no soporta IndexedDB");
                reject("IndexedDB no soportado");
                return;
            }

            const request = window.indexedDB.open(this.dbName, this.dbVersion);

            request.onerror = (event) => {
                console.error("Error al abrir la base de datos:", event.target.error);
                reject("Error al abrir la base de datos");
            };

            request.onsuccess = (event) => {
                this.db = event.target.result;
                console.log("Base de datos abierta exitosamente");
                resolve(this.db);
            };

            request.onupgradeneeded = (event) => {
                const db = event.target.result;
                
                // Crear el object store para favoritos
                if (!db.objectStoreNames.contains(this.storeName)) {
                    const objectStore = db.createObjectStore(this.storeName, { keyPath: 'id' });
                    objectStore.createIndex('userId', 'userId', { unique: false });
                    console.log("Object store creado");
                }
            };
        });
    }

    // Agregar una película a favoritos
    addFavorite(movieId, userId, movieData) {
        return new Promise((resolve, reject) => {
            if (!this.db) {
                this.initDB().then(() => {
                    this.addFavorite(movieId, userId, movieData).then(resolve).catch(reject);
                }).catch(reject);
                return;
            }

            const transaction = this.db.transaction([this.storeName], 'readwrite');
            const store = transaction.objectStore(this.storeName);

            const favorite = {
                id: movieId,
                userId: userId,
                timestamp: new Date().getTime(),
                ...movieData
            };

            const request = store.put(favorite);

            request.onsuccess = () => {
                console.log("Película agregada a favoritos");
                resolve(true);
            };

            request.onerror = (event) => {
                console.error("Error al agregar a favoritos:", event.target.error);
                reject("Error al agregar a favoritos");
            };
        });
    }

    // Eliminar una película de favoritos
    removeFavorite(movieId) {
        return new Promise((resolve, reject) => {
            if (!this.db) {
                this.initDB().then(() => {
                    this.removeFavorite(movieId).then(resolve).catch(reject);
                }).catch(reject);
                return;
            }

            const transaction = this.db.transaction([this.storeName], 'readwrite');
            const store = transaction.objectStore(this.storeName);
            const request = store.delete(movieId);

            request.onsuccess = () => {
                console.log("Película eliminada de favoritos");
                resolve(true);
            };

            request.onerror = (event) => {
                console.error("Error al eliminar de favoritos:", event.target.error);
                reject("Error al eliminar de favoritos");
            };
        });
    }

    // Verificar si una película está en favoritos
    isFavorite(movieId) {
        return new Promise((resolve, reject) => {
            if (!this.db) {
                this.initDB().then(() => {
                    this.isFavorite(movieId).then(resolve).catch(reject);
                }).catch(reject);
                return;
            }

            const transaction = this.db.transaction([this.storeName], 'readonly');
            const store = transaction.objectStore(this.storeName);
            const request = store.get(movieId);

            request.onsuccess = (event) => {
                resolve(!!event.target.result);
            };

            request.onerror = (event) => {
                console.error("Error al verificar favorito:", event.target.error);
                reject("Error al verificar favorito");
            };
        });
    }

    // Obtener todos los favoritos de un usuario
    getFavoritesByUser(userId) {
        return new Promise((resolve, reject) => {
            if (!this.db) {
                this.initDB().then(() => {
                    this.getFavoritesByUser(userId).then(resolve).catch(reject);
                }).catch(reject);
                return;
            }

            const transaction = this.db.transaction([this.storeName], 'readonly');
            const store = transaction.objectStore(this.storeName);
            const index = store.index('userId');
            const request = index.getAll(userId);

            request.onsuccess = (event) => {
                resolve(event.target.result);
            };

            request.onerror = (event) => {
                console.error("Error al obtener favoritos:", event.target.error);
                reject("Error al obtener favoritos");
            };
        });
    }
}

// Instancia global de la base de datos
const favoritesDB = new FavoritesDB();
