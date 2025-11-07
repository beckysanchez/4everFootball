<?php
header('Content-Type: application/json; charset=utf-8');
session_start();
require_once(__DIR__ . '/../conexion.php');

// --- VERIFICAR SESIÓN ---
if (!isset($_SESSION['user'])) {
    echo json_encode(['ok' => false, 'error' => 'Sesión no iniciada']);
    exit;
}
if (strtolower($_SESSION['user']['rol']) !== 'admin') {
    echo json_encode(['ok' => false, 'error' => 'No autorizado']);
    exit;
}

// --- OBTENER DATOS ---
$id = $_POST['id'] ?? '';
$estado = strtoupper(trim($_POST['estado'] ?? ''));

if (!$id || !$estado) {
    echo json_encode(['ok' => false, 'error' => 'Faltan parámetros']);
    exit;
}

try {
    if (is_array($id)) {
        // Múltiples IDs
        $ids = implode(',', array_map('intval', $id));
        $sql = "UPDATE publicacion SET estatus = ? WHERE publicacion_id IN ($ids)";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param('s', $estado);
    } else {
        // Un solo ID
        $sql = "UPDATE publicacion SET estatus = ? WHERE publicacion_id = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param('si', $estado, $id);
    }

    if (!$stmt->execute()) {
        throw new Exception($stmt->error);
    }

    echo json_encode(['ok' => true, 'msg' => 'Estado actualizado correctamente']);
} catch (Exception $e) {
    echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
}
