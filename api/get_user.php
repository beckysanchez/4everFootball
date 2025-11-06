<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=UTF-8');

// Incluir la sesión global
require_once __DIR__ . '/../config/session_init.php';

// Si no hay sesión activa, devolvemos null
if (empty($_SESSION['user'])) {
    echo json_encode(null);
    exit;
}

// Devolver los datos del usuario actual
echo json_encode($_SESSION['user'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
