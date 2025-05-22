<?php
// Establecer el tipo de contenido como imagen
header('Content-Type: image/png');

// Crear una imagen de 600x400 píxeles
$image = imagecreatetruecolor(600, 400);

// Definir algunos colores
$background = imagecolorallocate($image, 41, 41, 41); // Fondo oscuro
$text_color = imagecolorallocate($image, 255, 255, 255); // Texto blanco
$accent_color = imagecolorallocate($image, 229, 9, 20); // Rojo Netflix

// Rellenar el fondo
imagefill($image, 0, 0, $background);

// Dibujar un rectángulo para simular un póster de película
imagefilledrectangle($image, 50, 50, 550, 350, $accent_color);
imagefilledrectangle($image, 60, 60, 540, 340, $background);

// Añadir texto
$title = isset($_GET['title']) ? $_GET['title'] : 'Película de Muestra';
imagettftext($image, 24, 0, 120, 150, $text_color, 'arial.ttf', $title);
imagettftext($image, 16, 0, 150, 200, $text_color, 'arial.ttf', 'CineFlix Original');
imagettftext($image, 14, 0, 180, 250, $text_color, 'arial.ttf', 'Imagen de muestra');

// Mostrar la imagen
imagepng($image);

// Liberar memoria
imagedestroy($image);
?>
