<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');

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

$in = implode(',', array_fill(0, count($mundiales_seguidos), '?'));
$search = trim($_GET['q'] ?? '');
$cat = trim($_GET['cat'] ?? '');
$orden = trim($_GET['orden'] ?? 'reciente');

$sql = "
    SELECT
        p.publicacion_id,
        p.titulo,
        p.descripcion,
        p.tipo_media,
        p.media_url,
        p.creada_en,
      p.total_likes,
p.total_comentarios,
        c.nombre AS categoria,
        m.nombre_comunidad AS sede_nombre,
        m.slug AS sede_slug,
        m.logo_url AS sede_logo
    FROM publicacion p
    JOIN categoria c ON c.categoria_id = p.categoria_id
    JOIN mundial m ON m.mundial_id = p.mundial_id
    WHERE p.mundial_id IN ($in)
";

if (!empty($cat)) {
    $sql .= " AND c.nombre = ? ";
}

if (!empty($search)) {
    $sql .= " AND (
        p.titulo LIKE ? OR
        p.descripcion LIKE ? OR
        c.nombre LIKE ? OR
        m.nombre_comunidad LIKE ?
    )";
}

switch($orden){
    case 'likes':
    $sql .= " ORDER BY p.total_likes DESC ";
    break;
case 'comentarios':
    $sql .= " ORDER BY p.total_comentarios DESC ";
    break;
    default:
        $sql .= " ORDER BY p.creada_en DESC ";
}

$sql .= " LIMIT ?, ?";

$stmt = $conexion->prepare($sql);
$types = str_repeat('i', count($mundiales_seguidos));

if (!empty($cat)) {
    $types .= 's';
}
if (!empty($search)) {
    $types .= 'ssss';
}

$types .= 'ii';
$params = [...$mundiales_seguidos];

if (!empty($cat)) {
    $params[] = $cat;
}
if (!empty($search)) {
    $like = "%$search%";
    $params = array_merge($params, [$like, $like, $like, $like]);
}

$params[] = $offset;
$params[] = $limit;

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
