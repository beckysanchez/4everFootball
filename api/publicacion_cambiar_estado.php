<?php
declare(strict_types=1);
header("Content-Type: application/json; charset=UTF-8");
error_reporting(E_ALL);

// 🔒 Protección unificada de sesión y rol
require_once __DIR__ . '/../config/api_guard.php';
require_role_api('ADMIN');

// 🔌 Conexión
require_once __DIR__ . '/../conexion.php';

// --- Validar método ---
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Método no permitido']);
    exit;
}

$admin_id = (int) ($_SESSION['user']['id'] ?? 0);

// --- Obtener datos del POST ---
$id      = $_POST['id']      ?? null;
$estado  = strtoupper(trim($_POST['estado'] ?? ''));

// --- Validaciones ---
if (empty($id) || empty($estado)) {
    echo json_encode(['ok' => false, 'error' => 'Faltan parámetros requeridos']);
    exit;
}

// Estados permitidos
$permitidos = ["APROBADA", "RECHAZADA", "PENDIENTE"];
if (!in_array($estado, $permitidos, true)) {
    echo json_encode(["ok" => false, "error" => "Estado inválido"]);
    exit;
}

// --- Normalizar IDs ---
$ids = [];
if (is_array($id)) {
    foreach ($id as $val) {
        $val = (int)$val;
        if ($val > 0) $ids[] = $val;
    }
} else {
    $val = (int)$id;
    if ($val > 0) $ids[] = $val;
}

if (!$ids) {
    echo json_encode(['ok' => false, 'error' => 'IDs inválidos']);
    exit;
}

try {
    // --- Construcción dinámica del IN() seguro ---
    $in_placeholders = implode(',', array_fill(0, count($ids), '?'));
    $types = str_repeat('i', count($ids));

    // --- SQL condicional según el estado ---
    if ($estado === 'PENDIENTE') {
        // Si vuelve a revisión, limpia campos de aprobación
        $sql = "UPDATE publicacion 
                SET estatus = 'PENDIENTE', aprobada_por = NULL, aprobada_en = NULL
                WHERE publicacion_id IN ($in_placeholders)";
        $stmt = $conexion->prepare($sql);
        if (!$stmt) throw new Exception('Error al preparar la consulta: ' . $conexion->error);
        $stmt->bind_param($types, ...$ids);
    } else {
        // Si se aprueba o rechaza, actualiza con datos del admin
        $sql = "UPDATE publicacion 
                SET estatus = ?, aprobada_por = ?, aprobada_en = NOW()
                WHERE publicacion_id IN ($in_placeholders)";
        $stmt = $conexion->prepare($sql);
        if (!$stmt) throw new Exception('Error al preparar la consulta: ' . $conexion->error);

        $bind_types = 'si' . $types;
        $bind_values = array_merge([$estado, $admin_id], $ids);
        $stmt->bind_param($bind_types, ...$bind_values);
    }

    // --- Ejecutar ---
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(['ok' => true, 'mensaje' => '✅ Publicación(es) actualizada(s) correctamente']);
    } else {
        echo json_encode(['ok' => false, 'error' => 'No se actualizó ningún registro']);
    }

    $stmt->close();
    $conexion->close();

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'ok' => false,
        'error' => 'Error al actualizar: ' . $e->getMessage()
    ]);
}
