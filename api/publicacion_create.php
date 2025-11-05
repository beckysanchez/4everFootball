<?php
header('Content-Type: application/json; charset=utf-8');
require_once(__DIR__ . '/../conexion.php');

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(["ok" => false, "error" => "No se recibieron datos o JSON inválido"]);
    exit;
}

$usuario_id   = $data['usuario_id'] ?? null;
$mundial_id   = $data['mundial_id'] ?? null;
$categoria_id = $data['categoria_id'] ?? null;
$titulo       = $data['titulo'] ?? null;
$descripcion  = $data['descripcion'] ?? null;
$tipo_media   = $data['tipo_media'] ?? 'IMAGEN';
$media_url    = $data['media_url'] ?? null;

if (!$usuario_id || !$mundial_id || !$categoria_id || !$titulo) {
    echo json_encode(["ok" => false, "error" => "Datos incompletos"]);
    exit;
}

$stmt = $conexion->prepare("
    INSERT INTO publicacion 
    (usuario_id, mundial_id, categoria_id, titulo, descripcion, tipo_media, media_url, estatus)
    VALUES (?, ?, ?, ?, ?, ?, ?, 'APROBADA')
");
$stmt->bind_param("iiissss", $usuario_id, $mundial_id, $categoria_id, $titulo, $descripcion, $tipo_media, $media_url);

if ($stmt->execute()) {
    echo json_encode(["ok" => true, "mensaje" => "Publicación creada exitosamente", "id" => $stmt->insert_id]);
} else {
    echo json_encode(["ok" => false, "error" => $stmt->error]);
}
?>
