<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../conexion.php'; // ajusta la ruta según tu estructura

try {
    $query = "SELECT id, nombre, slug, logo, portada, sede, YEAR(fecha_creacion) AS anio
              FROM comunidades
              ORDER BY anio DESC
              LIMIT 6";
    $res = $conexion->query($query);

    $data = [];
    while ($row = $res->fetch_assoc()) {
        $data[] = [
            'id' => $row['id'],
            'nombre' => $row['nombre'],
            'slug' => $row['slug'],
            'logo' => $row['logo'],
            'anio' => $row['anio'],
        ];
    }

    echo json_encode(['ok' => true, 'data' => $data]);
} catch (Exception $e) {
    echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
}
?>
