document.addEventListener('DOMContentLoaded', function() {
    // Variables globales
    const userId = document.querySelector('body').dataset.userId || null;
    const searchInput = document.getElementById('search');
    const movieCards = document.querySelectorAll('.movie-card');
    const categoryLinks = document.querySelectorAll('.dropdown-item[data-category]');
    
    // Cambiar color de la barra de navegación al hacer scroll
    window.addEventListener('scroll', function() {
        const navbar = document.querySelector('.navbar');
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });
    
    // Búsqueda en tiempo real
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            
            movieCards.forEach(card => {
                const title = card.dataset.title.toLowerCase();
                if (title.includes(searchTerm)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    }
    
    // Filtrado por categorías
    categoryLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const category = this.dataset.category;
            
            movieCards.forEach(card => {
                if (category === 'all' || card.dataset.category === category) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
    
    // Modal de película
    const movieModal = document.getElementById('movieModal');
    if (movieModal) {
        movieModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const movieId = button.getAttribute('data-id');
            
            // Obtener datos de la película mediante AJAX
            fetch(`../backend/get_movie.php?id=${movieId}`)
                .then(response => response.json())
                .then(movie => {
                    document.getElementById('movieTitle').textContent = movie.title;
                    document.getElementById('movieDescription').textContent = movie.description;
                    document.getElementById('movieGenre').textContent = movie.genre;
                    document.getElementById('movieYear').textContent = movie.year;
                    
                    // Cargar video
                    const videoContainer = document.getElementById('videoContainer');
                    if (movie.trailer_url) {
                        // Verificar si es un enlace de YouTube
                        if (movie.trailer_url.includes('youtube.com') || movie.trailer_url.includes('youtu.be')) {
                            // Extraer ID del video de YouTube
                            let videoId = '';
                            if (movie.trailer_url.includes('v=')) {
                                videoId = movie.trailer_url.split('v=')[1].split('&')[0];
                            } else if (movie.trailer_url.includes('youtu.be/')) {
                                videoId = movie.trailer_url.split('youtu.be/')[1];
                            }
                            
                            videoContainer.innerHTML = `
                                <iframe width="100%" height="400" src="https://www.youtube.com/embed/${videoId}" 
                                frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; 
                                gyroscope; picture-in-picture" allowfullscreen></iframe>
                            `;
                        } else {
                            // Video normal
                            videoContainer.innerHTML = `
                                <video width="100%" height="400" controls>
                                    <source src="${movie.trailer_url}" type="video/mp4">
                                    Tu navegador no soporta el elemento de video.
                                </video>
                            `;
                        }
                    } else {
                        // Si no hay trailer, mostrar el video de muestra
                        videoContainer.innerHTML = `
                            <iframe width="100%" height="400" src="assets/videos/sample_video.php" 
                            frameborder="0" allowfullscreen></iframe>
                        `;
                    }
                    
                    // Verificar si la película está en favoritos
                    if (userId) {
                        favoritesDB.isFavorite(parseInt(movieId))
                            .then(isFavorite => {
                                const favBtn = document.querySelector(`.favorite-btn[data-id="${movieId}"]`);
                                if (favBtn) {
                                    if (isFavorite) {
                                        favBtn.classList.add('active');
                                        favBtn.querySelector('i').classList.remove('far');
                                        favBtn.querySelector('i').classList.add('fas');
                                    } else {
                                        favBtn.classList.remove('active');
                                        favBtn.querySelector('i').classList.remove('fas');
                                        favBtn.querySelector('i').classList.add('far');
                                    }
                                }
                            });
                    }
                    
                    // Sistema de valoración
                    const ratingStars = document.querySelectorAll('#userRating i');
                    if (ratingStars.length > 0) {
                        // Obtener valoración actual del usuario
                        fetch(`../backend/get_user_rating.php?movie_id=${movieId}&user_id=${userId}`)
                            .then(response => response.json())
                            .then(data => {
                                if (data.rating) {
                                    updateStars(data.rating);
                                }
                            });
                        
                        // Función para actualizar estrellas
                        function updateStars(rating) {
                            ratingStars.forEach((star, index) => {
                                if (index < rating) {
                                    star.classList.remove('far');
                                    star.classList.add('fas');
                                } else {
                                    star.classList.remove('fas');
                                    star.classList.add('far');
                                }
                            });
                        }
                        
                        // Evento para valorar
                        ratingStars.forEach(star => {
                            star.addEventListener('click', function() {
                                const rating = parseInt(this.dataset.rating);
                                
                                // Enviar valoración al servidor
                                fetch('../backend/rate_movie.php', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/x-www-form-urlencoded',
                                    },
                                    body: `movie_id=${movieId}&user_id=${userId}&rating=${rating}`
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        updateStars(rating);
                                    }
                                });
                            });
                            
                            // Hover effect
                            star.addEventListener('mouseover', function() {
                                const rating = parseInt(this.dataset.rating);
                                ratingStars.forEach((s, index) => {
                                    if (index < rating) {
                                        s.classList.remove('far');
                                        s.classList.add('fas');
                                    } else {
                                        s.classList.remove('fas');
                                        s.classList.add('far');
                                    }
                                });
                            });
                            
                            // Restaurar valoración al quitar el hover
                            document.getElementById('userRating').addEventListener('mouseout', function() {
                                fetch(`../backend/get_user_rating.php?movie_id=${movieId}&user_id=${userId}`)
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.rating) {
                                            updateStars(data.rating);
                                        } else {
                                            ratingStars.forEach(s => {
                                                s.classList.remove('fas');
                                                s.classList.add('far');
                                            });
                                        }
                                    });
                            });
                        });
                    }
                })
                .catch(error => {
                    console.error('Error al cargar datos de la película:', error);
                });
        });
        
        // Detener video al cerrar el modal
        movieModal.addEventListener('hidden.bs.modal', function() {
            const videoContainer = document.getElementById('videoContainer');
            videoContainer.innerHTML = '';
        });
    }
    
    // Gestión de favoritos
    const favoriteButtons = document.querySelectorAll('.favorite-btn');
    favoriteButtons.forEach(btn => {
        const movieId = parseInt(btn.dataset.id);
        
        // Verificar si la película está en favoritos
        if (userId) {
            favoritesDB.isFavorite(movieId)
                .then(isFavorite => {
                    if (isFavorite) {
                        btn.classList.add('active');
                        btn.querySelector('i').classList.remove('far');
                        btn.querySelector('i').classList.add('fas');
                    }
                });
        }
        
        // Evento para agregar/quitar de favoritos
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            if (!userId) {
                alert('Debes iniciar sesión para agregar favoritos');
                return;
            }
            
            const isActive = btn.classList.contains('active');
            
            if (isActive) {
                // Quitar de favoritos
                favoritesDB.removeFavorite(movieId)
                    .then(() => {
                        btn.classList.remove('active');
                        btn.querySelector('i').classList.remove('fas');
                        btn.querySelector('i').classList.add('far');
                        
                        // Sincronizar con el servidor
                        fetch('../backend/remove_favorite.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: `movie_id=${movieId}&user_id=${userId}`
                        });
                    });
            } else {
                // Obtener datos de la película
                fetch(`../backend/get_movie.php?id=${movieId}`)
                    .then(response => response.json())
                    .then(movie => {
                        // Agregar a favoritos en IndexedDB
                        favoritesDB.addFavorite(movieId, userId, movie)
                            .then(() => {
                                btn.classList.add('active');
                                btn.querySelector('i').classList.remove('far');
                                btn.querySelector('i').classList.add('fas');
                                
                                // Sincronizar con el servidor
                                fetch('../backend/add_favorite.php', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/x-www-form-urlencoded',
                                    },
                                    body: `movie_id=${movieId}&user_id=${userId}`
                                });
                            });
                    });
            }
        });
    });
    
    // Animaciones con GSAP
    gsap.from('.section-title', {
        duration: 1,
        y: 50,
        opacity: 0,
        stagger: 0.2,
        scrollTrigger: {
            trigger: '.section-title',
            start: 'top 80%'
        }
    });
    
    gsap.from('.movie-card', {
        duration: 0.8,
        y: 100,
        opacity: 0,
        stagger: 0.1,
        scrollTrigger: {
            trigger: '.movie-grid',
            start: 'top 80%'
        }
    });
});
