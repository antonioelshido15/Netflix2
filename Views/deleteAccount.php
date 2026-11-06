
<?php

session_start();
require_once __DIR__ . "/../database.php";

// Verificar que el usuario haya iniciado sesión
if (!isset($_SESSION["id_cuenta"])) {
    header("Location: " . BASE_URL . "Views/Auth/login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: " . BASE_URL . "Views/account.php");
    exit;
}

// Verificar el token de seguridad
if (
    !isset($_POST["token"]) ||
    !isset($_SESSION["csrf_token"]) ||
    !hash_equals($_SESSION["csrf_token"], $_POST["token"])
) {
    die("Solicitud no válida.");
}

// Obtener el ID de la cuenta
$id_cuenta = $_SESSION["id_cuenta"];

try {
    $conexion = getConnection();

    // Eliminar la cuenta
    $consulta = $conexion->prepare(
        "DELETE FROM Cuentas WHERE id_cuenta = ?"
    );

    $consulta->execute([$id_cuenta]);

    if ($consulta->rowCount() > 0) {

        // Destruir la sesión
        $_SESSION = [];
        session_destroy();

        // Redirigir al index
        header("Location: " . BASE_URL . "index.php?cuenta=eliminada");
        exit;

    } else {

        echo "No se encontró la cuenta que deseas eliminar.";

    }

} catch (PDOException $e) {

    echo "No fue posible eliminar la cuenta. Puede que existan registros relacionados con ella en otras tablas.";

}

?>
