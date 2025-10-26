<?php
// scripts/migrar_users_json.php
declare(strict_types=1);
header('Content-Type: text/plain; charset=utf-8');

// Mostrar errores durante la migración (útil en dev)
error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once __DIR__ . '/../conexion.php';

// Rutas base
$ROOT = realpath(__DIR__ . '/..');                 // /4everFootball
$JSON = $ROOT . '/data/users.json';

// Utilidades
function out(string $msg){ echo $msg . PHP_EOL; }
function fail(string $msg){ out('Error: ' . $msg); exit; }

// Validar archivo
if (!is_file($JSON)) {
  fail("No existe data/users.json en: $JSON");
}

// Leer JSON
$raw  = file_get_contents($JSON);
$data = json_decode($raw, true);
if (!is_array($data)) fail('JSON inválido o vacío.');

// Helper: obtener/crear país
function getPaisId(mysqli $db, ?string $nombre): ?int {
  $nombre = trim((string)$nombre);
  if ($nombre === '') return null;

  // Buscar
  $stmt = $db->prepare("SELECT pais_id FROM pais WHERE nombre=?");
  $stmt->bind_param('s', $nombre);
  $stmt->execute(); $stmt->bind_result($pid);
  if ($stmt->fetch()) { $stmt->close(); return (int)$pid; }
  $stmt->close();

  // Insertar
  $stmt = $db->prepare("INSERT INTO pais (nombre) VALUES (?)");
  $stmt->bind_param('s', $nombre);
  if (!$stmt->execute()) { $stmt->close(); return null; }
  $newId = $db->insert_id;
  $stmt->close();
  return (int)$newId;
}

// Resolver ruta de avatar del JSON a un path local
function resolveAvatarPath(string $avatar, string $ROOT): ?string {
  if ($avatar === '') return null;

  // Normaliza separadores "\" -> "/"
  $avatar = str_replace('\\', '/', $avatar);

  // Si viene con prefijo del proyecto (/4everFootball/...), quítalo
  $avatarTrim = preg_replace('#^/4everFootball#', '', $avatar);

  // Intenta varias ubicaciones comunes
  $candidates = [
    $_SERVER['DOCUMENT_ROOT'] . $avatar,               // absoluto desde docroot
    $_SERVER['DOCUMENT_ROOT'] . $avatarTrim,           // sin prefijo del proyecto
    $ROOT . $avatar,                                   // relativo al proyecto
    $ROOT . $avatarTrim,                               // relativo sin prefijo
    $ROOT . '/data/uploads/' . basename($avatarTrim),  // carpeta uploads por defecto
  ];

  foreach ($candidates as $c) {
    if ($c && is_file($c)) return $c;
  }
  return null;
}

// Asegurar rol ADMIN (idempotente)
$conexion->query("INSERT IGNORE INTO roles (nombre) VALUES ('ADMIN')");

// Mapeo de género de JSON → BD
$GEN_MAP = ['M' => 'Masculino', 'F' => 'Femenino', 'X' => 'Otro'];

// Iniciar transacción
$conexion->begin_transaction();

try {
  foreach ($data as $u) {
    $firstName = trim((string)($u['first_name']  ?? ''));
    $lastP     = trim((string)($u['last_name_p'] ?? ''));
    $lastM     = trim((string)($u['last_name_m'] ?? ''));
    $fullName  = trim($firstName . ' ' . $lastP . ' ' . $lastM);
    $birth     = trim((string)($u['birth_date']  ?? '2000-01-01'));
    $genderRaw = strtoupper(trim((string)($u['gender'] ?? 'X')));
    $genderDB  = $GEN_MAP[$genderRaw] ?? 'Otro';
    $email     = trim((string)($u['email'] ?? ''));
    $hash      = (string)($u['password_hash'] ?? '');
    $isAdmin   = !empty($u['isAdmin']);

    if ($fullName === '' || $email === '' || $hash === '') {
      out("Saltado (datos incompletos): $email");
      continue;
    }

    // País y nacionalidad (texto libre, creamos si no existe)
    $birthCountry = (string)($u['birth_country'] ?? '');
    $nationality  = (string)($u['nationality']   ?? $birthCountry);

    $paisNacId = getPaisId($conexion, $birthCountry);
    $nacId     = getPaisId($conexion, $nationality);

    // Evitar duplicado por email
    $chk = $conexion->prepare("SELECT usuario_id FROM usuarios WHERE email=?");
    $chk->bind_param('s', $email);
    $chk->execute(); $res = $chk->get_result();
    $exists = $res && $res->num_rows > 0;
    $chk->close();

    if ($exists) {
      out("Ya existe: $email (omitido)");
      continue;
    }

    // Foto (opcional) → blob
    $fotoBlob = null;
    $avatar   = (string)($u['avatar'] ?? '');
    if ($avatar !== '') {
      $resolved = resolveAvatarPath($avatar, $ROOT);
      if ($resolved && is_file($resolved)) {
        // Validar MIME real
        $fi = new finfo(FILEINFO_MIME_TYPE);
        $mime = $fi->file($resolved) ?: '';
        if (in_array($mime, ['image/jpeg','image/png','image/webp'], true)) {
          $size = filesize($resolved);
          if ($size !== false && $size <= 2 * 1024 * 1024) { // ≤ 2 MB
            $fotoBlob = file_get_contents($resolved);
          }
        }
      }
    }

    // Insertar usuario
    if ($fotoBlob !== null) {
      $stmt = $conexion->prepare("INSERT INTO usuarios
        (nombre_completo, fecha_nacimiento, genero, pais_nacimiento_id, nacionalidad_id,
         email, password_hash, foto_blob, activo)
        VALUES (?,?,?,?,?,?,?, ?,1)");
      $null = null; // placeholder para blob
      $stmt->bind_param('sssisssb', $fullName, $birth, $genderDB, $paisNacId, $nacId, $email, $hash, $null);
      $stmt->send_long_data(7, $fotoBlob); // índice 7 (0-based)
    } else {
      $stmt = $conexion->prepare("INSERT INTO usuarios
        (nombre_completo, fecha_nacimiento, genero, pais_nacimiento_id, nacionalidad_id,
         email, password_hash, foto_blob, activo)
        VALUES (?,?,?,?,?,?,?, NULL,1)");
      $stmt->bind_param('sssisss', $fullName, $birth, $genderDB, $paisNacId, $nacId, $email, $hash);
    }

    $stmt->execute();
    $uid = (int)$stmt->insert_id;
    $stmt->close();

    out("Migrado: $email (id=$uid)");

    // Rol ADMIN si corresponde
    if ($isAdmin) {
      $stmt = $conexion->prepare(
        "INSERT IGNORE INTO usuario_rol (usuario_id, rol_id)
         SELECT ?, r.rol_id
         FROM roles r
         WHERE r.nombre = 'ADMIN'
         LIMIT 1"
      );
      $stmt->bind_param('i', $uid);
      $stmt->execute();
      $stmt->close();
      out("  -> Rol ADMIN asignado");
    }
  }

  $conexion->commit();
  out("OK: Migración finalizada.");

} catch (Throwable $e) {
  $conexion->rollback();
  fail($e->getMessage());
}
