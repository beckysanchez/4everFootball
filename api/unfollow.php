<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/session_init.php';
require_once __DIR__ . '/../conexion.php';

// ====================
// VALIDAR SESIÓN
// ====================
$usuario_id = $_SESSION['user']['id'] ?? null;


if (!$usuario_id) {
    echo json_encode(['ok' => false, 'error' => 'No logueado']);
    exit;
}

// ====================
// LEER BODY JSON
// ====================
$data = json_decode(file_get_contents('php://input'), true);
$mundial_id = intval($data['mundial_id'] ?? 0);

if ($mundial_id <= 0) {
    echo json_encode(['ok' => false, 'error' => 'ID inválido']);
    exit;
}

// ====================
// ELIMINAR SEGUIMIENTO
// ====================
$stmt = $conexion->prepare("
    DELETE FROM usuario_mundial_seguido
    WHERE usuario_id = ? AND mundial_id = ?
");
$stmt->bind_param("ii", $usuario_id, $mundial_id);

if ($stmt->execute()) {
    echo json_encode(['ok' => true, 'accion' => 'no_seguido']);
} else {
    echo json_encode(['ok' => false, 'error' => $stmt->error]);
}
