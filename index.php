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


const DATA = [
  {
    id: 1,
    titulo: 'Golazo inaugural 2010',
    categoria: 'jugadas',
    sede: '2010',
    sedeNombre: 'Sudáfrica 2010',
    sedeSlug: 'sudafrica-2010',
    estado: 'aprobado',
    likes: 420,
    comentarios: 65,
    fecha: '2010-06-11',
    mediaType: 'image',
    src: `${BASE}/img/sample1.jpg`
  },
  {
    id: 2,
    titulo: 'Entrevista al crack 2014',
    categoria: 'entrevistas',
    sede: '2014',
    sedeNombre: 'Brasil 2014',
    sedeSlug: 'brasil-2014',
    estado: 'aprobado',
    likes: 120,
    comentarios: 10,
    fecha: '2014-07-10',
    mediaType: 'video',
    src: `${BASE}/img/sample2.mp4`,
    poster: `${BASE}/img/poster2.jpg`
  },
  {
    id: 3,
    titulo: 'Fiesta en Lusail 2022',
    categoria: 'sedes',
    sede: '2022',
    sedeNombre: 'Qatar 2022',
    sedeSlug: 'qatar-2022',
    estado: 'aprobado',
    likes: 980,
    comentarios: 112,
    fecha: '2022-12-18',
    mediaType: 'image',
    src: `${BASE}/img/sample4.jpg`
  },
  {
    id: 4,
    titulo: 'Final en Luzhnikí 2018',
    categoria: 'sedes',
    sede: '2018',
    sedeNombre: 'Rusia 2018',
    sedeSlug: 'rusia-2018',
    estado: 'aprobado',
    likes: 210,
    comentarios: 34,
    fecha: '2018-07-15',
    mediaType: 'image',
    src: `${BASE}/img/sample3.jpg`
  },
];

const GROUPS = [
  { slug:'qatar-2022',  nombre:'Qatar 2022',        img:`${BASE}/img/2022.png` },
  { slug:'rusia-2018',  nombre:'Rusia 2018',        img:`${BASE}/img/2018.png` },
  { slug:'brasil-2014', nombre:'Brasil 2014',       img:`${BASE}/img/2014.png` },
  { slug:'sudafrica-2010', nombre:'Sudáfrica 2010', img:`${BASE}/img/2010.png` },
  { slug:'alemania-2006',  nombre:'Alemania 2006',  img:`${BASE}/img/2006.png` },
  { slug:'corea-japon-2002', nombre:'Corea/Japón 2002', img:`${BASE}/img/2002.png` },
];


const GROUP_MAP = GROUPS.reduce((acc, g) => {
  acc[g.slug] = g;
  return acc;
}, {});

// Elements
const el = {
  header: document.getElementById('siteHeader'),
  profileBtn: document.getElementById('profileBtn'),
  profileMenu: document.getElementById('profileMenu'),
  cards: document.getElementById('cards'),
  empty: document.getElementById('empty'),
  form: document.getElementById('filterForm'),
  msg: document.getElementById('listMessage'),
  qHeader: document.getElementById('qHeader'),
  headerSearch: document.getElementById('headerSearch'),
  cat: document.getElementById('cat'),
  orden: document.getElementById('orden'),
  gruposRight: document.getElementById('gruposRight'),
  publishBtn: document.getElementById('publishBtn'),
};



// --- Sesión simulada ---
function getUser(){ try{ return JSON.parse(localStorage.getItem('ff_user')||'null'); }catch{ return null; } }
function setUser(u){ if (u) localStorage.setItem('ff_user', JSON.stringify(u)); else localStorage.removeItem('ff_user'); buildProfileMenu(); }
function logout(){ setUser(null); location.href = `${BASE}/index.php`; }

