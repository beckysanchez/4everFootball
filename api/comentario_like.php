<?php
declare(strict_types=1);
header("Content-Type: application/json; charset=UTF-8");
error_reporting(E_ALL);
require_once __DIR__ . '/../conexion.php';

// 📥 Obtener datos
$comentario_id = (int)($_POST['comentario_id'] ?? 0);
$usuario_id = (int)($_POST['usuario_id'] ?? 0);

if ($comentario_id <= 0 || $usuario_id <= 0) {
  echo json_encode(['ok' => false, 'error' => 'Datos inválidos']);
  exit;
}

// 🧩 Verificar existencia del comentario y usuario
$existeComentario = $conexion->query("SELECT 1 FROM comentario WHERE comentario_id = {$comentario_id}")->fetch_row();
$existeUsuario = $conexion->query("SELECT 1 FROM usuarios WHERE usuario_id = {$usuario_id}")->fetch_row();
if (!$existeComentario) {
  echo json_encode(['ok' => false, 'error' => 'Comentario no encontrado']);
  exit;
}
if (!$existeUsuario) {
  echo json_encode(['ok' => false, 'error' => 'Usuario no encontrado']);
  exit;
}

// 🔄 Alternar like/unlike
$existe = $conexion->query("
  SELECT 1 FROM comentario_reaccion 
  WHERE comentario_id = {$comentario_id} 
    AND usuario_id = {$usuario_id}
")->fetch_row();

if ($existe) {
  // Quitar like
  $conexion->query("
    DELETE FROM comentario_reaccion 
    WHERE comentario_id = {$comentario_id} 
      AND usuario_id = {$usuario_id}
  ");
  echo json_encode(['ok' => true, 'accion' => 'unlike']);
} else {
  // Dar like
  $stmt = $conexion->prepare("
    INSERT INTO comentario_reaccion (comentario_id, usuario_id) 
    VALUES (?, ?)
  ");
  $stmt->bind_param('ii', $comentario_id, $usuario_id);
  $stmt->execute();
  echo json_encode(['ok' => true, 'accion' => 'like']);
}
