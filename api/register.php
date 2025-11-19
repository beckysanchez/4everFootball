<?php
// api/register.php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok'=>false, 'error'=>'Método no permitido']);
    exit;
}

session_start();
require_once __DIR__ . '/../conexion.php';

// ===== Helpers =====
function jerror(string $msg, int $code=400){
    http_response_code($code);
    echo json_encode(['ok'=>false,'error'=>$msg]);
    exit;
}
function json_ok(array $data=[]){
    echo json_encode(['ok'=>true] + $data);
    exit;
}

// ===== CSRF =====
$csrf = $_POST['csrf'] ?? '';
if (!is_string($csrf) || $csrf === '' || empty($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $csrf)) {
    jerror('Token CSRF inválido', 400);
}

// ===== Campos =====
$first   = trim($_POST['first_name']   ?? '');
$last_p  = trim($_POST['last_name_p']  ?? '');
$last_m  = trim($_POST['last_name_m']  ?? '');
$birth   = trim($_POST['birth_date']   ?? '');
$gender  = trim($_POST['gender']       ?? '');
$country = trim($_POST['country']      ?? '');
$nation  = trim($_POST['nationality']  ?? '');
$email   = trim($_POST['email']        ?? '');
$pass    = (string)($_POST['password'] ?? '');

if ($first==='' || $last_p==='' || $last_m==='' || $birth==='' || $gender==='' || $country==='' || $nation==='' || $email==='' || $pass==='') {
    jerror('Faltan campos obligatorios.');
}

// ===== Edad mínima =====
function isAtLeast12(string $dateStr): bool {
    $bd = strtotime($dateStr);
    if ($bd === false) return false;
    $limit = strtotime('-12 years');
    return $bd <= $limit;
}
if (!isAtLeast12($birth)) jerror('Debes tener al menos 12 años.');

// ===== Correo =====
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) jerror('Correo inválido.');
if (stripos($email, '@4everAdmin.com') !== false) {
    jerror('No puedes registrarte con un correo de administrador.');
}

// ===== Contraseña =====
$hasMinLen = strlen($pass) >= 8;
$hasLower  = (bool)preg_match('/[a-z]/', $pass);
$hasUpper  = (bool)preg_match('/[A-Z]/', $pass);
$hasDigit  = (bool)preg_match('/\d/',     $pass);
$hasSpecial= (preg_match('/[^\p{L}\p{N}\s]/u', $pass) === 1) && (preg_match('/ñ/i', $pass) !== 1);
if (!($hasMinLen && $hasLower && $hasUpper && $hasDigit && $hasSpecial)) {
    jerror('La contraseña no cumple el formato requerido.');
}

// ===== Mapeo de género =====
$generoMap = ['M'=>'Masculino', 'F'=>'Femenino', 'X'=>'Otro'];
$generoDB  = $generoMap[$gender] ?? 'Otro';

// ===== Validar país/nacionalidad con API RestCountries =====
function validarPais(string $nombre): bool {
    $nombre = trim($nombre);
    if ($nombre === '') return false;

    $url = 'https://restcountries.com/v3.1/name/' . urlencode($nombre) . '?fields=name,translations';
    $ctx = stream_context_create(['http'=>['timeout'=>4]]);
    $json = @file_get_contents($url, false, $ctx);
    if ($json === false) return true; // si falla la API, no bloquea registro

    $data = json_decode($json, true);
    if (!is_array($data)) return false;

    $normalize = fn($s) => strtolower(trim(iconv('UTF-8','ASCII//TRANSLIT',$s ?? '')));
    $n = $normalize($nombre);

    foreach ($data as $country) {
        if (!empty($country['name']['common']) && $normalize($country['name']['common']) === $n) return true;
        if (!empty($country['name']['official']) && $normalize($country['name']['official']) === $n) return true;
        if (!empty($country['translations'])) {
            foreach ($country['translations'] as $t) {
                if (!empty($t['common']) && $normalize($t['common']) === $n) return true;
                if (!empty($t['official']) && $normalize($t['official']) === $n) return true;
            }
        }
    }
    return false;
}

if (!validarPais($country)) jerror('País de nacimiento inválido o no reconocido.');
if (!validarPais($nation)) jerror('Nacionalidad inválida o no reconocida.');

// ===== Foto opcional =====
$fotoBlob = null;
if (!empty($_FILES['photo']['name'])) {
    $f = $_FILES['photo'];
    if ($f['error'] !== UPLOAD_ERR_OK) jerror('Error al subir la imagen.');

    $fi   = new finfo(FILEINFO_MIME_TYPE);
    $mime = $fi->file($f['tmp_name']) ?: '';
    $validTypes = ['image/jpeg','image/png','image/webp'];
    if (!in_array($mime, $validTypes, true)) jerror('Formato de imagen inválido.');
    if ($f['size'] > 2 * 1024 * 1024) jerror('La imagen excede 2MB.');

    $fotoBlob = file_get_contents($f['tmp_name']);
}

// ===== Transacción =====
$conexion->begin_transaction();

try {
    $hash = password_hash($pass, PASSWORD_DEFAULT);
    $nombreCompleto = "$first $last_p $last_m";

    // Stored procedure para crear usuario con rol USUARIO
    $stmt = $conexion->prepare("CALL sp_crear_usuario(?, ?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) throw new RuntimeException('Error al preparar el procedimiento.');

    $stmt->bind_param('sssssssb',
        $nombreCompleto,   // p_nombre_completo
        $birth,            // p_fecha_nacimiento
        $generoDB,         // p_genero
        $country,          // p_pais_nacimiento
        $nation,           // p_nacionalidad
        $email,            // p_email
        $hash,             // p_password_hash
        $fotoBlob          // p_foto
    );

    if (!$stmt->execute()) throw new RuntimeException('Error al registrar usuario.');
    $res = $stmt->get_result();
    $data = $res ? $res->fetch_assoc() : [];
    $stmt->close();

    $conexion->commit();

    $userId = $data['nuevo_usuario_id'] ?? 0;
    if (!$userId) throw new RuntimeException('Error al obtener el ID del nuevo usuario.');
} catch (Throwable $e) {
    $conexion->rollback();
    jerror($e->getMessage(), 400);
}

// ===== Crear sesión PHP y devolver JSON =====
$_SESSION['user'] = [
    'id'      => (int)$userId,
    'email'   => $email,
    'name'    => $nombreCompleto,
    'isAdmin' => false
];
session_regenerate_id(true);

json_ok([
    'msg'  => 'Cuenta creada correctamente.',
    'next' => '/4everFootball/login.php',
    'user' => $_SESSION['user']
]);
