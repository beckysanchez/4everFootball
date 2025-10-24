
    (function guard(){
      function getUser(){ try { return JSON.parse(localStorage.getItem('ff_user')||'null'); } catch { return null; } }
      const u = getUser();
      if (!u){
        const url = new URL('estado.php', location.href);
        url.searchParams.set('type','warning');
        url.searchParams.set('title','Necesitas iniciar sesión');
        url.searchParams.set('msg','Para crear una publicación debes iniciar sesión o crear una cuenta.');
        url.searchParams.set('primary','Iniciar sesión:/login.php');
        url.searchParams.set('secondary','Crear cuenta:/register.php');
        location.replace(url.toString());
      }
    })();
 
    (() => {
      const SIM_MODE = true; 

      function getUser(){ try{ return JSON.parse(localStorage.getItem('ff_user')||'null'); }catch{ return null; } }
      function buildMenu(){
        const u = getUser();
        const menu = document.getElementById('profileMenu');
        let html = '';
        if (!u){
          html += `<a class="ff-dropdown-item" href="login.php">Iniciar sesión</a>`;
          html += `<a class="ff-dropdown-item" href="register.php">Crear cuenta</a>`;
        } else {
          html += `<div class="ff-dropdown-item text-secondary">Hola, ${u.name || u.email}</div>`;
          html += `<a class="ff-dropdown-item" href="perfil.php">Mi perfil</a>`;
          if (u.isAdmin){ html += `<a class="ff-dropdown-item" href="admin-aprobaciones.php">Panel admin</a>`; }
          html += `<button class="ff-dropdown-item text-start" id="logoutBtn" type="button">Cerrar sesión</button>`;
        }
        menu.innerHTML = html;
        document.getElementById('logoutBtn')?.addEventListener('click', ()=>{ localStorage.removeItem('ff_user'); location.href='index.php'; });
      }
      buildMenu();

  

      const setLoading = (loading) => {
        submit.disabled = loading;
        if (loading) { submit.dataset.originalText = submit.textContent; submit.innerHTML = '<span class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>Publicando…'; }
        else { submit.textContent = submit.dataset.originalText || 'Publicar'; }
      };

      const validFile = () => {
        const f = file.files?.[0]; if (!f) return true;
        const okType = /^image\//.test(f.type) || /^video\/(mp4|webm)/.test(f.type);
        const okSize = f.size <= 20 * 1024 * 1024;
        return okType && okSize;
      };

      const anyMediaProvided = () => {
        const hasFile = !!(file.files && file.files[0]);
        const hasUrl  = !!url.value.trim();
        return hasFile || hasUrl;
      };

      function validateAfterSubmit() {
        [title,cat,sede,mediaT,desc,file,url,rights].forEach(el => el.classList.remove('is-invalid'));
        let ok = true;
        if (!title.checkValidity()) { title.classList.add('is-invalid'); ok=false; }
        if (!cat.checkValidity())   { cat.classList.add('is-invalid'); ok=false; }
        if (!sede.checkValidity())  { sede.classList.add('is-invalid'); ok=false; }
        if (!mediaT.checkValidity()){ mediaT.classList.add('is-invalid'); ok=false; }
        if (desc.value && desc.value.length < 10){ desc.classList.add('is-invalid'); ok=false; }

        if (!anyMediaProvided()){
          file.classList.add('is-invalid'); url.classList.add('is-invalid'); ok=false;
        } else if (!validFile()){
          file.classList.add('is-invalid'); ok=false;
        } else if (url.value && !url.checkValidity()){
          url.classList.add('is-invalid'); ok=false;
        }

        if (!rights.checked){ rights.classList.add('is-invalid'); ok=false; }
        return ok;
      }

      // Contador de descripción
      desc.addEventListener('input', () => { descCount.textContent = desc.value.length; });

   

      // Enviar
      form.addEventListener('submit', async (e) => {
        e.preventDefault();
        clearMsg();

        const ok = validateAfterSubmit();
        if (!ok) { showMsg('Corrige los campos marcados.'); return; }

        setLoading(true);
       
      });

      
    })();
