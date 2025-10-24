<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Crear comunidad | 4everFootball</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="./css/pagcss.css?v=32">

 
  
</head>
<body>
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

  <main class="container py-5">
    <section class="glass-card">
      <h1 class="mb-3">Crear comunidad</h1>
      <p class="text-secondary">Crea tu espacio de discusión (tipo subreddit) con un nombre, descripción, sede y un logo.</p>

      <form id="communityForm" action="index.php" method="post" enctype="multipart/form-data" novalidate>
        <!-- Nombre -->
        <div class="mb-3">
          <label for="name" class="form-label">Nombre de la comunidad</label>
          <input id="name" name="name" type="text" class="form-control" required minlength="3" maxlength="60" placeholder="Ej. FutbolRetro">
          <div class="invalid-feedback">El nombre debe tener entre 3 y 60 caracteres.</div>
        </div>

        <div class="mb-3">
          <label for="description" class="form-label">Descripción</label>
          <textarea id="description" name="description" rows="4" class="form-control" maxlength="500" placeholder="Cuéntanos de qué trata tu comunidad…"></textarea>
          <div class="form-text"><span id="descCount">0</span>/500</div>
        </div>

        <div class="mb-3">
          <label for="sede" class="form-label">Sede</label>
          <select id="sede" name="sede" class="form-select" required>
            <option value="" disabled selected>Selecciona…</option>
            <option value="mexico">México</option>
            <option value="usa">USA</option>
            <option value="brasil">Brasil</option>
            <option value="rusia">Rusia</option>
            <option value="qatar">Qatar</option>
          </select>
          <div class="invalid-feedback">Selecciona una sede.</div>
        </div>

<div class="mb-3">
  <label for="categoria" class="form-label">Categoría</label>
  <select id="categoria" name="categoria" class="form-select" required>
    <option value="" disabled selected>Selecciona…</option>
    <option value="cultura-internet">Cultura de internet</option>
    <option value="juegos">Juegos</option>
    <option value="preguntas-respuestas">Preguntas y respuestas</option>
    <option value="tecnologia">Tecnología</option>
    <option value="cultura-popular">Cultura popular</option>
    <option value="peliculas-tv">Películas y TV</option>
  </select>
  <div class="invalid-feedback">Selecciona una categoría.</div>
</div>


     
        <div class="mb-3">
          <label for="logo" class="form-label">Logo de la comunidad</label>
          <div class="logo-preview" id="logoPreview">Logo</div>
          <input id="logo" name="logo" type="file" class="form-control" accept="image/*">
          <div class="form-text">Formato JPG o PNG, máximo 5MB.</div>
        </div>

        <div class="d-flex justify-content-end gap-2">
          <a href="index.php" class="btn btn-outline-light">Cancelar</a>
          <button class="btn btn-primary" type="submit">Crear comunidad</button>
        </div>
      </form>
    </section>
  </main>

  <script>
    const desc = document.getElementById('description');
    const descCount = document.getElementById('descCount');
    const logoInput = document.getElementById('logo');
    const logoPreview = document.getElementById('logoPreview');


    desc.addEventListener('input', () => {
      descCount.textContent = desc.value.length;
    });

   
    logoInput.addEventListener('change', () => {
      const file = logoInput.files?.[0];
      if (file && file.type.startsWith('image/')) {
        const src = URL.createObjectURL(file);
        logoPreview.innerHTML = `<img src="${src}" alt="Logo">`;
      } else {
        logoPreview.textContent = "Logo";
      }
    });

const onScroll = () => {
  if (window.scrollY > 6) el.header.classList.add('ff-header-scrolled');
  else el.header.classList.remove('ff-header-scrolled');
};
document.addEventListener('scroll', onScroll); onScroll();


  </script>

</body>
</html>
