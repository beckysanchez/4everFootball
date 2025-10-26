<?php
// /4everFootball/admin_only.php
session_start();
$BASE = '/4everFootball';

if (empty($_SESSION['user'])) {
  $next = urlencode($_SERVER['REQUEST_URI'] ?? "$BASE/index.php");
  header("Location: $BASE/login.php?next=$next"); exit;
}
if (empty($_SESSION['user']['isAdmin'])) {
  http_response_code(403);
  echo 'Acceso restringido para administradores.'; exit;
}


//esto es un middleware para el administrador