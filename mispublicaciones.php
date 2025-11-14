<?php
session_start();
require_once(__DIR__ . '/conexion.php');
$BASE = '/4everFootball';

// Verificar sesión
if (empty($_SESSION['user'])) {
  echo "<h2 style='color:white; text-align:center; margin-top:2rem;'>Debes iniciar sesión para ver tus publicaciones.</h2>";
  exit;
}

$usuario_id = $_SESSION['user']['id'];

// Obtener publicaciones del usuario logueado
$stmt = $conexion->prepare("
  SELECT 
    p.publicacion_id,
    p.titulo,
    p.descripcion,
    p.tipo_media,
    p.media_url,
    p.estatus,
    p.creada_en,
    c.nombre AS categoria,
    m.nombre_comunidad AS sede_nombre
  FROM publicacion p
  JOIN categoria c ON c.categoria_id = p.categoria_id
  JOIN mundial m ON m.mundial_id = p.mundial_id
  WHERE p.usuario_id = ?
  ORDER BY p.creada_en DESC
");
$stmt->bind_param('i', $usuario_id);
$stmt->execute();
$res = $stmt->get_result();
$publicaciones = $res->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Mis publicaciones | 4everFootball</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="<?= $BASE ?>/css/styles.css?v=<?= time() ?>">
</head>
<body class="ff-bg">

<header id="siteHeader" class="ff-header sticky-top">
  <div class="container d-flex align-items-center gap-3 py-2">
    <a href="<?= $BASE ?>/index.php" class="d-flex align-items-center gap-2 text-decoration-none">
      <img src="<?= $BASE ?>/img/logo.svg" alt="4everFootball" style="height:34px">
    </a>
    <form id="headerSearch" class="ms-auto me-auto w-50 d-flex">
      <div class="input-group ff-search w-100">
        <span class="input-group-text">🔎</span>
        <input id="qHeader" type="search" class="form-control" placeholder="Buscar en 4everFootball…">
      </div>
    </form>
    <nav class="d-flex align-items-center gap-2">
      <a class="btn btn-register" href="<?= $BASE ?>/crear-publicacion.php">Publicar</a>
      <div class="ff-profile position-relative">
        <button id="profileBtn" class="ff-avatar-btn" type="button">
          <img src="<?= $BASE ?>/img/icon_iniciarsesion.png?v=1" alt="Perfil" class="ff-avatar-img" width="36" height="36">
        </button>
      </div>
    </nav>
  </div>
</header>

<main class="container py-4">
  <h2 class="text-white mb-4">Mis publicaciones</h2>

  <?php if (empty($publicaciones)): ?>
    <div class="glass-card p-4 text-center text-secondary">
      Aún no has publicado nada. ¡Comparte tu primera publicación!
    </div>
  <?php else: ?>
    <div class="row">
      <?php foreach ($publicaciones as $p): ?>
        <?php
          $titulo = htmlspecialchars($p['titulo']);
          $categoria = htmlspecialchars($p['categoria']);
          $sede = htmlspecialchars($p['sede_nombre']);
          $fecha = date('d/m/Y', strtotime($p['creada_en']));
          $estado = strtoupper($p['estatus']);
          $media = htmlspecialchars($p['media_url']);
          $tipo = $p['tipo_media'];
          $likes = rand(50, 900); // 👍 valor temporal simulado
          $id = (int)$p['publicacion_id'];

          $chipColor = match($estado) {
            'APROBADA' => 'success',
            'RECHAZADA' => 'danger',
            default => 'warning'
          };
        ?>
        <div class="col-12 col-md-6 col-lg-4 mb-4">
          <div class="card ff-post shadow-sm border-0" style="border-radius:0.5rem; overflow:hidden;">
            <?php if ($tipo === 'IMAGEN' && $media): ?>
              <img src="<?= $media ?>" alt="<?= $titulo ?>" class="card-img-top" style="height:200px; object-fit:cover;">
            <?php elseif ($tipo === 'VIDEO' && $media): ?>
              <video src="<?= $media ?>" class="card-img-top" controls style="height:200px; object-fit:cover;"></video>
            <?php else: ?>
              <div class="bg-secondary d-flex align-items-center justify-content-center text-white" style="height:200px;">Sin imagen</div>
            <?php endif; ?>

            <div class="card-body p-2">
              <div class="d-flex justify-content-between align-items-start mb-1">
           <strong id="miTitulo"><?= $titulo ?></strong>
                <span class="badge bg-<?= $chipColor ?>"><?= $estado ?></span>
              </div>
              <div class="text-secondary mb-1" style="font-size:0.85rem; color:#b0b0b0;">
                <?= $sede ?> · <?= $fecha ?>
              </div>
              <span class="ff-chip text-uppercase" style="font-size:0.75rem;"><?= $categoria ?></span>
              <div class="d-flex gap-1 mt-2">
                <button class="btn btn-outline-light btn-sm">👍 <?= $likes ?></button>
                <a href="<?= $BASE ?>/detalle-publicacion.php?id=<?= $id ?>" class="btn btn-login btn-sm">Comentar</a>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
