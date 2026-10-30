<?php
session_start();

require_once __DIR__ . "/../../database.php";

// Si ya inició sesión, revisamos si tiene un plan.
if (isset($_SESSION["id_cuenta"])) {

    if (empty($_SESSION["id_plan"])) {
        header(
            "Location: " . BASE_URL .
            "Views/auth/planes.php"
        );
    } else {
        // Redirige a Views/home.php si ya tiene plan
        header("Location: " . BASE_URL . "Views/home.php");
    }

    exit;
}

$mensaje = "";

// Mensaje después de registrarse.
if (isset($_GET["registro"]) &&
    $_GET["registro"] === "exitoso") {
    $mensaje = "¡Cuenta creada! Inicia sesión para continuar.";
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($email === "" || $password === "") {

        $mensaje = "Completa todos los campos.";

    } else {

        $conexion = getConnection();

        $consulta = $conexion->prepare(
            "SELECT id_cuenta, id_plan, email,
                    password, estado_cuenta
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

            session_regenerate_id(true);

            $_SESSION["id_cuenta"] = $cuenta["id_cuenta"];
            $_SESSION["email"] = $cuenta["email"];
            $_SESSION["id_plan"] = $cuenta["id_plan"];

            // Si no tiene plan, primero debe elegirlo.
            if (empty($cuenta["id_plan"])) {

                header(
                    "Location: " . BASE_URL .
                    "Views/auth/plans.php"
                );

            } else {

                // Redirige a Views/home.php al iniciar sesión exitosamente
                header("Location: " . BASE_URL . "Views/home.php");

            }

            exit;

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

    <link rel="stylesheet"
          href="<?= BASE_URL ?>CSS/bootstrap.min.css">

    <link rel="stylesheet"
          href="<?= BASE_URL ?>CSS/styles.css">
</head>

<body class="auth-page">

<div class="auth-wrapper">
    <main class="auth-card">

        <h1 class="auth-title">INICIAR SESION</h1>

        <?php if ($mensaje !== ""): ?>
            <div class="auth-message" role="alert">
                <?= htmlspecialchars(
                    $mensaje,
                    ENT_QUOTES,
                    "UTF-8"
                ) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">

            <label class="auth-label" for="email">
                EMAIL:
            </label>

            <input
                class="auth-input"
                type="email"
                id="email"
                name="email"
                maxlength="100"
                autocomplete="email"
                required
                value="<?= htmlspecialchars(
                    $_POST["email"] ?? "",
                    ENT_QUOTES,
                    "UTF-8"
                ) ?>"
            >

            <label class="auth-label" for="password">
                CONTRASEÑA:
            </label>

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

        <a class="auth-link"
           href="<?= BASE_URL ?>Views/auth/register.php">
            ¿No tienes cuenta? Regístrate
        </a>

    </main>
</div>

</body>
</html>