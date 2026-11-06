<?php
require_once '../config.php';
require_once '../database.php';

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Validar que el usuario haya iniciado sesión y sea administrador
if (!isset($_SESSION['id_cuenta']) || !isset($_SESSION['es_admin']) || $_SESSION['es_admin'] != 1) {
    header("Location: " . BASE_URL . "Views/home.php");
    exit();
}

// Obtener todo el contenido (usando la función procedimental)
$pdo = getConnection();
$stmt = $pdo->query("SELECT id_contenido, titulo, tipo_contenido, clasificacion_edad 
                     FROM Contenidos 
                     ORDER BY id_contenido DESC");
$contenidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo - Glitch TV Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/styles.css">
    <link rel="stylesheet" href="../CSS/accountStyles.css">
</head>
<body class="bg-dark text-white">
    <?php include 'navbar.php'; ?>

    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-danger fw-bold">Catálogo de Contenido</h2>
            <button class="btn btn-danger">Añadir Nuevo</button>
        </div>
        
        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Título</th>
                        <th>Tipo</th>
                        <th>Clasificación</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($contenidos as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['id_contenido']) ?></td>
                        <td><?= htmlspecialchars($item['titulo']) ?></td>
                        <td><?= htmlspecialchars($item['tipo_contenido']) ?></td>
                        <td><span class="badge border border-light text-light"><?= htmlspecialchars($item['clasificacion_edad']) ?></span></td>
                        <td>
                            <button class="btn btn-sm btn-outline-info">Editar</button>
                            <button class="btn btn-sm btn-outline-danger">Eliminar</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <a href="account.php" class="btn btn-outline-light mt-3">Volver al Panel</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>