<?php
declare(strict_types=1);
require_once __DIR__ . '/config/session_init.php';

$BASE = '/4everFootball';

// Detectar si hay sesión activa
$user = $_SESSION['user'] ?? null;
$isLogged = !empty($user);
$isAdmin = $isLogged && ($user['rol'] ?? '') === 'ADMIN';
?>


<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Inicio | 4everFootball</title>

  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  
  <link rel="stylesheet" href="<?= $BASE ?>/css/index.css?v=37" />
</head>
<body class="ff-bg">

  <!-- ===== HEADER ===== -->
  <header id="siteHeader" class="ff-header sticky-top">
    <div class="container d-flex align-items-center gap-3 py-2">
      <a href="<?= $BASE ?>/index.php" class="d-flex align-items-center gap-2 text-decoration-none">
        <img src="<?= $BASE ?>/img/logo.svg" alt="4everFootball" style="height:34px" />
      </a>

      <form id="headerSearch" class="ms-auto me-auto w-50 d-flex" role="search" novalidate>
        <div class="input-group ff-search w-100">
          <span class="input-group-text">🔎</span>
          <input id="qHeader" type="search" class="form-control" placeholder="Buscar en 4everFootball…" />
        </div>
      </form>

      <nav class="d-flex align-items-center gap-2">
       <!-- <button id="publishBtn" class="btn btn-register" type="button">Publicar</button>-->

        <div class="ff-profile position-relative">
          <button id="profileBtn" class="ff-avatar-btn" type="button"
                  aria-haspopup="true" aria-expanded="false"
                  aria-controls="profileMenu" title="Cuenta">
            <img src="<?= $BASE ?>/img/icon_iniciarsesion.png?v=1"
                 alt="Perfil" class="ff-avatar-img" width="36" height="36"
                 decoding="async" loading="lazy"
                 onerror="this.style.visibility='hidden';this.parentElement.classList.add('ff-avatar-fallback');" />
          </button>
          <div id="profileMenu" class="ff-dropdown" role="menu" aria-labelledby="profileBtn">
  <?php if ($isLogged): ?>
    <div class="px-3 py-2 small text-white">
      <strong><?= htmlspecialchars($user['nombre']) ?></strong><br>
      <?= htmlspecialchars($user['email']) ?>
    </div>
    <hr class="m-1">
    <a href="<?= $BASE ?>/perfil.php" class="dropdown-item">Mi perfil</a>
    <a href="<?= $BASE ?>/mis-posts.php" class="dropdown-item">Mis publicaciones</a>
    <?php if ($isAdmin): ?>
      <a href="<?= $BASE ?>/admin-aprobaciones.php" class="dropdown-item">Panel de aprobaciones</a>
    <?php endif; ?>
    <a href="<?= $BASE ?>/api/logout.php" class="dropdown-item text-danger">Cerrar sesión</a>
  <?php else: ?>
    <a href="<?= $BASE ?>/login.php" class="dropdown-item">Iniciar sesión</a>
    <a href="<?= $BASE ?>/register.php" class="dropdown-item">Crear cuenta</a>
  <?php endif; ?>
