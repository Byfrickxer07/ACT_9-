<?php
session_start();
require_once '../backend/config.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plataforma de Películas</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/styles.css">
    <!-- Fixes CSS -->
    <link rel="stylesheet" href="css/fixes.css">
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
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
                        <a class="nav-link active" href="index.php">Inicio</a>
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
                    <div class="search-container me-2">
                        <input type="text" id="search" class="form-control" placeholder="Buscar películas...">
                    </div>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="favorites.php" class="btn btn-outline-light me-2">
                            <i class="fas fa-heart"></i> Favoritos
                        </a>
                        <a href="../backend/logout.php" class="btn btn-danger">
                            <i class="fas fa-sign-out-alt"></i> Salir
                        </a>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-outline-light me-2">Ingresar</a>
                        <a href="register.php" class="btn btn-primary">Registrarse</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-4">
        <!-- Featured Movies Carousel -->
        <div id="featuredCarousel" class="carousel slide mb-5" data-bs-ride="carousel">
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#featuredCarousel" data-bs-slide-to="0" class="active"></button>
                <button type="button" data-bs-target="#featuredCarousel" data-bs-slide-to="1"></button>
                <button type="button" data-bs-target="#featuredCarousel" data-bs-slide-to="2"></button>
            </div>
            <div class="carousel-inner">
                <?php
                // Obtener películas destacadas
                $featuredQuery = "SELECT * FROM movies WHERE featured = 1 LIMIT 3";
                $featuredResult = $conn->query($featuredQuery);
                
                $active = true;
                while ($movie = $featuredResult->fetch_assoc()) {
                    $activeClass = $active ? 'active' : '';
                    $active = false;
                    ?>
                    <div class="carousel-item <?php echo $activeClass; ?>">
                      
                        <div class="carousel-caption d-none d-md-block" style="background: none; box-shadow: none;">
                            <h2 class="animate__animated animate__fadeInDown" style="text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.9);"><?php echo $movie['title']; ?></h2>
                            <div class="animate__animated animate__fadeInUp" style="margin: 15px 0; text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.9);">
                                <p><?php echo $movie['description']; ?></p>
                            </div>
                            <div style="margin-top: 20px;">
                                <a href="#" class="btn-trailer animate__animated animate__fadeInUp" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#movieModal" 
                                        data-id="<?php echo $movie['id']; ?>">
                                    <i class="fas fa-play"></i> Ver Trailer
                                </a>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#featuredCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
                <span class="visually-hidden">Anterior</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#featuredCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
                <span class="visually-hidden">Siguiente</span>
            </button>
        </div>

        <!-- Movie Categories -->
        <h2 class="section-title">Películas Populares</h2>
        <div class="row movie-grid" id="popular-movies">
            <?php
            // Obtener películas populares
            $popularQuery = "SELECT * FROM movies ORDER BY rating DESC LIMIT 8";
            $popularResult = $conn->query($popularQuery);
            
            while ($movie = $popularResult->fetch_assoc()) {
                ?>
                <div class="col-md-3 col-sm-6 mb-4 movie-card" data-title="<?php echo $movie['title']; ?>" data-category="<?php echo $movie['genre']; ?>">
                    <div class="card h-100">
                        <img src="<?php echo $movie['image_url']; ?>" class="card-img-top" alt="<?php echo $movie['title']; ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $movie['title']; ?></h5>
                            <p class="card-text text-muted"><?php echo $movie['year']; ?> | <?php echo $movie['genre']; ?></p>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="rating">
                                    <?php 
                                    // Mostrar calificación numérica
                                    echo '<span class="rating-number me-2">'.number_format($movie['rating'], 1).'</span>';
                                    
                                    // Mostrar estrellas
                                    for ($i = 1; $i <= 5; $i++) { 
                                        if ($i <= $movie['rating']) {
                                            echo '<i class="fas fa-star text-warning"></i>';
                                        } else {
                                            echo '<i class="far fa-star text-warning"></i>';
                                        }
                                    } 
                                    ?>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between">
                                <a href="#" class="btn-ver" data-bs-toggle="modal" data-bs-target="#movieModal" data-id="<?php echo $movie['id']; ?>">
                                    <i class="fas fa-play"></i> Ver
                                </a>
                                <?php if (isset($_SESSION['user_id'])): ?>
                                <button class="btn btn-sm btn-outline-danger favorite-btn" data-id="<?php echo $movie['id']; ?>">
                                    <i class="far fa-heart"></i>
                                </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
<br><br><br>
        <!-- Recently Added -->
        <h2 class="section-title mt-5">Agregadas Recientemente</h2>
        <div class="row movie-grid" id="recent-movies">
            <?php
            // Obtener películas recientes
            $recentQuery = "SELECT * FROM movies ORDER BY id DESC LIMIT 4";
            $recentResult = $conn->query($recentQuery);
            
            while ($movie = $recentResult->fetch_assoc()) {
                ?>
                <div class="col-md-3 col-sm-6 mb-4 movie-card" data-title="<?php echo $movie['title']; ?>" data-category="<?php echo $movie['genre']; ?>">
                    <div class="card h-100">
                        <div class="new-badge">Nueva</div>
                        <img src="<?php echo $movie['image_url']; ?>" class="card-img-top" alt="<?php echo $movie['title']; ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $movie['title']; ?></h5>
                            <p class="card-text text-muted"><?php echo $movie['year']; ?> | <?php echo $movie['genre']; ?></p>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="rating">
                                    <?php 
                                    // Mostrar calificación numérica
                                    echo '<span class="rating-number me-2">'.number_format($movie['rating'], 1).'</span>';
                                    
                                    // Mostrar estrellas
                                    for ($i = 1; $i <= 5; $i++) { 
                                        if ($i <= $movie['rating']) {
                                            echo '<i class="fas fa-star text-warning"></i>';
                                        } else {
                                            echo '<i class="far fa-star text-warning"></i>';
                                        }
                                    } 
                                    ?>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between">
                                <a href="#" class="btn-ver" data-bs-toggle="modal" data-bs-target="#movieModal" data-id="<?php echo $movie['id']; ?>">
                                    <i class="fas fa-play"></i> Ver
                                </a>
                                <?php if (isset($_SESSION['user_id'])): ?>
                                <button class="btn btn-sm btn-outline-danger favorite-btn" data-id="<?php echo $movie['id']; ?>">
                                    <i class="far fa-heart"></i>
                                </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
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
                        <div class="container p-0">
                            <div class="row">
                                <div class="col-12">
                                    <p id="movieDescription" class="text-break">Cargando...</p>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge bg-primary me-2" id="movieGenre">Género</span>
                                <span class="badge bg-secondary" id="movieYear">Año</span>
                            </div>
                            <?php if (isset($_SESSION['user_id'])): ?>
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
                            <?php endif; ?>
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
    <!-- GSAP for animations -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.4/gsap.min.js"></script>
    <!-- IndexedDB for favorites -->
    <script src="js/indexedDB.js"></script>
    <!-- Image Handler -->
    <script src="js/image-handler.js"></script>
    <!-- Custom JS -->
    <script src="js/main.js"></script>
</body>
</html>
