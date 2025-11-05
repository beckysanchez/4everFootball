<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once(__DIR__ . '/../conexion.php');

if (!isset($_SESSION['user']) || !($_SESSION['user']['isAdmin'] ?? false)) {
    echo json_encode(['ok' => false, 'error' => 'No autorizado']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$nombre = trim($data['nombre'] ?? '');
$slug = trim($data['slug'] ?? '');

if (!$nombre || !$slug) {
    echo json_encode(['ok' => false, 'error' => 'Campos incompletos']);
    exit;
}

$stmt = $conexion->prepare("INSERT INTO categorias (nombre, slug) VALUES (?, ?)");
$stmt->bind_param("ss", $nombre, $slug);

if ($stmt->execute()) echo json_encode(['ok' => true]);
else echo json_encode(['ok' => false, 'error' => $stmt->error]);
?>
