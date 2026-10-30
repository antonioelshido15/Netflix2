<?php
session_start();
require_once __DIR__ . "/../database.php";

// Protección: Redirigir al login si no hay sesión activa[cite: 7]
if (!isset($_SESSION["id_cuenta"])) {
    header("Location: " . BASE_URL . "Views/Auth/login.php");
    exit;
}

$id_cuenta = $_SESSION["id_cuenta"];
$mensaje = "";

// Procesar el formulario cuando se envía para crear un nuevo perfil[cite: 7]
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre_perfil = trim($_POST["nombre_perfil"] ?? "");
    $avatar = trim($_POST["avatar"] ?? "avatar1.png");
    $es_infantil = isset($_POST["es_infantil"]) ? 1 : 0;
    $idioma_preferido = trim($_POST["idioma_preferido"] ?? "es");

    if ($nombre_perfil !== "") {
        try {
            $conexion = getConnection();
            $stmt = $conexion->prepare("INSERT INTO perfiles (id_cuenta, nombre_perfil, avatar, es_infantil, idioma_preferido) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$id_cuenta, $nombre_perfil, $avatar, $es_infantil, $idioma_preferido]);
            $mensaje = "¡Perfil creado exitosamente!";
        } catch (Exception $e) {
            $mensaje = "Error al crear el perfil: " . $e->getMessage();
        }
    } else {
        $mensaje = "El nombre del perfil no puede estar vacío.";
    }
}

// Consultar los perfiles existentes de esta cuenta[cite: 7]
try {
    $conexion = getConnection();
    $stmt = $conexion->prepare("SELECT * FROM perfiles WHERE id_cuenta = ?");
    $stmt->execute([$id_cuenta]);
    $perfiles = $stmt->fetchAll();
} catch (Exception $e) {
    $perfiles = [];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Perfiles - Glitch TV</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>CSS/styles.css">
</head>
<body class="auth-page">

    <!-- Incluimos el Navbar superior[cite: 7] -->
    <?php include_once __DIR__ . "/navbar.php"; ?>

    <div class="main-container" style="flex-direction: column; align-items: stretch; max-width: 900px; margin: 0 auto; padding-top: 100px;">
        
        <!-- Tarjeta del Formulario (Estilo Glitch Card) -->
        <div class="glitch-card mx-auto mb-5 w-100" style="max-width: 600px;">
            <h1 class="main-title">Administrar Perfiles</h1>
            <p class="description-text">Crea un nuevo perfil, elige tu avatar y gestiona tu experiencia.</p>

            <?php if ($mensaje !== ""): ?>
                <div class="alert alert-info text-center bg-dark text-white border-secondary mb-4 py-2" role="alert">
                    <?= htmlspecialchars($mensaje, ENT_QUOTES, "UTF-8") ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="" class="glitch-form text-start">
                <div class="form-group mb-3">
                    <label for="nombre_perfil" class="auth-label">Nombre del perfil:</label>
                    <input type="text" class="glitch-input" id="nombre_perfil" name="nombre_perfil" maxlength="50" required placeholder="Ej. Alex">
                </div>

                <!-- Selector de Avatares -->
                <div class="form-group mb-3">
                    <label class="auth-label d-block">Elige un avatar:</label>
                    <div class="d-flex justify-content-around align-items-center gap-2 py-2">
                        <?php 
                            $avatares = ["avatar1.png", "avatar2.png", "avatar3.png", "avatar4.png"];
                            foreach ($avatares as $index => $img): 
                        ?>
                            <label class="position-relative text-center">
                                <input type="radio" name="avatar" value="<?= $img ?>" class="avatar-radio d-none" <?= $index === 0 ? 'checked' : '' ?>>
                                <img src="<?= BASE_URL ?>Resources/<?= $img ?>" alt="Avatar" width="60" height="60" class="avatar-option object-fit-cover">
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label for="idioma_preferido" class="auth-label">Idioma preferido:</label>
                    <select class="glitch-input auth-select" id="idioma_preferido" name="idioma_preferido">
                        <option value="es">Español</option>
                        <option value="en">Inglés</option>
                    </select>
                </div>

                <div class="form-check my-3">
                    <input type="checkbox" class="form-check-input" id="es_infantil" name="es_infantil" value="1">
                    <label class="form-check-label text-white fw-bold" for="es_infantil">¿Es perfil infantil?</label>
                </div>

                <button type="submit" class="btn-glitch w-100 mt-2">Guardar perfil</button>
            </form>
        </div>

        <!-- Listado de perfiles actuales (Estilo Glitch Card) -->
        <div class="glitch-card mx-auto mb-5 w-100" style="max-width: 800px;">
            <h3 class="main-title fs-3 mb-4">Tus Perfiles Activos</h3>
            <div class="row justify-content-center g-3">
                <?php if (!empty($perfiles)): ?>
                    <?php foreach ($perfiles as $p): ?>
                        <?php 
                            $es_activo = (isset($_SESSION["id_perfil"]) && $_SESSION["id_perfil"] == $p["id_perfil"]);
                            $avatarImg = !empty($p["avatar"]) ? BASE_URL . "Resources/" . $p["avatar"] : BASE_URL . "Resources/Glitch_Productions_2023.svg.webp";
                        ?>
                        <div class="col-6 col-md-4 mb-3">
                            <div class="card bg-dark text-center p-3 h-100 shadow-sm position-relative" style="border: 2px solid <?= $es_activo ? 'var(--glitch-cyan)' : 'var(--input-border)' ?>; border-radius: 8px;">
                                
                                <!-- Botón para eliminar perfil usando select_profile.php -->
                                <a href="<?= BASE_URL ?>Views/select_profile.php?action=delete&id=<?= $p["id_perfil"] ?>" 
                                   onclick="return confirm('¿Estás seguro de eliminar este perfil?');" 
                                   class="position-absolute top-0 end-0 m-2 text-danger fs-5" title="Eliminar perfil">
                                    <i class="bi bi-trash-fill"></i>
                                </a>

                                <!-- Enlace para seleccionar el perfil y entrar -->
                                <a href="<?= BASE_URL ?>Views/select_profile.php?id=<?= $p["id_perfil"] ?>" class="text-decoration-none text-white d-block">
                                    <img src="<?= $avatarImg ?>" alt="Avatar" width="65" height="65" class="rounded-circle mx-auto mb-2 object-fit-cover border border-secondary shadow-sm">
                                    <h5 class="card-title text-white fs-6 text-truncate mb-1"><?= htmlspecialchars($p["nombre_perfil"], ENT_QUOTES, "UTF-8") ?></h5>
                                    <p class="card-text small text-muted mb-2">Idioma: <?= htmlspecialchars($p["idioma_preferido"], ENT_QUOTES, "UTF-8") ?></p>
                                </a>

                                <?php if ($es_activo): ?>
                                    <span class="badge bg-info text-dark mt-auto fw-bold">Perfil Activo</span>
                                <?php endif; ?>
                                <?php if ($p["es_infantil"]): ?>
                                    <span class="badge bg-warning text-dark mt-1 fw-bold">Infantil</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-center text-muted">No hay perfiles registrados en esta cuenta.</p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="text-center pb-5">
            <a href="<?= BASE_URL ?>Views/home.php" class="btn-glitch">Volver al inicio</a>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>