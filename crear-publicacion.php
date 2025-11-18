<?php
session_start();
if (empty($_SESSION['user']['id'])) {
  header("Location: login.php");
  exit;
}
$usuario_id = $_SESSION['user']['id'];
$mundial_id = $_GET['mundial_id'] ?? null;
if (!$mundial_id) {
  echo "<p>Error: No se especificó el mundial.</p>";
  exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Crear publicación | 4everFootball</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="./css/styles.css?v=42">
  <style>
    .ff-post-media { position:relative; width:100%; aspect-ratio:16/9; background:#0b1220; border-radius:12px; overflow:hidden; }
    .ff-post-media img, .ff-post-media video { width:100%; height:100%; object-fit:cover; display:block; }
    .ff-post-media.ff-empty { background:linear-gradient(135deg,#0b1220 0%, #151e36 100%); }
    .ff-post-media.ff-empty::after {
      content:"Previsualización (sin archivo)";
      position:absolute; inset:0; display:flex;
      align-items:center; justify-content:center;
      color:#9aa0a6; font-weight:600;
    }
  </style>
</head>

<body class="ff-bg">

<header id="siteHeader" class="ff-header sticky-top">
  <div class="container d-flex align-items-center gap-3 py-2">
    <a href="index.php" class="d-flex align-items-center gap-2 text-decoration-none">
      <img src="img/logo.svg" alt="4everFootball" style="height:34px">
    </a>
    <form id="headerSearch" class="ms-auto me-auto w-50" role="search" method="GET" action="<?= $BASE ?>/index.php">
  <div class="input-group ff-search w-100" 
       style="background:#2a2a2a; border-radius:2rem; overflow:hidden; border:1px solid #444;">
    <span class="input-group-text" 
          style="background:transparent; border:none; color:#bbb; padding-left:1rem;">
      🔎
    </span>
    <input
      id="qHeader"
      type="search"
      name="q"
      class="form-control"
      placeholder="Buscar en 4everFootball…"
      value="<?= htmlspecialchars($_GET['q'] ?? '') ?>"
      style="background:transparent; border:none; color:white; box-shadow:none;"
    >
  </div>
</form>

  </div>
</header>

<main class="container py-4 pb-5">

  <section class="glass-card p-3 p-md-4 mb-4">
    <h1 class="ff-title mb-2">Crear publicación</h1>
    <p class="text-secondary mb-0">
      Tu contenido se publicará con estado 
      <span class="ff-chip">En revisión</span> hasta que un administrador lo apruebe.
    </p>
  </section>

  <div id="pubMsg" class="alert d-none" role="alert"></div>

  <form id="pubForm" class="glass-card p-3 p-md-4" enctype="multipart/form-data">
    <input type="hidden" name="mundial_id" value="<?php echo htmlspecialchars($mundial_id); ?>">
    <input type="hidden" name="usuario_id" value="<?php echo htmlspecialchars($usuario_id); ?>">

    <div class="row g-3">
      <div class="col-12">
        <label for="titulo" class="form-label">Título</label>
        <input id="titulo" name="titulo" type="text" class="form-control" required minlength="4" maxlength="120" placeholder="Ej. Golazo de volea en 1986">
        <div class="invalid-feedback">El título debe tener al menos 4 caracteres.</div>
      </div>

      <div class="col-12 col-md-6">
        <label for="categoria" class="form-label">Categoría</label>
        <select id="categoria" name="categoria_id" class="form-select" required>
          <option value="" disabled selected>Selecciona…</option>
          <option value="1">Jugadas históricas</option>
          <option value="2">Entrevistas</option>
          <option value="3">Sedes y estadios</option>
          <option value="4">Finales míticas</option>
          <option value="5">Goles icónicos</option>
        </select>
        <div class="invalid-feedback">Selecciona una categoría.</div>
      </div>

      <div class="col-12">
        <label for="descripcion" class="form-label">Descripción</label>
        <textarea id="descripcion" name="descripcion" class="form-control" rows="4" minlength="10" maxlength="800" placeholder="Cuenta el contexto de la jugada, entrevista o estadística." required></textarea>
        <div class="form-text"><span id="descCount">0</span>/800</div>
      </div>

      <div class="col-12 col-md-6">
        <label for="file" class="form-label">Archivo (imagen o video)</label>
        <input id="file" name="file" type="file" class="form-control" accept="image/*,video/mp4,video/webm" required>
        <div class="form-text">Máx. 20MB. Tipos: imágenes o MP4/WebM.</div>
      </div>

      <div class="col-12">
        <label class="form-label">Previsualización</label>
        <div id="preview" class="ff-post-media ff-empty"></div>
      </div>

      <div class="col-12">
        <div class="form-check">
          <input id="rights" type="checkbox" class="form-check-input" required>
          <label for="rights" class="form-check-label">
            Declaro que tengo derechos para publicar este contenido y acepto las normas de la comunidad.
          </label>
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

<script>
document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("pubForm");
  const preview = document.getElementById("preview");
  const desc = document.getElementById("descripcion");
  const descCount = document.getElementById("descCount");
  const msgBox = document.getElementById("pubMsg");
  const fileInput = document.getElementById("file");
  const submitBtn = document.getElementById("submitBtn");

  // === Contador descripción ===
  desc.addEventListener("input", () => {
    descCount.textContent = desc.value.length;
    if (desc.value.length > 800) desc.value = desc.value.substring(0, 800);
  });

  // === Previsualización automática ===
  fileInput.addEventListener("change", () => {
    const file = fileInput.files[0];
    preview.innerHTML = "";
    preview.classList.remove("ff-empty");

    if (!file) {
      preview.classList.add("ff-empty");
      return;
    }

    const blobURL = URL.createObjectURL(file);

    if (file.type.startsWith("video/")) {
      const video = document.createElement("video");
      video.src = blobURL;
      video.controls = true;
      video.autoplay = true;
      video.muted = true;
      preview.appendChild(video);
    } else if (file.type.startsWith("image/")) {
      const img = document.createElement("img");
      img.src = blobURL;
      img.alt = "Previsualización";
      img.style.objectFit = "cover";
      preview.appendChild(img);
    } else {
      preview.classList.add("ff-empty");
      preview.innerHTML = "<p class='text-center text-secondary mt-3'>Formato no compatible</p>";
    }
  });

  // === Envío del formulario ===
  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    form.classList.add("was-validated");
    msgBox.classList.add("d-none");

    if (!form.checkValidity()) {
      showMsg("Completa todos los campos requeridos.", "danger");
      return;
    }

    const file = fileInput.files[0];
    if (!file) {
      showMsg("Debes subir un archivo (imagen o video).", "danger");
      return;
    }

    if (file.size > 20 * 1024 * 1024) {
      showMsg("El archivo supera los 20MB permitidos.", "danger");
      return;
    }

    const formData = new FormData(form);

    try {
      // Desactivar el botón mientras se envía
      submitBtn.disabled = true;
      submitBtn.textContent = "Publicando...";
      showMsg("⏳ Enviando publicación...", "info");

      const res = await fetch("api/publicacion_create.php", { method: "POST", body: formData });
      const data = await res.json();

      if (data.ok) {
        showMsg("✅ Tu publicación ha sido enviada para revisión.", "success");
        form.reset();
        preview.classList.add("ff-empty");
        preview.innerHTML = "";
        descCount.textContent = "0";
      } else {
        showMsg("⚠️ " + (data.error || "No se pudo crear la publicación."), "danger");
      }
    } catch (err) {
      showMsg("❌ Error de conexión con el servidor.", "danger");
      console.error(err);
    } finally {
      // Reactivar botón siempre
      submitBtn.disabled = false;
      submitBtn.textContent = "Publicar";
    }
  });

  // === Mostrar mensajes dinámicos ===
  function showMsg(text, type = "info") {
    msgBox.className = `alert alert-${type}`;
    msgBox.textContent = text;
    msgBox.classList.remove("d-none");
  }
});
</script>


</body>
</html>
