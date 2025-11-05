<?php
// api/upload_image.php
header('Content-Type: application/json; charset=utf-8');

$targetDir = __DIR__ . '/../data/uploads/';

// Si no existe la carpeta, la creamos
if (!is_dir($targetDir)) {
  mkdir($targetDir, 0777, true);
}

if (!isset($_FILES['file'])) {
  echo json_encode(['ok' => false, 'error' => 'No se recibió archivo']);
  exit;
}

$file = $_FILES['file'];
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

// Validar tipo de archivo
$allowed = ['jpg', 'jpeg', 'png', 'gif'];
if (!in_array($ext, $allowed)) {
  echo json_encode(['ok' => false, 'error' => 'Tipo de archivo no permitido']);
  exit;
}

// Nombre único
$filename = uniqid('img_') . '.' . $ext;
$targetPath = $targetDir . $filename;

// Mover el archivo subido
if (move_uploaded_file($file['tmp_name'], $targetPath)) {
  $url = '/4everFootball/data/uploads/' . $filename;
  echo json_encode(['ok' => true, 'url' => $url]);
} else {
  echo json_encode(['ok' => false, 'error' => 'Error al subir el archivo']);
}
?>
