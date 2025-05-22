<?php
session_start();
require_once '../backend/config.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Obtener favoritos del usuario desde la base de datos
$user_id = $_SESSION['user_id'];
$favorites_query = "SELECT m.* FROM movies m 
                    INNER JOIN favorites f ON m.id = f.movie_id 
                    WHERE f.user_id = ?";
$stmt = $conn->prepare($favorites_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$favorites_result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Favoritos - CineFlix</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">CineFlix</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Inicio</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="categoriesDropdown" role="button" data-bs-toggle="dropdown">
                            Categorías
                        </a>
                        <ul class="dropdown-menu" id="categories-menu">
                            <li><a class="dropdown-item" href="#" data-category="accion">Acción</a></li>
                            <li><a class="dropdown-item" href="#" data-category="comedia">Comedia</a></li>
                            <li><a class="dropdown-item" href="#" data-category="drama">Drama</a></li>
                            <li><a class="dropdown-item" href="#" data-category="terror">Terror</a></li>
                            <li><a class="dropdown-item" href="#" data-category="ciencia-ficcion">Ciencia Ficción</a></li>
                        </ul>
                    </li>
                    <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="admin.php">Panel Admin</a>
                    </li>
                    <?php endif; ?>
                </ul>
                <div class="d-flex">
                    <a href="favorites.php" class="btn btn-outline-light me-2 active">
                        <i class="fas fa-heart"></i> Favoritos
                    </a>
                    <a href="../backend/logout.php" class="btn btn-danger">
                        <i class="fas fa-sign-out-alt"></i> Salir
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-4">
        <h2 class="section-title">Mis Películas Favoritas</h2>
        
        <?php if ($favorites_result->num_rows === 0): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> No tienes películas favoritas aún. 
                <a href="index.php" class="alert-link">Explora nuestro catálogo</a> y agrega algunas.
            </div>
        <?php else: ?>
            <div class="row movie-grid" id="favorites-container">
                <?php while ($movie = $favorites_result->fetch_assoc()): ?>
                    <div class="col-md-3 col-sm-6 mb-4 movie-card" data-title="<?php echo $movie['title']; ?>" data-category="<?php echo $movie['genre']; ?>">
                        <div class="card h-100">
                            <img src="<?php echo $movie['image_url']; ?>" class="card-img-top" alt="<?php echo $movie['title']; ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $movie['title']; ?></h5>
                                <p class="card-text text-muted"><?php echo $movie['year']; ?> | <?php echo $movie['genre']; ?></p>
                                <div class="rating mb-2">
                                    <?php for ($i = 1; $i <= 5; $i++) { 
                                        if ($i <= $movie['rating']) {
                                            echo '<i class="fas fa-star text-warning"></i>';
                                        } else {
                                            echo '<i class="far fa-star text-warning"></i>';
                                        }
                                    } ?>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#movieModal" data-id="<?php echo $movie['id']; ?>">
                                        <i class="fas fa-play"></i> Ver
                                    </button>
                                    <button class="btn btn-sm btn-danger favorite-btn active" data-id="<?php echo $movie['id']; ?>">
                                        <i class="fas fa-heart"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Movie Modal -->
    <div class="modal fade" id="movieModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="movieTitle">Cargando...</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="embed-responsive embed-responsive-16by9 mb-3">
                        <div id="videoContainer"></div>
                    </div>
                    <div class="movie-details">
                        <p id="movieDescription">Cargando...</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge bg-primary me-2" id="movieGenre">Género</span>
                                <span class="badge bg-secondary" id="movieYear">Año</span>
                            </div>
                            <div class="rating-container">
                                <span>Tu valoración: </span>
                                <div class="user-rating" id="userRating">
                                    <i class="far fa-star" data-rating="1"></i>
                                    <i class="far fa-star" data-rating="2"></i>
                                    <i class="far fa-star" data-rating="3"></i>
                                    <i class="far fa-star" data-rating="4"></i>
                                    <i class="far fa-star" data-rating="5"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>CineFlix</h5>
                    <p>Tu plataforma de películas y documentales</p>
                </div>
                <div class="col-md-3">
                    <h5>Enlaces</h5>
                    <ul class="list-unstyled">
                        <li><a href="index.php" class="text-white">Inicio</a></li>
                        <li><a href="#" class="text-white">Categorías</a></li>
                        <li><a href="#" class="text-white">Acerca de</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h5>Síguenos</h5>
                    <div class="social-icons">
                        <a href="#" class="text-white me-2"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-white me-2"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-white me-2"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
            <hr>
            <div class="text-center">
                <p>&copy; 2025 CineFlix. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- IndexedDB for favorites -->
    <script src="js/indexedDB.js"></script>
    <!-- Image Handler -->
    <script src="js/image-handler.js"></script>
    <!-- Custom JS -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const userId = <?php echo $_SESSION['user_id']; ?>;
            
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
                
                // Evento para quitar de favoritos
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    // Quitar de favoritos en IndexedDB
                    favoritesDB.removeFavorite(movieId)
                        .then(() => {
                            // Quitar de favoritos en el servidor
                            fetch('../backend/remove_favorite.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded',
                                },
                                body: `movie_id=${movieId}&user_id=${userId}`
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    // Eliminar la tarjeta de película de la vista
                                    const movieCard = btn.closest('.movie-card');
                                    movieCard.classList.add('animate__animated', 'animate__fadeOut');
                                    setTimeout(() => {
                                        movieCard.remove();
                                        
                                        // Verificar si no quedan favoritos
                                        const remainingCards = document.querySelectorAll('.movie-card');
                                        if (remainingCards.length === 0) {
                                            const container = document.getElementById('favorites-container');
                                            container.innerHTML = `
                                                <div class="col-12">
                                                    <div class="alert alert-info">
                                                        <i class="fas fa-info-circle"></i> No tienes películas favoritas aún. 
                                                        <a href="index.php" class="alert-link">Explora nuestro catálogo</a> y agrega algunas.
                                                    </div>
                                                </div>
                                            `;
                                        }
                                    }, 500);
                                }
                            });
                        });
                });
            });
        });
    </script>
</body>
</html>
