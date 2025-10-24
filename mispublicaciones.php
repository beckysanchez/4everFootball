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

  <link rel="stylesheet" href="<?= $BASE ?>/css/styles.css?v=37" />
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
  
<main>
<div class="row justify-content-center">
 
  <div class="col-12 col-md-6 col-lg-4 mb-4">
    <div class="card ff-post" style="border-radius:0.5rem; overflow:hidden;">
      <img src="img/sample1.jpg" class="card-img-top" alt="Gol histórico 1986" style="height:200px; object-fit:cover; width:100%;">
      <div class="card-body p-2">
        <div class="d-flex justify-content-between align-items-start mb-1">
          <strong>Gol histórico 1986</strong>
          <span class="ff-chip" style="font-size:0.75rem;">Aprobado</span>
        </div>
        <div class="ff-post-sub mb-2" style="font-size:0.8rem;">México 1986 · 22/06/1986</div>
        <span class="ff-chip text-uppercase" style="font-size:0.7rem;">jugadas</span>
        <div class="d-flex gap-1 mt-2">
          <button class="btn btn-outline-light like-btn p-1" style="font-size:0.8rem;">👍 <span class="like-count">532</span></button>
          <button class="btn btn-login comment-btn p-1" style="font-size:0.8rem;">Comentar</button>
        </div>
      </div>
    </div>
  </div>

  <div class="col-12 col-md-6 col-lg-4 mb-4">
    <div class="card ff-post" style="border-radius:0.5rem; overflow:hidden;">
      <video src="img/sample2.mp4" poster="img/poster2.jpg" controls playsinline class="card-img-top" style="height:200px; object-fit:cover; width:100%;"></video>
      <div class="card-body p-2">
        <div class="d-flex justify-content-between align-items-start mb-1">
          <strong>Entrevista al crack 2014</strong>
          <span class="ff-chip" style="font-size:0.75rem;">Aprobado</span>
        </div>
        <div class="ff-post-sub mb-2" style="font-size:0.8rem;">Brasil 2014 · 10/07/2014</div>
        <span class="ff-chip text-uppercase" style="font-size:0.7rem;">entrevistas</span>
        <div class="d-flex gap-1 mt-2">
          <button class="btn btn-outline-light like-btn p-1" style="font-size:0.8rem;">👍 <span class="like-count">120</span></button>
          <button class="btn btn-login comment-btn p-1" style="font-size:0.8rem;">Comentar</button>
        </div>
      </div>
    </div>
  </div>

  <div class="col-12 col-md-6 col-lg-4 mb-4">
    <div class="card ff-post" style="border-radius:0.5rem; overflow:hidden;">
      <img src="img/sample4.jpg" class="card-img-top" alt="Estadio icónico 1994" style="height:200px; object-fit:cover; width:100%;">
      <div class="card-body p-2">
        <div class="d-flex justify-content-between align-items-start mb-1">
          <strong>Estadio icónico 1994</strong>
          <span class="ff-chip" style="font-size:0.75rem;">Aprobado</span>
        </div>
        <div class="ff-post-sub mb-2" style="font-size:0.8rem;">USA 1994 · 20/06/1994</div>
        <span class="ff-chip text-uppercase" style="font-size:0.7rem;">sedes</span>
        <div class="d-flex gap-1 mt-2">
          <button class="btn btn-outline-light like-btn p-1" style="font-size:0.8rem;">👍 <span class="like-count">210</span></button>
          <button class="btn btn-login comment-btn p-1" style="font-size:0.8rem;">Comentar</button>
        </div>
      </div>
    </div>
  </div>
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

</body>
</html>
