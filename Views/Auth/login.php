<?php
session_start();

require_once __DIR__ . "/../../database.php";

// Si ya inició sesión, revisamos si tiene un plan.
if (isset($_SESSION["id_cuenta"])) {
    if (empty($_SESSION["id_plan"])) {
        header("Location: " . BASE_URL . "Views/auth/planes.php");
    } else {
        // Redirige a Views/home.php si ya tiene plan
        header("Location: " . BASE_URL . "Views/home.php");
    }
    exit;
}

$mensaje = "";
$cuenta_suspendida = false;

// Mensaje después de registrarse.
if (isset($_GET["registro"]) && $_GET["registro"] === "exitoso") {
    $mensaje = "¡Cuenta creada! Inicia sesión para continuar.";
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($email === "" || $password === "") {
        $mensaje = "Completa todos los campos.";
    } else {
        $conexion = getConnection();

        // Agregamos 'suspendido' y 'es_admin' a la consulta
        $consulta = $conexion->prepare(
            "SELECT id_cuenta, id_plan, email, password, estado_cuenta, suspendido, es_admin
             FROM Cuentas
             WHERE email = ?"
        );

        $consulta->execute([$email]);
        $cuenta = $consulta->fetch();

        if (
            $cuenta &&
            password_verify($password, $cuenta["password"]) &&
            $cuenta["estado_cuenta"] === "Activa"
        ) {
            // Verificamos si la cuenta fue suspendida por un administrador
            if ($cuenta["suspendido"] == 1) {
                $cuenta_suspendida = true;
            } else {
                // Inicio de sesión exitoso
                session_regenerate_id(true);

                $_SESSION["id_cuenta"] = $cuenta["id_cuenta"];
                $_SESSION["email"] = $cuenta["email"];
                $_SESSION["id_plan"] = $cuenta["id_plan"];
                $_SESSION["es_admin"] = $cuenta["es_admin"]; // Guardamos si es admin para las vistas

                // Si no tiene plan, primero debe elegirlo.
                if (empty($cuenta["id_plan"])) {
                    header("Location: " . BASE_URL . "Views/auth/plans.php");
                } else {
                    header("Location: " . BASE_URL . "Views/home.php");
                }
                exit;
            }
        } else {
            $mensaje = "Correo o contraseña incorrectos, o cuenta no activa.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>CSS/bootstrap.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>CSS/styles.css">
</head>

<body class="auth-page">

<div class="auth-wrapper">
    <main class="auth-card">

        <h1 class="auth-title">INICIAR SESION</h1>

        <?php if ($cuenta_suspendida): ?>
            <!-- RECUADRO DE CUENTA SUSPENDIDA -->
            <div class="alert mt-4 p-4 text-center" style="background-color: #1a1a1a; border: 2px solid #dc3545; border-radius: 8px;">
                <h5 class="text-white fw-bold mb-0">La cuenta ha sido suspendida indefinidamente.</h5>
            </div>
            
            <!-- BOTÓN DE CERRAR SESIÓN CON ESTILO GLITCH -->
            <a href="<?= BASE_URL ?>Views/logout.php" class="auth-button" style="display: block; text-align: center; text-decoration: none; margin-top: 25px;">
                CERRAR SESIÓN
            </a>
        <?php else: ?>
            <!-- FORMULARIO NORMAL (Solo se muestra si no está suspendido) -->
            <?php if ($mensaje !== ""): ?>
                <div class="auth-message" role="alert">
                    <?= htmlspecialchars($mensaje, ENT_QUOTES, "UTF-8") ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <label class="auth-label" for="email">EMAIL:</label>
                <input
                    class="auth-input"
                    type="email"
                    id="email"
                    name="email"
                    maxlength="100"
                    autocomplete="email"
                    required
                    value="<?= htmlspecialchars($_POST["email"] ?? "", ENT_QUOTES, "UTF-8") ?>"
                >

                <label class="auth-label" for="password">CONTRASEÑA:</label>
                <input
                    class="auth-input"
                    type="password"
                    id="password"
                    name="password"
                    autocomplete="current-password"
                    required
                >

                <button class="auth-button" type="submit">
                    INICIAR SESION
                </button>
            </form>

            <a class="auth-link" href="<?= BASE_URL ?>Views/auth/register.php">
                ¿No tienes cuenta? Regístrate
            </a>
        <?php endif; ?>

    </main>
</div>

</body>
</html>