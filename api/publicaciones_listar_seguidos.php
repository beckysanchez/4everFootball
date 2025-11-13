<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');

// Sesión
require_once __DIR__ . '/../config/session_init.php';

$usuario_id = $_SESSION['user']['id'] ?? null;
if (!$usuario_id) {
    echo json_encode(['ok' => false, 'error' => 'No logueado']);
    exit;
}

require_once __DIR__ . '/../conexion.php';

// OFFSET para scroll infinito
$offset = intval($_GET['offset'] ?? 0);
$limit  = 10; // cuántas cargar por bloque

//
// 1. OBTENER LISTA DE MUNDIALES QUE EL USUARIO SIGUE
//
$stmt = $conexion->prepare("SELECT mundial_id FROM usuario_mundial_seguido WHERE usuario_id = ?");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$res = $stmt->get_result();

$mundiales_seguidos = [];
while ($row = $res->fetch_assoc()) {
    $mundiales_seguidos[] = intval($row['mundial_id']);
}
$stmt->close();

if (empty($mundiales_seguidos)) {
    echo json_encode([
        'ok' => true,
        'seguido' => false,
        'publicaciones' => []
    ]);
    exit;
}

//
// 2. QUERY CON PLACEHOLDERS DINÁMICOS
//
$in = implode(',', array_fill(0, count($mundiales_seguidos), '?'));

$sql = "
    SELECT
        p.publicacion_id,
        p.titulo,
        p.descripcion,
        p.tipo_media,
        p.media_url,
        p.creada_en,
        c.nombre AS categoria,

        -- Datos extra para mostrar en el feed
        m.nombre_comunidad AS sede_nombre,
        m.slug AS sede_slug,
        m.logo_url AS sede_logo

    FROM publicacion p
    JOIN categoria c ON c.categoria_id = p.categoria_id
    JOIN mundial m ON m.mundial_id = p.mundial_id
    WHERE p.mundial_id IN ($in)
    ORDER BY p.creada_en DESC
    LIMIT ?, ?
";

$stmt = $conexion->prepare($sql);

//
// 3. BIND DINÁMICO
//
$types = str_repeat('i', count($mundiales_seguidos)) . 'ii';
$params = [...$mundiales_seguidos, $offset, $limit];

$stmt->bind_param($types, ...$params);

$stmt->execute();
$res = $stmt->get_result();

$pubs = [];
while ($row = $res->fetch_assoc()) {
    $pubs[] = $row;
}

echo json_encode([
    'ok' => true,
    'seguido' => true,
    'publicaciones' => $pubs
], JSON_UNESCAPED_UNICODE);
