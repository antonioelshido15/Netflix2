<?php
// Activar errores para diagnóstico
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Cargar la conexión a la base de datos
require_once __DIR__ . "/../database.php";

// Obtener el ID de la serie y el ID del episodio desde la URL (por defecto serie 1, episodio 1)
$id_contenido = isset($_GET['id']) ? intval($_GET['id']) : 1;
$id_ep = isset($_GET['id_ep']) ? intval($_GET['id_ep']) : 1;

/**
 * =========================================================================
 * CONFIGURACIÓN DE SERIES Y SUS RESPECTIVOS EPISODIOS (id_ep)
 * =========================================================================
 */
$recursos_extra = [
    1 => [ // ID 1: The Amazing Digital Circus (9 Episodios)
        'poster' => BASE_URL . 'Resources/Poster/Poster2.png',
        'episodios' => [
            1 => ['titulo' => 'Episodio 1: Piloto', 'min' => '25:30', 'url_video' => 'https://www.youtube.com/embed/HwAPLk_sQ3w'],
            2 => ['titulo' => 'Episodio 2', 'min' => '23:10', 'url_video' => 'https://www.youtube.com/embed/4ofJpOEXrZs'],
            3 => ['titulo' => 'Episodio 3', 'min' => '20:00', 'url_video' => 'https://www.youtube.com/embed/bKjfw77cxeQ'],
            4 => ['titulo' => 'Episodio 4', 'min' => '21:00', 'url_video' => 'https://www.youtube.com/embed/Q9KWcWKo2T8'],
            5 => ['titulo' => 'Episodio 5', 'min' => '22:00', 'url_video' => 'https://www.youtube.com/embed/L4p2gN2CzsA'],
            6 => ['titulo' => 'Episodio 6', 'min' => '20:30', 'url_video' => 'https://www.youtube.com/embed/mOvhHim78YA'],
            7 => ['titulo' => 'Episodio 7', 'min' => '24:10', 'url_video' => 'https://www.youtube.com/embed/oaOG1xOk7XY'],
            8 => ['titulo' => 'Episodio 8', 'min' => '22:45', 'url_video' => 'https://www.youtube.com/embed/DMNlzf8PiEM'],
            9 => ['titulo' => 'Episodio 9', 'min' => '26:00', 'url_video' => 'https://www.youtube.com/embed/PVy-bcTsO8A'],
        ]
    ],
    2 => [ // ID 2: Gaslight District (1 Episodio)
        'poster' => BASE_URL . 'Resources/Poster/Poster3.png',
        'episodios' => [
            1 => ['titulo' => 'Episodio 1: Piloto', 'min' => '18:45', 'url_video' => 'https://www.youtube.com/embed/IC8KsZniulw']
        ]
    ],
    3 => [ // ID 3: Knights of Guinevere (2 Episodios)
        'poster' => BASE_URL . 'Resources/Poster/Poster4.png',
        'episodios' => [
            1 => ['titulo' => 'Episodio 1: Piloto', 'min' => '22:15', 'url_video' => 'https://www.youtube.com/embed/MCAdbUaMlAE'],
            2 => ['titulo' => 'Episodio 2', 'min' => '20:15', 'url_video' => 'https://www.youtube.com/embed/vluX-LA1Vc8']
        ]
    ],
    4 => [ // ID 4: Gameoverse (1 Episodio)
        'poster' => BASE_URL . 'Resources/Poster/Poster1.png',
        'episodios' => [
            1 => ['titulo' => 'Episodio 1: Piloto', 'min' => '19:50', 'url_video' => 'https://www.youtube.com/embed/bNcqwwTkohY']
        ]
    ]
];

// 2. Consulta a la base de datos para los datos de la serie actual
$contenido = null;
$lista_series = [];

try {
    $pdo = getConnection(); 

    // Obtener la serie seleccionada por su id
    $stmt = $pdo->prepare("SELECT * FROM contenidos WHERE id_contenido = ?");
    $stmt->execute([$id_contenido]);
    $contenido = $stmt->fetch(PDO::FETCH_ASSOC);

    // Fallback si no existe
    if (!$contenido) {
        $stmt->execute([1]);
        $contenido = $stmt->fetch(PDO::FETCH_ASSOC);
        $id_contenido = 1;
    }

    // Obtener todas las series para la sección inferior "Más Series"
    $stmt_all = $pdo->query("SELECT * FROM contenidos");
    $lista_series = $stmt_all->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    $contenido = [
        'id_contenido' => 1,
        'titulo' => 'Error de conexión',
        'descripcion' => 'No se pudo conectar a la base de datos.',
        'tipo_contenido' => 'Sistema',
        'clasificacion_edad' => 'Todo',
        'anio_lanzamiento' => '2026'
    ];
}

