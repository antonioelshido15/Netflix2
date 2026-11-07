<?php
require_once '../config.php';
require_once '../database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['id_cuenta']) || !isset($_SESSION['es_admin']) || $_SESSION['es_admin'] != 1) {
    header("Location: " . BASE_URL . "Views/home.php");
    exit();
}

$pdo = getConnection();

// --- LÓGICA PARA ACTUALIZAR USUARIO ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar_usuario'])) {
    $id_editar = $_POST['id_cuenta'];
    $nombre_nuevo = $_POST['nombre'];
    $email_nuevo = $_POST['email'];
    $es_admin_nuevo = $_POST['es_admin'];

    $sql_update = "UPDATE Cuentas SET nombre = :nombre, email = :email, es_admin = :es_admin WHERE id_cuenta = :id_cuenta";
    $stmt_update = $pdo->prepare($sql_update);
    $stmt_update->execute([':nombre' => $nombre_nuevo, ':email' => $email_nuevo, ':es_admin' => $es_admin_nuevo, ':id_cuenta' => $id_editar]);

    header("Location: admin_user.php");
    exit();
}

// --- LÓGICA PARA SUSPENDER / REACTIVAR USUARIO ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_suspension'])) {
    $id_suspender = $_POST['id_cuenta'];
    $estado_actual = $_POST['estado_actual'];
    $nuevo_estado = $estado_actual ? 0 : 1; // Si es 1 lo pasa a 0, si es 0 lo pasa a 1

    $stmt_susp = $pdo->prepare("UPDATE Cuentas SET suspendido = :estado WHERE id_cuenta = :id");
    $stmt_susp->execute([':estado' => $nuevo_estado, ':id' => $id_suspender]);

    header("Location: admin_user.php");
    exit();
}

// --- OBTENER CUENTAS (Incluyendo estado de suspensión) ---
$stmt = $pdo->query("SELECT c.id_cuenta, c.nombre, c.email, c.es_admin, c.suspendido, p.nombre_plan 
                     FROM Cuentas c 
                     LEFT JOIN PlanesSuscripcion p ON c.id_plan = p.id_plan
                     ORDER BY c.id_cuenta ASC"); 
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
                        <th>Estado</th>
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
                            <?php if ($user['suspendido']): ?>
                                <span class="badge border border-danger text-danger">Suspendido</span>
                            <?php else: ?>
                                <span class="badge border border-success text-success">Activo</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#editModal<?= $user['id_cuenta'] ?>">Editar</button>
                            
                            <!-- Formulario para Suspender/Reactivar -->
                            <form method="POST" action="admin_user.php" class="d-inline">
                                <input type="hidden" name="id_cuenta" value="<?= $user['id_cuenta'] ?>">
                                <input type="hidden" name="estado_actual" value="<?= $user['suspendido'] ?>">
                                <?php if ($user['suspendido']): ?>
                                    <button type="submit" name="toggle_suspension" class="btn btn-sm btn-outline-success">Reactivar</button>
                                <?php else: ?>
                                    <button type="submit" name="toggle_suspension" class="btn btn-sm btn-outline-danger">Suspender</button>
                                <?php endif; ?>
                            </form>
                        </td>
                    </tr>

                    <!-- MODAL DE EDICIÓN PARA ESTE USUARIO -->
                    <div class="modal fade" id="editModal<?= $user['id_cuenta'] ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content bg-dark text-white border-info">
                                <div class="modal-header border-secondary">
                                    <h5 class="modal-title text-info fw-bold">Editar Usuario: <?= htmlspecialchars($user['nombre']) ?></h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form method="POST" action="admin_user.php">
                                    <div class="modal-body">
                                        <input type="hidden" name="id_cuenta" value="<?= $user['id_cuenta'] ?>">
                                        <div class="mb-3">
                                            <label class="form-label">Nombre</label>
                                            <input type="text" class="form-control bg-secondary text-white border-0" name="nombre" value="<?= htmlspecialchars($user['nombre']) ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Email</label>
                                            <input type="email" class="form-control bg-secondary text-white border-0" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Rol de Cuenta</label>
                                            <select class="form-select bg-secondary text-white border-0" name="es_admin">
                                                <option value="0" <?= !$user['es_admin'] ? 'selected' : '' ?>>Usuario Normal</option>
                                                <option value="1" <?= $user['es_admin'] ? 'selected' : '' ?>>Administrador</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-secondary">
                                        <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cancelar</button>
                                        <button type="submit" name="editar_usuario" class="btn btn-info text-dark fw-bold">Guardar Cambios</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- FIN DEL MODAL -->

                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <a href="account.php" class="btn btn-outline-light mt-3">Volver al Panel</a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>