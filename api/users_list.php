<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../conexion.php';
require_once __DIR__ . '/../config/api_guard.php';
require_role_api('ADMIN');

// Parámetros
$q = trim($_POST['q'] ?? '');
$rol = trim($_POST['rol'] ?? '');
$activo = $_POST['activo'] ?? '';
$orden = $_POST['orden'] ?? 'reciente';
$perPage = max(1, min(100, (int)($_POST['perPage'] ?? 10)));
$page = max(1, (int)($_POST['page'] ?? 1));

$where = [];
$params = [];
$types = '';

if ($q !== '') {
  $where[] = "(u.nombre_completo LIKE ? OR u.email LIKE ?)";
  $params[] = "%$q%";
  $params[] = "%$q%";
  $types .= 'ss';
}

if ($rol !== '') {
  $where[] = "r.nombre = ?";
  $params[] = $rol;
  $types .= 's';
}

if ($activo !== '') {
  $where[] = "u.activo = ?";
  $params[] = (int)$activo;
  $types .= 'i';
}

$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$orderSQL = match($orden) {
  'antiguo' => 'u.creado_en ASC',
  'nombre' => 'u.nombre_completo ASC',
  'email' => 'u.email ASC',
  default => 'u.creado_en DESC'
};

// Conteo total
$sqlTotal = "
  SELECT COUNT(*) AS total
  FROM usuarios u
  LEFT JOIN usuario_rol ur ON ur.usuario_id = u.usuario_id
  LEFT JOIN roles r ON r.rol_id = ur.rol_id
  $whereSQL
";
$stmt = $conexion->prepare($sqlTotal);
if ($types) $stmt->bind_param($types, ...$params);
$stmt->execute();
$total = $stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

$offset = ($page - 1) * $perPage;

// Consulta principal
$sql = "
  SELECT 
    u.usuario_id AS id,
    u.nombre_completo AS name,
    u.email,
    IFNULL(r.nombre, 'USUARIO') AS role,
    u.creado_en AS created,
    u.activo
  FROM usuarios u
  LEFT JOIN usuario_rol ur ON ur.usuario_id = u.usuario_id
  LEFT JOIN roles r ON r.rol_id = ur.rol_id
  $whereSQL
  ORDER BY $orderSQL
  LIMIT ? OFFSET ?
";

$params2 = $params;
$types2 = $types . 'ii';
$params2[] = $perPage;
$params2[] = $offset;

$stmt = $conexion->prepare($sql);
$stmt->bind_param($types2, ...$params2);
$stmt->execute();
$res = $stmt->get_result();
$items = $res->fetch_all(MYSQLI_ASSOC);

echo json_encode([
  'ok' => true,
  'total' => (int)$total,
  'page' => $page,
  'pages' => ceil($total / $perPage),
  'items' => $items
], JSON_UNESCAPED_UNICODE);
