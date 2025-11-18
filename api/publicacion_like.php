<?php
declare(strict_types=1);
header("Content-Type: application/json; charset=UTF-8");
error_reporting(E_ALL);
require_once __DIR__ . '/../conexion.php';

// 📥 Obtener datos
$publicacion_id = (int)($_POST['publicacion_id'] ?? 0);
$usuario_id = (int)($_POST['usuario_id'] ?? 0);

if ($publicacion_id <= 0 || $usuario_id <= 0) {
  echo json_encode(['ok' => false, 'error' => 'Datos inválidos']);
  exit;
}

// 🧩 Verificar existencia de la publicación y el usuario
$existePublicacion = $conexion->query("SELECT 1 FROM publicacion WHERE publicacion_id = {$publicacion_id}")->fetch_row();
$existeUsuario = $conexion->query("SELECT 1 FROM usuarios WHERE usuario_id = {$usuario_id}")->fetch_row();

if (!$existePublicacion) {
  echo json_encode(['ok' => false, 'error' => 'Publicación no encontrada']);
  exit;
}
if (!$existeUsuario) {
  echo json_encode(['ok' => false, 'error' => 'Usuario no encontrado']);
  exit;
}

// 🔄 Alternar like/unlike
$existe = $conexion->query("
  SELECT 1 FROM reaccion
  WHERE publicacion_id = {$publicacion_id}
    AND usuario_id = {$usuario_id}
")->fetch_row();

if ($existe) {
  // Quitar like
  $conexion->query("
    DELETE FROM reaccion
    WHERE publicacion_id = {$publicacion_id}
      AND usuario_id = {$usuario_id}
  ");
  echo json_encode(['ok' => true, 'accion' => 'unlike']);
} else {
  // Dar like
  $stmt = $conexion->prepare("
    INSERT INTO reaccion (publicacion_id, usuario_id, tipo)
    VALUES (?, ?, 'LIKE')
  ");
  $stmt->bind_param('ii', $publicacion_id, $usuario_id);
  $stmt->execute();
  echo json_encode(['ok' => true, 'accion' => 'like']);
}
