<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Mi perfil | 4everFootball</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <link rel="stylesheet" href="./css/index.css?v=41">
</head>
<body class="ff-bg">

 
  <header id="siteHeader" class="ff-header sticky-top">
  <div class="container d-flex align-items-center gap-3 py-2">
    <a href="index.php" class="d-flex align-items-center gap-2 text-decoration-none">
      <img src="img/logo.svg" alt="4everFootball" style="height:34px">
    </a>


    <form class="d-none d-md-flex flex-grow-1 mx-3" role="search">
      <div class="input-group ff-search w-100" style="max-width:680px;margin-left:auto;margin-right:auto;">
        <span class="input-group-text">🔎</span>
        <input id="qHeader" type="search" class="form-control" placeholder="Buscar en 4everFootball…">
      </div>
    </form>

    
    <nav class="d-flex align-items-center gap-2 ms-auto">
      <button id="publishBtn" class="btn btn-register" type="button">Publicar</button>

      <div class="ff-profile position-relative">
        <button id="profileBtn" class="ff-avatar-btn" type="button"
                aria-haspopup="true" aria-expanded="false"
                aria-controls="profileMenu" title="Cuenta">
          <img src="img/icon_iniciarsesion.png?v=1"
               alt="Perfil" class="ff-avatar-img" width="36" height="36"
               decoding="async" loading="lazy"
               onerror="this.style.visibility='hidden';this.parentElement.classList.add('ff-avatar-fallback');" />
        </button>
        <div id="profileMenu" class="ff-dropdown" role="menu" aria-labelledby="profileBtn"></div>
      </div>
    </nav>
  </div>
