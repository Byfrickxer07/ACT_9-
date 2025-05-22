<?php
session_start();
require_once '../backend/config.php';

// Verificar si el usuario es administrador
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$success_message = '';
$error_message = '';

// Obtener todas las películas
$movies_query = "SELECT * FROM movies ORDER BY id DESC";
$movies_result = $conn->query($movies_query);

// Obtener todos los usuarios
$users_query = "SELECT * FROM users ORDER BY id DESC";
$users_result = $conn->query($users_query);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - CineFlix</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .admin-sidebar {
            background-color: #1a1a1a;
            min-height: 100vh;
            padding-top: 20px;
        }
        .admin-sidebar .nav-link {
            color: #ddd;
            padding: 10px 15px;
            margin-bottom: 5px;
            border-radius: 5px;
        }
        .admin-sidebar .nav-link:hover,
        .admin-sidebar .nav-link.active {
            background-color: #e50914;
            color: white;
        }
        .admin-sidebar .nav-link i {
            margin-right: 10px;
        }
        .admin-content {
            padding: 20px;
        }
        .card {
            margin-bottom: 20px;
        }
        .movie-image {
            height: 150px;
            object-fit: cover;
        }
        .tab-content {
            padding-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 admin-sidebar">
                <h3 class="text-center text-white mb-4">CineFlix Admin</h3>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" id="dashboard-tab" data-bs-toggle="pill" href="#dashboard">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="movies-tab" data-bs-toggle="pill" href="#movies">
                            <i class="fas fa-film"></i> Películas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="add-movie-tab" data-bs-toggle="pill" href="#add-movie">
                            <i class="fas fa-plus"></i> Agregar Película
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="users-tab" data-bs-toggle="pill" href="#users">
                            <i class="fas fa-users"></i> Usuarios
                        </a>
                    </li>
                    <li class="nav-item mt-5">
                        <a class="nav-link" href="index.php">
                            <i class="fas fa-home"></i> Ir al Sitio
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../backend/logout.php">
                            <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                        </a>
                    </li>
                </ul>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-10 bg-dark text-white">
                <div class="admin-content">
                    <h2 class="mb-4">Panel de Administración</h2>
                    
                    <?php if ($success_message): ?>
                        <div class="alert alert-success"><?php echo $success_message; ?></div>
                    <?php endif; ?>
                    
                    <?php if ($error_message): ?>
                        <div class="alert alert-danger"><?php echo $error_message; ?></div>
                    <?php endif; ?>
                    
                    <div class="tab-content">
                        <!-- Dashboard -->
                        <div class="tab-pane fade show active" id="dashboard">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="card bg-primary text-white">
                                        <div class="card-body">
                                            <h5 class="card-title"><i class="fas fa-film"></i> Total Películas</h5>
                                            <h2 class="card-text">
                                                <?php 
                                                $count_movies = "SELECT COUNT(*) as total FROM movies";
                                                $result_movies = $conn->query($count_movies);
                                                $row_movies = $result_movies->fetch_assoc();
                                                echo $row_movies['total'];
                                                ?>
                                            </h2>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-success text-white">
                                        <div class="card-body">
                                            <h5 class="card-title"><i class="fas fa-users"></i> Total Usuarios</h5>
                                            <h2 class="card-text">
                                                <?php 
                                                $count_users = "SELECT COUNT(*) as total FROM users";
                                                $result_users = $conn->query($count_users);
                                                $row_users = $result_users->fetch_assoc();
                                                echo $row_users['total'];
                                                ?>
                                            </h2>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-info text-white">
                                        <div class="card-body">
                                            <h5 class="card-title"><i class="fas fa-star"></i> Valoraciones</h5>
                                            <h2 class="card-text">
                                                <?php 
                                                $count_ratings = "SELECT COUNT(*) as total FROM ratings";
                                                $result_ratings = $conn->query($count_ratings);
                                                $row_ratings = $result_ratings->fetch_assoc();
                                                echo $row_ratings['total'];
                                                ?>
                                            </h2>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <div class="card bg-dark">
                                        <div class="card-header">
                                            <h5>Películas Recientes</h5>
                                        </div>
                                        <div class="card-body">
                                            <table class="table table-dark table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Título</th>
                                                        <th>Género</th>
                                                        <th>Fecha</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $recent_movies = "SELECT * FROM movies ORDER BY id DESC LIMIT 5";
                                                    $recent_result = $conn->query($recent_movies);
                                                    while ($movie = $recent_result->fetch_assoc()) {
                                                        echo "<tr>";
                                                        echo "<td>" . $movie['title'] . "</td>";
                                                        echo "<td>" . $movie['genre'] . "</td>";
                                                        echo "<td>" . $movie['year'] . "</td>";
                                                        echo "</tr>";
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card bg-dark">
                                        <div class="card-header">
                                            <h5>Usuarios Recientes</h5>
                                        </div>
                                        <div class="card-body">
                                            <table class="table table-dark table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Nombre</th>
                                                        <th>Email</th>
                                                        <th>Rol</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $recent_users = "SELECT * FROM users ORDER BY id DESC LIMIT 5";
                                                    $recent_users_result = $conn->query($recent_users);
                                                    while ($user = $recent_users_result->fetch_assoc()) {
                                                        echo "<tr>";
                                                        echo "<td>" . $user['name'] . "</td>";
                                                        echo "<td>" . $user['email'] . "</td>";
                                                        echo "<td>" . $user['role'] . "</td>";
                                                        echo "</tr>";
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Movies List -->
                        <div class="tab-pane fade" id="movies">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h3>Gestión de Películas</h3>
                                <a href="#add-movie" class="btn btn-success" data-bs-toggle="pill">
                                    <i class="fas fa-plus"></i> Agregar Nueva
                                </a>
                            </div>
                            
                            <div class="table-responsive">
                                <table class="table table-dark table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Imagen</th>
                                            <th>Título</th>
                                            <th>Género</th>
                                            <th>Año</th>
                                            <th>Rating</th>
                                            <th>Destacada</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($movie = $movies_result->fetch_assoc()): ?>
                                            <tr>
                                                <td><?php echo $movie['id']; ?></td>
                                                <td>
                                                    <img src="<?php echo $movie['image_url']; ?>" alt="<?php echo $movie['title']; ?>" 
                                                         class="img-thumbnail" style="width: 80px; height: 50px; object-fit: cover;">
                                                </td>
                                                <td><?php echo $movie['title']; ?></td>
                                                <td><?php echo $movie['genre']; ?></td>
                                                <td><?php echo $movie['year']; ?></td>
                                                <td><?php echo $movie['rating']; ?></td>
                                                <td>
                                                    <?php if ($movie['featured']): ?>
                                                        <span class="badge bg-success">Sí</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary">No</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <a href="#" class="btn btn-sm btn-primary edit-movie" data-id="<?php echo $movie['id']; ?>">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="../backend/delete_movie.php?id=<?php echo $movie['id']; ?>" 
                                                       class="btn btn-sm btn-danger" 
                                                       onclick="return confirm('¿Está seguro de eliminar esta película?')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <!-- Add Movie Form -->
                        <div class="tab-pane fade" id="add-movie">
                            <h3 class="mb-4">Agregar Nueva Película</h3>
                            
                            <form action="../backend/add_movie.php" method="POST" enctype="multipart/form-data">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="title" class="form-label">Título</label>
                                            <input type="text" class="form-control" id="title" name="title" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="description" class="form-label">Descripción</label>
                                            <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label for="genre" class="form-label">Género</label>
                                            <select class="form-control" id="genre" name="genre" required>
                                                <option value="">Seleccionar género</option>
                                                <option value="accion">Acción</option>
                                                <option value="comedia">Comedia</option>
                                                <option value="drama">Drama</option>
                                                <option value="terror">Terror</option>
                                                <option value="ciencia-ficcion">Ciencia Ficción</option>
                                                <option value="romance">Romance</option>
                                                <option value="animacion">Animación</option>
                                                <option value="documental">Documental</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="year" class="form-label">Año</label>
                                            <input type="number" class="form-control" id="year" name="year" min="1900" max="2099" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="image" class="form-label">Imagen</label>
                                            <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="trailer_url" class="form-label">URL del Trailer</label>
                                            <input type="url" class="form-control" id="trailer_url" name="trailer_url" 
                                                   placeholder="https://www.youtube.com/watch?v=...">
                                            <small class="text-muted">Puede ser un enlace de YouTube o una URL directa al video</small>
                                        </div>
                                        <div class="mb-3">
                                            <label for="rating" class="form-label">Rating (1-5)</label>
                                            <input type="number" class="form-control" id="rating" name="rating" min="1" max="5" required>
                                        </div>
                                        <div class="mb-3 form-check">
                                            <input type="checkbox" class="form-check-input" id="featured" name="featured" value="1">
                                            <label class="form-check-label" for="featured">Película Destacada</label>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Guardar Película
                                </button>
                            </form>
                        </div>
                        
                        <!-- Users List -->
                        <div class="tab-pane fade" id="users">
                            <h3 class="mb-4">Gestión de Usuarios</h3>
                            
                            <div class="table-responsive">
                                <table class="table table-dark table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nombre</th>
                                            <th>Email</th>
                                            <th>Rol</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($user = $users_result->fetch_assoc()): ?>
                                            <tr>
                                                <td><?php echo $user['id']; ?></td>
                                                <td><?php echo $user['name']; ?></td>
                                                <td><?php echo $user['email']; ?></td>
                                                <td>
                                                    <?php if ($user['role'] === 'admin'): ?>
                                                        <span class="badge bg-danger">Admin</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-info">Usuario</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <a href="#" class="btn btn-sm btn-primary edit-user" data-id="<?php echo $user['id']; ?>">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <?php if ($user['id'] != $_SESSION['user_id']): // No permitir eliminar al usuario actual ?>
                                                    <a href="../backend/delete_user.php?id=<?php echo $user['id']; ?>" 
                                                       class="btn btn-sm btn-danger" 
                                                       onclick="return confirm('¿Está seguro de eliminar este usuario?')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Edit Movie Modal -->
    <div class="modal fade" id="editMovieModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content bg-dark text-white">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Película</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editMovieForm" action="../backend/edit_movie.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" id="edit_movie_id" name="id">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_title" class="form-label">Título</label>
                                    <input type="text" class="form-control" id="edit_title" name="title" required>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_description" class="form-label">Descripción</label>
                                    <textarea class="form-control" id="edit_description" name="description" rows="4" required></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_genre" class="form-label">Género</label>
                                    <select class="form-control" id="edit_genre" name="genre" required>
                                        <option value="accion">Acción</option>
                                        <option value="comedia">Comedia</option>
                                        <option value="drama">Drama</option>
                                        <option value="terror">Terror</option>
                                        <option value="ciencia-ficcion">Ciencia Ficción</option>
                                        <option value="romance">Romance</option>
                                        <option value="animacion">Animación</option>
                                        <option value="documental">Documental</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_year" class="form-label">Año</label>
                                    <input type="number" class="form-control" id="edit_year" name="year" min="1900" max="2099" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_image" class="form-label">Nueva Imagen (opcional)</label>
                                    <input type="file" class="form-control" id="edit_image" name="image" accept="image/*">
                                    <div class="mt-2" id="current_image_container">
                                        <p>Imagen actual:</p>
                                        <img id="current_image" src="" alt="Imagen actual" class="img-thumbnail" style="max-height: 100px;">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_trailer_url" class="form-label">URL del Trailer</label>
                                    <input type="url" class="form-control" id="edit_trailer_url" name="trailer_url">
                                </div>
                                <div class="mb-3">
                                    <label for="edit_rating" class="form-label">Rating (1-5)</label>
                                    <input type="number" class="form-control" id="edit_rating" name="rating" min="1" max="5" required>
                                </div>
                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="edit_featured" name="featured" value="1">
                                    <label class="form-check-label" for="edit_featured">Película Destacada</label>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Guardar Cambios
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Edit User Modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content bg-dark text-white">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Usuario</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editUserForm" action="../backend/edit_user.php" method="POST">
                        <input type="hidden" id="edit_user_id" name="id">
                        <div class="mb-3">
                            <label for="edit_user_name" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="edit_user_name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_user_email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="edit_user_email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_user_role" class="form-label">Rol</label>
                            <select class="form-control" id="edit_user_role" name="role" required>
                                <option value="user">Usuario</option>
                                <option value="admin">Administrador</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit_user_password" class="form-label">Nueva Contraseña (dejar en blanco para mantener la actual)</label>
                            <input type="password" class="form-control" id="edit_user_password" name="password">
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Guardar Cambios
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Image Handler -->
    <script src="js/image-handler.js"></script>
    
    <script>
        // Editar película
        $('.edit-movie').click(function(e) {
            e.preventDefault();
            const movieId = $(this).data('id');
            
            // Obtener datos de la película mediante AJAX
            $.ajax({
                url: '../backend/get_movie.php',
                type: 'GET',
                data: { id: movieId },
                dataType: 'json',
                success: function(movie) {
                    $('#edit_movie_id').val(movie.id);
                    $('#edit_title').val(movie.title);
                    $('#edit_description').val(movie.description);
                    $('#edit_genre').val(movie.genre);
                    $('#edit_year').val(movie.year);
                    $('#edit_trailer_url').val(movie.trailer_url);
                    $('#edit_rating').val(movie.rating);
                    $('#edit_featured').prop('checked', movie.featured == 1);
                    $('#current_image').attr('src', movie.image_url);
                    
                    $('#editMovieModal').modal('show');
                },
                error: function() {
                    alert('Error al cargar los datos de la película');
                }
            });
        });
        
        // Editar usuario
        $('.edit-user').click(function(e) {
            e.preventDefault();
            const userId = $(this).data('id');
            
            // Obtener datos del usuario mediante AJAX
            $.ajax({
                url: '../backend/get_user.php',
                type: 'GET',
                data: { id: userId },
                dataType: 'json',
                success: function(user) {
                    $('#edit_user_id').val(user.id);
                    $('#edit_user_name').val(user.name);
                    $('#edit_user_email').val(user.email);
                    $('#edit_user_role').val(user.role);
                    
                    $('#editUserModal').modal('show');
                },
                error: function() {
                    alert('Error al cargar los datos del usuario');
                }
            });
        });
    </script>
</body>
</html>