// Validar que la serie y el episodio existan en el arreglo, si no, tomar el primero por defecto
$current_serie = $recursos_extra[$id_contenido] ?? $recursos_extra[1];
if (!isset($current_serie['episodios'][$id_ep])) {
    $id_ep = array_key_first($current_serie['episodios']);
}
$episodio_actual = $current_serie['episodios'][$id_ep];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($contenido['titulo']) ?> - <?= $episodio_actual['titulo'] ?> - Glitch TV</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>CSS/styles.css">
    
    <style>
        .episode-card, .series-card {
            background-color: var(--card-bg, rgba(30, 30, 35, 0.85));
            border: 1px solid var(--input-border, #4d4d5a);
            transition: transform 0.2s, border-color 0.2s;
            cursor: pointer;
        }
        .episode-card:hover, .series-card:hover {
            border-color: var(--glitch-cyan, #00e5ff);
            transform: translateY(-2px);
        }
        .episode-card.active-ep {
            border-color: var(--glitch-pink, #ff2a5f);
            background-color: rgba(255, 42, 95, 0.15);
        }
        .sidebar-playlist {
            max-height: 500px;
            overflow-y: auto;
            scrollbar-width: thin;
        }
    </style>
</head>
<body>

    <!-- Incluir Navbar -->
    <?php 
    if (file_exists(__DIR__ . "/navbar.php")) {
        include_once __DIR__ . "/navbar.php";
    }
    ?>

    <main class="container-fluid px-lg-5 my-4 pt-5">
        <!-- Fila Superior: Reproductor y Lista de Episodios -->
        <div class="row g-4">
            <!-- Columna Izquierda: Reproductor y Detalles -->
            <div class="col-lg-8">
                <div class="ratio ratio-16x9 rounded overflow-hidden shadow mb-4 border border-secondary">
                    <iframe 
                        src="<?= $episodio_actual['url_video'] ?>?autoplay=1&modestbranding=1&rel=0" 
                        title="<?= htmlspecialchars($contenido['titulo']) ?>" 
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                        allowfullscreen>
                    </iframe>
                </div>

                <div class="p-4 rounded bg-dark text-white border border-secondary shadow-sm">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <h1 class="fw-bold fs-3 m-0 text-white"><?= htmlspecialchars($contenido['titulo']) ?></h1>
                            <p class="text-info mb-0 fw-semibold mt-1"><?= $episodio_actual['titulo'] ?></p>
                        </div>
                        <div>
                            <span class="badge bg-info text-dark me-2"><?= htmlspecialchars($contenido['tipo_contenido']) ?></span>
                            <span class="badge bg-warning text-dark me-2">Clasificación: <?= htmlspecialchars($contenido['clasificacion_edad']) ?></span>
                            <span class="badge bg-secondary"><?= htmlspecialchars($contenido['anio_lanzamiento']) ?></span>
                        </div>
                    </div>
                    <p class="text-white-50 mb-0 mt-3 fs-6"><?= htmlspecialchars($contenido['descripcion']) ?></p>
                </div>
            </div>

            <!-- Columna Derecha: Lista de Episodios dinámicos con id y id_ep -->
            <div class="col-lg-4">
                <div class="p-3 rounded bg-dark border border-secondary shadow-sm">
                    <h4 class="text-white text-uppercase fs-6 fw-bold mb-3 border-bottom pb-2">
                        <i class="fa-solid fa-play-circle me-2 text-info"></i> Episodios
                    </h4>
                    <div class="sidebar-playlist d-flex flex-column gap-3">
                        <?php foreach ($current_serie['episodios'] as $num_ep => $ep): 
                            $is_active = ($num_ep === $id_ep);
                        ?>
                            <a href="Content.php?id=<?= $id_contenido ?>&id_ep=<?= $num_ep ?>" class="text-decoration-none">
                                <div class="episode-card d-flex align-items-center rounded p-2 gap-3 text-white <?= $is_active ? 'active-ep' : '' ?>">
                                    <img src="<?= $current_serie['poster'] ?>" alt="Miniatura" class="rounded object-fit-cover" style="width: 110px; height: 65px;">
                                    <div class="overflow-hidden">
                                        <h6 class="mb-1 text-truncate fw-bold text-white fs-6"><?= htmlspecialchars($ep['titulo']) ?></h6>
                                        <p class="mb-0 text-muted small"><i class="fa-regular fa-clock me-1"></i> <?= $ep['min'] ?></p>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fila Inferior: Más Series -->
        <div class="row mt-5">
            <div class="col-12">
                <h4 class="text-white text-uppercase fs-5 fw-bold mb-3 border-bottom pb-2">
                    <i class="fa-solid fa-film me-2 text-info"></i> Más Series
                </h4>
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-3">
                    <?php foreach ($lista_series as $item): 
                        $item_id = $item['id_contenido'];
                        $item_poster = $recursos_extra[$item_id]['poster'] ?? $recursos_extra[1]['poster'];
                    ?>
                        <div class="col">
                            <!-- Al cambiar de serie, mandamos el id y por defecto el id_ep=1 del nuevo show -->
                            <a href="Content.php?id=<?= $item_id ?>&id_ep=1" class="text-decoration-none">
                                <div class="series-card p-2 rounded text-white h-100 d-flex flex-column">
                                    <img src="<?= $item_poster ?>" alt="<?= htmlspecialchars($item['titulo']) ?>" class="rounded object-fit-cover w-100 mb-2" style="height: 180px;">
                                    <h6 class="fw-bold text-white text-truncate mb-1"><?= htmlspecialchars($item['titulo']) ?></h6>
                                    <p class="text-muted small mb-0"><?= htmlspecialchars($item['tipo_contenido']) ?> (<?= htmlspecialchars($item['anio_lanzamiento']) ?>)</p>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>