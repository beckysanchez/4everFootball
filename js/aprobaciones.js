document.addEventListener('DOMContentLoaded', () => {
  console.log('✅ aprobaciones.js listo');

  // --- DOM caché ---
  const form = document.getElementById('filterForm');
  const tbody = document.getElementById('tbody');
  const resetBtn = document.getElementById('resetBtn');

  const modal = new bootstrap.Modal(document.getElementById('previewModal'));
  const modalTitle  = document.getElementById('modalTitle');
  const modalDesc   = document.getElementById('modalDesc');
  const modalMedia  = document.getElementById('modalMedia');
  const modalCat    = document.getElementById('modalCat');
  const modalSede   = document.getElementById('modalSede');
  const modalEstado = document.getElementById('modalEstado');
  const modalMeta   = document.getElementById('modalMeta');

  if (!form || !tbody) {
    console.error('❌ Falta form o tbody en el DOM');
    return;
  }

  // Ajusta según tu entorno
  const BASE = window.BASE_PATH || '/4everFootball';

  // --- Snackbar pequeño ---
  let sbEl;
  function snackbar(msg, type = 'ok') {
    if (!sbEl) {
      sbEl = document.createElement('div');
      sbEl.id = 'ff-snackbar';
      sbEl.style.cssText = `
        position:fixed; left:50%; bottom:24px; transform:translateX(-50%);
        background:${type === 'error' ? '#7f1d1d' : '#14532d'};
        color:#fff; padding:10px 14px; border-radius:10px; z-index:9999;
        box-shadow:0 8px 24px rgba(0,0,0,.35); font:500 14px system-ui;
        opacity:0; transition:.25s ease; pointer-events:none;
      `;
      document.body.appendChild(sbEl);
    }
    sbEl.style.background = type === 'error' ? '#7f1d1d' : '#14532d';
    sbEl.textContent = msg;
    sbEl.style.opacity = 1;
    clearTimeout(sbEl._t);
    sbEl._t = setTimeout(() => (sbEl.style.opacity = 0), 2000);
  }

  // ======================================================================
  // Cargar filtros dinámicamente
  // ======================================================================
  async function cargarFiltros() {
    try {
      const res = await fetch(`${BASE}/api/filtros_datos.php`);
      const data = await res.json();
      if (!data.ok) throw new Error('Respuesta inválida de filtros');

      const catSel   = document.getElementById('cat');
      const sedeSel  = document.getElementById('sede');
      const estSel   = document.getElementById('estado');

      // Categorías
      catSel.innerHTML =
        `<option value="">Todas</option>` +
        (data.categorias || []).map(c => `<option value="${c}">${c}</option>`).join('');

      // Sedes
      sedeSel.innerHTML = `<option value="">Todas</option>`;
      (data.sedes || []).forEach(s => {
        if (s && typeof s === 'object') {
          sedeSel.insertAdjacentHTML('beforeend',
            `<option value="${s.id}">${s.nombre}</option>`);
        } else {
          sedeSel.insertAdjacentHTML('beforeend',
            `<option value="${s}">${s}</option>`);
        }
      });

      // Estados (del enum en BD)
      estSel.innerHTML =
        `<option value="">Todos</option>` +
        (data.estados || [])
          .map(e => `<option value="${String(e).toUpperCase()}">${String(e).toUpperCase()}</option>`)
          .join('');

    } catch (err) {
      console.error('💥 Error cargando filtros:', err);
      snackbar('No se pudieron cargar los filtros', 'error');
    }
  }

  // ======================================================================
  // Render de tabla
  // ======================================================================
  function renderTabla(rows) {
    tbody.innerHTML = '';
    rows.forEach(pub => {
      const tr = document.createElement('tr');
      tr.dataset.id = pub.publicacion_id;

      const thumb = (pub.tipo_media === 'IMAGEN' && pub.media_url)
        ? `<img src="${BASE}/${pub.media_url}" alt="${pub.titulo}"
             style="width:50px;height:50px;object-fit:cover;border-radius:6px;">`
        : '';

      tr.innerHTML = `
        <td>
          <input type="checkbox" class="form-check-input ff-check" value="${pub.publicacion_id}">
        </td>
        <td>
          <div class="d-flex align-items-center gap-2">
            ${thumb}
            <span>${pub.titulo}</span>
          </div>
        </td>
        <td class="d-none d-md-table-cell">${pub.autor || '-'}</td>
        <td>${pub.categoria || '-'}</td>
        <td class="d-none d-sm-table-cell">${pub.sede || '-'}</td>
        <td class="d-none d-lg-table-cell">${pub.creada_en || '-'}</td>
        <td><span class="ff-status-badge ff-status-${String(pub.estatus || 'PENDIENTE').toLowerCase()}">${pub.estatus}</span></td>
        <td class="text-end ff-action">
          <button class="btn btn-sm btn-outline-light ff-ver" data-id="${pub.publicacion_id}">  Ver  </button>
        </td>
      `;
      tbody.appendChild(tr);
    });

    tbody.querySelectorAll('.ff-ver').forEach(btn => {
      btn.addEventListener('click', () => verPreview(btn.dataset.id));
    });
  }

  // ======================================================================
  // Cargar publicaciones
  // ======================================================================
  async function cargarPublicaciones() {
    const q      = document.getElementById('q')?.value || '';
    const cat    = document.getElementById('cat')?.value || '';
    const sede   = document.getElementById('sede')?.value || '';
    const estado = document.getElementById('estado')?.value || '';
    const orden  = document.getElementById('orden')?.value || '';

    tbody.innerHTML = `
      <tr><td colspan="8" class="text-center text-secondary py-4">
        Cargando publicaciones...
      </td></tr>`;

    try {
      const url = new URL(`${BASE}/api/publicaciones_listar.php`, location.origin);
      url.searchParams.set('q', q);
      url.searchParams.set('cat', cat);
      url.searchParams.set('sede', sede);
      url.searchParams.set('estado', estado);
      url.searchParams.set('orden', orden);

      const res = await fetch(url);
      const data = await res.json();

      if (!res.ok) throw new Error(`HTTP ${res.status}`);
      if (!data.ok) {
        tbody.innerHTML = `<tr><td colspan="8" class="text-danger text-center py-4">
          ${data.error || 'Error en la respuesta'}
        </td></tr>`;
        return;
      }

      if (!data.data?.length) {
        tbody.innerHTML = `<tr><td colspan="8" class="text-secondary text-center py-4">
          No hay publicaciones
        </td></tr>`;
        return;
      }

      renderTabla(data.data);
    } catch (err) {
      console.error('💥 Error al cargar publicaciones:', err);
      tbody.innerHTML = `<tr><td colspan="8" class="text-danger text-center py-4">
        Error al conectar con el servidor
      </td></tr>`;
    }
  }

  // ======================================================================
  // Vista previa
  // ======================================================================
  async function verPreview(id) {
    try {
      const url = new URL(`${BASE}/api/publicaciones_listar.php`, location.origin);
      url.searchParams.set('id', id);
      const res = await fetch(url);
      const data = await res.json();

      if (!data.ok || !data.data?.length) {
        snackbar('No se pudo cargar la publicación', 'error');
        return;
      }

      const p = data.data[0];
      modalTitle.dataset.id = p.publicacion_id;
      modalTitle.textContent = p.titulo;
      modalDesc.textContent  = p.descripcion || 'Sin descripción.';
      modalMeta.textContent  = `${p.autor || 'Anónimo'} · ${p.creada_en || 'Fecha desconocida'}`;
      modalCat.textContent   = p.categoria || 'Sin categoría';
      modalSede.textContent  = p.sede || 'Sin sede';
      modalEstado.textContent= p.estatus || 'PENDIENTE';

      if (p.media_url) {
        modalMedia.classList.remove('ff-empty');
        modalMedia.innerHTML = (p.tipo_media === 'VIDEO')
          ? `<video src="${BASE}/${p.media_url}" controls playsinline></video>`
          : `<img src="${BASE}/${p.media_url}" alt="${p.titulo}">`;
      } else {
        modalMedia.classList.add('ff-empty');
        modalMedia.innerHTML = '';
      }

      modal.show();
    } catch (err) {
      console.error('💥 Error vista previa:', err);
      snackbar('Error cargando vista previa', 'error');
    }
  }
  window.verPreview = verPreview;

  // ======================================================================
  // Cambiar estado (uno / varios)
  // ======================================================================
  async function cambiarEstadoUno(id, estado) {
    try {
      const fd = new FormData();
      fd.append('id', id);
      fd.append('estado', estado); // PENDIENTE | APROBADA | RECHAZADA

      const res = await fetch(`${BASE}/api/publicacion_cambiar_estado.php`, {
        method: 'POST',
        body: fd
      });
      const data = await res.json();
      if (!data.ok) throw new Error(data.error || 'Error al cambiar estado');

      snackbar(`Publicación ${estado.toLowerCase()} correctamente`, 'ok');
      modal.hide();
      cargarPublicaciones();
    } catch (err) {
      console.error('💥 Estado (uno):', err);
      snackbar(`Error al actualizar estado: ${err.message}`, 'error');
    }
  }

  async function cambiarEstadoSeleccionados(estado) {
    const checks = [...tbody.querySelectorAll('.ff-check:checked')];
    if (!checks.length) {
      snackbar('Selecciona al menos una publicación', 'error');
      return;
    }
    try {
      const fd = new FormData();
      checks.forEach(chk => fd.append('id[]', chk.value));
      fd.append('estado', estado);

      const res = await fetch(`${BASE}/api/publicacion_cambiar_estado.php`, {
        method: 'POST',
        body: fd
      });
      const data = await res.json();
      if (!data.ok) throw new Error(data.error || 'Error al cambiar estado');

      snackbar(`${checks.length} publicación(es) ${estado.toLowerCase()}(s)`, 'ok');
      cargarPublicaciones();
    } catch (err) {
      console.error('💥 Estado (masivo):', err);
      snackbar(`Error masivo: ${err.message}`, 'error');
    }
  }

  // ======================================================================
  // Eventos
  // ======================================================================
  form.addEventListener('submit', (e) => { e.preventDefault(); cargarPublicaciones(); });

  resetBtn?.addEventListener('click', () => {
    form.reset();
    cargarFiltros();
    cargarPublicaciones();
  });

  document.getElementById('modalApprove')?.addEventListener('click', () => {
    const id = modalTitle.dataset.id;
    if (id) cambiarEstadoUno(id, 'APROBADA');
  });
  document.getElementById('modalReject')?.addEventListener('click', () => {
    const id = modalTitle.dataset.id;
    if (id) cambiarEstadoUno(id, 'RECHAZADA');
  });

  document.getElementById('bulkApprove')?.addEventListener('click', () => cambiarEstadoSeleccionados('APROBADA'));
  document.getElementById('bulkReject')?.addEventListener('click', () => cambiarEstadoSeleccionados('RECHAZADA'));

  // Init
  cargarFiltros();
  cargarPublicaciones();
});
