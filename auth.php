<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class Auth {
    // Valida que el usuario normal haya iniciado sesión
    public static function requireAuth() {
        if (!isset($_SESSION['id_cuenta'])) {
            header("Location: " . BASE_URL . "Views/Auth/login.php");
            exit();
        }
    }

    // Valida que el usuario sea exclusivamente administrador
    public static function requireAdmin() {
        self::requireAuth(); // Primero verifica que esté logueado
        
        if (!isset($_SESSION['es_admin']) || $_SESSION['es_admin'] != 1) {
            // Si no es admin, lo patea de regreso al inicio
            header("Location: " . BASE_URL . "Views/home.php");
            exit();
        }
    }
}
?>