
  
    function getUser(){ try{ return JSON.parse(localStorage.getItem('ff_user')||'null'); }catch{ return null; } }

    function buildProfileMenu(){
      const u = getUser();
      const menu = document.getElementById('profileMenu');
      let html = '';
      if (!u){
        html += `<a class="ff-dropdown-item" href="login.php">Iniciar sesión</a>`;
        html += `<a class="ff-dropdown-item" href="register.php">Crear cuenta</a>`;
      } else {
        html += `<div class="ff-dropdown-item text-secondary">Hola, ${u.name || u.email}</div>`;
        html += `<a class="ff-dropdown-item" href="perfil.php">Mi perfil</a>`;
        if (u.isAdmin){ html += `<a class="ff-dropdown-item" href="admin-aprobaciones.php">Aprobaciones</a>`; }
        html += `<button class="ff-dropdown-item text-start" id="logoutBtn" type="button">Cerrar sesión</button>`;
      }
      menu.innerHTML = html;
      document.getElementById('logoutBtn')?.addEventListener('click', ()=>{ localStorage.removeItem('ff_user'); location.href='index.php'; });
    }
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
 


  

