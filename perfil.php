<?php
session_start();
require_once "conexion.php"; // asegúrate de tener tu conexión MySQLi en este archivo

// 🔒 Verifica sesión
if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
  header("Location: login.php");
  exit;
}

// Variables iniciales
$usuario_id = (int)$_SESSION['user']['id'];
$mensaje_info = "";
$mensaje_pwd = "";

// =====================
// ACTUALIZAR DATOS DE PERFIL
// =====================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'actualizar_info') {
  $nombre = trim($_POST['name']);
  $apellidos = trim($_POST['lastname']);
  $genero = trim($_POST['gender']);
  $pais = trim($_POST['birth_country']);
  $nacionalidad = trim($_POST['nationality']);

  $nombre_completo = trim("$nombre $apellidos");

  $stmt = $conexion->prepare("UPDATE usuarios SET nombre_completo=?, genero=?, actualizado_en=NOW() WHERE usuario_id=?");
  $stmt->bind_param("ssi", $nombre_completo, $genero, $usuario_id);

  if ($stmt->execute()) {
    $mensaje_info = "<div class='alert alert-success small'>✅ Datos actualizados correctamente.</div>";
  } else {
    $mensaje_info = "<div class='alert alert-danger small'>❌ Error al actualizar los datos.</div>";
  }
  $stmt->close();
}
// =====================
// CAMBIO DE CONTRASEÑA
// =====================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'cambiar_pwd') {
  $pwd1 = $_POST['password'] ?? '';
  $pwd2 = $_POST['password2'] ?? '';

  if ($pwd1 === $pwd2 && strlen($pwd1) >= 8) {
    $hash = password_hash($pwd1, PASSWORD_BCRYPT);
    $stmt = $conexion->prepare("UPDATE usuarios SET password_hash=? WHERE usuario_id=?");
    $stmt->bind_param("si", $hash, $usuario_id);

    if ($stmt->execute()) {
      $mensaje_pwd = "<div class='alert alert-success small'>🔒 Contraseña actualizada correctamente.</div>";
    } else {
      $mensaje_pwd = "<div class='alert alert-danger small'>⚠️ Error al cambiar la contraseña.</div>";
    }
    $stmt->close();
  } else {
    $mensaje_pwd = "<div class='alert alert-danger small'>⚠️ Las contraseñas no coinciden o no cumplen los requisitos.</div>";
  }
}

// =====================
// CONSULTA DE DATOS DEL USUARIO
// =====================
$stmt = $conexion->prepare("
  SELECT u.nombre_completo, u.genero, 
         p1.nombre AS pais_nacimiento_nombre,
         p2.nombre AS nacionalidad_nombre,
         u.email
  FROM usuarios u
  LEFT JOIN pais p1 ON p1.pais_id = u.pais_nacimiento_id
  LEFT JOIN pais p2 ON p2.pais_id = u.nacionalidad_id
  WHERE u.usuario_id = ?");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Separar nombre y apellidos
$nombre_completo = $user['nombre_completo'] ?? '';
$partes = explode(' ', $nombre_completo, 2);
$nombre = $partes[0] ?? '';
$apellidos = $partes[1] ?? '';
$genero = $user['genero'] ?? '';
$pais = $user['pais_nacimiento_nombre'] ?? '';
$nacionalidad = $user['nacionalidad_nombre'] ?? '';
$email = $user['email'] ?? '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Mi perfil | 4everFootball</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="./css/index.css?v=42">
</head>
<body class="ff-bg">

<header class="ff-header sticky-top">
  <div class="container d-flex align-items-center justify-content-between py-2">
    <a href="index.php"><img src="img/logo.svg" alt="4everFootball" style="height:34px"></a>
    <div>
      <a href="logout.php" class="btn btn-outline-light btn-sm">Cerrar sesión</a>
    </div>
  </div>
</header>

<main class="container py-4 pb-5">
  <section class="glass-card p-3 p-md-4 mb-4">
    <h1 class="ff-title mb-0">Mi perfil</h1>
  </section>

    <section class="glass-card p-3 p-md-4 mb-4">
    <h2 class="h5 text-white mb-3">Mis datos</h2>
    <?= $mensaje_info ?>

    <form method="post" novalidate>
      <input type="hidden" name="accion" value="actualizar_info">
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Nombre</label>
          <input class="form-control" type="text" name="name" value="<?= htmlspecialchars($nombre) ?>" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Apellidos</label>
          <input class="form-control" type="text" name="lastname" value="<?= htmlspecialchars($apellidos) ?>" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Género</label>
          <select class="form-select" name="gender">
            <option value="" disabled <?= $genero==''?'selected':'' ?>>Selecciona...</option>
            <option value="Masculino" <?= $genero=='Masculino'?'selected':'' ?>>Masculino</option>
            <option value="Femenino" <?= $genero=='Femenino'?'selected':'' ?>>Femenino</option>
            <option value="Otro" <?= $genero=='Otro'?'selected':'' ?>>Otro</option>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">País de nacimiento</label>
          <input class="form-control" type="text" name="birth_country" value="<?= htmlspecialchars($pais) ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label">Nacionalidad</label>
          <input class="form-control" type="text" name="nationality" value="<?= htmlspecialchars($nacionalidad) ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label">Correo electrónico</label>
          <input class="form-control" type="email" value="<?= htmlspecialchars($email) ?>" readonly>
        </div>
        <div class="col-12 d-flex justify-content-end gap-2">
          <a href="index.php" class="btn btn-outline-light">Cancelar</a>
          <button type="submit" class="btn btn-login">Guardar cambios</button>
        </div>
      </div>
    </form>
  </section>

    <section class="glass-card p-3 p-md-4">
    <h2 class="h5 text-white mb-3">Cambiar contraseña</h2>
    <?= $mensaje_pwd ?>

    <form method="post" novalidate>
      <input type="hidden" name="accion" value="cambiar_pwd">
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Nueva contraseña</label>
          <input class="form-control" type="password" name="password" required minlength="8">
        </div>
        <div class="col-md-6">
          <label class="form-label">Confirmar nueva contraseña</label>
          <input class="form-control" type="password" name="password2" required minlength="8">
        </div>
        <div class="col-12 d-flex justify-content-end">
          <button type="submit" class="btn btn-login">Actualizar contraseña</button>
        </div>
      </div>
    </form>
  </section>
</main>

</body>
</html>
