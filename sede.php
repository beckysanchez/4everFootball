<?php
  $BASE = '/4everFootball';
  $slug = $_GET['slug'] ?? 'sudafrica-2010';

  
  $SEDE = [
    'sudafrica-2010' => [
      'nombre' => 'Sudáfrica 2010',
      'cover'  => $BASE . '/img/sample3.jpg?v=' . time(),  // banner
      'avatar' => $BASE . '/img/2010.png?v=' . time(),     // logo sede
      'about'  => 'La Copa del Mundo celebrada en Sudáfrica. Vuvuzelas, el Jabulani y un torneo inolvidable.'
    ],
  ];
  $meta = $SEDE[$slug] ?? null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= $meta ? $meta['nombre'] : 'Sede en construcción' ?> | 4everFootball</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="<?= $BASE ?>/css/index.css?v=40">
</head>
<body class="ff-bg">

  <!-- Header igual al de index -->
  <header id="siteHeader" class="ff-header sticky-top">
    <div class="container d-flex align-items-center gap-3 py-2">
      <a href="<?= $BASE ?>/index.php" class="d-flex align-items-center gap-2 text-decoration-none">
        <img src="<?= $BASE ?>/img/logo.svg?v=<?= time() ?>" alt="4everFootball" style="height:34px">
      </a>
      <form id="headerSearch" class="ms-auto me-auto w-50 d-flex" role="search">
        <div class="input-group ff-search w-100">
          <span class="input-group-text">🔎</span>
          <input id="qHeader" type="search" class="form-control" placeholder="Buscar en 4everFootball…">
        </div>
      </form>
      <nav class="d-flex align-items-center gap-2">
        <a class="btn btn-register" href="<?= $BASE ?>/crear-publicacion.php">Publicar</a>
        <div class="ff-profile position-relative">
          <button id="profileBtn" class="ff-avatar-btn" type="button" aria-haspopup="true" aria-expanded="false" aria-controls="profileMenu" title="Cuenta">
            <img src="<?= $BASE ?>/img/icon_iniciarsesion.png?v=1" alt="Perfil" class="ff-avatar-img" width="36" height="36"
                 onerror="this.style.visibility='hidden';this.parentElement.classList.add('ff-avatar-fallback');">
          </button>
          <div id="profileMenu" class="ff-dropdown" role="menu" aria-labelledby="profileBtn"></div>
        </div>
      </nav>
    </div>
  </header>

  <main class="container pb-5">

    <?php if ($meta): ?>
    
    <section class="my-3 glass-card overflow-hidden">
      <div style="height:220px; background:url('<?= $meta['cover'] ?>') center/cover; filter:brightness(.85)"></div>
      <div class="p-3 p-md-4 d-flex align-items-center gap-3">
        <img src="<?= $meta['avatar'] ?>" alt="<?= $meta['nombre'] ?>" width="72" height="72" style="border-radius:50%; background:#111; padding:6px; box-shadow:0 0 0 2px rgba(92,214,92,.7)">
        <div class="flex-grow-1">
          <h1 class="ff-title m-0"><?= $meta['nombre'] ?></h1>
          <small class="text-secondary">Página de sede · Mundial</small>
        </div>
        <a class="btn btn-login" href="#destacados">Ver destacados</a>
      </div>
    </section>

    
    <section class="glass-card p-3 p-md-4 my-3">
      <p class="m-0"><?= htmlspecialchars($meta['about']) ?></p>
    </section>

    
    <section class="ff-feed">
      <div id="cards" class="d-flex flex-column gap-4"></div>
      <div id="empty" class="glass-card p-4 mt-3 d-none">
        <p class="mb-1">Aún no hay publicaciones para esta sede.</p>
        <small class="text-secondary">Vuelve más tarde.</small>
      </div>
    </section>

    <?php else: ?>
      <section class="glass-card p-4 my-4">
        <h1 class="ff-title mb-2">Sede en construcción</h1>
        <p class="mb-3">Aún no tenemos la página de esta sede. Pronto estará lista.</p>
        <a class="btn btn-register" href="<?= $BASE ?>/index.php">Volver al inicio</a>
      </section>
    <?php endif; ?>

  </main>

  <script>
    const BASE = '<?= $BASE ?>';
    const SLUG = '<?= $slug ?>';

  
    const DATA = [
      { id:1, titulo:'Golazo inaugural 2010', categoria:'jugadas', sede:'2010', sedeNombre:'Sudáfrica 2010', sedeSlug:'sudafrica-2010', estado:'aprobado', likes:420, comentarios:65, fecha:'2010-06-11', mediaType:'image', src:`${BASE}/img/sample1.jpg?v=<?= time() ?>` },
      { id:2, titulo:'Entrevista al crack 2014', categoria:'entrevistas', sede:'2014', sedeNombre:'Brasil 2014',    sedeSlug:'brasil-2014',    estado:'aprobado', likes:120, comentarios:10, fecha:'2014-07-10', mediaType:'video', src:`${BASE}/img/sample2.mp4?v=<?= time() ?>`, poster:`${BASE}/img/poster2.jpg?v=<?= time() ?>` },
      { id:3, titulo:'Fiesta en Lusail 2022',   categoria:'sedes',      sede:'2022', sedeNombre:'Qatar 2022',      sedeSlug:'qatar-2022',    estado:'aprobado', likes:980, comentarios:112, fecha:'2022-12-18', mediaType:'image', src:`${BASE}/img/sample4.jpg?v=<?= time() ?>` },
      { id:4, titulo:'Final en Luzhnikí 2018',  categoria:'sedes',      sede:'2018', sedeNombre:'Rusia 2018',      sedeSlug:'rusia-2018',    estado:'aprobado', likes:210, comentarios:34, fecha:'2018-07-15', mediaType:'image', src:`${BASE}/img/sample1.jpg?v=<?= time() ?>` },
    ];

  
    const cardsEl = document.getElementById('cards');
    const emptyEl = document.getElementById('empty');

    function badgeEstado(row){
      return row.estado === 'aprobado'
        ? '<span class="ff-chip">Aprobado</span>'
        : '<span class="ff-chip">En revisión</span>';
    }
    function mediaBlock(row){
      const hasSrc = !!row.src;
      if (row.mediaType === 'video') {
        return `<div class="ff-post-media ${hasSrc ? '' : 'ff-empty'}">
          ${hasSrc ? `<video src="${row.src}" ${row.poster?`poster="${row.poster}"`:''} controls playsinline></video>` : ''}</div>`;
      }
      return `<div class="ff-post-media ${hasSrc ? '' : 'ff-empty'}">
        ${hasSrc ? `<img src="${row.src}" alt="${row.titulo}" onerror="this.closest('.ff-post-media').classList.add('ff-empty'); this.remove();">` : ''}</div>`;
    }
    function card(row){
      return `
        <article class="ff-post" id="destacados">
          <div class="ff-post-header">
            <div class="ff-post-meta">
              <div class="ff-avatar"></div>
              <div>
                <div class="d-flex align-items-center gap-2">
                  <strong>${row.titulo}</strong>
                  ${badgeEstado(row)}
                </div>
                <div class="ff-post-sub">
                  <a class="ff-group-link-mini" href="${BASE}/sede.php?slug=${encodeURIComponent(row.sedeSlug)}">${row.sedeNombre}</a>
                  · ${new Date(row.fecha).toLocaleDateString()}
                </div>
              </div>
            </div>
            <span class="ff-chip text-uppercase">${row.categoria}</span>
          </div>
          ${mediaBlock(row)}
          <div class="ff-actions">
            <button class="btn btn-outline-light">👍 ${row.likes}</button>
            <a class="btn btn-login" href="${BASE}/detalle-publicacion.php?id=${row.id}">Comentar</a>
          </div>
        </article>`;
    }

    const list = DATA.filter(r => r.sedeSlug === SLUG);
    if (list.length === 0){
      emptyEl.classList.remove('d-none');
    } else {
      cardsEl.innerHTML = list.map(card).join('');
    }
//hola profe
  </script>
</body>
</html>
