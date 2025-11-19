<?php
session_start();
require_once(__DIR__ . '/conexion.php');
$BASE = '/4everFootball';

// --- Obtener ID ---
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
  echo "<h2>⚠️ Publicación no encontrada.</h2>";
  exit;
}

// Incrementar contador de vistas
$conexion->query("UPDATE publicacion SET views = views + 1 WHERE publicacion_id = $id");


// --- Cargar publicación ---
$stmt = $conexion->prepare("
  SELECT 
    p.*,
    u.nombre_completo AS autor,
    c.nombre AS categoria,
    m.nombre_comunidad AS sede_nombre,
    m.slug AS sede_slug,
    m.logo_url AS sede_logo,
    m.portada_url AS sede_portada,
    m.descripcion AS sede_descripcion,
    m.sede AS sede_pais
  FROM publicacion p
  JOIN usuarios u ON u.usuario_id = p.usuario_id
  JOIN categoria c ON c.categoria_id = p.categoria_id
  JOIN mundial m ON m.mundial_id = p.mundial_id
  WHERE p.publicacion_id = ?
  LIMIT 1
");

$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$pub = $result->fetch_assoc();
$stmt->close();

if (!$pub) {
  echo "<h2>❌ Publicación no encontrada.</h2>";
  exit;
}

// --- Variables limpias ---
$titulo      = htmlspecialchars($pub['titulo']);
$descripcion = nl2br(htmlspecialchars($pub['descripcion']));
$categoria   = htmlspecialchars($pub['categoria']);
$autor       = htmlspecialchars($pub['autor']);
$fecha       = date('d/m/Y', strtotime($pub['creada_en']));
$estado      = htmlspecialchars($pub['estatus']);
$media       = htmlspecialchars($pub['media_url']);
$tipo_media  = $pub['tipo_media'];
$sede_nombre = htmlspecialchars($pub['sede_nombre']);
$sede_slug   = strtolower(str_replace(' ', '-', $sede_nombre));
$stmtLikes = $conexion->prepare("SELECT fn_total_reacciones_publicacion(?) AS total_likes");
$stmtLikes->bind_param("i", $id);
$stmtLikes->execute();
$likesRow = $stmtLikes->get_result()->fetch_assoc();
$likes = (int)$likesRow['total_likes'];
$stmtLikes->close();

$sede_slug   = htmlspecialchars($pub['sede_slug']);
$sede_logo   = htmlspecialchars($pub['sede_logo']);
$sede_portada = htmlspecialchars($pub['sede_portada']);
$sede_pais    = htmlspecialchars($pub['sede_pais']);
$sede_desc    = htmlspecialchars($pub['sede_descripcion']);

?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= $titulo ?> | 4everFootball</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet" href="<?= $BASE ?>/css/index.css?v=<?= time() ?>">
</head>
<body class="ff-bg">

<header id="siteHeader" class="ff-header sticky-top">
  <div class="container d-flex align-items-center gap-3 py-2">
    <a href="<?= $BASE ?>/index.php" class="d-flex align-items-center gap-2 text-decoration-none">
      <img src="<?= $BASE ?>/img/logo.svg" alt="4everFootball" style="height:34px">
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
      
    </nav>
  </div>
</header>

<main class="container py-4 pb-5">

  <article id="post" class="ff-post mb-4">
    <div class="ff-post-header">
      <div class="ff-post-meta">
        <div class="ff-avatar">
  <img src="<?= $sede_logo ?>" width="42" height="42" class="rounded-circle" alt="<?= $sede_nombre ?>">
</div>

        <div>
          <div class="d-flex align-items-center gap-2">
            <strong id="postTitle"><?= $titulo ?></strong>
            <span id="postState" class="ff-chip"><?= ucfirst(strtolower($estado)) ?></span>
          </div>
          <div class="ff-post-sub">
            <a class="ff-group-link-mini" href="<?= $BASE ?>/sede.php?slug=<?= htmlspecialchars($sede_slug) ?>">
    <?= $sede_nombre ?>
</a>

            · <?= $fecha ?> · <?= $autor ?>
          </div>
        </div>
      </div>
      <span id="postCat" class="ff-chip text-uppercase"><?= $categoria ?></span>
    </div>

    <div id="postMedia" class="ff-post-media mt-3">
      <?php if ($tipo_media === 'IMAGEN' && $media): ?>
        <img src="<?= $media ?>" alt="<?= $titulo ?>" class="img-fluid rounded">
      <?php elseif ($tipo_media === 'VIDEO' && $media): ?>
        <video src="<?= $media ?>" controls class="w-100 rounded"></video>
      <?php endif; ?>
    </div>

    <div class="ff-actions mt-3">
      <button id="likeBtn" class="btn btn-outline-light">👍 <span id="likeCount"><?= $likes ?></span></button>
     <a class="btn btn-login" href="#comments">Comentar</a>
    </div>
  </article>

  <section class="glass-card p-3 p-md-4 mb-4">
    <h2 class="h5 text-white mb-2">Descripción</h2>
    <p class="mb-0 text-secondary"><?= $descripcion ?></p>
  </section>

  <section id="comments" class="glass-card p-3 p-md-4">
    <h2 class="h5 text-white mb-3">Comentarios</h2>

    <div id="commentList" class="d-flex flex-column gap-3 mb-3"></div>

    <div id="cmtMsg" class="small mb-3" role="alert" aria-live="polite"></div>

    <form id="cmtForm" class="row g-2" novalidate>
      <div class="col-12">
        <label for="cmtText" class="form-label">Escribe un comentario</label>
        <textarea id="cmtText" class="form-control" rows="3" required minlength="2" maxlength="500" placeholder="Sé respetuoso y aporta al tema…"></textarea>
        <div class="invalid-feedback">El comentario debe tener al menos 2 caracteres.</div>
      </div>
      <div class="col-12 d-flex justify-content-end">
        <button type="submit" class="btn btn-login">Publicar comentario</button>
      </div>
    </form>
  </section>
</main>


<script>
const BASE = '<?= $BASE ?>';

// Obtén el usuario actual (almacenado al iniciar sesión)
const user = JSON.parse(localStorage.getItem('ff_user') || 'null');

// Obtén el ID de la publicación desde la URL (?id=9)
const params = new URLSearchParams(window.location.search);
const publicacion_id = parseInt(params.get('id'));

// Contenedor principal de comentarios
const list = document.getElementById('commentList');
const form = document.getElementById('cmtForm');
const textarea = document.getElementById('cmtText');

// 🧩 --- 1. FUNCIÓN PARA CARGAR COMENTARIOS DESDE EL SERVIDOR ---
async function cargarComentarios() {
  try {
    const res = await fetch(`${BASE}/api/comentarios_listar.php?publicacion_id=${publicacion_id}`);
    const data = await res.json();
    if (data.ok) {
      renderComentarios(data.comentarios);
    } else {
      list.innerHTML = `<p class='text-secondary'>No hay comentarios.</p>`;
    }
  } catch (err) {
    console.error(err);
    list.innerHTML = `<p class='text-danger'>Error al cargar comentarios.</p>`;
  }
}

// 🧩 --- 2. FUNCIÓN PARA RENDERIZAR TODOS LOS COMENTARIOS ---
function renderComentarios(comentarios) {
  list.innerHTML = comentarios.map(renderComentarioHTML).join('');
}

// 🧩 --- 3. FUNCIÓN PARA CONSTRUIR EL HTML DE CADA COMENTARIO ---
function renderComentarioHTML(c) {
  const liked = c.likedByMe ?? false;
  const esAdmin = user?.rol === 'ADMIN';

  const eliminado = c.eliminado == 1;

  // Si el comentario está eliminado, reemplazamos el texto
  const contenido = eliminado
    ? `<i class="text-secondary small">Este comentario fue eliminado por un administrador.</i>`
    : escapeHTML(c.contenido);

  // Foto de perfil (si el usuario tiene o un ícono genérico)
  const foto = c.foto
    ? `<img src="${c.foto}" width="36" height="36" class="rounded-circle">`
    : `<img src="${BASE}/img/icon_iniciarsesion.png" width="36" height="36" class="rounded-circle">`;

  // Hijo (respuesta) recursivo
  const hijos = c.hijos && c.hijos.length
    ? `<div class="mt-3 ps-4 border-start border-secondary">${c.hijos.map(renderComentarioHTML).join('')}</div>`
    : '';

  return `
  <div class="cmt mb-3" data-id="${c.comentario_id}">
    <div class="d-flex gap-2">
      ${foto}
      <div class="flex-grow-1">
        <strong>${escapeHTML(c.autor)}</strong>
        <div class="small text-secondary">${new Date(c.creado_en).toLocaleDateString()}</div>
        <p class="mb-2">${contenido}</p>

        <div class="d-flex gap-2 align-items-center">
          <button class="btn btn-sm ${liked ? 'btn-login' : 'btn-outline-light'} c-like" data-id="${c.comentario_id}">
            👍 <span>${c.likes || 0}</span>
          </button>
          <button class="btn btn-sm btn-outline-light c-reply" data-id="${c.comentario_id}">Responder</button>
          ${esAdmin ? `
            <div class="dropdown d-inline">
              <button class="btn btn-sm btn-outline-danger dropdown-toggle" data-bs-toggle="dropdown">⋮</button>
              <ul class="dropdown-menu dropdown-menu-dark">
                <li><button class="dropdown-item c-del" data-id="${c.comentario_id}">Eliminar</button></li>
              </ul>
            </div>` : ''}
        </div>

        <!-- Caja de respuesta (oculta al inicio) -->
        <div class="mt-2 d-none c-replybox" id="replybox-${c.comentario_id}">
          <textarea class="form-control form-control-sm c-replytext" rows="2" maxlength="300" placeholder="Tu respuesta…"></textarea>
          <div class="d-flex justify-content-end gap-2 mt-2">
            <button class="btn btn-outline-light btn-sm c-cancel-reply" data-id="${c.comentario_id}">Cancelar</button>
            <button class="btn btn-login btn-sm c-send-reply" data-id="${c.comentario_id}">Responder</button>
          </div>
        </div>

        ${hijos}
      </div>
    </div>
  </div>`;
}

// 🧩 --- 4. ESCAPAR TEXTO PARA EVITAR INYECCIONES ---
function escapeHTML(str) {
  return String(str).replace(/[&<>"']/g, s => ({
    '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'
  }[s]));
}

