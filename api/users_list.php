
<?php
//api para el distado con filtros
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');

session_start();
if (empty($_SESSION['user']['isAdmin'])) { http_response_code(403); echo json_encode(['ok'=>false,'error'=>'Solo admins']); exit; }

require_once __DIR__ . '/../conexion.php';

$q       = trim($_POST['q'] ?? '');
$rol     = trim($_POST['rol'] ?? '');       // '', ADMIN, USUARIO
$activo  = trim($_POST['activo'] ?? '');    // '', '1', '0'
$orden   = trim($_POST['orden'] ?? 'reciente');
$perPage = max(1, min(100, (int)($_POST['perPage'] ?? 10)));
$page    = max(1, (int)($_POST['page'] ?? 1));
$offset  = ($page-1)*$perPage;

$w   = []; $params = []; $types = '';
if ($q !== '') {
  $w[] = "(u.nombre_completo LIKE CONCAT('%',?,'%') OR u.email LIKE CONCAT('%',?,'%'))";
  $params[] = $q; $params[] = $q; $types .= 'ss';
}
if ($activo === '0' || $activo === '1') {
  $w[] = 'u.activo = ?'; $params[] = (int)$activo; $types .= 'i';
}
if ($rol === 'ADMIN') {
  $w[] = "EXISTS(SELECT 1 FROM usuario_rol ur JOIN roles r ON r.rol_id=ur.rol_id WHERE ur.usuario_id=u.usuario_id AND r.nombre='ADMIN')";
} elseif ($rol === 'USUARIO') {
  $w[] = "NOT EXISTS(SELECT 1 FROM usuario_rol ur JOIN roles r ON r.rol_id=ur.rol_id WHERE ur.usuario_id=u.usuario_id AND r.nombre='ADMIN')";
}
$where = $w ? ('WHERE '.implode(' AND ', $w)) : '';

$orderby = match($orden){
  'antiguo' => 'u.creado_en ASC',
  'nombre'  => 'u.nombre_completo ASC',
  'email'   => 'u.email ASC',
  default   => 'u.creado_en DESC'
};

$sqlCount = "SELECT COUNT(*) c FROM usuarios u $where";
$stmt = $conexion->prepare($sqlCount);
if ($types) $stmt->bind_param($types, ...$params);
$stmt->execute(); $res = $stmt->get_result(); $total = (int)($res->fetch_assoc()['c'] ?? 0);
$stmt->close();

$sql = "SELECT 
          u.usuario_id, u.nombre_completo, u.email, u.creado_en, u.activo,
          EXISTS(SELECT 1 FROM usuario_rol ur JOIN roles r ON r.rol_id=ur.rol_id WHERE ur.usuario_id=u.usuario_id AND r.nombre='ADMIN') AS isAdmin
        FROM usuarios u
        $where
        ORDER BY $orderby
        LIMIT ? OFFSET ?";
$params2 = $params; $types2 = $types . 'ii';
$params2[] = $perPage; $params2[] = $offset;

$stmt = $conexion->prepare($sql);
$stmt->bind_param($types2, ...$params2);
$stmt->execute(); $res = $stmt->get_result();
$items = [];
while ($row = $res->fetch_assoc()){
  $items[] = [
    'id'     => (int)$row['usuario_id'],
    'name'   => (string)$row['nombre_completo'],
    'email'  => (string)$row['email'],
    'created'=> (string)$row['creado_en'],
    'activo' => (int)$row['activo']===1,
    'role'   => !empty($row['isAdmin']) ? 'ADMIN' : 'USUARIO'
  ];
}
$stmt->close();

echo json_encode([
  'ok'    => true,
  'items' => $items,
  'total' => $total,
  'page'  => $page,
  'pages' => max(1, (int)ceil($total/$perPage)),
]);
