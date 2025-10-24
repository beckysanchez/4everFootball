<?php
// api/register.php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok'=>false, 'error'=>'Método no permitido']); exit;
}

session_start();
require_once __DIR__ . '/../conexion.php';

// ===== Helpers =====
function jerror(string $msg, int $code=400){ http_response_code($code); echo json_encode(['ok'=>false,'error'=>$msg]); exit; }
function json_ok(array $data=[]){ echo json_encode(['ok'=>true] + $data); exit; }

// CSRF
$csrf = $_POST['csrf'] ?? '';
if (!is_string($csrf) || $csrf === '' || empty($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $csrf)) {
    jerror('Token CSRF inválido', 400);
}

// next (opcional)
$next = (isset($_POST['next']) && is_string($_POST['next']) && $_POST['next'] !== '')
      ? $_POST['next']
      : '/4everFootball/index.php';

// ===== 1) Campos =====
$first   = trim($_POST['first_name']   ?? '');
$last_p  = trim($_POST['last_name_p']  ?? '');
$last_m  = trim($_POST['last_name_m']  ?? '');
$birth   = trim($_POST['birth_date']   ?? '');
$gender  = trim($_POST['gender']       ?? '');  // M/F/X
$country = trim($_POST['country']      ?? '');  // texto
$nation  = trim($_POST['nationality']  ?? '');  // texto
$email   = trim($_POST['email']        ?? '');
$pass    = (string)($_POST['password'] ?? '');

if ($first==='' || $last_p==='' || $last_m==='' || $birth==='' || $gender==='' || $country==='' || $nation==='' || $email==='' || $pass==='') {
    jerror('Faltan campos obligatorios.');
}

// Edad ≥ 12
function isAtLeast12(string $dateStr): bool {
    $bd = strtotime($dateStr);
    if ($bd === false) return false;
    $limit = strtotime('-12 years');
    return $bd <= $limit;
}
if (!isAtLeast12($birth)) jerror('Debes tener al menos 12 años.');

// Email válido
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) jerror('Correo inválido.');

// Password fuerte (sin contar “ñ” como especial)
$hasMinLen = strlen($pass) >= 8;
$hasLower  = (bool)preg_match('/[a-z]/', $pass);
$hasUpper  = (bool)preg_match('/[A-Z]/', $pass);
$hasDigit  = (bool)preg_match('/\d/',     $pass);
// Especial = no letra ni número (excluye espacios); y además no considerar "ñ" como especial
$hasSpecial= (preg_match('/[^\p{L}\p{N}\s]/u', $pass) === 1) && (preg_match('/ñ/i', $pass) !== 1);
if (!($hasMinLen && $hasLower && $hasUpper && $hasDigit && $hasSpecial)) {
    jerror('La contraseña no cumple el formato requerido.');
}

// Mapear género
$generoMap = ['M'=>'Masculino', 'F'=>'Femenino', 'X'=>'Otro'];
$generoDB  = $generoMap[$gender] ?? 'Otro';

// ===== 2) Helpers de países (con mysqli) =====
function getPaisId(mysqli $db, string $nombre): ?int {
    $nombre = trim($nombre);
    if ($nombre === '') return null;

    // Buscar
    $sql = "SELECT pais_id FROM pais WHERE nombre = ?";
    $stmt = $db->prepare($sql);
    if (!$stmt) return null;
    $stmt->bind_param('s', $nombre);
    $stmt->execute();
    $stmt->bind_result($pid);
    if ($stmt->fetch()) { $stmt->close(); return (int)$pid; }
    $stmt->close();

    // Insertar
    $sql = "INSERT INTO pais (nombre) VALUES (?)";
    $stmt = $db->prepare($sql);
    if (!$stmt) return null;
    $stmt->bind_param('s', $nombre);
    if (!$stmt->execute()) {
        // si ya existe por UNIQUE, recupéralo
        if ($db->errno == 1062) {
            $stmt->close();
            $stmt = $db->prepare("SELECT pais_id FROM pais WHERE nombre=?");
            $stmt->bind_param('s', $nombre);
            $stmt->execute();
            $stmt->bind_result($pid2);
            if ($stmt->fetch()) { $stmt->close(); return (int)$pid2; }
        }
        $stmt->close();
        return null;
    }
    $newId = $db->insert_id;
    $stmt->close();
    return (int)$newId;
}