// 🧩 --- 5. PUBLICAR NUEVO COMENTARIO ---
form.addEventListener('submit', async e => {
  e.preventDefault();
  const contenido = textarea.value.trim();
  if (!contenido) return alert('El comentario no puede estar vacío.');

  if (!user) return alert('Debes iniciar sesión para comentar.');

  const body = {
    usuario_id: user.id,
    publicacion_id,
    contenido
  };

  const res = await fetch(`${BASE}/api/comentario_add.php`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(body)
  });

  const data = await res.json();
  if (data.ok) {
    textarea.value = '';
    cargarComentarios(); // recarga la lista
  } else {
    alert('❌ Error: ' + data.error);
  }
});

// 🧩 --- 6. MANEJO DE BOTONES (LIKE, RESPONDER, ELIMINAR) ---
list.addEventListener('click', async e => {
  const btn = e.target.closest('button');
  if (!btn) return;
  const id = parseInt(btn.dataset.id);

  // --- Dar like ---
  if (btn.classList.contains('c-like')) {
    if (!user) return alert('Debes iniciar sesión para dar like.');
    const fd = new FormData();
    fd.append('comentario_id', id);
    fd.append('usuario_id', user.id);
    const res = await fetch(`${BASE}/api/comentario_like.php`, { method: 'POST', body: fd });
    const data = await res.json();
    if (data.ok) cargarComentarios();
  }

  // --- Mostrar caja de respuesta ---
  if (btn.classList.contains('c-reply')) {
    if (!user) return alert('Debes iniciar sesión para responder.');
    document.getElementById(`replybox-${id}`)?.classList.toggle('d-none');
  }

  // --- Cancelar respuesta ---
  if (btn.classList.contains('c-cancel-reply')) {
    document.getElementById(`replybox-${id}`)?.classList.add('d-none');
  }

  // --- Enviar respuesta ---
  if (btn.classList.contains('c-send-reply')) {
    const box = document.getElementById(`replybox-${id}`);
    const txt = box.querySelector('.c-replytext').value.trim();
    if (!txt) return alert('No puedes enviar un comentario vacío.');

    const body = {
      usuario_id: user.id,
      publicacion_id,
      contenido: txt,
      comentario_padre_id: id
    };

    const res = await fetch(`${BASE}/api/comentario_add.php`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(body)
    });

    const data = await res.json();
    if (data.ok) cargarComentarios();
  }

  // --- Eliminar comentario (solo admin) ---
  if (btn.classList.contains('c-del')) {
    if (!confirm('¿Seguro que quieres eliminar este comentario?')) return;
    const fd = new FormData();
    fd.append('comentario_id', id);
    const res = await fetch(`${BASE}/api/comentario_eliminar.php`, { method: 'POST', body: fd });
    const data = await res.json();
    if (data.ok) cargarComentarios();
  }
});

// 🧩 --- 7. CARGA INICIAL ---
cargarComentarios();

const likeBtn = document.getElementById('likeBtn');
const likeCount = document.getElementById('likeCount');

likeBtn?.addEventListener('click', async () => {
  if (!user) return alert('Debes iniciar sesión para dar like.');

  const fd = new FormData();
  fd.append('publicacion_id', publicacion_id);
  fd.append('usuario_id', user.id);

  try {
    const res = await fetch(`${BASE}/api/publicacion_like.php`, {
      method: "POST",
      body: fd
    });
    const data = await res.json();

    if (data.ok) {
      let count = parseInt(likeCount.textContent);

      if (data.accion === 'like') {
        likeCount.textContent = count + 1;
        likeBtn.classList.remove('btn-outline-light');
        likeBtn.classList.add('btn-login');
      } else {
        likeCount.textContent = count - 1;
        likeBtn.classList.add('btn-outline-light');
        likeBtn.classList.remove('btn-login');
      }
    }
  } catch (err) {
    console.error(err);
    alert('Error de conexión');
  }
});
</script>




</body>
</html>
