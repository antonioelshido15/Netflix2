<?php
session_start();

$_SESSION = [];

session_destroy();

require_once __DIR__ . "/../config.php";

// Redirige al index principal de la raíz
header("Location: " . BASE_URL . "index.php");
exit;
?>