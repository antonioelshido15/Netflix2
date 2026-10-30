<?php
session_start();
require_once __DIR__ . "/../database.php";

// Verificar que haya sesión y que llegue el ID del perfil
if (isset($_SESSION["id_cuenta"]) && isset($_GET["id"])) {
    $id_perfil = intval($_GET["id"]);
    $id_cuenta = $_SESSION["id_cuenta"];

    try {
        $conexion = getConnection();

        // Si se recibe una acción de eliminar, borramos el perfil
        if (isset($_GET["action"]) && $_GET["action"] === "delete") {
            $stmtDel = $conexion->prepare("DELETE FROM perfiles WHERE id_perfil = ? AND id_cuenta = ?");
            $stmtDel->execute([$id_perfil, $id_cuenta]);

            // Si el perfil que se eliminó era el que estaba activo en la sesión, lo limpiamos
            if (isset($_SESSION["id_perfil"]) && $_SESSION["id_perfil"] == $id_perfil) {
                unset($_SESSION["id_perfil"]);
                unset($_SESSION["nombre_perfil"]);
                unset($_SESSION["avatar"]);
            }

            // Redirigir de regreso a la administración o donde prefieras
            header("Location: " . BASE_URL . "Views/profiles.php");
            exit;
        }

        // Comportamiento normal: Verificamos que el perfil pertenezca a la cuenta activa[cite: 8]
        $stmt = $conexion->prepare("SELECT * FROM perfiles WHERE id_perfil = ? AND id_cuenta = ?");
        $stmt->execute([$id_perfil, $id_cuenta]);
        $perfil = $stmt->fetch();

        if ($perfil) {
            // Guardamos los datos del perfil activo en la sesión[cite: 8]
            $_SESSION["id_perfil"] = $perfil["id_perfil"];
            $_SESSION["nombre_perfil"] = $perfil["nombre_perfil"];
            $_SESSION["avatar"] = $perfil["avatar"] ?? "default.png";
        }
    } catch (Exception $e) {
        // Manejo de error opcional[cite: 8]
    }
}

// Redirigir siempre al home (si fue selección normal)[cite: 8]
header("Location: " . BASE_URL . "Views/home.php");
exit;