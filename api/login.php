<?php declare(strict_types=1);

// api/login.php
header('Content-Type: application/json; charset=UTF-8');

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
  http_response_code(405);
  echo json_encode(['ok'=>false,'error'=>'Método no permitido']); exit;
}

session_start();

// Validación CSRF (el login.php ya envía "csrf" hidden)
$csrf = $_POST['csrf'] ?? '';
if (!is_string($csrf) || $csrf === '' || empty($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $csrf)) {
  http_response_code(400);
  echo json_encode(['ok'=>false,'error'=>'Token CSRF inválido']); exit;
}

// next opcional: a dónde regresar tras login
$next = isset($_POST['next']) && is_string($_POST['next']) && $_POST['next'] !== ''
  ? $_POST['next']
  : '/4everFootball/index.php';

// Normalizar entradas
$email = trim((string)($_POST['email'] ?? ''));
$pass  = (string)($_POST['password'] ?? '');

if ($email === '' || $pass === '') {
  http_response_code(400);
  echo json_encode(['ok'=>false,'error'=>'Correo y contraseña requeridos']); exit;
}
require_once __DIR__ . '/../conexion.php'; // $conexion (mysqli)

try {
  // Buscar usuario por email
  $sql  = "SELECT usuario_id, nombre_completo, email, password_hash, activo
           FROM usuarios
           WHERE email = ? LIMIT 1";
  $stmt = $conexion->prepare($sql);
  if (!$stmt) { throw new Exception('Error de preparación SQL'); }

  $stmt->bind_param('s', $email);
  $stmt->execute();
  $res  = $stmt->get_result();
  $user = $res?->fetch_assoc() ?: null;
  $stmt->close();

  if (!$user) {
    http_response_code(401);
    echo json_encode(['ok'=>false,'error'=>'Correo o contraseña incorrectos']); exit;
  }

  if (isset($user['activo']) && (int)$user['activo'] === 0) {
    http_response_code(403);
    echo json_encode(['ok'=>false,'error'=>'Cuenta inactiva. Contacta al administrador.']); exit;
  }

  // Verificar contraseña
  $hash = (string)($user['password_hash'] ?? '');
  if ($hash === '' || !password_verify($pass, $hash)) {
    http_response_code(401);
    echo json_encode(['ok'=>false,'error'=>'Correo o contraseña incorrectos']); exit;
  }

} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['ok'=>false,'error'=>'Error del servidor']); exit;
}

// Aquí podrías consultar rol/isAdmin si lo tienes en tu tabla
$isAdmin = false;

$_SESSION['user'] = [
  'id'      => (int)$user['usuario_id'],
  'email'   => (string)$user['email'],
  'name'    => (string)$user['nombre_completo'],
  'isAdmin' => $isAdmin
];

// Opcional: regenerar sesión por seguridad
session_regenerate_id(true);

echo json_encode([
  'ok'   => true,
  'msg'  => 'Inicio de sesión correcto.',
  'next' => $next,
  'user' => $_SESSION['user']
]);
