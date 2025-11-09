<?php
// api/comentarios_listar.php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
error_reporting(E_ALL);
require_once __DIR__ . '/../conexion.php';

// 1️⃣ Validar parámetro de publicación
$publicacion_id = isset($_GET['publicacion_id']) ? (int)$_GET['publicacion_id'] : 0;
if ($publicacion_id <= 0) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'ID de publicación inválido']);
    exit;
}

// 2️⃣ Consultar comentarios con información del autor y likes
$sql = "
SELECT 
    c.comentario_id,
    c.contenido,
    c.creado_en,
    c.eliminado,
    c.comentario_padre_id,
    u.nombre_completo AS autor,
    u.foto_blob,
    (SELECT COUNT(*) 
        FROM comentario_reaccion r 
        WHERE r.comentario_id = c.comentario_id) AS likes
FROM comentario c
JOIN usuarios u ON u.usuario_id = c.usuario_id
WHERE c.publicacion_id = ?
ORDER BY c.creado_en ASC
";

$stmt = $conexion->prepare($sql);
$stmt->bind_param('i', $publicacion_id);
$stmt->execute();
$res = $stmt->get_result();

// 3️⃣ Armar mapa temporal para anidar hijos
$comentarios = [];
$map = [];

while ($row = $res->fetch_assoc()) {
    $row['foto'] = $row['foto_blob']
        ? 'data:image/jpeg;base64,' . base64_encode($row['foto_blob'])
        : null;
    unset($row['foto_blob']);

    $row['likes'] = (int)$row['likes'];
    $row['hijos'] = []; // contenedor para respuestas

    $map[$row['comentario_id']] = $row;
}

// 4️⃣ Construir árbol de respuestas (anidamiento)
foreach ($map as $id => &$c) {
    if (!empty($c['comentario_padre_id'])) {
        $padre_id = $c['comentario_padre_id'];
        if (isset($map[$padre_id])) {
            $map[$padre_id]['hijos'][] = &$c;
        }
    } else {
        $comentarios[] = &$c;
    }
}
unset($c);

// 5️⃣ Devolver respuesta JSON
echo json_encode([
    'ok' => true,
    'comentarios' => $comentarios
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
