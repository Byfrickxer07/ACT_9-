-- Crear base de datos
CREATE DATABASE IF NOT EXISTS cineflix;
USE cineflix;

-- Tabla de usuarios
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabla de películas
CREATE TABLE IF NOT EXISTS movies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    genre VARCHAR(50) NOT NULL,
    year INT NOT NULL,
    rating DECIMAL(3,1) DEFAULT 0,
    image_url VARCHAR(255) NOT NULL,
    trailer_url VARCHAR(255),
    featured TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabla de valoraciones
CREATE TABLE IF NOT EXISTS ratings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    movie_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE,
    UNIQUE KEY user_movie (user_id, movie_id)
);

-- Tabla de favoritos
CREATE TABLE IF NOT EXISTS favorites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    movie_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE,
    UNIQUE KEY user_movie (user_id, movie_id)
);

-- Insertar usuario administrador por defecto
-- Contraseña: admin123 (hash generado con password_hash)
INSERT INTO users (name, email, password, role) VALUES 
('Administrador', 'admin@cineflix.com', '$2y$10$8MJO1TvHiLuQqYdcJrKCCOBiKwEfJ/c7YV2rFaLvOZuLEUMKyy1Mu', 'admin');

-- Insertar algunas películas de ejemplo
INSERT INTO movies (title, description, genre, year, rating, image_url, trailer_url, featured) VALUES
('Inception', 'Un ladrón que roba secretos corporativos a través del uso de la tecnología de compartir sueños, se le da la tarea inversa de plantar una idea en la mente de un CEO.', 'ciencia-ficcion', 2010, 4.8, 'assets/uploads/inception.jpg', 'https://www.youtube.com/watch?v=YoHD9XEInc0', 1),
('The Shawshank Redemption', 'Dos hombres encarcelados se unen durante varios años, encontrando consuelo y eventual redención a través de actos de decencia común.', 'drama', 1994, 4.9, 'assets/uploads/shawshank.jpg', 'https://www.youtube.com/watch?v=6hB3S9bIaco', 1),
('The Dark Knight', 'Batman se enfrenta a la amenaza más grande que ha enfrentado Gotham: el Joker.', 'accion', 2008, 4.7, 'assets/uploads/dark_knight.jpg', 'https://www.youtube.com/watch?v=EXeTwQWrcwY', 1),
('Pulp Fiction', 'Las vidas de dos sicarios, un boxeador, la esposa de un gángster y un par de bandidos se entrelazan en cuatro historias de violencia y redención.', 'drama', 1994, 4.6, 'assets/uploads/pulp_fiction.jpg', 'https://www.youtube.com/watch?v=s7EdQ4FqbhY', 0),
('The Matrix', 'Un programador informático descubre que la realidad es una simulación y se une a la rebelión contra las máquinas que controlan a la humanidad.', 'ciencia-ficcion', 1999, 4.5, 'assets/uploads/matrix.jpg', 'https://www.youtube.com/watch?v=vKQi3bBA1y8', 0),
('Forrest Gump', 'Las décadas en la vida de Forrest Gump, un hombre con un coeficiente intelectual bajo pero buenas intenciones.', 'drama', 1994, 4.4, 'assets/uploads/forrest_gump.jpg', 'https://www.youtube.com/watch?v=bLvqoHBptjg', 0),
('The Godfather', 'El patriarca envejecido de una dinastía del crimen organizado transfiere el control de su imperio clandestino a su reacio hijo.', 'drama', 1972, 4.9, 'assets/uploads/godfather.jpg', 'https://www.youtube.com/watch?v=sY1S34973zA', 0),
('Interstellar', 'Un equipo de exploradores viaja a través de un agujero de gusano en el espacio en un intento de garantizar la supervivencia de la humanidad.', 'ciencia-ficcion', 2014, 4.6, 'assets/uploads/interstellar.jpg', 'https://www.youtube.com/watch?v=zSWdZVtXT7E', 0),
('The Silence of the Lambs', 'Una joven cadete del FBI busca la ayuda de un asesino caníbal encarcelado para atrapar a otro asesino en serie.', 'terror', 1991, 4.3, 'assets/uploads/silence_lambs.jpg', 'https://www.youtube.com/watch?v=W6Mm8Sbe__o', 0),
('Parasite', 'La familia Kim, todos desempleados, se interesan en la vida de la rica familia Park.', 'drama', 2019, 4.7, 'assets/uploads/parasite.jpg', 'https://www.youtube.com/watch?v=5xH0HfJHsaY', 0),
('The Conjuring', 'Los investigadores paranormales Ed y Lorraine Warren trabajan para ayudar a una familia aterrorizada por una presencia oscura en su granja.', 'terror', 2013, 4.0, 'assets/uploads/conjuring.jpg', 'https://www.youtube.com/watch?v=k10ETZ41q5o', 0),
('Deadpool', 'Un ex operativo de las Fuerzas Especiales se somete a un experimento que lo deja con poderes de curación acelerada y busca venganza contra el hombre que arruinó su vida.', 'comedia', 2016, 4.2, 'assets/uploads/deadpool.jpg', 'https://www.youtube.com/watch?v=ONHBaC-pfsk', 0);

-- Nota: Las URLs de las imágenes son ejemplos. En una implementación real, 
-- las imágenes deberían ser subidas por el administrador y las URLs serían generadas dinámicamente.
