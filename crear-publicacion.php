<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Crear publicación | 4everFootball</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="./css/styles.css?v=32">

  <style>
    .ff-post-media{ position:relative; width:100%; aspect-ratio:16/9; background:#0b1220; border-radius:12px; overflow:hidden; }
    .ff-post-media img,.ff-post-media video{ width:100%; height:100%; object-fit:cover; display:block; }
    .ff-post-media.ff-empty{ background:linear-gradient(135deg,#0b1220 0%, #151e36 100%); }
    .ff-post-media.ff-empty::after{
      content:"Previsualización (sin archivo)"; position:absolute; inset:0; display:flex;
      align-items:center; justify-content:center; color:#9aa0a6; font-weight:600;
    }
  </style>
</head>
<body class="ff-bg">

  <!-- HEADER sticky -->
  <header id="siteHeader" class="ff-header sticky-top">
  <div class="container d-flex align-items-center gap-3 py-2">
    <a href="index.php" class="d-flex align-items-center gap-2 text-decoration-none">
      <img src="img/logo.svg" alt="4everFootball" style="height:34px">
    </a>

    <form class="ms-auto me-auto w-50 d-none d-md-flex" role="search">
      <div class="input-group ff-search">
        <span class="input-group-text">🔎</span>
        <input type="search" class="form-control" placeholder="Buscar en 4everFootball…">
      </div>
    </form>

    
  </div>
</header>

  <main class="container py-4 pb-5">

    <section class="glass-card p-3 p-md-4 mb-4">
      <h1 class="ff-title mb-2">Crear publicación</h1>
      <p class="text-secondary mb-0">Tu contenido se publicará con estado <span class="ff-chip">En revisión</span> hasta que un administrador lo apruebe.</p>
    </section>

    <div id="pubMsg" class="small mb-3" role="alert" aria-live="polite"></div>

    <form id="pubForm" class="glass-card p-3 p-md-4" action="index.php" method="post" enctype="multipart/form-data" novalidate>
      <div class="row g-3">

        <div class="col-12">
          <label for="title" class="form-label">Título</label>
          <input id="title" name="title" type="text" class="form-control" required minlength="4" maxlength="120" placeholder="Ej. Golazo de volea en 1986">
          <div class="invalid-feedback">El título debe tener al menos 4 caracteres.</div>
        </div>

        <div class="col-12 col-md-4">
          <label for="cat" class="form-label">Categoría</label>
          <select id="cat" name="cat" class="form-select" required>
            <option value="" selected disabled>Selecciona…</option>
            <option value="jugadas">Jugadas</option>
            <option value="entrevistas">Entrevistas</option>
            <option value="estadisticas">Estadísticas</option>
            <option value="sedes">Sedes</option>
          </select>
          <div class="invalid-feedback">Selecciona una categoría.</div>
        </div>

        <div class="col-12 col-md-4">
          <label for="sede" class="form-label">Sede / Año</label>
          <select id="sede" name="sede" class="form-select" required>
            <option value="" selected disabled>Selecciona…</option>
            <option value="1986">México 1986</option>
            <option value="1994">USA 1994</option>
            <option value="2014">Brasil 2014</option>
            <option value="2018">Rusia 2018</option>
            <option value="2022">Qatar 2022</option>
          </select>
          <div class="invalid-feedback">Selecciona una sede/año.</div>
        </div>

        <div class="col-12 col-md-4">
          <label for="mediaType" class="form-label">Tipo de media</label>
          <select id="mediaType" name="mediaType" class="form-select" required>
            <option value="" selected disabled>Selecciona…</option>
            <option value="image">Imagen</option>
            <option value="video">Video</option>
          </select>
          <div class="invalid-feedback">Selecciona el tipo de media.</div>
        </div>

        <div class="col-12">
          <label for="desc" class="form-label">Descripción</label>
          <textarea id="desc" name="desc" class="form-control" rows="4" minlength="10" maxlength="800" placeholder="Cuenta el contexto de la jugada, entrevista o estadística."></textarea>
          <div class="form-text"><span id="descCount">0</span>/800</div>
        </div>

        <div class="col-12 col-md-6">
          <label for="file" class="form-label">Archivo (imagen o video)</label>
          <input id="file" name="file" type="file" class="form-control" accept="image/*,video/mp4,video/webm">
          <div class="form-text">Máx. 20MB. Tipos: imágenes o MP4/WebM.</div>
          <div class="invalid-feedback">Archivo inválido o demasiado grande.</div>
        </div>

        <div class="col-12 col-md-6">
          <label for="url" class="form-label">o URL de la media</label>
          <input id="url" name="url" type="url" class="form-control" placeholder="https://…">
          <div class="form-text">Si subes archivo, ignoraremos la URL.</div>
          <div class="invalid-feedback">Introduce una URL válida (http/https).</div>
        </div>

        <div class="col-12">
          <label class="form-label">Previsualización</label>
          <div id="preview" class="ff-post-media ff-empty"></div>
        </div>

        <div class="col-12">
          <div class="form-check">
            <input id="rights" type="checkbox" class="form-check-input" required>
            <label for="rights" class="form-check-label">Declaro que tengo derechos para publicar este contenido y acepto las normas de la comunidad.</label>
            <div class="invalid-feedback">Debes aceptar esta declaración.</div>
          </div>
        </div>

        <div class="col-12 d-flex justify-content-end gap-2">
          <a href="index.php" class="btn btn-outline-light">Cancelar</a>
          <button id="submitBtn" class="btn btn-login" type="submit">Publicar</button>
        </div>
      </div>
    </form>
  </main>
 <script src="js/crear-publicacion.js"></script>
</body>
</html>
