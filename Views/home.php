<?php
session_start();
require_once __DIR__ . "/../database.php";

// Protección: Si no ha iniciado sesión, redirigirlo al login
if (!isset($_SESSION["id_cuenta"])) {
    header("Location: " . BASE_URL . "Views/Auth/login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Glitch TV - Inicio</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Iconos de Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- CSS Propio -->
    <link rel="stylesheet" href="<?= BASE_URL ?>CSS/styles.css">
   
</head>
<body>
<a href="<?= BASE_URL ?>Views/Content.php?id=1"></a>
    <!-- ================= NAVBAR (Incluido desde archivo externo) ================= -->
    <?php include_once __DIR__ . "/navbar.php"; ?>

    <!-- ================= HERO CAROUSEL ================= -->
    <header class="hero-section mt-5 pt-5">
        <div class="hero-container d-flex justify-content-center align-items-center">
            <div class="hero-item">
                <a href="Content.php?id=1">
                    <img src="<?= BASE_URL ?>Resources/maxresdefault.jpg" alt="Anterior">
                </a>
            </div>
            <div class="hero-item center-img">
                <a href="Content.php?id=2">
                    <img src="<?= BASE_URL ?>Resources/4svfcykr6pve1.jpeg" alt="Principal">
                </a>
            </div>
            <div class="hero-item">
                <a href="Content.php?id=3">
                    <img src="<?= BASE_URL ?>Resources/maxresdefault (1).jpg" alt="Siguiente">
                </a>
            </div>
        </div>
        
        <!-- Indicadores (Puntos) -->
        <div class="hero-indicators text-center mt-3">
            <span class="dot active"></span>
            <span class="dot"></span>
            <span class="dot"></span>
        </div>
    </header>

    <!-- ================= CONTENIDO (FILA DESLIZANTE) ================= -->
    <section class="content-section mt-5 mb-5 position-relative">
        <h2 class="text-white fw-bold mb-3 ms-4 text-uppercase fs-4">Contenido</h2>
        
        <!-- Contenedor relativo para posicionar las flechas -->
        <div class="slider-container position-relative px-4">
            
            <!-- Botón Anterior -->
            <button class="slider-btn slider-btn-prev d-none" id="btnPrev">
                <i class="bi bi-chevron-left"></i>
            </button>

            <!-- Contenedor con scroll horizontal -->
            <div class="scrolling-wrapper d-flex flex-nowrap overflow-hidden" id="sliderWrapper">
                
                <!-- Tarjeta 1 -->
                <div class="content-card">
                    <a href="Content.php?id=4">
                        <img src="<?= BASE_URL ?>Resources/Poster/Poster1.png" width="300" height="400" class="img-fluid rounded" alt="Poster 1">
                    </a>
                </div>
                
                <!-- Tarjeta 2 -->
                <div class="content-card">
                    <a href="Content.php?id=1">
                        <img src="<?= BASE_URL ?>Resources/Poster/Poster2.png" width="300" height="400" class="img-fluid rounded" alt="Poster 2">
                    </a>
                </div>
                
                <!-- Tarjeta 3 -->
                <div class="content-card">
                    <a href="Content.php?id=2">
                        <img src="<?= BASE_URL ?>Resources/Poster/Poster3.png" width="300" height="400" class="img-fluid rounded" alt="Poster 3">
                    </a>
                </div>
                
                <!-- Tarjeta 4 -->
                <div class="content-card">
                    <a href="Content.php?id=3">
                        <img src="<?= BASE_URL ?>Resources/Poster/Poster4.png" width="300" height="400" class="img-fluid rounded" alt="Poster 4">
                    </a>
                </div>

            </div>

            <!-- Botón Siguiente -->
            <button class="slider-btn slider-btn-next d-none" id="btnNext">
                <i class="bi bi-chevron-right"></i>
            </button>
            
        </div>
    </section>

    <!-- ================= FOOTER ================= -->
    <footer class="custom-footer mt-5 pb-5">
        <div class="container-fluid px-4 ms-lg-3">
            
            <!-- Redes Sociales -->
            <div class="d-flex gap-4 mb-4">
                <a href="#" class="social-icon"><i class="bi bi-youtube"></i></a>
                <a href="#" class="social-icon"><i class="bi bi-twitter-x"></i></a>
                <a href="#" class="social-icon"><i class="bi bi-facebook"></i></a>
                <a href="#" class="social-icon"><i class="bi bi-instagram"></i></a>
                <a href="#" class="social-icon"><i class="bi bi-tiktok"></i></a>
            </div>
            
            <!-- Enlaces Legales y de Ayuda -->
            <div class="d-flex flex-wrap gap-4 mb-4 legal-links">
                <a href="#">Accesibilidad</a>
                <a href="#">Política de Privacidad</a>
                <a href="#">Términos de Uso</a>
                <a href="#">Gestionar cookies</a>
                <a href="#">Información</a>
                <a href="#">Ayuda</a>
            </div>
            
            <!-- Copyright -->
            <div>
                <p class="copyright-text mb-0">&copy; 2026 Glitch Productions. Todos los derechos reservados.</p>
            </div>
            
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= BASE_URL ?>JS/homescript.js"></script>
</body>
</html>