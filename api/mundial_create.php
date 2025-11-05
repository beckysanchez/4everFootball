<?php
declare(strict_types=1);
session_start();
header('Content-Type: application/json; charset=utf-8');

// Tragar cualquier salida accidental (BOM, echos) de archivos incluidos
ob_start();
require_once __DIR__ . '/../conexion.php';
ob_end_clean();

function jexit(array $payload): void {
  echo json_encode($payload, JSON_UNESCAPED_UNICODE);
  exit;
}

// Verifica sesión (admin)
if (empty($_SESSION['user']) || !($_SESSION['user']['isAdmin'] ?? false)) {
  jexit(['ok' => false, 'error' => 'No autorizado']);
}

// Lee cuerpo JSON
$raw = file_get_contents('php://input');
if ($raw === false || $raw === '') {
  jexit(['ok' => false, 'error' => 'Cuerpo vacío']);
}
$data = json_decode($raw, true);
if (!is_array($data)) {
  jexit(['ok' => false, 'error' => 'JSON inválido']);
}

// Campos
$nombre_comunidad = trim($data['nombre_comunidad'] ?? '');
$descripcion      = trim($data['descripcion'] ?? '');
$sede             = trim($data['sede'] ?? '');
$logo_url         = trim($data['logo_url'] ?? '');
$portada_url      = trim($data['portada_url'] ?? '');

// Valida mínimos
if ($nombre_comunidad === '' || $descripcion === '') {
  jexit(['ok' => false, 'error' => 'Faltan campos obligatorios']);
}

// Inserta
$stmt = $conexion->prepare("
  INSERT INTO mundial (nombre_comunidad, descripcion, sede, logo_url, portada_url, slug)
VALUES (?, ?, ?, ?, ?, ?)

");
if (!$stmt) {
  jexit(['ok' => false, 'error' => 'Prep error: ' . $conexion->error]);
}
$slug = trim($data['slug'] ?? '');

$stmt->bind_param("ssssss", $nombre_comunidad, $descripcion, $sede, $logo_url, $portada_url, $slug);


if ($stmt->execute()) {
  jexit(['ok' => true, 'id' => $stmt->insert_id]);
}
jexit(['ok' => false, 'error' => $stmt->error]);
