<?php

session_start();
$_SESSION = [];

// Eliminar la cookie de sesión
if (ini_get("session.use_cookies")) {

    $parametros = session_get_cookie_params();

    setcookie(
        session_name(),
        "",
        time() - 42000,
        $parametros["path"],
        $parametros["domain"],
        $parametros["secure"],
        $parametros["httponly"]
    );
}

session_destroy();

require_once __DIR__ . "/../config.php";

// Regresar al index
header("Location: " . BASE_URL . "index.php");
exit;

?>