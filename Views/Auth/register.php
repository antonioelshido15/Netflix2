<?php
session_start();

require_once __DIR__ . "/../../database.php";

if (isset($_SESSION["id_cuenta"])) {
    if (empty($_SESSION["id_plan"])) {
        header("Location: " . BASE_URL . "Views/auth/planes.php");
    } else {
        header("Location: " . BASE_URL . "index.php");
    }
    exit;
}

$mensaje = "";
$conexion = getConnection();

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nombre = trim($_POST["nombre"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirmar = $_POST["confirmar"] ?? "";
    $terminos = isset($_POST["terminos"]);

    if ($nombre === "" || strlen($nombre) > 100) {
        $mensaje = "Escribe tu nombre (máximo 100 caracteres).";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensaje = "Escribe un correo electrónico válido.";
    } elseif (strlen($email) > 100) {
        $mensaje = "El correo no puede superar los 100 caracteres.";
    } elseif (strlen($password) < 8) {
        $mensaje = "La contraseña debe tener al menos 8 caracteres.";
    } elseif ($password !== $confirmar) {
        $mensaje = "Las contraseñas no coinciden.";
    } elseif (!$terminos) {
        $mensaje = "Debes aceptar los términos y condiciones.";
    } else {

        $consulta = $conexion->prepare(
            "SELECT id_cuenta FROM cuentas WHERE email = ?"
        );
        $consulta->execute([$email]);

        if ($consulta->fetch()) {
            $mensaje = "Ese correo ya está registrado.";
        } else {
            $passwordHash = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            // ID del plan que se asignará automáticamente al registrarse
            $id_plan_por_defecto = 1; 

            // Inserción incluyendo el nombre y el id_plan automático
            $consulta = $conexion->prepare(
                "INSERT INTO cuentas (nombre, id_plan, email, password) VALUES (?, ?, ?, ?)"
            );

            $consulta->execute([
                $nombre,
                $id_plan_por_defecto,
                $email,
                $passwordHash
            ]);

            header(
                "Location: " . BASE_URL .
                "Views/auth/login.php?registro=exitoso"
            );
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear cuenta</title>

    <link rel="stylesheet" href="<?= BASE_URL ?>CSS/bootstrap.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>CSS/authStyle.css">
</head>
<body class="auth-page">

<div class="auth-wrapper">
    <main class="auth-card">
        <h1 class="auth-title">CREAR CUENTA</h1>

        <?php if ($mensaje !== ""): ?>
            <div class="auth-message" role="alert">
                <?= htmlspecialchars($mensaje, ENT_QUOTES, "UTF-8") ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">

            <label class="auth-label" for="nombre">
                NOMBRE COMPLETO:
            </label>

            <input
                class="auth-input"
                type="text"
                id="nombre"
                name="nombre"
                maxlength="100"
                autocomplete="name"
                value="<?= htmlspecialchars($_POST["nombre"] ?? "", ENT_QUOTES, "UTF-8") ?>"
                required
            >

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
                value="<?= htmlspecialchars($_POST["email"] ?? "", ENT_QUOTES, "UTF-8") ?>"
                required
            >

            <label class="auth-label" for="password">
                CONTRASEÑA:
            </label>

            <input
                class="auth-input"
                type="password"
                id="password"
                name="password"
                minlength="8"
                autocomplete="new-password"
                required
            >

            <small>Usa al menos 8 caracteres.</small>

            <label class="auth-label" for="confirmar">
                CONFIRMAR CONTRASEÑA:
            </label>

            <input
                class="auth-input"
                type="password"
                id="confirmar"
                name="confirmar"
                minlength="8"
                autocomplete="new-password"
                required
            >
            
            <div style="margin-top: 20px;">
                <input
                    type="checkbox"
                    id="terminos"
                    name="terminos"
                    required
                >

                <label for="terminos">
                    Acepto los términos y condiciones.
                </label>
            </div>
            
            <button class="auth-button" type="submit">
                CREAR CUENTA
            </button>
        </form>

        <a class="auth-link" style="text-decoration: none; color: white;" href="<?= BASE_URL ?>Views/auth/login.php">
            ¿Ya tienes cuenta? Inicia sesión
        </a>
    </main>
</div>

</body>
</html>