// ===== 3) Validación de foto y lectura BLOB =====
$fotoBlob = null;
if (!empty($_FILES['photo']['name'])) {
    $f = $_FILES['photo'];
    if ($f['error'] !== UPLOAD_ERR_OK) jerror('Error al subir la imagen.');

    // MIME real (finfo)
    $fi   = new finfo(FILEINFO_MIME_TYPE);
    $mime = $fi->file($f['tmp_name']) ?: '';
    $validTypes = ['image/jpeg','image/png','image/webp'];
    if (!in_array($mime, $validTypes, true)) jerror('Formato de imagen inválido.');
    if ($f['size'] > 2 * 1024 * 1024) jerror('La imagen excede 2MB.');

    $fotoBlob = file_get_contents($f['tmp_name']);
}

// ===== 4) Transacción: país/nacionalidad + usuario =====
$conexion->begin_transaction();

try {
    // Email único
    $stmt = $conexion->prepare("SELECT 1 FROM usuarios WHERE email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) { $stmt->close(); throw new RuntimeException('Ese correo ya está registrado.'); }
    $stmt->close();

    // IDs de país/nacionalidad
    $paisNacId = getPaisId($conexion, $country);
    $nacId     = getPaisId($conexion, $nation);
    if ($paisNacId === null || $nacId === null) throw new RuntimeException('No fue posible registrar país/nacionalidad.');

    // Insert usuario
    $nombreCompleto = $first . ' ' . $last_p . ' ' . $last_m;
    $hash = password_hash($pass, PASSWORD_DEFAULT);

    if ($fotoBlob !== null) {
        $sql = "INSERT INTO usuarios
                (nombre_completo, fecha_nacimiento, genero, pais_nacimiento_id, nacionalidad_id,
                 email, password_hash, foto_blob, activo)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)";
        $stmt = $conexion->prepare($sql);
        if (!$stmt) throw new RuntimeException('Error de preparación SQL (usuario con foto)');
        $null = null;
        $stmt->bind_param('sssisssb',
            $nombreCompleto, $birth, $generoDB, $paisNacId, $nacId, $email, $hash, $null
        );
        // BLOB en el índice 7 (0-based)
        $stmt->send_long_data(7, $fotoBlob);
    } else {
        $sql = "INSERT INTO usuarios
                (nombre_completo, fecha_nacimiento, genero, pais_nacimiento_id, nacionalidad_id,
                 email, password_hash, foto_blob, activo)
                VALUES (?, ?, ?, ?, ?, ?, ?, NULL, 1)";
        $stmt = $conexion->prepare($sql);
        if (!$stmt) throw new RuntimeException('Error de preparación SQL (usuario sin foto)');
        $stmt->bind_param('sssisss',
            $nombreCompleto, $birth, $generoDB, $paisNacId, $nacId, $email, $hash
        );
    }

    if (!$stmt->execute()) {
        $stmt->close();
        throw new RuntimeException('No fue posible crear la cuenta. Intenta más tarde.');
    }
    $userId = $stmt->insert_id;
    $stmt->close();

    // Commit
    $conexion->commit();

} catch (Throwable $e) {
    $conexion->rollback();
    jerror($e->getMessage(), 400);
}

// ===== 5) Crear sesión y responder =====
$_SESSION['user'] = [
    'id'      => (int)$userId,
    'email'   => $email,
    'name'    => $nombreCompleto,
    'isAdmin' => false
];
// Por seguridad, regenerar ID
session_regenerate_id(true);

json_ok([
    'msg'  => 'Cuenta creada correctamente.',
    'next' => $next,
    'user' => $_SESSION['user']
]);
