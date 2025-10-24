<?php
// /4everFootball/auth.php
session_start();
$BASE = '/4everFootball';

if (empty($_SESSION['user'])) {
  $next = urlencode($_SERVER['REQUEST_URI'] ?? "$BASE/index.php");
  header("Location: $BASE/login.php?next=$next");
  exit;
}
