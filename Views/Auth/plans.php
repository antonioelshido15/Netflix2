
<?php
session_start();

require_once __DIR__ . "/../../database.php";

if (!isset($_SESSION["id_cuenta"])) {
    header(
        "Location: " . BASE_URL .
        "Views/auth/login.php"
    );
    exit;
}

$conexion = getConnection();
$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $id_plan = filter_input(
        INPUT_POST,
        "id_plan",
        FILTER_VALIDATE_INT
    );

    if (!$id_plan) {
        $mensaje = "Selecciona un plan válido.";
    } else {
        $consulta = $conexion->prepare(
            "SELECT id_plan
             FROM PlanesSuscripcion
             WHERE id_plan = ?"
        );

        $consulta->execute([$id_plan]);

        if (!$consulta->fetch()) {
            $mensaje = "El plan seleccionado no existe.";
        } else {
            $consulta = $conexion->prepare(
                "UPDATE Cuentas
                 SET id_plan = ?
                 WHERE id_cuenta = ?"
            );

            $consulta->execute([
                $id_plan,
                $_SESSION["id_cuenta"]
            ]);

            $_SESSION["id_plan"] = $id_plan;

            header("Location: " . BASE_URL . "index.php");
            exit;
        }
    }
}

$consulta = $conexion->query(
    "SELECT id_plan, nombre_plan, precio,
            pantallas_simultaneas, resolucion_maxima
     FROM PlanesSuscripcion
     ORDER BY precio"
);

$planes = $consulta->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Elige tu plan</title>

    <link rel="stylesheet" href="<?= BASE_URL ?>CSS/bootstrap.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>CSS/authStyle.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>CSS/plansStyle.css">

</head>

<body class="auth-page">

<div class="auth-wrapper" style="max-width: 1100px; margin: auto;">
    <main style="width: 100%;">

        <h1 class="auth-title">ELIGE UN PLAN DE SUSCRIPCIÓN</h1>

        <center><p>Selecciona en plan para comenzar.</p></center>

        <?php if ($mensaje !== ""): ?>
            <div class="auth-message" role="alert">
                <?= htmlspecialchars(
                    $mensaje,
                    ENT_QUOTES,
                    "UTF-8"
                ) ?>
            </div>
        <?php endif; ?>

        <?php if (empty($planes)): ?>
            <p>No hay planes disponibles en la base de datos.</p>
        <?php else: ?>

            <form method="POST">
                <div class="planes-grid">

                    <?php foreach ($planes as $plan): ?>

                        <article class="plan-card">
                            <h2><strong>
                                <?= htmlspecialchars(
                                    $plan["nombre_plan"],
                                    ENT_QUOTES,
                                    "UTF-8"
                                ) ?>
                                <hr class="my-3">
                            </strong></h2>
                                
                            <p>
                                <strong>
                                    $<?= number_format(
                                        $plan["precio"],
                                        2
                                    ) ?>
                                </strong>
                                MXN / mes
                            </p>

                            <p>
                                Pantallas simultáneas:
                                <?= (int)$plan["pantallas_simultaneas"] ?>
                            </p>

                            <p>
                                Resolución:
                                <?= htmlspecialchars(
                                    $plan["resolucion_maxima"],
                                    ENT_QUOTES,
                                    "UTF-8"
                                ) ?>
                            </p>

                            <button
                                class="auth-button"
                                type="submit"
                                name="id_plan"
                                value="<?= (int)$plan["id_plan"] ?>"
                            >
                                ELEGIR PLAN
                            </button>
                        </article>

                    <?php endforeach; ?>

                </div>
            </form>

        <?php endif; ?>

    </main>
</div>

</body>
</html>
