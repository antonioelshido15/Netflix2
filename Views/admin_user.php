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

// Obtener todas las cuentas (usando la función procedimental)
$pdo = getConnection();
$stmt = $pdo->query("SELECT c.id_cuenta, c.nombre, c.email, c.es_admin, p.nombre_plan 
                     FROM Cuentas c 
                     LEFT JOIN PlanesSuscripcion p ON c.id_plan = p.id_plan
                     ORDER BY c.id_cuenta DESC");
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios - Glitch TV Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/styles.css">
    <link rel="stylesheet" href="../CSS/accountStyles.css">
</head>
<body class="bg-dark text-white">
    <?php include 'navbar.php'; ?>

    <div class="container mt-5">
        <h2 class="mb-4 text-info fw-bold">Gestión de Usuarios</h2>
        
        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Plan</th>
                        <th>Rol</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['id_cuenta']) ?></td>
                        <td><?= htmlspecialchars($user['nombre']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= htmlspecialchars($user['nombre_plan'] ?? 'Sin Plan') ?></td>
                        <td>
                            <?php if ($user['es_admin']): ?>
                                <span class="badge bg-danger">Admin</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Usuario</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-outline-info">Editar</button>
                            <button class="btn btn-sm btn-outline-danger">Suspender</button>
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