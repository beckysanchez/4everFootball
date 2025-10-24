<?php
  
  $BASE = '/4everFootball';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Publicación | 4everFootball</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  
  <link rel="stylesheet" href="<?= $BASE ?>/css/index.css?v=41">
</head>
<body class="ff-bg">

  <header id="siteHeader" class="ff-header sticky-top">
    <div class="container d-flex align-items-center gap-3 py-2">
      <a href="<?= $BASE ?>/index.php" class="d-flex align-items-center gap-2 text-decoration-none">
        <img src="<?= $BASE ?>/img/logo.svg" alt="4everFootball" style="height:34px">
      </a>

      
      <form id="headerSearch" class="ms-auto me-auto w-50 d-flex" role="search" novalidate>
        <div class="input-group ff-search w-100">
          <span class="input-group-text">🔎</span>
          <input id="qHeader" type="search" class="form-control" placeholder="Buscar en 4everFootball…">
        </div>
      </form>

      <nav class="d-flex align-items-center gap-2">
        <button id="publishBtn" class="btn btn-register" type="button">Publicar</button>

 
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


  <main class="container py-4 pb-5">

    <article id="post" class="ff-post mb-4">
      <div class="ff-post-header">
        <div class="ff-post-meta">
          <div class="ff-avatar"></div>
          <div>
            <div class="d-flex align-items-center gap-2">
              <strong id="postTitle">Título de la publicación</strong>
              <span id="postState" class="ff-chip">Aprobado</span>
            </div>
            <div class="ff-post-sub" id="postMeta">Sede · Fecha</div>
          </div>
        </div>
        <span id="postCat" class="ff-chip text-uppercase">CATEGORÍA</span>
      </div>

      <div id="postMedia" class="ff-post-media ff-empty"></div>

      <div class="ff-actions">
        <button id="likeBtn" class="btn btn-outline-light">👍 <span id="likeCount">0</span></button>
        <a class="btn btn-login" href="#comments">Comentar</a>
      </div>
    </article>

    <section class="glass-card p-3 p-md-4 mb-4">
      <h2 class="h5 text-white mb-2">Descripción</h2>
      <p id="postDesc" class="mb-0 text-secondary"></p>
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

    const GROUPS = [
      { slug:'qatar-2022',  nombre:'Qatar 2022',        img:`${BASE}/img/2022.png` },
      { slug:'rusia-2018',  nombre:'Rusia 2018',        img:`${BASE}/img/2018.png` },
      { slug:'brasil-2014', nombre:'Brasil 2014',       img:`${BASE}/img/2014.png` },
      { slug:'sudafrica-2010', nombre:'Sudáfrica 2010', img:`${BASE}/img/2010.png` },
      { slug:'alemania-2006',  nombre:'Alemania 2006',  img:`${BASE}/img/2006.png` },
      { slug:'corea-japon-2002', nombre:'Corea/Japón 2002', img:`${BASE}/img/2002.png` },
    ];
    const GROUP_MAP = GROUPS.reduce((a,g)=>{ a[g.slug]=g; return a; }, {});

    function getUser(){ try { return JSON.parse(localStorage.getItem('ff_user')||'null'); } catch { return null; } }
    function goEstado(type,title,msg,primary,secondary){
      const u = new URL(`${BASE}/estado.php`, location.origin);
      u.searchParams.set('type', type);
      u.searchParams.set('title', title);
      u.searchParams.set('msg', msg);
      if (primary)   u.searchParams.set('primary', primary);
      if (secondary) u.searchParams.set('secondary', secondary);
      location.href = u.toString();
    }
    function requireAuth({title='Necesitas iniciar sesión', msg='Para continuar debes iniciar sesión o crear una cuenta.'}={}) {
      const u = getUser(); if (u) return true;
      goEstado('warning', title, msg, `Iniciar sesión:${BASE}/login.php`, `Crear cuenta:${BASE}/register.php`);
      return false;
    }


    function buildProfileMenu(){
      const u = getUser();
      const menu = document.getElementById('profileMenu');
      let html = '';
      if (!u){
        html += `<a class="ff-dropdown-item" href="${BASE}/login.php">Iniciar sesión</a>`;
        html += `<a class="ff-dropdown-item" href="${BASE}/register.php">Crear cuenta</a>`;
      } else {
        html += `<div class="ff-dropdown-item ff-user-greet">Hola, ${u.name || u.email}</div>`;
        html += `<a class="ff-dropdown-item" href="${BASE}/perfil.php">Mi perfil</a>`;
        html += `<a class="ff-dropdown-item" href="${BASE}/mispublicaciones.php">Mis posts</a>`;
        if (u.isAdmin){
          html += `<a class="ff-dropdown-item" href="${BASE}/admin-aprobaciones.php">Panel de admin</a>`;
          html += `<a class="ff-dropdown-item" href="${BASE}/pagina.php">Crear comunidad</a>`;
        }
        html += `<button class="ff-dropdown-item logout text-start" id="logoutBtn" type="button">Cerrar sesión</button>`;
      }
      menu.innerHTML = html;
      document.getElementById('logoutBtn')?.addEventListener('click', ()=>{ localStorage.removeItem('ff_user'); location.href=`${BASE}/index.php`; });
    }
    buildProfileMenu();

    // Dropdown
    const btn = document.getElementById('profileBtn');
    const menu= document.getElementById('profileMenu');
    btn.addEventListener('click', ()=>{ const open = menu.classList.toggle('show'); btn.setAttribute('aria-expanded', String(open)); });
    document.addEventListener('click', (e)=>{ if (!menu.contains(e.target) && !btn.contains(e.target)){ menu.classList.remove('show'); btn.setAttribute('aria-expanded','false'); } });


    document.getElementById('publishBtn').addEventListener('click', ()=>{
      if (!requireAuth({title:'Necesitas iniciar sesión', msg:'Para publicar contenido debes iniciar sesión o crear una cuenta.'})) return;
      location.href = `${BASE}/crear-publicacion.php`;
    });

    // sonseadas de jairo
    const POST = {
      id: 2,
      titulo: 'Entrevista al crack 2014',
      categoria: 'entrevistas',
      sedeNombre: 'Brasil 2014',
      sedeSlug: 'brasil-2014',
      fecha: '2014-07-10',
      estado: 'aprobado',
      likes: 120,
      mediaType: 'video',
      src: `${BASE}/img/sample2.mp4`,
      poster: `${BASE}/img/poster2.jpg`,
      desc: 'Conversación exclusiva con el delantero después del partido decisivo.'
    };

    
    const COMMENTS = [
      { id:1, user:'Ana',  text:'¡Tremenda entrevista! Se nota la emoción.', ts:'2024-08-01', likes:3, replies:[] },
      { id:2, user:'Luis', text:'Me gustó cuando habló de su preparación física.', ts:'2024-08-02', likes:0, replies:[] },
    ];
    let nextCmtId = (Math.max(0, ...collectIds(COMMENTS)) || 0) + 1;
    function collectIds(arr){ const out=[]; (function rec(a){ a.forEach(c=>{ out.push(c.id); if(c.replies?.length) rec(c.replies); }); })(arr); return out; }

   
    function renderPost(){
      document.getElementById('postTitle').textContent = POST.titulo;
      document.getElementById('postCat').textContent = POST.categoria.toUpperCase();
      document.getElementById('postState').textContent = POST.estado==='aprobado' ? 'Aprobado' : 'En revisión';
      document.getElementById('postDesc').textContent = POST.desc || '';
      document.getElementById('likeCount').textContent = POST.likes;

      // Meta con link a sede
      const meta = document.getElementById('postMeta');
      meta.innerHTML = `
        <a class="ff-group-link-mini" href="${BASE}/sede.php?slug=${encodeURIComponent(POST.sedeSlug)}" title="Ver ${POST.sedeNombre}">
          ${POST.sedeNombre}
        </a>
        · ${new Date(POST.fecha).toLocaleDateString()}
      `;

      const g = GROUP_MAP[POST.sedeSlug];
      const av = document.querySelector('.ff-post .ff-avatar');
      av.innerHTML = g && g.img ? `<img src="${g.img}" alt="${g?.nombre || 'Sede'}" onerror="this.remove()">` : '';

    
      const media = document.getElementById('postMedia');
      media.classList.remove('ff-empty'); media.innerHTML='';
      if (!POST.src){ media.classList.add('ff-empty'); }
      else if (POST.mediaType==='video'){
        media.innerHTML = `<video src="${POST.src}" ${POST.poster?`poster="${POST.poster}"`:''} controls playsinline></video>`;
      } else {
        media.innerHTML = `<img src="${POST.src}" alt="${POST.titulo}" onerror="this.closest('.ff-post-media').classList.add('ff-empty'); this.remove();">`;
      }
    }

    document.getElementById('likeBtn').addEventListener('click', ()=>{
      if (!requireAuth({title:'Necesitas iniciar sesión', msg:'Para dar like debes iniciar sesión o crear una cuenta.'})) return;
      const span = document.getElementById('likeCount');
      span.textContent = parseInt(span.textContent,10)+1;
    });

    
    const LIKE_KEY = `ff_cmt_likes_${POST.id}`;
    function getLikedSet(){
      try{ return new Set(JSON.parse(localStorage.getItem(LIKE_KEY) || '[]')); }catch{ return new Set(); }
    }
    function saveLikedSet(set){
      try{ localStorage.setItem(LIKE_KEY, JSON.stringify(Array.from(set))); }catch{}
    }
    let likedByMe = getLikedSet();

    const list = document.getElementById('commentList');

    function renderComments(){
      list.innerHTML = COMMENTS.map(c => renderCommentHTML(c)).join('');
    }

    function renderCommentHTML(c){
      const liked = likedByMe.has(c.id);
      const children = (c.replies && c.replies.length)
        ? `<div class="ms-4 ps-3 border-start border-secondary-subtle mt-3">${c.replies.map(r => renderCommentHTML(r)).join('')}</div>`
        : '';

      const isAdmin = !!getUser()?.isAdmin;
      const delBtn = isAdmin ? `<button class="btn btn-outline-light btn-sm text-danger c-del" data-id="${c.id}">Eliminar</button>` : '';

      return `
        <div class="cmt" data-id="${c.id}">
          <div class="d-flex gap-2">
            <div class="ff-avatar-sm"></div>
            <div class="flex-grow-1">
              <strong class="text-white">${escapeHTML(c.user)}</strong>
              <div class="text-secondary small">${new Date(c.ts).toLocaleDateString()}</div>
              <p class="mb-2">${escapeHTML(c.text)}</p>

              <div class="d-flex flex-wrap gap-1">
                <button class="btn ${liked ? 'btn-login' : 'btn-outline-light'} btn-sm c-like" data-id="${c.id}" aria-pressed="${liked}">
                  👍 <span class="c-like-count">${c.likes || 0}</span>
                </button>
                <button class="btn btn-outline-light btn-sm c-reply" data-id="${c.id}">Responder</button>
                ${delBtn}
              </div>

              <div class="mt-2 d-none c-replybox" id="replybox-${c.id}">
                <textarea class="form-control form-control-sm c-replytext" rows="2" maxlength="300" placeholder="Tu respuesta…"></textarea>
                <div class="d-flex justify-content-end gap-2 mt-2">
                  <button class="btn btn-outline-light btn-sm c-cancel-reply" data-id="${c.id}">Cancelar</button>
                  <button class="btn btn-login btn-sm c-send-reply" data-id="${c.id}">Responder</button>
                </div>
              </div>

              ${children}
            </div>
          </div>
        </div>
      `;
    }

    function escapeHTML(s){ return String(s).replace(/[&<>"']/g, ch => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[ch])); }

    
    function findCommentById(id, arr=COMMENTS){
      for (let i=0;i<arr.length;i++){
        const c = arr[i];
        if (c.id === id) return { parent: arr, index: i, node: c };
        if (c.replies && c.replies.length){
          const r = findCommentById(id, c.replies);
          if (r) return r;
        }
      }
      return null;
    }

    list.addEventListener('click', (e)=>{
      const btn = e.target.closest('button');
      if (!btn) return;
      const id = parseInt(btn.dataset.id, 10);
      if (!id) return;

      
      if (btn.classList.contains('c-reply')) {
        if (!requireAuth({title:'Necesitas iniciar sesión', msg:'Para responder debes iniciar sesión o crear una cuenta.'})) return;
        const box = document.getElementById(`replybox-${id}`);
        box?.classList.toggle('d-none');
        return;
      }

      if (btn.classList.contains('c-cancel-reply')) {
        document.getElementById(`replybox-${id}`)?.classList.add('d-none');
        return;
      }

      if (btn.classList.contains('c-send-reply')) {
        if (!requireAuth({title:'Necesitas iniciar sesión', msg:'Para responder debes iniciar sesión o crear una cuenta.'})) return;
        const box = document.getElementById(`replybox-${id}`);
        const txt = box?.querySelector('.c-replytext');
        const val = (txt?.value || '').trim();
        if (!val) { txt?.classList.add('is-invalid'); return; }
        txt?.classList.remove('is-invalid');

        const u = getUser();
        const found = findCommentById(id);
        if (!found) return;
        found.node.replies = found.node.replies || [];
        found.node.replies.push({
          id: nextCmtId++,
          user: u?.name || u?.email || 'Tú',
          text: val,
          ts: new Date().toISOString(),
          likes: 0,
          replies: []
        });
        txt.value = '';
        box?.classList.add('d-none');
        renderComments();
        showMsg('Respuesta publicada (simulación).', 'success');
        return;
      }

      if (btn.classList.contains('c-del')) {
        const u = getUser();
        if (!u?.isAdmin) return;
        const found = findCommentById(id);
        if (!found) return;
      
        found.parent.splice(found.index, 1);
        
        likedByMe.delete(id); saveLikedSet(likedByMe);
        renderComments();
        showMsg('Comentario eliminado (simulación).', 'success');
        return;
      }
    });

    
    const cmtForm = document.getElementById('cmtForm');
    const cmtText = document.getElementById('cmtText');
    const cmtMsg  = document.getElementById('cmtMsg');

    function showMsg(text, type='danger'){
      cmtMsg.className = `alert alert-${type} small`;
      cmtMsg.textContent = text;
    }
    function clearMsg(){ cmtMsg.className='small mb-3'; cmtMsg.textContent=''; }

    cmtForm.addEventListener('submit', (e)=>{
      e.preventDefault();
      clearMsg();

      if (!requireAuth({title:'Necesitas iniciar sesión', msg:'Para comentar debes iniciar sesión o crear una cuenta.'})) return;

      if (!cmtText.checkValidity()){
        cmtText.classList.add('is-invalid');
        showMsg('Corrige tu comentario.');
        return;
      }
      cmtText.classList.remove('is-invalid');

      const u = getUser();
      COMMENTS.unshift({
        id: nextCmtId++,
        user: u?.name || u?.email || 'Tú',
        text: cmtText.value.trim(),
        ts: new Date().toISOString(),
        likes: 0,
        replies: []
      });
      cmtText.value='';
      renderComments();
      showMsg('Comentario publicado (simulación).', 'success');
    });
    cmtText.addEventListener('input', ()=> cmtText.classList.remove('is-invalid'));

 
    renderPost();
    renderComments();
  </script>
</body>
</html>
