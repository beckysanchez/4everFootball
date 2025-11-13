<?php

header('Content-Type: application/json');
require_once __DIR__ . '/../conexion.php';
session_start();

$usuario_id = $_SESSION['usuario_id'] ?? null;

if (!$usuario_id) {
    echo json_encode([
        'ok' => false,
        'error' => 'Usuario no logueado',
        'publicaciones' => []
    ]);
    exit;
}

$offset = intval($_GET['offset'] ?? 0);
$limit  = 10; // lazy loading

// 1. Ver qué mundiales sigue el usuario
$sqlSeguidos = "SELECT mundial_id
                FROM usuario_mundial_seguido
                WHERE usuario_id = $usuario_id";

$seg = $conexion->query($sqlSeguidos);

if ($seg->num_rows === 0) {
    echo json_encode([
        'ok' => true,
        'seguido' => false,
        'mensaje' => 'Sigue alguna sede o comunidad para ver publicaciones.',
        'publicaciones' => []
    ]);
    exit;
}

$mundiales = [];
while ($fila = $seg->fetch_assoc()) {
    $mundiales[] = $fila['mundial_id'];
}

$lista = implode(",", $mundiales);

// 2. Cargar publicaciones solo de esos mundiales
$sqlFeed = "
SELECT * 
FROM vista_publicaciones_detalle
WHERE mundial_id IN ($lista)
ORDER BY creada_en DESC
LIMIT $limit OFFSET $offset";

$res = $conexion->query($sqlFeed);

$pubs = [];
while ($row = $res->fetch_assoc()) {
    $pubs[] = $row;
}

echo json_encode([
    'ok' => true,
    'seguido' => true,
    'publicaciones' => $pubs
]);
