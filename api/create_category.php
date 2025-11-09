<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once(__DIR__ . '/../conexion.php');

// ✅ Validar sesión con compatibilidad extendida para admins
if (empty($_SESSION['user'])) {
    echo json_encode(['ok' => false, 'error' => 'No autorizado - sin sesión']);
    exit;
}

$user = $_SESSION['user'];
$rol = strtoupper($user['rol'] ?? '');
$esAdmin = !empty($user['isAdmin']) || $rol === 'ADMIN';

if (!$esAdmin) {
    echo json_encode(['ok' => false, 'error' => 'No autorizado - rol insuficiente']);
    exit;
}

// ✅ Procesar datos
$data = json_decode(file_get_contents('php://input'), true);
$nombre = trim($data['nombre'] ?? '');
$slug = trim($data['slug'] ?? '');

if (!$nombre || !$slug) {
    echo json_encode(['ok' => false, 'error' => 'Campos incompletos']);
    exit;
}

// ✅ Insertar categoría
$stmt = $conexion->prepare("INSERT INTO categorias (nombre, slug) VALUES (?, ?)");
if (!$stmt) {
    echo json_encode(['ok' => false, 'error' => 'Error al preparar consulta: ' . $conexion->error]);
    exit;
}

$stmt->bind_param("ss", $nombre, $slug);

if ($stmt->execute()) {
    echo json_encode(['ok' => true]);
} else {
    echo json_encode(['ok' => false, 'error' => $stmt->error]);
}
?>
