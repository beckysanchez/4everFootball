<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');

// 🔒 Protección de sesión y rol admin
require_once __DIR__ . '/../config/api_guard.php';
require_role_api('ADMIN');

// 🔌 Conexión
require_once __DIR__ . '/../conexion.php';

try {
    // --- Categorías ---
    $cats = $conexion->query("
        SELECT nombre 
        FROM categoria 
        ORDER BY nombre ASC
    ")->fetch_all(MYSQLI_ASSOC);

    // --- Mundiales (sede/año) con IDs ---
    $sedes = $conexion->query("
        SELECT mundial_id AS id, nombre_comunidad AS nombre 
        FROM mundial 
        ORDER BY nombre_comunidad ASC
    ")->fetch_all(MYSQLI_ASSOC);

    // --- Estados únicos ---
    $estados = $conexion->query("
        SELECT DISTINCT estatus 
        FROM publicacion 
        ORDER BY estatus ASC
    ")->fetch_all(MYSQLI_ASSOC);

    echo json_encode([
        "ok" => true,
        "categorias" => array_column($cats, "nombre"),
        "sedes" => $sedes, // devuelve id + nombre
        "estados" => array_column($estados, "estatus")
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        "ok" => false,
        "error" => "Error al cargar los filtros: " . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
