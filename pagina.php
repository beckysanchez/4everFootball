<?php
session_start();
$BASE = '/4everFootball';
if (!isset($_SESSION['user']) || !$_SESSION['user']['isAdmin']) {
  header("Location: index.php");
  exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Crear comunidad | 4everFootball</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="<?= $BASE ?>/css/pagcss.css?v=41">
</head>
<body class="ff-bg">

<header id="siteHeader" class="ff-header sticky-top">
  <div class="container d-flex align-items-center gap-3 py-2">
    <a href="<?= $BASE ?>/index.php" class="d-flex align-items-center gap-2 text-decoration-none">
      <img src="<?= $BASE ?>/img/logo.svg?v=<?= time() ?>" alt="4everFootball" style="height:34px">
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
  <section class="glass-card p-4">
    <h1 class="mb-3">Crear comunidad</h1>
    <p class="text-secondary">Crea tu espacio de discusión con nombre, descripción, sede, logo y portada.</p>

    <form id="communityForm" novalidate>
      <!-- Nombre -->
      <div class="mb-3">
        <label for="nombre" class="form-label">Nombre de la comunidad</label>
        <input id="nombre" name="nombre" type="text" class="form-control" required minlength="3" maxlength="60" placeholder="Ej. Sudáfrica 2010">
        <div class="invalid-feedback">El nombre debe tener entre 3 y 60 caracteres.</div>
      </div>

      <!-- Descripción -->
      <div class="mb-3">
        <label for="descripcion" class="form-label">Descripción</label>
        <textarea id="descripcion" name="descripcion" rows="4" class="form-control" maxlength="500" placeholder="Cuéntanos de qué trata tu comunidad…"></textarea>
        <div class="form-text"><span id="descCount">0</span>/500</div>
      </div>

      <!-- Sede -->
      <div class="mb-3">
        <label for="sede" class="form-label">Sede</label>
        <input id="sede" name="sede" type="text" class="form-control" placeholder="Ej. Sudáfrica">
      </div>

      <!-- Logo -->
      <div class="mb-3">
        <label for="logo" class="form-label">Logo de la comunidad</label>
        <div class="logo-preview" id="logoPreview">Logo</div>
        <input id="logo_url" name="logo_url" type="url" class="form-control mb-2" placeholder="Pega una URL (opcional)">
        <input id="logo_file" name="logo_file" type="file" class="form-control" accept="image/*">
        <div class="form-text">Puedes subir una imagen JPG o PNG, o pegar una URL.</div>
      </div>

      <!-- Portada -->
      <div class="mb-3">
        <label for="portada" class="form-label">Portada</label>
        <div class="logo-preview" id="portadaPreview">Portada</div>
        <input id="portada_url" name="portada_url" type="url" class="form-control mb-2" placeholder="Pega una URL (opcional)">
        <input id="portada_file" name="portada_file" type="file" class="form-control" accept="image/*">
        <div class="form-text">Puedes subir una imagen JPG o PNG, o pegar una URL.</div>
      </div>
      <!-- Botones -->
      <div class="d-flex justify-content-end gap-2 mt-4">
        <a href="<?= $BASE ?>/index.php" class="btn btn-outline-light">Cancelar</a>
        <button class="btn btn-success" type="submit">Crear comunidad</button>
      </div>
    </form>
  </section>
</main>

<!-- Snackbar -->
<div id="snackbar" class="position-fixed bottom-0 start-50 translate-middle-x text-center bg-success text-white py-2 px-4 rounded" style="display:none; z-index:9999;">✅ Comunidad creada correctamente</div>

<script>
const form = document.getElementById('communityForm');
const desc = document.getElementById('descripcion');
const descCount = document.getElementById('descCount');
const snackbar = document.getElementById('snackbar');

desc.addEventListener('input', () => descCount.textContent = desc.value.length);

// Slug generator
function generarSlug(nombre) {
  return nombre.toLowerCase()
    .normalize("NFD").replace(/[\u0300-\u036f]/g, "")
    .replace(/[^a-z0-9]+/g, "-")
    .replace(/^-+|-+$/g, "");
}
// Envío del formulario
form.addEventListener('submit', async (e) => {
  e.preventDefault();
  const data = {
    nombre_comunidad: form.nombre.value.trim(),
    descripcion: form.descripcion.value.trim(),
    sede: form.sede.value.trim(),
    logo_url: form.logo_url.value.trim(),
    portada_url: form.portada_url.value.trim(),
    slug: generarSlug(form.nombre.value.trim())
  };

  if (!data.nombre_comunidad || !data.descripcion) {
    alert("Por favor, completa al menos el nombre y la descripción.");
    return;
  }

  try {
    if (form.logo_file.files[0]) data.logo_url = await uploadImage(form.logo_file.files[0]);
    if (form.portada_file.files[0]) data.portada_url = await uploadImage(form.portada_file.files[0]);

    const res = await fetch('<?= $BASE ?>/api/mundial_create.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(data),
      credentials: 'include'
    });

    const text = await res.text();
    let json;
    try { json = JSON.parse(text); }
    catch {
      console.error("⚠️ Respuesta inválida del servidor:", text);
      alert("Error: respuesta del servidor no válida. Revisa consola (F12).");
      return;
    }

    if (json.ok) {
      snackbar.textContent = "✅ Comunidad creada correctamente";
      snackbar.style.display = 'block';
      setTimeout(() => snackbar.style.display = 'none', 3000);
      form.reset();
      descCount.textContent = "0";
      document.getElementById('logoPreview').textContent = 'Logo';
      document.getElementById('portadaPreview').textContent = 'Portada';
    } else {
      alert("Error: " + (json.error || "No se pudo crear la comunidad"));
    }

  } catch (err) {
    alert("Error al conectar con el servidor: " + err.message);
  }
});

// Función de subida de imagen
async function uploadImage(file) {
  const fd = new FormData();
  fd.append('file', file);
  const res = await fetch('<?= $BASE ?>/api/upload_image.php', {
    method: 'POST',
    body: fd,
    credentials: 'include'
  });
  const data = await res.json();
  if (data.ok) return data.url;
  throw new Error(data.error || 'Error al subir imagen');
}

// Validación de fuentes de imagen
function toggleImageInputs(urlInput, fileInput) {
  urlInput.addEventListener("input", () => {
    if (urlInput.value.trim() !== "") {
      fileInput.disabled = true;
      fileInput.value = "";
    } else fileInput.disabled = false;
  });
  fileInput.addEventListener("change", () => {
    if (fileInput.files.length > 0) {
      urlInput.disabled = true;
      urlInput.value = "";
    } else urlInput.disabled = false;
  });
}

toggleImageInputs(document.getElementById("logo_url"), document.getElementById("logo_file"));
toggleImageInputs(document.getElementById("portada_url"), document.getElementById("portada_file"));

// Validar resolución mínima
function validarResolucionMinima(fileInput, minWidth, minHeight, tipo) {
  fileInput.addEventListener("change", function() {
    const file = this.files[0];
    if (!file) return;
    const img = new Image();
    img.src = URL.createObjectURL(file);
    img.onload = function() {
      if (img.width < minWidth || img.height < minHeight) {
        alert(`❌ La imagen del ${tipo} es demasiado pequeña.\nMínimo: ${minWidth}x${minHeight}px\nTu imagen: ${img.width}x${img.height}px`);
        fileInput.value = "";
      }
      URL.revokeObjectURL(img.src);
    };
  });
}

validarResolucionMinima(document.getElementById("logo_file"), 300, 300, "logo");
validarResolucionMinima(document.getElementById("portada_file"), 1200, 400, "portada");
</script>
</body>
</html>