</div>

        </div>
      </nav>
    </div>
  </header>

  
  <main class="container pb-5">
    <div class="ff-shell">

      
      <aside class="ff-leftnav">
        <nav class="ff-leftnav-menu">
          <ul class="list-unstyled m-0">
            <li><a class="ff-leftnav-link" href="<?= $BASE ?>/index.php">Inicio</a></li>
            <li><a class="ff-leftnav-link" href="#" data-sort="likes">Popular</a></li>
          </ul>

          <div class="mt-3 small text-secondary px-2">TEMAS</div>
          <ul class="list-unstyled m-0 mt-1">
            <li><a class="ff-leftnav-link" href="#" data-cat="jugadas">Jugadas históricas</a></li>
            <li><a class="ff-leftnav-link" href="#" data-cat="entrevistas">Entrevistas</a></li>
            <li><a class="ff-leftnav-link" href="#" data-cat="sedes">Sedes y estadios</a></li>
            <li><a class="ff-leftnav-link" href="#" data-q="final">Finales míticas</a></li>
            <li><a class="ff-leftnav-link" href="#" data-q="gol">Goles icónicos</a></li>
          </ul>
        </nav>
      </aside>

      <!-- Columna CENTRAL (Feed) -->
      <section class="ff-feed">
        <section class="glass-card p-3 p-md-4 my-3">
          <h1 class="ff-title mb-3">Noticias y jugadas de Mundiales</h1>

          <div id="listMessage" class="small mb-3" role="alert" aria-live="polite"></div>

          <form id="filterForm" class="row g-2 align-items-end" novalidate>
            <div class="col-12 col-md-6">
              <label for="cat" class="form-label">Categoría</label>
              <select id="cat" name="cat" class="form-select">
                <option value="">Todas</option>
                <option value="jugadas">Jugadas</option>
                <option value="entrevistas">Entrevistas</option>
                <option value="estadisticas">Estadísticas</option>
                <option value="sedes">Sedes</option>
              </select>
            </div>

            <div class="col-12 col-md-6">
              <label for="orden" class="form-label">Ordenar por</label>
              <select id="orden" name="orden" class="form-select">
                <option value="reciente">Más reciente</option>
                <option value="likes">Más likes</option>
                <option value="comentarios">Más comentadas</option>
              </select>
            </div>

            <div class="col-12 d-flex gap-2 justify-content-end">
              <button class="btn btn-login" type="submit">Aplicar filtros</button>
            </div>
          </form>
        </section>

        <!-- Feed -->
        <section aria-label="Resultados">
          <div id="cards" class="d-flex flex-column gap-4"></div>
          <div id="empty" class="glass-card p-4 mt-3 d-none">
            <p class="mb-1">No encontramos publicaciones con esos filtros.</p>
            <small class="text-secondary">Prueba quitando algún filtro o busca otro término.</small>
          </div>
        </section>
      </section>

      <!-- Columna DERECHA (Sedes) -->
      <aside class="ff-right">
        <div class="ff-group-card p-3 my-3">
          <h2 class="m-0" style="font-size:1rem;">Grupos que puedes seguir</h2>
          <div id="gruposRight" class="d-flex flex-column gap-1 mt-2"></div>
          <div class="text-secondary small mt-2">Sugerencias basadas en sedes populares.</div>
        </div>
      </aside>

    </div>

    
    <div class="modal fade" id="createCategoryModal" tabindex="-1" aria-labelledby="createCategoryLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="createCategoryLabel">Crear nueva categoría</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
          </div>
          <div class="modal-body">
            <form id="formCreateCategory">
              <div class="mb-3">
                <label for="categoryName" class="form-label">Nombre de la categoría</label>
                <input type="text" class="form-control" id="categoryName" required>
              </div>
              <div class="mb-3">
                <label for="categorySlug" class="form-label">Slug</label>
                <input type="text" class="form-control" id="categorySlug" required>
              </div>
            </form>
            <div id="createCategoryMsg" class="text-danger small"></div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="button" class="btn btn-primary" id="saveCategoryBtn">Guardar</button>
          </div>
        </div>
      </div>
    </div>

  </main>

  
  <script>
const BASE = '<?= $BASE ?>';

/* ======================================================
   ELEMENTOS GLOBALES
====================================================== */
const el = {
  cards: document.getElementById('cards'),
  empty: document.getElementById('empty'),
  msg: document.getElementById('listMessage'),
  profileBtn: document.getElementById('profileBtn'),
  profileMenu: document.getElementById('profileMenu'),
  gruposRight: document.getElementById('gruposRight')
};

/* ======================================================
   SESIÓN
====================================================== */
async function fetchUser(){
  try {
    const res = await fetch(`${BASE}/api/get_user.php`);
    const user = await res.json();
    if (user) localStorage.setItem('ff_user', JSON.stringify(user));
    else localStorage.removeItem('ff_user');
    buildProfileMenu();
  } catch {
    buildProfileMenu();
  }
}

function getUser(){
  try { return JSON.parse(localStorage.getItem('ff_user')); }
  catch { return null; }
}

