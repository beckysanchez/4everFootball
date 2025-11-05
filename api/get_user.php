<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user'])) {
    echo json_encode(null);
    exit;
}

echo json_encode($_SESSION['user']);
?>
