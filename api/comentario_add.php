<?php
// api/comentario_add.php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
  http_response_code(405);
  echo json_encode(['ok'=>false,'error'=>'Método no permitido']); exit;
}

require_once __DIR__ . '/../conexion.php';

function jerror($m, $c=400){ http_response_code($c); echo json_encode(['ok'=>false,'error'=>$m]); exit; }

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) jerror('JSON inválido');

$usuario_id    = isset($input['usuario_id']) ? (int)$input['usuario_id'] : 0;
$publicacion_id= isset($input['publicacion_id']) ? (int)$input['publicacion_id'] : 0;
$contenido     = trim((string)($input['contenido'] ?? ''));

if ($usuario_id<=0 || $publicacion_id<=0 || $contenido==='') {
  jerror('Datos incompletos');
}

// valida FK rápidas
$existeU = $conexion->query("SELECT 1 FROM usuarios WHERE usuario_id={$usuario_id}")->fetch_row();
$existeP = $conexion->query("SELECT 1 FROM publicacion WHERE publicacion_id={$publicacion_id}")->fetch_row();
if (!$existeU) jerror('Usuario no existe', 404);
if (!$existeP) jerror('Publicación no existe', 404);

// inserta comentario
$stmt = $conexion->prepare("
  INSERT INTO comentario (publicacion_id, usuario_id, contenido, creado_en)
  VALUES (?, ?, ?, NOW())
");
$stmt->bind_param('iis', $publicacion_id, $usuario_id, $contenido);

if ($stmt->execute()) {
  echo json_encode(['ok'=>true, 'mensaje'=>'Comentario agregado', 'id'=>$stmt->insert_id]);
} else {
  jerror('No se pudo guardar el comentario');
}