/* ======================================================
   MENÚ DE PERFIL
====================================================== */
function buildProfileMenu(){
  const u = getUser();
  let html = '';

  if (!u){
    html += `<a class="ff-dropdown-item" href="${BASE}/login.php">Iniciar sesión</a>`;
    html += `<a class="ff-dropdown-item" href="${BASE}/register.php">Crear cuenta</a>`;
  } else {
    html += `<div class="ff-dropdown-item ff-user-greet">Hola, ${u.nombre}</div>`;
    html += `<a class="ff-dropdown-item" href="${BASE}/perfil.php">Mi perfil</a>`;
    html += `<a class="ff-dropdown-item" href="${BASE}/mispublicaciones.php">Mis posts</a>`;

    if (u.rol === 'ADMIN'){
      html += `<a class="ff-dropdown-item" href="${BASE}/admin-aprobaciones.php">Panel de aprobaciones</a>`;
      html += `<a class="ff-dropdown-item" href="${BASE}/admin-usuarios.php">Administrar Usuarios</a>`;
      html += `<a class="ff-dropdown-item" href="${BASE}/pagina.php">Crear comunidad</a>`;
       html += `<a href="#" class="ff-dropdown-item" id="btnCreateCategory">Crear categoría</a>`;
    }

    html += `<button class="ff-dropdown-item logout text-start" id="logoutBtn">Cerrar sesión</button>`;
  }

  el.profileMenu.innerHTML = html;
  document.getElementById('logoutBtn')?.addEventListener('click', logout);
}
// Listener para abrir el modal de categoría
document.addEventListener('click', (e)=>{
  if (e.target.id === "btnCreateCategory") {
    e.preventDefault();

    const modalEl = document.getElementById('createCategoryModal');
    const modal = new bootstrap.Modal(modalEl);
    modal.show();
  }
});

// Abrir/cerrar dropdown
el.profileBtn?.addEventListener('click', () => {
  const open = el.profileMenu.classList.toggle('show');
  el.profileBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
});
document.addEventListener('click', (e)=>{
  if (!el.profileMenu.contains(e.target) && !el.profileBtn.contains(e.target)){
    el.profileMenu.classList.remove('show');
  }
});

/* ======================================================
   FEED PERSONALIZADO + LAZY LOADING
====================================================== */
let offset = 0;
let cargando = false;
let usuarioSigue = null;

// Cargar primeras publicaciones
cargarFeed();

async function cargarFeed(){
  if (cargando) return;
  cargando = true;

  el.msg.textContent = "Cargando publicaciones...";

  try {
    const res = await fetch(`${BASE}/api/publicaciones_listar_seguidos.php?offset=${offset}`);

    const json = await res.json();

    if (json.seguido === false){
      el.cards.innerHTML = `
        <div class="glass-card p-4 text-center">
          <h5 class="mb-2">Aún no sigues ninguna sede o comunidad</h5>
          <p class="text-secondary mb-0">Sigue una para comenzar a ver publicaciones.</p>
        </div>
      `;
      el.msg.textContent = "";
      return;
    }

    usuarioSigue = true;

    renderFeed(json.publicaciones);

    offset += 10;
    cargando = false;
    el.msg.textContent = "";

  } catch (e){
    el.msg.textContent = "Error de conexión con el servidor.";
    console.error(e);
  }
}

function renderFeed(list){
  if (!list?.length){
    if (offset === 0){
      el.cards.innerHTML = `
        <div class="glass-card p-4 text-center">
          <p class="mb-0">No encontramos publicaciones, Sigue a un grupo.</p>
        </div>
      `;
    }
    return;
  }

  list.forEach(p => {
    el.cards.innerHTML += renderCard(p);
  });
}

function renderCard(p){
  return `
    <article class="ff-post glass-card p-3">

      <!-- ENCABEZADO DEL GRUPO -->
      <div class="d-flex align-items-center gap-2 mb-2">
        <img src="${p.sede_logo || BASE + '/img/default_logo.png'}"
             width="32" height="32"
             class="rounded-circle"
             style="background:#111;">
        <a href="${BASE}/sede.php?slug=${p.sede_slug}"
           class="text-decoration-none fw-bold text-white">
           ${p.sede_nombre}
        </a>
        <small class="text-secondary ms-auto">
          ${p.categoria} · ${new Date(p.creada_en).toLocaleDateString()}
        </small>
      </div>

      <!-- TÍTULO -->
      <h5 class="mt-1">${p.titulo}</h5>

      <!-- MEDIA -->
      ${p.tipo_media === "VIDEO"
        ? `<video class="w-100 mt-2" src="${p.media_url}" controls></video>`
        : `<img class="w-100 mt-2" src="${p.media_url}" alt="imagen" onerror="this.style.display='none'">`
      }

      <!-- DESCRIPCIÓN -->
      <p class="mt-2">${p.descripcion || ""}</p>

      <!-- ACCIONES -->
      <div class="d-flex gap-2">
        <button class="btn btn-sm btn-outline-light">
          👍 Like
        </button>
        <a href="${BASE}/detalle-publicacion.php?id=${p.publicacion_id}" 
           class="btn btn-sm btn-login">
           💬 Comentar
        </a>
      </div>
    </article>
  `;
}




