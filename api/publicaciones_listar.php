<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');

// 🔒 Protección global de sesión y rol
require_once __DIR__ . '/../config/api_guard.php';
require_role_api('ADMIN');

// 🔌 Conexión a la BD
require_once __DIR__ . '/../conexion.php';

// --- Parámetros GET ---
$q      = trim($_GET['q']      ?? '');
$cat    = trim($_GET['cat']    ?? '');
$sede   = trim($_GET['sede']   ?? '');
$estado = trim($_GET['estado'] ?? '');
$orden  = trim($_GET['orden']  ?? 'reciente');

// --- Base del query ---
$sql = "
    SELECT 
        p.publicacion_id,
        p.titulo,
        p.descripcion,
        p.tipo_media,
        p.media_url,
        p.estatus,
        p.creada_en,
        COALESCE(c.nombre, 'Sin categoría') AS categoria,
        COALESCE(m.nombre_comunidad, 'Sin mundial') AS sede,
        COALESCE(u.nombre_completo, 'Usuario desconocido') AS autor
    FROM publicacion p
    LEFT JOIN categoria c ON p.categoria_id = c.categoria_id
    LEFT JOIN mundial m   ON p.mundial_id = m.mundial_id
    LEFT JOIN usuarios u  ON p.usuario_id = u.usuario_id
    WHERE 1
";

$params = [];
$types  = '';

// --- Filtros dinámicos ---
if ($q !== '') {
    $sql .= " AND (p.titulo LIKE CONCAT('%', ?, '%') OR u.nombre_completo LIKE CONCAT('%', ?, '%'))";
    $params[] = $q;
    $params[] = $q;
    $types .= 'ss';
}

if ($cat !== '') {
    $sql .= " AND c.nombre = ?";
    $params[] = $cat;
    $types .= 's';
}

if ($sede !== '') {
    if (is_numeric($sede)) {
        $sql .= " AND p.mundial_id = ?";
        $params[] = (int)$sede;
        $types .= 'i';
    } else {
        $sql .= " AND LOWER(m.nombre_comunidad) LIKE LOWER(CONCAT('%', ?, '%'))";
        $params[] = $sede;
        $types .= 's';
    }
}

if ($estado !== '') {
    $sql .= " AND UPPER(p.estatus) = ?";
    $params[] = strtoupper($estado);
    $types .= 's';
} else {
    // Por defecto: mostrar solo pendientes
    $sql .= " AND UPPER(p.estatus) = 'PENDIENTE'";
}

// --- Orden ---
switch ($orden) {
    case 'antiguo': $sql .= " ORDER BY p.creada_en ASC"; break;
    case 'titulo':  $sql .= " ORDER BY p.titulo ASC"; break;
    default:        $sql .= " ORDER BY p.creada_en DESC"; break;
}

// --- Ejecución ---
try {
    $stmt = $conexion->prepare($sql);
    if (!empty($params)) $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $res  = $stmt->get_result();
    $rows = $res->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    echo json_encode([
        'ok'   => true,
        'data' => $rows
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'ok'    => false,
        'error' => 'Error al listar publicaciones: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
