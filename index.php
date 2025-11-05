<?php
  
  $BASE = '/4everFootball';
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
          <div id="profileMenu" class="ff-dropdown" role="menu" aria-labelledby="profileBtn"></div>
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

// ======== ELEMENTOS GLOBALES ========
const el = {
  cards: document.getElementById('cards'),
  empty: document.getElementById('empty'),
  msg: document.getElementById('listMessage'),
  profileBtn: document.getElementById('profileBtn'),
  profileMenu: document.getElementById('profileMenu'),
  gruposRight: document.getElementById('gruposRight'),
  formCreateCategory: document.getElementById('formCreateCategory'),
  saveCategoryBtn: document.getElementById('saveCategoryBtn'),
  createCategoryMsg: document.getElementById('createCategoryMsg')
};

// ======== SESIÓN ========
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

function getUser(){ try{ return JSON.parse(localStorage.getItem('ff_user')); }catch{ return null; } }
function logout(){ localStorage.removeItem('ff_user'); location.href = `${BASE}/index.php`; }

// ======== MENÚ PERFIL ========
function buildProfileMenu(){
  const u = getUser();
  let html = '';

  if (!u){
    html += `<a class="ff-dropdown-item" href="${BASE}/login.php">Iniciar sesión</a>`;
    html += `<a class="ff-dropdown-item" href="${BASE}/register.php">Crear cuenta</a>`;
  } else {
    html += `<div class="ff-dropdown-item ff-user-greet">Hola, ${u.nombre || u.name || u.email}</div>`;
    html += `<a class="ff-dropdown-item" href="${BASE}/perfil.php">Mi perfil</a>`;
    html += `<a class="ff-dropdown-item" href="${BASE}/mispublicaciones.php">Mis posts</a>`;

    if (u.isAdmin || u.rol_id == 1) {
      html += `<a class="ff-dropdown-item" href="${BASE}/admin-usuarios.php">Administrar Usuarios</a>`;
      html += `<a class="ff-dropdown-item" href="${BASE}/admin-aprobaciones.php">Aprobar Publicacion</a>`;
      html += `<a class="ff-dropdown-item" href="${BASE}/pagina.php">Crear comunidad</a>`;
      html += `<a href="#" class="ff-dropdown-item" id="btnCreateCategory">Crear categoría</a>`;
    }

    html += `<button class="ff-dropdown-item logout text-start" id="logoutBtn" type="button">Cerrar sesión</button>`;
  }

  el.profileMenu.innerHTML = html;

  document.getElementById('logoutBtn')?.addEventListener('click', logout);

  const btnCreate = document.getElementById('btnCreateCategory');
  if (btnCreate){
    btnCreate.addEventListener('click', (e)=>{
      e.preventDefault();
      const user = getUser();
      if (!user || !(user.isAdmin || user.rol_id == 1)) {
        alert('Solo administradores pueden crear categorías.');
        return;
      }
      const modalEl = document.getElementById('createCategoryModal');
      const modal = new bootstrap.Modal(modalEl);
      modal.show();
    });
  }
}

// Dropdown toggle
el.profileBtn.addEventListener('click', () => {
  const open = el.profileMenu.classList.toggle('show');
  el.profileBtn.setAttribute('aria-expanded', String(open));
});
document.addEventListener('click', (e) => {
  if (!el.profileMenu.contains(e.target) && !el.profileBtn.contains(e.target)) {
    el.profileMenu.classList.remove('show');
    el.profileBtn.setAttribute('aria-expanded', 'false');
  }
});

// ======== GUARDAR NUEVA CATEGORÍA ========
el.saveCategoryBtn?.addEventListener('click', async ()=>{
  const nombre = document.getElementById('categoryName').value.trim();
  const slug = document.getElementById('categorySlug').value.trim();

  if (!nombre || !slug) {
    el.createCategoryMsg.textContent = 'Todos los campos son obligatorios.';
    return;
  }

  el.createCategoryMsg.textContent = 'Guardando...';

  try {
    const res = await fetch(`${BASE}/api/create_category.php`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ nombre, slug })
    });
    const json = await res.json();

    if (json.ok) {
      el.createCategoryMsg.textContent = '✅ Categoría creada correctamente';
      setTimeout(()=> location.reload(), 1000);
    } else {
      el.createCategoryMsg.textContent = '❌ ' + (json.error || 'Error al crear categoría');
    }
  } catch {
    el.createCategoryMsg.textContent = '❌ Error de conexión con el servidor';
  }
});

// ======== FEED ========
async function loadFeed(){
  el.msg.textContent = 'Cargando publicaciones...';
  try {
    const res = await fetch(`${BASE}/api/feed.php`);
    const json = await res.json();
    if (json.ok) renderFeed(json.data);
    else el.msg.textContent = 'No se pudieron cargar las publicaciones.';
  } catch {
    el.msg.textContent = 'Error de conexión con el servidor.';
  }
}

function renderFeed(list){
  el.cards.innerHTML = list.map(row => `
    <article class="ff-post">
      <div class="ff-post-header">
        <h5>${row.titulo}</h5>
        <small>${row.categoria} · ${new Date(row.fecha).toLocaleDateString()}</small>
      </div>
      ${row.mediaType === 'video'
        ? `<video src="${row.src}" ${row.poster ? `poster="${row.poster}"` : ''} controls playsinline></video>`
        : `<img src="${row.src}" alt="${row.titulo}" onerror="this.remove()">`}
    </article>
  `).join('');
  el.empty.classList.toggle('d-none', list.length > 0);
  el.msg.textContent = '';
}

// ======== GRUPOS (dinámico desde BD) ========
async function loadGroups() {
  try {
    const res = await fetch(`${BASE}/api/get_grupos.php`);
    const json = await res.json();
    if (json.ok) renderGroups(json.data);
    else el.gruposRight.innerHTML = `<small class="text-secondary">Sin comunidades disponibles aún.</small>`;
  } catch {
    el.gruposRight.innerHTML = `<small class="text-secondary">Error al cargar comunidades.</small>`;
  }
}

function renderGroups(grupos) {
  if (!grupos.length) {
    el.gruposRight.innerHTML = `<small class="text-secondary">Sin comunidades creadas todavía.</small>`;
    return;
  }

  el.gruposRight.innerHTML = grupos.map(g => `
    <div class="ff-group-item d-flex justify-content-between align-items-center">
      <a class="d-flex align-items-center gap-2 text-decoration-none" href="${BASE}/sede.php?slug=${g.slug}">
        <img src="${g.logo}" alt="${g.nombre}" style="width:28px;height:28px;border-radius:50%;object-fit:cover">
        <span>${g.nombre}</span>
      </a>
      <button class="btn btn-sm btn-outline-light">Seguir</button>
    </div>
  `).join('');
}


// ======== INICIALIZACIÓN ========
fetchUser();
loadFeed();
loadGroups();

</script>

</body>
</html>