function requireAuth({title='Necesitas iniciar sesión', msg='Para continuar debes iniciar sesión o crear una cuenta.'}={}) {
  const u = getUser();
  if (u) return true;
  const url = new URL(`${BASE}/estado.php`, location.origin);
  url.searchParams.set('type','warning');
  url.searchParams.set('title', title);
  url.searchParams.set('msg', msg);
  url.searchParams.set('primary','Iniciar sesión:' + `${BASE}/login.php`);
  url.searchParams.set('secondary','Crear cuenta:' + `${BASE}/register.php`);
  location.href = url.toString();
  return false;
}

// --- Build Profile Menu ---
function buildProfileMenu(){
  const u = getUser();
  let html = '';

  if (!u){
    html += `<a class="ff-dropdown-item" href="${BASE}/login.php">Iniciar sesión</a>`;
    html += `<a class="ff-dropdown-item" href="${BASE}/register.php">Crear cuenta</a>`;
  } else {
    html += `<div class="ff-dropdown-item ff-user-greet">Hola, ${u.name || u.email}</div>`;

    // Accesos para cualquier usuario logueado
    html += `<a class="ff-dropdown-item" href="${BASE}/perfil.php">Mi perfil</a>`;
    html += `<a class="ff-dropdown-item" href="${BASE}/mispublicaciones.php">Mis posts</a>`;

    // SOLO ADMIN
    if (u.isAdmin){
      // Panel de usuarios (el que acabamos de hacer)
      html += `<a class="ff-dropdown-item" href="${BASE}/admin-usuarios.php">Administrar Usuarios</a>`;

      // Si luego tienes un panel de aprobaciones, déjalo listo:
      html += `<a class="ff-dropdown-item" href="${BASE}/admin-aprobaciones.php">Aprobar Publicacion</a>`;

      // Crear comunidad (tu página protegida por admin_only.php)
      html += `<a class="ff-dropdown-item" href="${BASE}/pagina.php">Crear comunidad</a>`;

      // Abrir modal de nueva categoría
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
      if (!getUser()?.isAdmin) return alert('Solo administradores pueden crear categorías.');
      const modalEl = document.getElementById('createCategoryModal');
      const modal = new bootstrap.Modal(modalEl);
      modal.show();
    });
  }
}
buildProfileMenu();

// --- Dropdown perfil ---
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

// para ver que tenga login
function getFollowing(){
  try{ return new Set(JSON.parse(localStorage.getItem('ff_following')||'[]')); }catch{ return new Set(); }
}
function setFollowing(set){ localStorage.setItem('ff_following', JSON.stringify(Array.from(set))); }
const following = getFollowing();
function initials(name){ return name.split(' ').map(w=>w[0]).slice(0,2).join('').toUpperCase(); }

function groupItem(g){
  const isFollowing = following.has(g.slug);
  const btnClass = isFollowing ? 'ff-follow-btn ff-following' : 'ff-follow-btn ff-notfollowing';
  const btnText  = isFollowing ? 'Seguido' : 'Seguir';
  const href = `${BASE}/sede.php?slug=${encodeURIComponent(g.slug)}`; 
  const avatar = g.img
    ? `<span class="ff-avatar-sm"><img src="${g.img}" alt="${g.nombre}" onerror="this.remove();this.closest('.ff-avatar-sm').textContent='${initials(g.nombre)}'"></span>`
    : `<span class="ff-avatar-sm">${initials(g.nombre)}</span>`;
  return `
    <div class="ff-group-item" data-slug="${g.slug}">
      <a class="ff-group-link" href="${href}" title="${g.nombre}">
        ${avatar}<span>${g.nombre}</span>
      </a>
      <button type="button" class="${btnClass} btn btn-sm">${btnText}</button>
    </div>`;
}

