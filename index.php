<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Glitch Productions - Inicio</title>
    
    <!-- Fuente Montserrat -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;800;900&display=swap" rel="stylesheet">
    
    <!-- Archivo CSS -->
    <link rel="stylesheet" href="CSS/styles.css">
</head>
<body>

    <!-- Contenedor para la imagen de fondo -->
    <div class="glitch-bg"></div>

    <!-- NAVBAR -->
    <nav class="navbar">
        <a href="index.php" class="logo-container">
            <img src="Resources/Glitch_Productions_2023.svg.webp" alt="Glitch Productions Logo" class="glitch-logo">
        </a>

        <div class="nav-controls">
            <a href="Views/Auth/login.php" class="btn-glitch nav-btn">
                INICIAR SESIÓN
            </a>
        </div>
    </nav>

    <!-- CONTENIDO PRINCIPAL -->
    <main class="main-container">
        <div class="glitch-card">
            <h1 class="main-title">BIENVENIDO A GLITCH</h1>
            
            <p class="main-subtitle">SERIES ANIMADAS, SHOWS Y MUCHO MÁS</p>
            <p class="description-text">Ingresa tu email para registrarte y comenzar a disfrutar el contenido.</p>

            <!-- Formulario hacia Registro -->
            <form action="Views/Auth/register.php" method="GET" class="glitch-form">
                <div class="form-group">
                    <label for="email">EMAIL:</label>
                    <input type="email" id="email" name="email" class="glitch-input" placeholder="Tu correo electrónico..." required>
                </div>

                <button type="submit" class="btn-glitch submit-btn">
                    COMENZAR
                </button>
            </form>

            <div class="auth-link">
                ¿Ya tienes una cuenta? <a href="Views/Auth/login.php">Inicia sesión aquí</a>
            </div>
        </div>
    </main>

    <!-- Archivo JS -->
    <script src="JS/main.js"></script>
</body>
</html>