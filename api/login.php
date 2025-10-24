<?php declare(strict_types=1);


header('Content-Type: application/json; charset=UTF-8');

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
  http_response_code(405);
  echo json_encode(['ok'=>false,'msg'=>'Método no permitido']); exit;
}

$ROOT     = realpath(__DIR__ . '/..');                   
$DATA_DIR = $ROOT . DIRECTORY_SEPARATOR . 'data';
$USERS    = $DATA_DIR . DIRECTORY_SEPARATOR . 'users.json';

function json_read(string $file): array {
  if (!is_file($file)) return [];
  $raw = file_get_contents($file);
  if ($raw === false || $raw === '') return [];
  $data = json_decode($raw, true);
  return is_array($data) ? $data : [];
}

$email = trim($_POST['email'] ?? '');
$pass  = $_POST['password'] ?? '';

if ($email === '' || $pass === '') {
  http_response_code(400);
  echo json_encode(['ok'=>false,'msg'=>'Correo y contraseña requeridos.']); exit;
}

$users = json_read($USERS);

// Buscar por email (case-insensitive)
$found = null;
foreach ($users as $u) {
  if (isset($u['email']) && strcasecmp($u['email'], $email) === 0) {
    $found = $u; break;
  }
}

if (!$found) {
  http_response_code(401);
  echo json_encode(['ok'=>false,'msg'=>'Correo o contraseña incorrectos.']); exit;
}

$hash = $found['password_hash'] ?? '';
if (!is_string($hash) || !password_verify($pass, $hash)) {
  http_response_code(401);
  echo json_encode(['ok'=>false,'msg'=>'Correo o contraseña incorrectos.']); exit;
}

// Éxito
$name    = (string)($found['first_name'] ?? '');
$isAdmin = !empty($found['isAdmin']);

echo json_encode([
  'ok'      => true,
  'msg'     => 'Bienvenid@ ' . $name,
  'name'    => $name,
  'isAdmin' => $isAdmin,
  'user'    => [
    'email'   => $found['email'],
    'name'    => $name,
    'isAdmin' => $isAdmin,
  ],
]);
