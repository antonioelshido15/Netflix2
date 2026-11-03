<?php
// Consultar los perfiles de la cuenta actual usando la tabla 'perfiles' de tu base de datos
$id_cuenta = $_SESSION["id_cuenta"] ?? null;
$perfiles = [];

if ($id_cuenta) {
    try {
        $conexion = getConnection();
        // Consulta adaptada a tu estructura: id_perfil, id_cuenta, nombre_perfil, avatar, es_infantil, idioma_preferido
        $consultaPerfiles = $conexion->prepare("SELECT id_perfil, id_cuenta, nombre_perfil, avatar, es_infantil, idioma_preferido FROM perfiles WHERE id_cuenta = ?");
        $consultaPerfiles->execute([$id_cuenta]);
        $perfiles = $consultaPerfiles->fetchAll();
    } catch (Exception $e) {
        $perfiles = [];
    }
}

// Obtener el avatar del perfil activo para mostrarlo en el botón superior de la barra de navegación
$avatarActivo = isset($_SESSION["avatar"]) ? BASE_URL . "Resources/" . $_SESSION["avatar"] : BASE_URL . "Resources/Glitch_Productions_2023.svg.webp";
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-custom-dark fixed-top px-4">
    <div class="container-fluid">
        <!-- Logo -->
        <a class="navbar-brand" href="<?= BASE_URL ?>Views/home.php">
            <img src="<?= BASE_URL ?>Resources/Glitch_Productions_2023.svg.webp" alt="Logo" height="40" class="glitch-logo">
        </a>
        
        <!-- Iconos derecha -->
        <div class="d-flex align-items-center ms-auto gap-3">
            <a href="#" class="text-white fs-4"><i class="bi bi-search"></i></a>
            
            <!-- Menú Desplegable Combinado (Estilo Dos Columnas en Español) -->
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle hide-arrow" id="accountDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="<?= $avatarActivo ?>" alt="Avatar" width="35" height="35" class="rounded-circle border border-light object-fit-cover">
                </a>
                
                <!-- Panel flotante con diseño de dos columnas en español -->
                <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end shadow p-4" aria-labelledby="accountDropdown" style="background-color: #141414; border: 1px solid #333; width: 420px; border-radius: 8px;">
                    <div class="row">
                        
                        <!-- Columna Izquierda: Tu Cuenta -->
                        <div class="col-6 border-end border-secondary">
                            <h6 class="text-uppercase text-white fs-7 mb-3 fw-bold" style="font-size: 0.75rem;">Tu cuenta</h6>
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2"><a class="text-decoration-none text-white small" href="#">Ayuda</a></li>
                                <li class="mb-2"><a class="text-decoration-none text-white small" href="#">Ver en cualquier lugar</a></li>
                                <li class="mb-2"><a class="text-decoration-none text-white small" href="<?= BASE_URL ?>Views/account.php">Cuenta y configuración</a></li>
                                <li class="mt-4"><a class="text-decoration-none text-danger small fw-bold" href="<?= BASE_URL ?>Views/logout.php">Cerrar sesión</a></li>
                            </ul>
                        </div>
                        
                       <!-- Columna Derecha: Perfiles -->
                        <div class="col-6 ps-3">
                            <h6 class="text-uppercase text-white fs-7 mb-3 fw-bold" style="font-size: 0.75rem;">Perfiles</h6>
                            <ul class="list-unstyled mb-0">
                                <?php if (!empty($perfiles)): ?>
                                    <?php foreach ($perfiles as $perfil): ?>
                                        <?php 
                                            // Comprobamos si este perfil es el activo actualmente
                                            $es_activo = (isset($_SESSION["id_perfil"]) && $_SESSION["id_perfil"] == $perfil["id_perfil"]);
                                            
                                            // Definir la ruta de la imagen del avatar de cada perfil
                                            $avatarImg = !empty($perfil["avatar"]) ? BASE_URL . "Resources/" . $perfil["avatar"] : BASE_URL . "Resources/avatar1.png";
                                        ?>
                                        <li class="mb-2">
                                            <a class="text-decoration-none small d-flex align-items-center gap-2 <?= $es_activo ? 'text-danger fw-bold' : 'text-white' ?>" 
                                               href="<?= BASE_URL ?>Views/select_profile.php?id=<?= $perfil["id_perfil"] ?>">
                                                <!-- Imagen de la foto de perfil -->
                                                <img src="<?= $avatarImg ?>" alt="Avatar" width="24" height="24" class="rounded-circle object-fit-cover border border-secondary">
                                                <span><?= htmlspecialchars($perfil["nombre_perfil"], ENT_QUOTES, "UTF-8") ?></span>
                                                <?php if ($es_activo): ?>
                                                    <span class="badge bg-danger ms-auto" style="font-size: 0.6rem;">Activo</span>
                                                <?php endif; ?>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <li class="mb-2"><span class="text-white small">No hay perfiles registrados</span></li>
                                <?php endif; ?>
                                <li class="mt-4"><a class="text-decoration-none text-white small" href="<?= BASE_URL ?>Views/profiles.php">Administrar perfiles</a></li>
                            </ul>
                        </div>

                    </div>
                </ul>
            </div>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#menuNavegacion">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
    </div>
</nav>