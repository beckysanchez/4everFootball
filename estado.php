<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Estado | 4everFootball</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="./css/styles.css?v=31">
</head>
<body class="ff-bg">

  <!-- ===== HEADER (solo logo) ===== -->
  <header id="siteHeader" class="ff-header sticky-top">
    <div class="container d-flex align-items-center gap-3 py-2">
      <a href="index.php" class="d-flex align-items-center gap-2 text-decoration-none">
        <img src="img/logo.svg" alt="4everFootball" style="height:34px">
      </a>

      <form id="headerSearch" class="ms-auto me-auto w-50 d-none d-md-flex" role="search" novalidate>
        <div class="input-group ff-search">
          <span class="input-group-text">🔎</span>
          <input id="qHeader" type="search" class="form-control" placeholder="Buscar en 4everFootball…">
        </div>
      </form>

     
    </div>
  </header>

  <main class="container py-4 pb-5">
    <section class="glass-card p-3 p-md-4 mb-4">
      <div id="alertBox" class="alert mb-3" role="alert"></div>
      <div class="d-flex gap-2" id="ctaWrap"></div>
    </section>
  </main>
<script>
 
  function getUser(){ try{ return JSON.parse(localStorage.getItem('ff_user')||'null'); }catch{ return null; } }
 
  function buildProfileMenu(){
    const menu = document.getElementById('profileMenu');
    if (!menu) return; // <-- CLAVE: en estado.php no hay menú
 
    const u = getUser();
    let html = '';
    if (!u){
      html += `<a class="ff-dropdown-item" href="login.php">Iniciar sesión</a>`;
      html += `<a class="ff-dropdown-item" href="register.php">Crear cuenta</a>`;
    } else {
      html += `<div class="ff-dropdown-item text-secondary">Hola, ${u.name || u.email}</div>`;
      html += `<a class="ff-dropdown-item" href="perfil.php">Mi perfil</a>`;
      if (u.isAdmin){ html += `<a class="ff-dropdown-item" href="admin-aprobaciones.php">Panel de admin</a>`; }
      html += `<button class="ff-dropdown-item text-start" id="logoutBtn" type="button">Cerrar sesión</button>`;
    }
    menu.innerHTML = html;
    document.getElementById('logoutBtn')?.addEventListener('click', ()=>{
      localStorage.removeItem('ff_user'); location.href='index.php';
    });
  }
  buildProfileMenu();
 
 
  {
    const btn  = document.getElementById('profileBtn');
    const menu = document.getElementById('profileMenu');
    if (btn && menu){
      btn.addEventListener('click', ()=>{
        const open = menu.classList.toggle('show');
        btn.setAttribute('aria-expanded', String(open));
      });
      document.addEventListener('click', (e)=>{
        if (!menu.contains(e.target) && !btn.contains(e.target)){
          menu.classList.remove('show');
          btn.setAttribute('aria-expanded','false');
        }
      });
    }
  }

  document.getElementById('publishBtn')?.addEventListener('click', ()=>{
    if (!getUser()){
      const url = new URL('estado.php', location.href);
      url.searchParams.set('type','warning');
      url.searchParams.set('title','Necesitas iniciar sesión');
      url.searchParams.set('msg','Para publicar contenido debes iniciar sesión o crear una cuenta.');
      url.searchParams.set('primary','Iniciar sesión:/login.php');
      url.searchParams.set('secondary','Crear cuenta:/register.php');
      location.href = url.toString();
      return;
    }
    location.href = 'crear-publicacion.php';
  });
 
  
  const params = new URLSearchParams(location.search);
  const type  = (params.get('type')||'info').toLowerCase();   
  const title = params.get('title') || 'Aviso';
  const msg   = params.get('msg')   || 'Acción requerida.';
  const primary   = params.get('primary');   
  const secondary = params.get('secondary'); 
 
  const mapTypeToClass = { success:'success', info:'info', warning:'warning', error:'danger' };
  const bsClass = mapTypeToClass[type] || 'info';
 
  const alertBox = document.getElementById('alertBox');
  alertBox.className = `alert alert-${bsClass} mb-3`;
  alertBox.innerHTML = `
<div class="d-flex align-items-start gap-2">
<div style="font-size:1.2rem">⚠️</div>
<div>
<div class="fw-bold mb-1">${title}</div>
<div>${msg}</div>
</div>
</div>
  `;
 
  function btnFromParam(pair, style='primary'){
    if (!pair) return '';
    const idx = pair.lastIndexOf(':');
    if (idx<=0) return '';
    const text = pair.slice(0, idx);
    const href = pair.slice(idx+1);
    return `<a class="btn btn-${style}" href="${href.trim()}">${text.trim()}</a>`;
  }
 
  const ctaWrap = document.getElementById('ctaWrap');
  ctaWrap.innerHTML = [
    btnFromParam(primary, 'login'),        
    btnFromParam(secondary, 'outline-light')
  ].join('');
</script>
</body>
</html>
