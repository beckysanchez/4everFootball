<?php
header('Content-Type: application/json; charset=utf-8');

// --- FIX GLOBAL DE SESIÓN ---
$root = realpath(__DIR__ . '/..');
ini_set('session.save_path', $root . '/sessions'); // Carpeta compartida
ini_set('session.cookie_path', '/'); // Asegura que sea global
session_start();

// --- CONEXIÓN ---
require_once(__DIR__ . '/../conexion.php');

// --- VERIFICACIÓN DE SESIÓN ---
if (!isset($_SESSION['user'])) {
    echo json_encode(['ok' => false, 'error' => 'Sesión no iniciada']);
    exit;
}

// --- DEBUG opcional ---
if (!isset($_SESSION['user']['rol'])) {
    echo json_encode(['ok' => false, 'error' => 'Rol no encontrado', 'session' => $_SESSION]);
    exit;
}

// --- VALIDACIÓN DE ADMIN ---
if (strtolower($_SESSION['user']['rol']) !== 'admin') {
    echo json_encode(['ok' => false, 'error' => 'No autorizado', 'rol' => $_SESSION['user']['rol']]);
    exit;
}

// --- PARÁMETROS ---
$q = trim($_GET['q'] ?? '');
$cat = trim($_GET['cat'] ?? '');
$sede = trim($_GET['sede'] ?? '');
$estado = trim($_GET['estado'] ?? '');
$orden = trim($_GET['orden'] ?? 'reciente');

// --- QUERY BASE ---
$query = "
    SELECT 
        p.publicacion_id,
        p.titulo,
        p.descripcion,
        p.tipo_media,
        p.media_url,
        p.estatus,
        p.creada_en,
        COALESCE(c.nombre, 'Sin categoría') AS categoria,
        COALESCE(m.nombre, 'Sin mundial') AS sede,
        COALESCE(u.nombre_completo, 'Usuario desconocido') AS autor
    FROM publicacion p
    LEFT JOIN categoria c ON p.categoria_id = c.categoria_id
    LEFT JOIN mundial m ON p.mundial_id = m.mundial_id
    LEFT JOIN usuarios u ON p.usuario_id = u.usuario_id
    WHERE 1
";

// --- FILTROS ---
$params = [];
$types = '';

if ($q) {
    $query .= " AND (p.titulo LIKE CONCAT('%', ?, '%') OR u.nombre_completo LIKE CONCAT('%', ?, '%'))";
    $params[] = $q;
    $params[] = $q;
    $types .= 'ss';
}
if ($cat) {
    $query .= " AND c.nombre = ?";
    $params[] = $cat;
    $types .= 's';
}
if ($sede) {
    $query .= " AND m.nombre LIKE CONCAT('%', ?, '%')";
    $params[] = $sede;
    $types .= 's';
}
if ($estado) {
    $query .= " AND p.estatus = ?";
    $params[] = strtoupper($estado);
    $types .= 's';
} else {
    $query .= " AND p.estatus = 'PENDIENTE'";
}

// --- ORDEN ---
switch ($orden) {
    case 'antiguo': $query .= " ORDER BY p.creada_en ASC"; break;
    case 'titulo': $query .= " ORDER BY p.titulo ASC"; break;
    default: $query .= " ORDER BY p.creada_en DESC"; break;
}

// --- EJECUCIÓN ---
$stmt = $conexion->prepare($query);
if (!empty($params)) $stmt->bind_param($types, ...$params);
$stmt->execute();
$res = $stmt->get_result();
$rows = $res->fetch_all(MYSQLI_ASSOC);

// --- RESPUESTA ---
echo json_encode(['ok' => true, 'data' => $rows], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