/* ======================================================
   SCROLL INFINITO
====================================================== */
window.addEventListener('scroll', () => {
  if (!usuarioSigue) return;

  const cercaDelFinal = window.innerHeight + window.scrollY >= document.body.offsetHeight - 600;

  if (cercaDelFinal) cargarFeed();
});

/* ======================================================
   GRUPOS (lado derecho)
====================================================== */
loadGroups();

async function loadGroups() {
  try {
    const res = await fetch(`${BASE}/api/get_grupos.php`);
    const json = await res.json();

    if (!json.ok){
      el.gruposRight.innerHTML = "<small class='text-secondary'>Sin comunidades.</small>";
      return;
    }

    renderGroups(json.data);

  } catch {
    el.gruposRight.innerHTML = "<small class='text-secondary'>Error al cargar.</small>";
  }
}

function renderGroups(grupos){
  if (!grupos.length){
    el.gruposRight.innerHTML = "<small class='text-secondary'>No hay comunidades creadas.</small>";
    return;
  }

  el.gruposRight.innerHTML = grupos.map(g => `
    <div class="ff-group-item d-flex justify-content-between align-items-center">
      <a href="${BASE}/sede.php?slug=${g.slug}" class="d-flex align-items-center gap-2 text-decoration-none">
        <img src="${g.logo || BASE + '/img/default_logo.png'}"
             class="rounded-circle" width="28" height="28">
        <span>${g.nombre}</span>
      </a>
      <button class="btn btn-sm btn-outline-light btn-seguir" data-id="${g.id}">
        Seguir
      </button>
    </div>
  `).join('');

  // Verificar cuales están seguidos
  grupos.forEach(g => marcarSiSigue(g.id));
}

async function marcarSiSigue(id){
  const res = await fetch(`${BASE}/api/sigue.php?id=${id}`);
  const json = await res.json();

  if (json.sigue){
    const btn = document.querySelector(`button[data-id="${id}"]`);
    if (btn){
      btn.classList.add('siguiendo');
      btn.textContent = 'Siguiendo';
    }
  }
}

/* ======================================================
   LOGOUT
====================================================== */
async function logout(){
  await fetch(`${BASE}/api/logout.php`, { method: 'POST' });
  localStorage.removeItem('ff_user');
  location.href = `${BASE}/index.php`;
}

/* ======================================================
   INICIALIZACIÓN
====================================================== */
fetchUser();

/* ======================================================
   BOTONES SEGUIR / SIGUIENDO
====================================================== */
document.addEventListener('click', async (e) => {
  if (!e.target.matches('.btn-seguir')) return;

  const btn = e.target;
  const mundial_id = Number(btn.dataset.id);

  if (!mundial_id) return;

  // Si ya sigue → unfollow
  if (btn.classList.contains('siguiendo')) {
    const res = await fetch(`${BASE}/api/unfollow.php`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ mundial_id })
    });
    const json = await res.json();

    if (json.ok) {
      btn.classList.remove('siguiendo');
      btn.textContent = 'Seguir';
      offset = 0;
      el.cards.innerHTML = "";
      cargarFeed();
    }
    return;
  }

  // Si NO sigue → follow
  const res = await fetch(`${BASE}/api/seguir.php`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ mundial_id })
  });
  const json = await res.json();

  if (json.ok) {
    btn.classList.add('siguiendo');
    btn.textContent = 'Siguiendo';
    offset = 0;
    el.cards.innerHTML = "";
    cargarFeed();
  }
});
</script>


</body>
</html>
