<?php
header('Content-Type: application/json; charset=utf-8');
require_once(__DIR__ . '/../conexion.php');
session_start();

if (empty($_SESSION['user']['id'])) {
    echo json_encode(['ok' => false, 'error' => 'No has iniciado sesión.']);
    exit;
}

$usuario_id = $_SESSION['user']['id'];
$mundial_id = $_POST['mundial_id'] ?? null;
$categoria_id = $_POST['categoria_id'] ?? null;
$titulo = trim($_POST['titulo'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');

if (!$mundial_id || !$categoria_id || !$titulo || !$descripcion) {
    echo json_encode(['ok' => false, 'error' => 'Faltan campos obligatorios.']);
    exit;
}

// === Manejo de archivo ===
$media_url = null;
$tipo_media = 'IMAGEN'; // por defecto

if (!empty($_FILES['file']['name'])) {
    $f = $_FILES['file'];
    if ($f['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['ok' => false, 'error' => 'Error al subir archivo.']);
        exit;
    }

    $mime = mime_content_type($f['tmp_name']);
    $allowed = ['image/jpeg', 'image/png', 'video/mp4', 'video/webm'];
    if (!in_array($mime, $allowed)) {
        echo json_encode(['ok' => false, 'error' => 'Formato no permitido.']);
        exit;
    }

    if ($f['size'] > 20 * 1024 * 1024) {
        echo json_encode(['ok' => false, 'error' => 'Archivo demasiado grande (máx 20 MB).']);
        exit;
    }

    // Crear carpeta si no existe
    $upload_dir = __DIR__ . '/../uploads/';
    if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);

    // Crear nombre único
    $ext = pathinfo($f['name'], PATHINFO_EXTENSION);
    $file_name = uniqid('media_', true) . '.' . $ext;
    $file_path = $upload_dir . $file_name;

    if (!move_uploaded_file($f['tmp_name'], $file_path)) {
        echo json_encode(['ok' => false, 'error' => 'No se pudo guardar el archivo.']);
        exit;
    }

    $media_url = 'uploads/' . $file_name;
    $tipo_media = (str_starts_with($mime, 'video/')) ? 'VIDEO' : 'IMAGEN';
}

// === Guardar en base de datos ===
$stmt = $conexion->prepare("
    INSERT INTO publicacion (
        usuario_id, mundial_id, categoria_id, titulo, descripcion,
        tipo_media, media_url, estatus, creada_en
    )
    VALUES (?, ?, ?, ?, ?, ?, ?, 'PENDIENTE', NOW())
");


$stmt->bind_param(
    "iiissss",
    $usuario_id,
    $mundial_id,
    $categoria_id,
    $titulo,
    $descripcion,
    $tipo_media,
    $media_url
);

if ($stmt->execute()) {
    echo json_encode(['ok' => true, 'msg' => 'Tu publicación ha sido enviada para revisión.']);
} else {
    echo json_encode(['ok' => false, 'error' => $stmt->error]);
}
?>
