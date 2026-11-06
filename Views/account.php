
<?php
session_start();

// Generar token de seguridad para eliminar la cuenta
if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

require_once __DIR__ . "/../database.php";

// 1. Protección de ruta
if (!isset($_SESSION["id_cuenta"])) {
    header("Location: " . BASE_URL . "Views/Auth/login.php");
    exit;
}

$id_cuenta = $_SESSION["id_cuenta"];
$cuenta = null;
$es_admin = false;
$mensaje_plan = "";

try {
    $conexion = getConnection();

    // Actualizar plan
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['nuevo_plan'])) {
        $id_nuevo_plan = intval($_POST['nuevo_plan']);

        $stmtUpdate = $conexion->prepare(
            "UPDATE Cuentas SET id_plan = ? WHERE id_cuenta = ?"
        );

        if ($stmtUpdate->execute([$id_nuevo_plan, $id_cuenta])) {
            $mensaje_plan = "¡Suscripción actualizada correctamente!";
        }
    }

    // Obtener planes disponibles
    $stmtPlanes = $conexion->query("SELECT * FROM PlanesSuscripcion");
    $lista_planes = $stmtPlanes->fetchAll(PDO::FETCH_ASSOC);

    // Obtener información de la cuenta
    $query = "SELECT c.nombre, c.email, c.es_admin, c.fecha_registro, c.estado_cuenta,
                     p.id_plan, p.nombre_plan, p.precio, p.pantallas_simultaneas, p.resolucion_maxima
              FROM Cuentas c
              LEFT JOIN PlanesSuscripcion p ON c.id_plan = p.id_plan
              WHERE c.id_cuenta = ?";

    $stmt = $conexion->prepare($query);
    $stmt->execute([$id_cuenta]);
    $cuenta = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($cuenta) {
        $es_admin = (bool) $cuenta['es_admin'];
        $_SESSION["es_admin"] = $es_admin;
    }

} catch (Exception $e) {
    $error = "Error de conexión o consulta.";
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Configuración de Cuenta - Glitch TV</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../CSS/bootstrap.css">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <!-- CSS propio -->
    <link rel="stylesheet" href="../CSS/styles.css">
    <link rel="stylesheet" href="../CSS/accountStyles.css">
</head>

<body>

    <!-- NAVBAR -->
    <?php include_once __DIR__ . "/navbar.php"; ?>

    <!-- CONTENEDOR PRINCIPAL -->
    <main class="main-container" style="align-items: flex-start; padding-top: 120px;">

        <div class="glitch-card w-100 mx-auto" style="max-width: 1000px; padding: 40px;">

            <h1 class="main-title text-start mb-4 fs-2">
                Configuración de Cuenta
            </h1>

            <div class="row">

                <!-- MENÚ LATERAL -->
                <div class="col-md-3 mb-4 border-end border-secondary">

                    <div class="nav flex-column nav-pills custom-nav-pills"
                        id="v-pills-tab" role="tablist"
                        aria-orientation="vertical">

                        <button class="nav-link active"
                            id="v-pills-cuenta-tab"
                            data-bs-toggle="pill"
                            data-bs-target="#v-pills-cuenta"
                            type="button" role="tab">

                            <i class="bi bi-person-badge me-2"></i>
                            Detalles
                        </button>

                        <button class="nav-link"
                            id="v-pills-plan-tab"
                            data-bs-toggle="pill"
                            data-bs-target="#v-pills-plan"
                            type="button" role="tab">

                            <i class="bi bi-credit-card me-2"></i>
                            Suscripción
                        </button>

                        <!-- PANEL DE ADMINISTRADOR -->
                        <?php if ($es_admin): ?>

                            <button class="nav-link nav-link-admin mt-4 text-info border border-info"
                                id="v-pills-admin-tab"
                                data-bs-toggle="pill"
                                data-bs-target="#v-pills-admin"
                                type="button" role="tab">

                                <i class="bi bi-shield-lock me-2"></i>
                                Panel de Control
                            </button>

                        <?php endif; ?>

                    </div>
                </div>

                <!-- CONTENIDO DE LAS PESTAÑAS -->
                <div class="col-md-9 px-md-4 text-start">

                    <div class="tab-content text-white" id="v-pills-tabContent">

                        <!-- DETALLES DE LA CUENTA -->
                        <div class="tab-pane fade show active"
                            id="v-pills-cuenta" role="tabpanel">

                            <h3 class="main-subtitle fs-5 border-bottom border-secondary pb-2 mb-4">
                                Información Personal
                            </h3>

                            <!-- FORMULARIO DE ACTUALIZACIÓN -->
                            <form id="updateAccountForm" action="" method="POST">

                                <div class="mb-3">

                                    <label class="auth-label">
                                        Nombre del Titular
                                    </label>

                                    <input type="text"
                                        class="glitch-input"
                                        value="<?= htmlspecialchars($cuenta['nombre'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

                                </div>

                                <div class="mb-3">

                                    <label class="auth-label">
                                        Correo Electrónico
                                    </label>

                                    <input type="email"
                                        class="glitch-input"
                                        value="<?= htmlspecialchars($cuenta['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                        readonly
                                        style="opacity: 0.7;">

                                    <small class="text-muted">
                                        El correo electrónico no se puede modificar.
                                    </small>

                                </div>

                                <div class="mb-4 mt-4">

                                    <label class="auth-label">
                                        Contraseña
                                    </label>

                                    <input type="password"
                                        class="glitch-input"
                                        value="********"
                                        readonly>

                                    <a href="#"
                                        class="text-decoration-none"
                                        style="color: var(--glitch-cyan);">

                                        Cambiar contraseña

                                    </a>

                                </div>

                            </form>

                            <!-- BOTONES DE ACCIÓN -->
                            <div class="d-flex flex-wrap align-items-center gap-3 mt-4">

                                <!-- ACTUALIZAR DATOS -->
                                <button type="submit"
                                    form="updateAccountForm"
                                    class="btn-glitch px-4">

                                    <i class="bi bi-pencil-square me-2"></i>
                                    Actualizar Datos

                                </button>

                                <!-- ELIMINAR CUENTA -->
                                <form id="deleteAccountForm"
                                    action="deleteAccount.php"
                                    method="POST"
                                    class="m-0"
                                    onsubmit="return confirm('¿Estás seguro de que deseas eliminar tu cuenta? Esta acción no se puede deshacer.');">

                                    <input type="hidden"
                                        name="token"
                                        value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">

                                    <button type="submit"
                                        class="btn-delete-account px-4">

                                        <i class="bi bi-trash3 me-2"></i>
                                        Eliminar cuenta

                                    </button>

                                </form>

                            </div>

                        </div>

                        <!-- PESTAÑA DE SUSCRIPCIÓN -->
                        <div class="tab-pane fade"
                            id="v-pills-plan" role="tabpanel">

                            <h3 class="main-subtitle fs-5 border-bottom border-secondary pb-2 mb-4">
                                Tu Plan Actual
                            </h3>

                            <!-- MENSAJE DE ÉXITO -->
                            <?php if ($mensaje_plan !== ""): ?>

                                <div class="alert alert-success bg-dark text-success border-success text-center py-2">

                                    <i class="bi bi-check-circle me-2"></i>
                                    <?= $mensaje_plan ?>

                                </div>

                            <?php endif; ?>

                            <?php if (!empty($cuenta['nombre_plan'])): ?>

                                <div class="d-flex justify-content-between align-items-center bg-dark rounded p-4 mb-4"
                                    style="border: 1px solid var(--input-border);">

                                    <div>

                                        <h4 class="mb-1 text-uppercase fw-bold"
                                            style="color: var(--glitch-pink);">

                                            <?= htmlspecialchars($cuenta['nombre_plan']) ?>

                                        </h4>

                                        <p class="mb-0 text-muted">

                                            Resolución <?= htmlspecialchars($cuenta['resolucion_maxima']) ?> •

                                            <?= htmlspecialchars($cuenta['pantallas_simultaneas']) ?>
                                            Pantallas simultáneas

                                        </p>

                                    </div>

                                    <div class="text-end">

                                        <p class="mb-0 fw-bold fs-3">

                                            $<?= number_format($cuenta['precio'], 2) ?>

                                            <span class="fs-6 fw-normal text-muted">
                                                /mes
                                            </span>

                                        </p>

                                    </div>

                                </div>

                            <?php else: ?>

                                <div class="alert alert-warning bg-transparent border-warning text-warning mb-4">

                                    Actualmente no tienes un plan activo.

                                </div>

                            <?php endif; ?>

                            <!-- CAMBIAR PLAN -->
                            <button class="btn btn-outline-light fw-bold"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#collapsePlan"
                                aria-expanded="false"
                                aria-controls="collapsePlan">

                                Cambiar de Plan

                            </button>

                            <div class="collapse mt-4" id="collapsePlan">

                                <div class="glitch-card bg-transparent text-start p-4"
                                    style="border: 1px dashed var(--glitch-cyan); max-width: 100%;">

                                    <form action="" method="POST">

                                        <label class="auth-label mb-2"
                                            style="color: var(--glitch-cyan);">

                                            Selecciona tu nuevo plan:

                                        </label>

                                        <select class="glitch-input auth-select mb-4"
                                            name="nuevo_plan"
                                            required>

                                            <option value="" disabled selected>
                                                -- Elige un plan --
                                            </option>

                                            <?php foreach ($lista_planes as $plan): ?>

                                                <option value="<?= $plan['id_plan'] ?>"
                                                    <?= ($cuenta['id_plan'] == $plan['id_plan']) ? 'disabled' : '' ?>>

                                                    <?= htmlspecialchars($plan['nombre_plan']) ?> -

                                                    $<?= number_format($plan['precio'], 2) ?>/mes

                                                    <?= ($cuenta['id_plan'] == $plan['id_plan']) ? '(Plan Actual)' : '' ?>

                                                </option>

                                            <?php endforeach; ?>

                                        </select>

                                        <button type="submit" class="btn-glitch w-100">
                                            Confirmar Cambio de Plan
                                        </button>

                                    </form>

                                </div>

                            </div>

                            <!-- ESTADO DE LA CUENTA -->
                            <h3 class="main-subtitle fs-5 border-bottom border-secondary pb-2 mb-4 mt-5">

                                Estado de la Cuenta

                            </h3>

                            <p class="fs-6">

                                Estado actual:

                                <span class="badge <?= ($cuenta['estado_cuenta'] ?? '') === 'Activa' ? 'bg-success' : 'bg-danger' ?> fs-6">

                                    <?= htmlspecialchars($cuenta['estado_cuenta'] ?? '') ?>

                                </span>

                            </p>

                            <p class="text-white small">

                                Miembro desde el

                                <?= !empty($cuenta['fecha_registro']) ? date('d/m/Y', strtotime($cuenta['fecha_registro'])) : '' ?>

                            </p>

                        </div>

                        <!-- PANEL DE ADMINISTRACIÓN -->
                        <?php if ($es_admin): ?>

                            <div class="tab-pane fade"
                                id="v-pills-admin" role="tabpanel">

                                <h3 class="main-subtitle fs-5 border-bottom border-info pb-2 mb-4"
                                    style="color: var(--glitch-cyan);">

                                    Gestión Administrativa

                                </h3>

                                <p class="text-white">
                                    Herramientas exclusivas para el personal de Glitch TV.
                                </p>

                                <div class="row g-4">

                                    <!-- CATÁLOGO -->
                                    <div class="col-md-6">

                                        <div class="card bg-dark text-white h-100 text-center p-3"
                                            style="border: 1px solid var(--glitch-pink);">

                                            <div class="card-body">

                                                <i class="bi bi-film mb-3 d-block"
                                                    style="font-size: 2.5rem; color: var(--glitch-pink);"></i>

                                                <h5 class="card-title fw-bold">
                                                    Catálogo de Contenido
                                                </h5>

                                                <p class="card-text text-white small mb-4">
                                                    Añadir, modificar o eliminar películas, series y documentales de la base de datos.
                                                </p>

                                                <a href="admin_catalog.php"
                                                    class="btn-glitch w-100"
                                                    style="padding: 10px;">

                                                    Gestionar Catálogo

                                                </a>

                                            </div>

                                        </div>

                                    </div>

                                    <!-- USUARIOS -->
                                    <div class="col-md-6">

                                        <div class="card bg-dark text-white h-100 text-center p-3"
                                            style="border: 1px solid var(--glitch-cyan);">

                                            <div class="card-body">

                                                <i class="bi bi-people mb-3 d-block"
                                                    style="font-size: 2.5rem; color: var(--glitch-cyan);"></i>

                                                <h5 class="card-title fw-bold">
                                                    Gestión de Usuarios
                                                </h5>

                                                <p class="card-text text-white small mb-4">
                                                    Visualizar cuentas registradas, modificar estados (suspender/activar) y revisar pagos.
                                                </p>

                                                <a href="admin_user.php"
                                                    class="btn-glitch w-100"
                                                    style="padding: 10px;">

                                                    Ver Usuarios

                                                </a>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        <?php endif; ?>

                    </div>
                </div>

            </div>

        </div>

    </main>

    <!-- BOOTSTRAP JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