function renderGroups(){
  el.gruposRight.innerHTML = GROUPS.map(groupItem).join('');
  el.gruposRight.querySelectorAll('.ff-group-item .btn').forEach(btn=>{
    btn.addEventListener('click', (ev)=>{
      if (!requireAuth({title:'Inicia sesión para seguir grupos', msg:'Para seguir y recibir contenido de un grupo necesitas iniciar sesión.'})) return;
      const wrap = ev.target.closest('.ff-group-item');
      const slug = wrap.dataset.slug;
      if (following.has(slug)) following.delete(slug);
      else following.add(slug);
      setFollowing(following);
      renderGroups();
    });
  });
}
renderGroups();

// 
function badgeEstado(row){
  return row.estado === 'aprobado'
    ? '<span class="ff-chip">Aprobado</span>'
    : '<span class="ff-chip">En revisión</span>';
}
function mediaBlock(row){
  const hasSrc = !!row.src;
  if (row.mediaType === 'video') {
    return `<div class="ff-post-media ${hasSrc ? '' : 'ff-empty'}">
      ${hasSrc ? `<video src="${row.src}" ${row.poster?`poster="${row.poster}"`:''} controls playsinline></video>` : ''}
    </div>`;
  }
  return `<div class="ff-post-media ${hasSrc ? '' : 'ff-empty'}">
    ${hasSrc ? `<img src="${row.src}" alt="${row.titulo}" onerror="this.closest('.ff-post-media').classList.add('ff-empty'); this.remove();">` : ''}
  </div>`;
}


function card(row){
  const g = GROUP_MAP[row.sedeSlug];
  const avatarHTML = (g && g.img)
    ? `<img src="${g.img}" alt="${g?.nombre || 'Sede'}" onerror="this.remove()">`
    : '';

  return `
  <article class="ff-post">
    <div class="ff-post-header">
      <div class="ff-post-meta">
        <div class="ff-avatar">${avatarHTML}</div>
        <div>
          <div class="d-flex align-items-center gap-2">
            <strong>${row.titulo}</strong>
            ${badgeEstado(row)}
          </div>
          <div class="ff-post-sub">
            <a class="ff-group-link-mini"
               href="${BASE}/sede.php?slug=${encodeURIComponent(row.sedeSlug)}"
               title="Ver página de ${row.sedeNombre}">
              ${row.sedeNombre}
            </a>
            · ${new Date(row.fecha).toLocaleDateString()}
          </div>
        </div>
      </div>
      <span class="ff-chip text-uppercase">${row.categoria}</span>
    </div>
    ${mediaBlock(row)}
    <div class="ff-actions">
      <button class="btn btn-outline-light like-btn" data-id="${row.id}">👍 <span class="like-count">${row.likes}</span></button>
      <button class="btn btn-login comment-btn" data-id="${row.id}">Comentar</button>
    </div>
  </article>`;
}


function render(list){
  el.cards.innerHTML = list.map(card).join('');
  el.empty.classList.toggle('d-none', list.length !== 0);
  el.msg.className = 'small mb-3'; el.msg.textContent = '';

  
  el.cards.querySelectorAll('.like-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      if (!requireAuth({title:'Inicia sesión para dar like', msg:'Para reaccionar a publicaciones necesitas iniciar sesión.'})) return;
      const count = btn.querySelector('.like-count');
      count.textContent = (parseInt(count.textContent || '0', 10) + 1);
    });
  });

  
  el.cards.querySelectorAll('.comment-btn').forEach(btn=>{
    btn.addEventListener('click', ()=>{
      if (!requireAuth({title:'Inicia sesión para comentar', msg:'Para escribir un comentario necesitas iniciar sesión.'})) return;
      const id = btn.dataset.id;
      location.href = `${BASE}/detalle-publicacion.php?id=${encodeURIComponent(id)}`;
    });
  });
}



// Publicar
if (el.publishBtn) {
  el.publishBtn.addEventListener('click', () => {
    if (!requireAuth({title:'Necesitas iniciar sesión', msg:'Para publicar contenido debes iniciar sesión o crear una cuenta.'})) return;
    location.href = `${BASE}/crear-publicacion.php`;
  });
}

// Render inicial
render(DATA);
  </script>
</body>
</html>
