/**
 * Script para manejar imágenes faltantes en la plataforma CineFlix
 * Reemplaza automáticamente las imágenes que no se pueden cargar con una imagen de muestra
 */
document.addEventListener('DOMContentLoaded', function() {
    // Función para reemplazar imágenes que no se pueden cargar
    function handleImageError(img, type) {
        const title = img.alt || 'Película';
        const movieId = img.closest('[data-id]')?.dataset.id || '';
        
        // Crear un iframe para mostrar la imagen de muestra
        const iframe = document.createElement('iframe');
        iframe.src = `assets/sample_image.php?title=${encodeURIComponent(title)}&type=${type}`;
        iframe.style.width = '100%';
        iframe.style.height = img.height + 'px';
        iframe.style.border = 'none';
        iframe.style.borderRadius = '8px 8px 0 0';
        iframe.title = title;
        
        // Reemplazar la imagen con el iframe
        img.parentNode.replaceChild(iframe, img);
    }
    
    // Manejar errores en imágenes de tarjetas de películas
    document.querySelectorAll('.card-img-top').forEach(img => {
        img.addEventListener('error', function() {
            handleImageError(this, 'thumbnail');
        });
    });
    
    // Manejar errores en imágenes del carrusel
    document.querySelectorAll('.carousel-item img').forEach(img => {
        img.addEventListener('error', function() {
            handleImageError(this, 'poster');
        });
    });
    
    // Manejar errores en imágenes de miniaturas en el panel de administración
    document.querySelectorAll('.img-thumbnail').forEach(img => {
        img.addEventListener('error', function() {
            handleImageError(this, 'thumbnail');
        });
    });
});
