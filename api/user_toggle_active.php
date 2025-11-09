<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../conexion.php';
require_once __DIR__ . '/../config/api_guard.php';
require_role_api('ADMIN');

// Validar datos de entrada
$id     = (int)($_POST['id'] ?? 0);
$activo = ($_POST['activo'] ?? '') === '1' ? 1 : 0;

if ($id <= 0) {
  http_response_code(400);
  echo json_encode(['ok' => false, 'error' => 'ID inválido']);
  exit;
}

// Evitar que un admin se desactive a sí mismo
session_start();
if (!empty($_SESSION['user']['id']) && (int)$_SESSION['user']['id'] === $id && $activo === 0) {
  http_response_code(400);
  echo json_encode(['ok' => false, 'error' => 'No puedes desactivar tu propia cuenta.']);
  exit;
}

// Ejecutar actualización
$stmt = $conexion->prepare("UPDATE usuarios SET activo=? WHERE usuario_id=?");
$stmt->bind_param('ii', $activo, $id);
$stmt->execute();
$ok = $stmt->affected_rows >= 0;
$stmt->close();

echo json_encode(['ok' => $ok]);
