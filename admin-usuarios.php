<?php
require __DIR__ . '/admin_only.php';
$BASE = '/4everFootball';
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Panel de admin · Usuarios | 4everFootball</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <!-- Estilos del panel admin -->
  <link rel="stylesheet" href="<?= $BASE ?>/css/admin-usuarios.css?v=1" />
</head>
<body>
  <div class="container ff-admin-container">
    <div class="d-flex align-items-center justify-content-between mb-3">
      <h1 class="h3 m-0 ff-admin-title">Usuarios</h1>
      <a class="btn btn-ff-outline" href="<?= $BASE ?>/index.php">← Volver</a>
    </div>

    <!-- Filtros -->
    <section class="ff-filtros glass glass--pad mb-3">
      <form id="filtros" class="row g-2 align-items-end">
        <div class="col-12 col-md-4">
          <label class="form-label" for="q">Buscar</label>
          <input id="q" name="q" class="form-control" placeholder="Nombre o correo">
        </div>
        <div class="col-6 col-md-2">
          <label class="form-label" for="rol">Rol</label>
          <select id="rol" name="rol" class="form-select">
            <option value="">Todos</option>
            <option value="ADMIN">ADMIN</option>
            <option value="USUARIO">USUARIO</option>
          </select>
        </div>
        <div class="col-6 col-md-2">
          <label class="form-label" for="activo">Estado</label>
          <select id="activo" name="activo" class="form-select">
            <option value="">Todos</option>
            <option value="1">Activos</option>
            <option value="0">Inactivos</option>
          </select>
        </div>
        <div class="col-6 col-md-2">
          <label class="form-label" for="orden">Orden</label>
          <select id="orden" name="orden" class="form-select">
            <option value="reciente">Más recientes</option>
            <option value="antiguo">Más antiguos</option>
            <option value="nombre">Nombre</option>
            <option value="email">Email</option>
          </select>
        </div>
        <div class="col-6 col-md-2">
          <label class="form-label" for="perPage">Por página</label>
          <select id="perPage" name="perPage" class="form-select">
            <option>10</option><option>20</option><option>50</option>
          </select>
        </div>
      </form>
    </section>

    <!-- Tabla -->
    <section class="table-wrap glass">
      <div class="table-responsive">
        <table class="table table-dark table-striped table-hover m-0">
          <thead>
            <tr>
              <th>ID</th>
              <th>Nombre</th>
              <th>Email</th>
              <th>Rol</th>
              <th>Creado</th>
              <th>Estado</th>
              <th class="text-end">Acciones</th>
            </tr>
          </thead>
          <tbody id="rows">
            <tr><td colspan="7" class="text-center p-4">Cargando…</td></tr>
          </tbody>
        </table>
      </div>
      <div class="d-flex justify-content-between align-items-center p-2">
        <div id="summary" class="small text-soft"></div>
        <nav aria-label="Paginación">
          <ul id="paginacion" class="pagination pagination-sm m-0"></ul>
        </nav>
      </div>
    </section>
  </div>

  <script>
    const API_LIST   = '<?= $BASE ?>/api/users_list.php';
    const API_TOGGLE = '<?= $BASE ?>/api/user_toggle_active.php';

    const st = { page:1 };
    const f = {
      q: document.getElementById('q'),
      rol: document.getElementById('rol'),
      activo: document.getElementById('activo'),
      orden: document.getElementById('orden'),
      perPage: document.getElementById('perPage')
    };
    Object.values(f).forEach(el => el.addEventListener('input', () => { st.page = 1; load(); }));

    function fmtDate(s){ try{ return new Date(s.replace(' ','T')).toLocaleString(); }catch{ return s; } }
    function badgeRol(r){ return r==='ADMIN' ? '<span class="badge badge-admin">ADMIN</span>' : '<span class="badge badge-user">USUARIO</span>'; }
    function badgeActivo(a){ return a ? '<span class="badge badge-state-on">Activo</span>' : '<span class="badge badge-state-off">Inactivo</span>'; }

    async function load(){
      const params = new URLSearchParams({
        q: f.q.value.trim(),
        rol: f.rol.value,
        activo: f.activo.value,
        orden: f.orden.value,
        perPage: f.perPage.value,
        page: st.page
      });
      const resp = await fetch(API_LIST, { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body: params });
      const data = await resp.json();
      render(data);
    }

    function render(data){
      const tbody = document.getElementById('rows');
      if (!data.ok){ tbody.innerHTML = `<tr><td colspan="7" class="text-center p-4 text-danger">${data.error||'Error'}</td></tr>`; return; }
      if (!data.items.length){ tbody.innerHTML = `<tr><td colspan="7" class="text-center p-4">Sin resultados</td></tr>`; }
      else {
        tbody.innerHTML = data.items.map(u => `
          <tr>
            <td>${u.id}</td>
            <td>${u.name}</td>
            <td>${u.email}</td>
            <td>${badgeRol(u.role)}</td>
            <td>${fmtDate(u.created)}</td>
            <td>${badgeActivo(u.activo)}</td>
            <td class="text-end">
              <button class="btn btn-sm ${u.activo?'btn-outline-warning':'btn-outline-success'}"
                      data-action="toggle" data-id="${u.id}" data-new="${u.activo?0:1}">
                ${u.activo?'Desactivar':'Activar'}
              </button>
            </td>
          </tr>`).join('');
        tbody.querySelectorAll('button[data-action="toggle"]').forEach(btn=>{
          btn.addEventListener('click', async () => {
            const id = btn.dataset.id, nuevo = btn.dataset.new;
            if (!confirm(`¿Seguro que deseas ${nuevo=='1'?'activar':'desactivar'} la cuenta #${id}?`)) return;
            const rp = await fetch(API_TOGGLE, { method: 'POST',
              headers:{'Content-Type':'application/x-www-form-urlencoded'},
              body: new URLSearchParams({ id, activo: nuevo })
            });
            const rj = await rp.json();
            if (!rj.ok) { alert(rj.error || 'No se pudo actualizar.'); return; }
            load();
          });
        });
      }

      // resumen y paginación
      document.getElementById('summary').textContent =
        `Mostrando ${data.items.length} de ${data.total} usuarios · página ${data.page}/${data.pages}`;
      const ul = document.getElementById('paginacion');
      ul.innerHTML = '';
      const mk = (p, txt, dis=false, act=false) => {
        const li = document.createElement('li');
        li.className = `page-item ${dis?'disabled':''} ${act?'active':''}`;
        li.innerHTML = `<a class="page-link" href="#">${txt}</a>`;
        if (!dis && !act) li.addEventListener('click', (e)=>{ e.preventDefault(); st.page = p; load(); });
        ul.appendChild(li);
      };
      mk(1, '«', st.page===1);
      mk(st.page-1, '‹', st.page===1);
      for (let p=Math.max(1, st.page-2); p<=Math.min(data.pages, st.page+2); p++){
        mk(p, p, false, p===st.page);
      }
      mk(st.page+1, '›', st.page===data.pages);
      mk(data.pages, '»', st.page===data.pages);
    }

    load();
  </script>
</body>
</html>
