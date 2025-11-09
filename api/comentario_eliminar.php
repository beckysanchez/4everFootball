<?php
declare(strict_types=1);
header("Content-Type: application/json; charset=UTF-8");
error_reporting(E_ALL);

require_once __DIR__ . '/../conexion.php';
require_once __DIR__ . '/../config/api_guard.php';
require_role_api('ADMIN'); // 🔒 Solo admins

// 📥 Datos
$id = (int)($_POST['comentario_id'] ?? 0);
$admin_id = (int)($_SESSION['user']['id'] ?? 0);

if ($id <= 0) {
  echo json_encode(['ok' => false, 'error' => 'ID de comentario inválido']);
  exit;
}

// 🧩 Verificar existencia
$existe = $conexion->query("SELECT 1 FROM comentario WHERE comentario_id = {$id}")->fetch_row();
if (!$existe) {
  echo json_encode(['ok' => false, 'error' => 'Comentario no encontrado']);
  exit;
}

// 🚨 Marcar como eliminado sin borrar
$stmt = $conexion->prepare("
  UPDATE comentario
  SET eliminado = 1,
      contenido = 'Este comentario fue eliminado por un administrador.',
      eliminado_por = ? 
  WHERE comentario_id = ?
");
$stmt->bind_param('ii', $admin_id, $id);

if ($stmt->execute()) {
  echo json_encode(['ok' => true, 'mensaje' => 'Comentario marcado como eliminado']);
} else {
  echo json_encode(['ok' => false, 'error' => 'Error al eliminar el comentario']);
}

$stmt->close();
$conexion->close();
