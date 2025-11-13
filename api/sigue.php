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
$mundial_id = intval($_GET['id'] ?? 0);

if ($mundial_id <= 0) {
    echo json_encode(['ok' => false, 'error' => 'ID inválido']);
    exit;
}

// ====================
// VERIFICAR SEGUIMIENTO
// ====================
$stmt = $conexion->prepare("
    SELECT 1
    FROM usuario_mundial_seguido
    WHERE usuario_id = ? AND mundial_id = ?
    LIMIT 1
");
$stmt->bind_param("ii", $usuario_id, $mundial_id);
$stmt->execute();
$stmt->store_result();

echo json_encode([
    'ok' => true,
    'sigue' => $stmt->num_rows > 0
]);
