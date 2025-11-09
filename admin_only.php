<?php
session_start();

// Si no hay sesión activa, redirige al login
if (!isset($_SESSION['user'])) {
  header('Location: /4everFootball/login.php');
  exit;
}

// Si existe usuario pero no es admin, mostrar mensaje
if ($_SESSION['user']['rol'] !== 'ADMIN') {
  echo "<h3 style='text-align:center; margin-top:3rem; color:white;'>Acceso restringido para administradores.</h3>";
  exit;
}
?>
