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
// VALIDAR INPUT
// ====================
$data = json_decode(file_get_contents('php://input'), true);
$mundial_id = intval($data['mundial_id'] ?? 0);

if ($mundial_id <= 0) {
    echo json_encode(['ok' => false, 'error' => 'ID inválido']);
    exit;
}

// ====================
// INSERTAR SEGUIMIENTO
// ====================
$stmt = $conexion->prepare("
    INSERT IGNORE INTO usuario_mundial_seguido (usuario_id, mundial_id)
    VALUES (?, ?)
");
$stmt->bind_param("ii", $usuario_id, $mundial_id);

if ($stmt->execute()) {
    echo json_encode(['ok' => true, 'accion' => 'seguido']);
} else {
    echo json_encode(['ok' => false, 'error' => $stmt->error]);
}
