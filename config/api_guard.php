<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');

// 🚀 Inicia o retoma la sesión unificada
require_once __DIR__ . '/session_init.php';

// ✅ Si no hay sesión iniciada, devuelve error JSON
if (empty($_SESSION['user'])) {
  http_response_code(401);
  echo json_encode([
    'ok' => false,
    'error' => 'Sesión no iniciada'
  ]);
  exit;
}

/**
 * 🔒 Valida que el usuario tenga un rol específico.
 * 
 * @param string $role Rol requerido (por ejemplo 'ADMIN' o 'USER')
 * 
 * Si el rol del usuario en sesión no coincide, devuelve error 403.
 */
function require_role_api(string $role): void {
  $user = $_SESSION['user'] ?? [];

  // Compatibilidad entre 'rol' textual y booleano isAdmin
  $rolActual = strtoupper($user['rol'] ?? '');
  $esAdmin = !empty($user['isAdmin']) || ($rolActual === 'ADMIN');

  if ($role === 'ADMIN' && !$esAdmin) {
    http_response_code(403);
    echo json_encode([
      'ok' => false,
      'error' => 'Acceso denegado — rol insuficiente'
    ]);
    exit;
  }
}

