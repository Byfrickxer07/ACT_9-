<?php
// Este archivo genera una imagen de muestra para las películas
// Parámetros: title (opcional), type (poster o thumbnail)

$title = isset($_GET['title']) ? $_GET['title'] : 'Película de Muestra';
$type = isset($_GET['type']) ? $_GET['type'] : 'poster';
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo htmlspecialchars($title); ?></title>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            overflow: hidden;
            background-color: #141414;
        }
        .poster-container {
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .movie-poster {
            width: <?php echo $type == 'thumbnail' ? '100%' : '300px'; ?>;
            height: <?php echo $type == 'thumbnail' ? '100%' : '450px'; ?>;
            background-color: #292929;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
            position: relative;
        }
        .poster-gradient {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to bottom, rgba(0,0,0,0.1) 0%, rgba(0,0,0,0.8) 100%);
        }
        .poster-content {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            padding: 20px;
            box-sizing: border-box;
            color: white;
            font-family: Arial, sans-serif;
        }
        .movie-title {
            font-size: <?php echo $type == 'thumbnail' ? '18px' : '24px'; ?>;
            font-weight: bold;
            margin-bottom: 8px;
        }
        .movie-info {
            font-size: <?php echo $type == 'thumbnail' ? '12px' : '14px'; ?>;
            opacity: 0.8;
            margin-bottom: 15px;
        }
        .movie-rating {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        .star {
            color: #e50914;
            margin-right: 2px;
        }
        .cineflix-logo {
            position: absolute;
            top: 20px;
            left: 20px;
            color: #e50914;
            font-size: <?php echo $type == 'thumbnail' ? '12px' : '16px'; ?>;
            font-weight: bold;
        }
        .poster-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            background-color: #333;
        }
    </style>
</head>
<body>
    <div class="poster-container">
        <div class="movie-poster">
            <div class="poster-image"></div>
            <div class="poster-gradient"></div>
            <div class="cineflix-logo">CINEFLIX</div>
            <div class="poster-content">
                <div class="movie-title"><?php echo htmlspecialchars($title); ?></div>
                <div class="movie-info">2025 | Acción, Aventura | 120 min</div>
                <div class="movie-rating">
                    <span class="star">★</span>
                    <span class="star">★</span>
                    <span class="star">★</span>
                    <span class="star">★</span>
                    <span class="star">☆</span>
                    <span style="margin-left: 5px;">4.0</span>
                </div>
                <?php if ($type != 'thumbnail'): ?>
                <div>Imagen de muestra para la plataforma CineFlix</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
