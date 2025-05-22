<?php
// Establecer el tipo de contenido como video MP4
header('Content-Type: text/html');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Video de Muestra</title>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            overflow: hidden;
        }
        .video-container {
            width: 100%;
            height: 100%;
            background-color: #000;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            font-family: Arial, sans-serif;
        }
        .play-button {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.2);
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 20px;
            cursor: pointer;
        }
        .play-button:hover {
            background-color: rgba(255, 255, 255, 0.3);
        }
        .triangle {
            width: 0;
            height: 0;
            border-style: solid;
            border-width: 15px 0 15px 30px;
            border-color: transparent transparent transparent #ffffff;
            margin-left: 5px;
        }
        .title {
            font-size: 24px;
            margin-bottom: 10px;
        }
        .description {
            font-size: 16px;
            text-align: center;
            max-width: 600px;
        }
        .video-player {
            display: none;
            width: 100%;
            height: 100%;
        }
    </style>
</head>
<body>
    <div class="video-container" id="placeholder">
        <div class="play-button" id="playBtn">
            <div class="triangle"></div>
        </div>
        <div class="title">Trailer de Película</div>
        <div class="description">
            Este es un video de muestra para la plataforma CineFlix. En una implementación real, 
            aquí se mostraría el trailer de la película seleccionada.
        </div>
    </div>
    
    <video class="video-player" id="videoPlayer" controls>
        <source src="https://www.w3schools.com/html/mov_bbb.mp4" type="video/mp4">
        Tu navegador no soporta el elemento de video.
    </video>
    
    <script>
        document.getElementById('playBtn').addEventListener('click', function() {
            document.getElementById('placeholder').style.display = 'none';
            const videoPlayer = document.getElementById('videoPlayer');
            videoPlayer.style.display = 'block';
            videoPlayer.play();
        });
    </script>
</body>
</html>
