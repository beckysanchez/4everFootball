<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once(__DIR__ . '/../conexion.php');

// Validar que esté logueado y sea admin
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

// Procesar datos del POST
$data = json_decode(file_get_contents('php://input'), true);
$nombre = trim($data['nombre'] ?? '');
$slug = trim($data['slug'] ?? ''); // sigue siendo el slug, pero lo guardamos en descripcion

if (!$nombre || !$slug) {
    echo json_encode(['ok' => false, 'error' => 'Campos incompletos']);
    exit;
}

// Insertar en la tabla real (categoria) y columna real (descripcion)
$stmt = $conexion->prepare("INSERT INTO categoria (nombre, descripcion) VALUES (?, ?)");
$stmt->bind_param("ss", $nombre, $slug);

if ($stmt->execute()) {
    echo json_encode([
        'ok' => true,
        'id' => $stmt->insert_id,
        'nombre' => $nombre,
        'slug' => $slug
    ]);
} else {
    echo json_encode(['ok' => false, 'error' => $stmt->error]);
}
?>
