<?php
if (session_status() === PHP_SESSION_NONE) {

    // Configuración estándar y estable (funciona en localhost sin errores CSRF)
    session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/4everFootball',
    'secure' => false,
    'httponly' => true,
    'samesite' => 'Lax'
]);
session_start();

}

// Generar token CSRF si no existe
if (empty($_SESSION['csrf']) || strlen($_SESSION['csrf']) < 20) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}
