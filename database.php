<?php

require_once __DIR__ . "/config.php";

function getConnection()
{
    try {
        $conexion = new PDO(
            "mysql:host=" . DB_HOST .
            ";dbname=" . DB_NAME .
            ";charset=" . DB_CHARSET,
            DB_USER,
            DB_PASS
        );

        $conexion->setAttribute(
            PDO::ATTR_ERRMODE,
            PDO::ERRMODE_EXCEPTION
        );

        $conexion->setAttribute(
            PDO::ATTR_DEFAULT_FETCH_MODE,
            PDO::FETCH_ASSOC
        );

        return $conexion;

    } catch (PDOException $e) {
        error_log($e->getMessage());
        die("No fue posible conectar con la base de datos.");
    }
}