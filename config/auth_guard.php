<?php
declare(strict_types=1);

/**
 * 🔒 auth_guard.php
 * Middleware de autenticación para 4everFootball
 *
 * Se encarga de:
 * - Iniciar la sesión de forma unificada.
 * - Redirigir al login si el usuario no está logueado.
 * - (Opcional) Validar rol de usuario.
 */

// Carga la sesión compartida
require_once __DIR__ . '/session_init.php';

// Si no hay sesión activa
if (empty($_SESSION['user'])) {
  header('Location: /4everFootball/login.php?next=' . urlencode($_SERVER['REQUEST_URI']));
  exit;
}

/**
 * 🚫 Validación opcional de roles
 * Si la página requiere un rol específico (por ejemplo ADMIN),
 * se puede llamar al guard así:
 *
 *   require_once __DIR__ . '/auth_guard.php';
 *   require_role('ADMIN');
 */
function require_role(string $role): void {
  if (empty($_SESSION['user']['rol']) || $_SESSION['user']['rol'] !== $role) {
    header('Location: /4everFootball/index.php');
    exit;
  }
}