</header>


  <main class="container py-4 pb-5">

    <section class="glass-card p-3 p-md-4 mb-4">
      <h1 class="ff-title mb-0">Mi perfil</h1>
    </section>

    <section class="glass-card p-3 p-md-4 mb-4">
      <h2 class="h5 text-white mb-3">Mis datos</h2>

      <div id="infoMsg" class="small mb-3" role="alert" aria-live="polite"></div>

      <form id="infoForm" action="perfil.php" method="post" novalidate>
        <div class="row g-3">
          <div class="col-12 col-md-6">
            <label class="form-label" for="name">Nombre</label>
            <input class="form-control" type="text" id="name" name="name" required>
            <div class="invalid-feedback">Ingresa tu nombre.</div>
          </div>

          <div class="col-12 col-md-6">
            <label class="form-label" for="lastname">Apellidos</label>
            <input class="form-control" type="text" id="lastname" name="lastname" required>
            <div class="invalid-feedback">Ingresa tus apellidos.</div>
          </div>

          <div class="col-12 col-md-6">
            <label class="form-label" for="gender">Género</label>
            <select class="form-select" id="gender" name="gender" required>
              <option value="" selected disabled>Selecciona…</option>
              <option value="Hombre">Hombre</option>
              <option value="Mujer">Mujer</option>
              <option value="Otro">Otro</option>
              <option value="Prefiero no decirlo">Prefiero no decirlo</option>
            </select>
            <div class="invalid-feedback">Selecciona una opción.</div>
          </div>

          <div class="col-12 col-md-6 d-none" id="genderOtherWrap">
            <label class="form-label" for="gender_other">Especifica tu género</label>
            <input class="form-control" type="text" id="gender_other" name="gender_other" maxlength="40">
            <div class="invalid-feedback">Este campo es obligatorio si elegiste “Otro”.</div>
          </div>

          <div class="col-12 col-md-6">
            <label class="form-label" for="birth_country">País de nacimiento</label>
            <input class="form-control" type="text" id="birth_country" name="birth_country" required>
            <div class="invalid-feedback">Indica tu país de nacimiento.</div>
          </div>

          <div class="col-12 col-md-6">
            <label class="form-label" for="nationality">Nacionalidad</label>
            <input class="form-control" type="text" id="nationality" name="nationality" required>
            <div class="invalid-feedback">Indica tu nacionalidad.</div>
          </div>

          <div class="col-12">
            <label class="form-label" for="email">Correo electrónico</label>
            <input class="form-control" type="email" id="email" name="email" value="" readonly>
            <div class="form-text">El correo no se puede cambiar desde aquí.</div>
          </div>

          <div class="col-12 d-flex justify-content-end gap-2">
            <a class="btn btn-outline-light" href="index.php">Cancelar</a>
            <button type="submit" class="btn btn-login" id="infoBtn">Guardar cambios</button>
          </div>
        </div>
      </form>
    </section>

    <section class="glass-card p-3 p-md-4">
      <h2 class="h5 text-white mb-3">Cambiar contraseña</h2>

      <div id="pwdMsg" class="small mb-3" role="alert" aria-live="polite"></div>

      <form id="pwdForm" action="password.php" method="post" novalidate>
        <div class="row g-3">
          <div class="col-12 col-md-6">
            <label class="form-label" for="password">Nueva contraseña</label>
            <input class="form-control" type="password" id="password" name="password"
                   required minlength="8"
                   pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\\d)(?=.*[!@#$%^&*()_+\\-=[\\]{};':&quot;\\\\|,.<>\\/?]).{8,}"
                   aria-describedby="pwdHelp">
            <div id="pwdHelp" class="form-text">Mín. 8, 1 mayúscula, 1 minúscula, 1 número y 1 carácter especial.</div>
            <div class="invalid-feedback">La contraseña no cumple los requisitos.</div>
          </div>

          <div class="col-12 col-md-6">
            <label class="form-label" for="password2">Confirmar nueva contraseña</label>
            <input class="form-control" type="password" id="password2" name="password2" required>
            <div class="invalid-feedback">Las contraseñas no coinciden.</div>
          </div>

          <div class="col-12 d-flex justify-content-end gap-2">
            <button type="submit" class="btn btn-login" id="pwdBtn">Actualizar contraseña</button>
          </div>
        </div>
      </form>
    </section>

  </main>

  <script>
  (() => {
    const SIM_MODE = true;

    function getUser(){ try{ return JSON.parse(localStorage.getItem('ff_user')||'null'); }catch{ return null; } }
    function requireAuthOrRedirect(){
      const u = getUser();
      if (u) return u;
      const url = new URL('estado.php', location.href);
      url.searchParams.set('type','warning');
      url.searchParams.set('title','Necesitas iniciar sesión');
      url.searchParams.set('msg','Para ver y editar tu perfil debes iniciar sesión.');
      url.searchParams.set('primary','Iniciar sesión:/login.php');
      url.searchParams.set('secondary','Crear cuenta:/register.php');
      location.replace(url.toString());
      return null;
    }
    function buildProfileMenu(){
      const u = getUser();
      const menu = document.getElementById('profileMenu');
      let html = '';
      if (!u){
        html += `<a class="ff-dropdown-item" href="login.php">Iniciar sesión</a>`;
        html += `<a class="ff-dropdown-item" href="register.php">Crear cuenta</a>`;
      } else {
        html += `<div class="ff-dropdown-item ff-user-greet">Hola, ${u.name || u.email}</div>`;
        html += `<a class="ff-dropdown-item" href="perfil.php">Mi perfil</a>`;
        html += `<a class="ff-dropdown-item" href="mispublicaciones.php">Mis posts</a>`;
        if (u.isAdmin){ 
          html += `<a class="ff-dropdown-item" href="admin-aprobaciones.php">Panel de admin</a>`;
          html += `<a class="ff-dropdown-item" href="pagina.php">Crear comunidad</a>`;
        }
        html += `<button class="ff-dropdown-item logout text-start" id="logoutBtn" type="button">Cerrar sesión</button>`;
      }
      menu.innerHTML = html;
      document.getElementById('logoutBtn')?.addEventListener('click', ()=>{
        localStorage.removeItem('ff_user');
        location.href = 'index.php';
      });
    }

    const header = document.getElementById('siteHeader');
    const onScroll = () => {
      if (window.scrollY > 6) header.classList.add('ff-header-scrolled');
      else header.classList.remove('ff-header-scrolled');
    };
    document.addEventListener('scroll', onScroll); onScroll();

    const btn = document.getElementById('profileBtn');
    const menu= document.getElementById('profileMenu');
    btn?.addEventListener('click', ()=>{
      const open = menu.classList.toggle('show');
      btn.setAttribute('aria-expanded', String(open));
    });
    document.addEventListener('click', (e)=>{
      if (!menu.contains(e.target) && !btn.contains(e.target)){
        menu.classList.remove('show');
        btn.setAttribute('aria-expanded','false');
      }
    });

    const USER = requireAuthOrRedirect();
    if (!USER) return;


    buildProfileMenu();


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

    const nameEl   = document.getElementById('name');
    const lastEl   = document.getElementById('lastname');
    const gender   = document.getElementById('gender');
    const genderWrap = document.getElementById('genderOtherWrap');
    const genderOther= document.getElementById('gender_other');
    const birthC   = document.getElementById('birth_country');
    const nation   = document.getElementById('nationality');
    const emailEl  = document.getElementById('email');

    if (USER){
      nameEl.value  = USER.name || '';
      lastEl.value  = USER.last || '';
      emailEl.value = USER.email || '';
      if (USER.gender){ gender.value = USER.gender; }
      if (USER.gender === 'Otro'){
        genderWrap.classList.remove('d-none');
        genderOther.setAttribute('required','required');
        genderOther.value = USER.gender_other || '';
      }
      if (USER.birth_country) birthC.value = USER.birth_country;
      if (USER.nationality)   nation.value = USER.nationality;
    }

    gender.addEventListener('change', () => {
      if (gender.value === 'Otro') {
        genderWrap.classList.remove('d-none');
        genderOther.setAttribute('required','required');
      } else {
        genderWrap.classList.add('d-none');
        genderOther.removeAttribute('required');
        genderOther.classList.remove('is-invalid');
        genderOther.value = '';
      }
    });

  
    const infoForm = document.getElementById('infoForm');
    const infoMsg  = document.getElementById('infoMsg');
    const infoBtn  = document.getElementById('infoBtn');

    let infoSubmittedOnce = false;
    const infoShowMsg = (text, type='danger') => { infoMsg.className = `alert alert-${type} small`; infoMsg.textContent = text; };
    const infoClearMsg = () => { infoMsg.className = 'small mb-3'; infoMsg.textContent=''; };

    function validateInfo(){
      [nameEl,lastEl,gender,genderOther,birthC,nation].forEach(el => el.classList.remove('is-invalid'));
      let ok = true;
      if (!nameEl.value.trim()) { nameEl.classList.add('is-invalid'); ok=false; }
      if (!lastEl.value.trim()) { lastEl.classList.add('is-invalid'); ok=false; }
      if (!gender.checkValidity()) { gender.classList.add('is-invalid'); ok=false; }
      if (gender.value === 'Otro' && !genderOther.value.trim()) { genderOther.classList.add('is-invalid'); ok=false; }
      if (!birthC.value.trim()) { birthC.classList.add('is-invalid'); ok=false; }
      if (!nation.value.trim()) { nation.classList.add('is-invalid'); ok=false; }
      return ok;
    }

    [nameEl,lastEl,gender,genderOther,birthC,nation].forEach(el=>{
      el.addEventListener('input', ()=> { if (infoSubmittedOnce) validateInfo(); else el.classList.remove('is-invalid'); });
      el.addEventListener('change', ()=> { if (infoSubmittedOnce) validateInfo(); else el.classList.remove('is-invalid'); });
    });

    infoForm.addEventListener('submit', async (e)=>{
      e.preventDefault();
      infoSubmittedOnce = true;
      infoClearMsg();

      if (!validateInfo()){ infoShowMsg('Corrige los campos marcados.'); return; }

      infoBtn.disabled = true;
      const original = infoBtn.textContent;
      infoBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Guardando…';

      try{
        if (SIM_MODE){
          const updated = {
            ...USER,
            name: nameEl.value.trim(),
            last: lastEl.value.trim(),
            gender: gender.value,
            gender_other: gender.value==='Otro' ? genderOther.value.trim() : '',
            birth_country: birthC.value.trim(),
            nationality: nation.value.trim(),
          };
          localStorage.setItem('ff_user', JSON.stringify(updated));
          infoShowMsg('Datos actualizados (simulación).', 'success');
          return;
        }
       
      }catch(err){
        console.error(err);
        infoShowMsg('Hubo un problema. Intenta más tarde.');
      }finally{
        infoBtn.disabled = false;
        infoBtn.textContent = original;
      }
    });


    const pwdForm = document.getElementById('pwdForm');
    const pwdMsg  = document.getElementById('pwdMsg');
    const pwdBtn  = document.getElementById('pwdBtn');
    const p1      = document.getElementById('password');
    const p2      = document.getElementById('password2');
    const pwdHelp = document.getElementById('pwdHelp');

    let pwdSubmittedOnce = false;
    const pwdShowMsg = (text, type='danger') => { pwdMsg.className = `alert alert-${type} small`; pwdMsg.textContent = text; };
    const pwdClearMsg = () => { pwdMsg.className='small mb-3'; pwdMsg.textContent=''; };

    function validatePwd(){
      [p1,p2].forEach(el => el.classList.remove('is-invalid'));
      pwdHelp.classList.remove('text-danger');
      let ok = true;
      if (!p1.checkValidity()) { p1.classList.add('is-invalid'); pwdHelp.classList.add('text-danger'); ok=false; }
      if (!p2.value.trim() || p1.value !== p2.value){ p2.classList.add('is-invalid'); ok=false; }
      return ok;
    }

    [p1,p2].forEach(el=>{
      el.addEventListener('input', ()=> { if (pwdSubmittedOnce) validatePwd(); else el.classList.remove('is-invalid'); });
      el.addEventListener('change', ()=> { if (pwdSubmittedOnce) validatePwd(); else el.classList.remove('is-invalid'); });
    });

    pwdForm.addEventListener('submit', async (e)=>{
      e.preventDefault();
      pwdSubmittedOnce = true;
      pwdClearMsg();

      if (!validatePwd()){ pwdShowMsg('Corrige los campos marcados.'); return; }

      pwdBtn.disabled = true;
      const original = pwdBtn.textContent;
      pwdBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Actualizando…';

      try{
        if (SIM_MODE){
          await new Promise(r=>setTimeout(r,800));
          pwdShowMsg('Contraseña actualizada (simulación).', 'success');
          p1.value=''; p2.value='';
          return;
        }
    
      }catch(err){
        console.error(err);
        pwdShowMsg('Hubo un problema. Intenta más tarde.');
      }finally{
        pwdBtn.disabled=false;
        pwdBtn.textContent = original;
      }
    });
  })();
  </script>
</body>
</html>
