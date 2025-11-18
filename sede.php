<?php
session_start();
require_once(__DIR__ . '/conexion.php');

$BASE = '/4everFootball';

// Obtenemos el slug de la URL o un valor por defecto
$slug = $_GET['slug'] ?? '';

// Si no hay slug, mostramos error
if (!$slug) {
  echo "<h2>⚠️ No se especificó ninguna sede.</h2>";
  exit;
}

// Obtenemos el mundial por slug
$stmt = $conexion->prepare("SELECT * FROM mundial WHERE REPLACE(LOWER(nombre_comunidad), ' ', '-') = ?");
$stmt->bind_param("s", $slug);
$stmt->execute();
$result = $stmt->get_result();
$mundial = $result->fetch_assoc();

if (!$mundial) {
  echo "<h2>❌ Sede no encontrada.</h2>";
  exit;
}

$mundial_id = $mundial['mundial_id'];

// Obtenemos publicaciones reales
$pubStmt = $conexion->prepare("
  SELECT p.*, u.nombre_completo AS autor, c.nombre AS categoria
  FROM publicacion p
  JOIN usuarios u ON u.usuario_id = p.usuario_id
  JOIN categoria c ON c.categoria_id = p.categoria_id
  WHERE p.mundial_id = ? AND p.estatus = 'APROBADA'
  ORDER BY p.creada_en DESC
");
$pubStmt->bind_param("i", $mundial_id);
$pubStmt->execute();
$publicaciones = $pubStmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($mundial['nombre_comunidad']) ?> | 4everFootball</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="<?= $BASE ?>/css/index.css?v=<?= time() ?>">
</head>
<body class="ff-bg">

<header id="siteHeader" class="ff-header sticky-top">
  <div class="container d-flex align-items-center gap-3 py-2">
    <a href="<?= $BASE ?>/index.php" class="d-flex align-items-center gap-2 text-decoration-none">
      <img src="<?= $BASE ?>/img/logo.svg?v=<?= time() ?>" alt="4everFootball" style="height:34px">
    </a>
   <form id="headerSearch" class="ms-auto me-auto w-50" role="search" method="GET" action="<?= $BASE ?>/index.php">
  <div class="input-group ff-search w-100" 
       style="background:#2a2a2a; border-radius:2rem; overflow:hidden; border:1px solid #444;">
    <span class="input-group-text" 
          style="background:transparent; border:none; color:#bbb; padding-left:1rem;">
      🔎
    </span>
    <input
      id="qHeader"
      type="search"
      name="q"
      class="form-control"
      placeholder="Buscar en 4everFootball…"
      value="<?= htmlspecialchars($_GET['q'] ?? '') ?>"
      style="background:transparent; border:none; color:white; box-shadow:none;"
    >
  </div>
</form>

    <nav class="d-flex align-items-center gap-2">
      <?php if (isset($_SESSION['user'])): ?>
        <a class="btn btn-register" href="<?= $BASE ?>/crear-publicacion.php?mundial_id=<?= $mundial_id ?>">Publicar</a>
      <?php endif; ?>
      <div class="ff-profile position-relative">
        <button id="profileBtn" class="ff-avatar-btn" type="button">
          <img src="<?= $BASE ?>/img/icon_iniciarsesion.png?v=1" alt="Perfil" class="ff-avatar-img" width="36" height="36">
        </button>
      </div>
    </nav>
  </div>
</header>

<main class="container pb-5">

  <!-- Banner -->
  <section class="my-3 glass-card overflow-hidden">
    <div style="height:220px; background:url('<?= htmlspecialchars($mundial['portada_url']) ?>') center/cover; filter:brightness(.85)"></div>
    <div class="p-3 p-md-4 d-flex align-items-center gap-3">
      <img src="<?= htmlspecialchars($mundial['logo_url']) ?>" alt="<?= htmlspecialchars($mundial['nombre_comunidad']) ?>"
           width="72" height="72"
           style="border-radius:50%; background:#111; padding:6px; box-shadow:0 0 0 2px rgba(92,214,92,.7)">
      <div class="flex-grow-1">
        <h1 class="ff-title m-0"><?= htmlspecialchars($mundial['nombre_comunidad']) ?></h1>
        <small class="text-secondary">Página de sede · <?= htmlspecialchars($mundial['sede']) ?></small>
      </div>
    </div>
  </section>

  <!-- Descripción -->
  <section class="glass-card p-3 p-md-4 my-3">
    <p class="m-0"><?= nl2br(htmlspecialchars($mundial['descripcion'])) ?></p>
  </section>

  <!-- Publicaciones -->
  <section class="ff-feed">
    <?php if (empty($publicaciones)): ?>
      <div class="glass-card p-4 mt-3">
        <p class="mb-1">Aún no hay publicaciones para esta sede.</p>
        <small class="text-secondary">Vuelve más tarde.</small>
      </div>
    <?php else: ?>
      <?php foreach ($publicaciones as $p): ?>

    <?php
      $id = (int)$p['publicacion_id'];

      // Obtener likes reales desde la FUNCTION
      $stLikes = $conexion->prepare("SELECT fn_total_reacciones_publicacion(?) AS likes");
      $stLikes->bind_param('i', $id);
      $stLikes->execute();
      $rLikes = $stLikes->get_result()->fetch_assoc();
      $likes = (int)$rLikes['likes'];
      $stLikes->close();
    ?>

    <article class="glass-card p-3 p-md-4 my-3 position-relative">


          <!-- Cabecera -->
          <div class="d-flex justify-content-between align-items-start mb-2">
            <div>
              <strong class="d-block"><?= htmlspecialchars($p['titulo']) ?></strong>
              <small class="text-secondary">
                <?= htmlspecialchars($p['autor']) ?> · 
                <?= htmlspecialchars($p['categoria']) ?> · 
                <?= date('d/m/Y', strtotime($p['creada_en'])) ?>
              </small>
            </div>

            <?php if (!empty($_SESSION['user']) && $_SESSION['user']['rol'] === 'ADMIN'): ?>
              <!-- Menú admin con tres puntos -->
              <div class="dropdown">
                <button class="btn btn-sm btn-outline-light" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                  ⋮
                </button>
                <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end">
                  <li><button class="dropdown-item" onclick="cambiarEstado(<?= $p['publicacion_id'] ?>, 'PENDIENTE')">Marcar como pendiente</button></li>
                  <li><button class="dropdown-item" onclick="cambiarEstado(<?= $p['publicacion_id'] ?>, 'RECHAZADA')">Rechazar publicación</button></li>
                </ul>
              </div>
            <?php endif; ?>
          </div>

          <!-- Imagen o video -->
          <?php if ($p['tipo_media'] === 'IMAGEN' && $p['media_url']): ?>
            <img src="<?= htmlspecialchars($p['media_url']) ?>" alt="Imagen de publicación" class="img-fluid rounded mb-3">
          <?php elseif ($p['tipo_media'] === 'VIDEO' && $p['media_url']): ?>
            <video src="<?= htmlspecialchars($p['media_url']) ?>" controls class="w-100 rounded mb-3"></video>
          <?php endif; ?>

          <!-- Descripción -->
          <p><?= nl2br(htmlspecialchars($p['descripcion'])) ?></p>

          <!-- Acciones -->
          <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="d-flex gap-2">
              <button class="btn btn-sm btn-outline-light like-btn" data-id="<?= $p['publicacion_id'] ?>">
                👍 <span><?= $likes ?></span>
              </button>

              <a href="<?= $BASE ?>/detalle-publicacion.php?id=<?= $p['publicacion_id'] ?>" class="btn btn-sm btn-login">💬 Comentar</a>

            </div>
          </div>

        </article>
      <?php endforeach; ?>
    <?php endif; ?>
  </section>

</main>

<!-- Bootstrap Bundle (para dropdowns y modales) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Función para cambiar estado -->
<script>
async function cambiarEstado(id, estado) {
  if (!confirm(`¿Seguro que quieres marcar esta publicación como ${estado}?`)) return;
  try {
    const fd = new FormData();
    fd.append('id', id);
    fd.append('estado', estado);
    const res = await fetch('<?= $BASE ?>/api/publicacion_cambiar_estado.php', {
      method: 'POST',
      body: fd,
      credentials: 'include'
    });
    const data = await res.json();
    if (data.ok) {
      alert('✅ Estado actualizado correctamente');
      location.reload();
    } else {
      alert('❌ ' + (data.error || 'Error al actualizar'));
    }
  } catch (err) {
    console.error(err);
    alert('Error de conexión');
  }
}

/* 👉 ESTE ES EL CÓDIGO NUEVO PARA DAR LIKE */
document.querySelectorAll('.like-btn').forEach(btn => {
  btn.addEventListener('click', async () => {
    const id = btn.dataset.id;

    const fd = new FormData();
   fd.append('publicacion_id', id);
   fd.append('usuario_id', <?= $_SESSION['user']['id'] ?? '0' ?>);

    const res = await fetch('<?= $BASE ?>/api/publicacion_like.php', {
      method: 'POST',
      body: fd,
      credentials: 'include'
    });
    const data = await res.json();

    if (data.ok) {
      let num = parseInt(btn.querySelector('span').innerText);
      btn.querySelector('span').innerText = data.accion === 'like' ? num + 1 : num - 1;
    } else {
      alert(data.error || 'Error al dar like');
    }
  });
});

</script>


</body>
</html>
