<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=UTF-8');

require_once __DIR__ . '/../config/session_init.php';

// Cerrar sesión global
if (session_status() === PHP_SESSION_ACTIVE) {
  $_SESSION = [];
  if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
  }
  session_destroy();
}

echo json_encode(['ok' => true, 'msg' => 'Sesión cerrada correctamente']);
