<?php declare(strict_types=1);

header('Content-Type: application/json; charset=UTF-8');
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
  http_response_code(405);
  echo json_encode(['ok'=>false,'error'=>'Método no permitido']); exit;
}

session_start();

// CSRF
$csrf = $_POST['csrf'] ?? '';
if (!is_string($csrf) || $csrf === '' || empty($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $csrf)) {
  http_response_code(400);
  echo json_encode(['ok'=>false,'error'=>'Token CSRF inválido']); exit;
}

// next
$next = (isset($_POST['next']) && is_string($_POST['next']) && $_POST['next']!=='')
  ? $_POST['next'] : '/4everFootball/index.php';

// entradas
$email = trim((string)($_POST['email'] ?? ''));
$pass  = (string)($_POST['password'] ?? '');
if ($email === '' || $pass === '') {
  http_response_code(400);
  echo json_encode(['ok'=>false,'error'=>'Correo y contraseña requeridos']); exit;
}

require_once __DIR__ . '/../conexion.php';

try {
  // 🔹 Trae al usuario y si es ADMIN (via roles/usuario_rol)
  $sql = "SELECT 
            u.usuario_id, u.nombre_completo, u.email, u.password_hash, u.activo,
            EXISTS(
              SELECT 1
              FROM usuario_rol ur
              JOIN roles r ON r.rol_id = ur.rol_id
              WHERE ur.usuario_id = u.usuario_id AND r.nombre = 'ADMIN'
            ) AS isAdmin
          FROM usuarios u
          WHERE u.email = ?
          LIMIT 1";
  $stmt = $conexion->prepare($sql);
  if (!$stmt) throw new Exception('Error de preparación SQL');
  $stmt->bind_param('s', $email);
  $stmt->execute();
  $res  = $stmt->get_result();
  $user = $res?->fetch_assoc() ?: null;
  $stmt->close();

  if (!$user) {
    http_response_code(401);
    echo json_encode(['ok'=>false,'error'=>'Correo o contraseña incorrectos']); exit;
  }
  if ((int)($user['activo'] ?? 0) === 0) {
    http_response_code(403);
    echo json_encode(['ok'=>false,'error'=>'Cuenta inactiva. Contacta al administrador.']); exit;
  }

  if (!password_verify($pass, (string)$user['password_hash'])) {
    http_response_code(401);
    echo json_encode(['ok'=>false,'error'=>'Correo o contraseña incorrectos']); exit;
  }
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['ok'=>false,'error'=>'Error del servidor']); exit;
}

// ✅ usa el flag que vino de la consulta
$isAdmin = !empty($user['isAdmin']);

$_SESSION['user'] = [
  'id'      => (int)$user['usuario_id'],
  'email'   => (string)$user['email'],
  'name'    => (string)$user['nombre_completo'],
  'isAdmin' => $isAdmin
];

session_regenerate_id(true);

echo json_encode([
  'ok'   => true,
  'msg'  => 'Inicio de sesión correcto.',
  'next' => $next,
  'user' => $_SESSION['user']
]);
