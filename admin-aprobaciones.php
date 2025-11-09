<?php
declare(strict_types=1);

// ✅ Protección de sesión y acceso
require_once __DIR__ . '/config/auth_guard.php';
require_role('ADMIN');

// Base para rutas
$BASE = '/4everFootball';
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Panel de aprobaciones | 4everFootball</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="<?= $BASE ?>/css/styles.css?v=31">

  <style>
    /* Ajustes puntuales para tabla y modal */
    .ff-table thead th{ position:sticky; top:0; background:rgba(24,24,24,.9); backdrop-filter: blur(6px); }
    .ff-status-badge{ background:#222; color:#cbd5e1; border-radius:999px; padding:.15rem .55rem; font-size:.75rem; }
    .ff-status-aprobado{ background:#14532d; color:#dcfce7; }
    .ff-status-revision{ background:#facc15; color:#1f2937; }
    .ff-status-rechazado{ background:#7f1d1d; color:#fee2e2; }
    .ff-action{ white-space: nowrap; }
    .ff-kbd{ font: 500 .8rem ui-monospace, SFMono-Regular, Menlo, Monaco; background:#111827; color:#e5e7eb; padding:.15rem .35rem; border-radius:.35rem; }
    .ff-modal-media{ position:relative; width:100%; aspect-ratio:16/9; background:#0b1220; border-radius:10px; overflow:hidden; }
    .ff-modal-media img, .ff-modal-media video{ width:100%; height:100%; object-fit:cover; display:block; }
    .ff-modal-media.ff-empty{ background:linear-gradient(135deg,#0b1220 0%, #151e36 100%); }
    .ff-modal-media.ff-empty::after{ content:"Sin imagen/vídeo"; position:absolute; inset:0; display:flex; align-items:center; justify-content:center; color:#9aa0a6; font-weight:600; }

    #snackbar {
      visibility: hidden;
      min-width: 260px;
      background-color: #222;
      color: #fff;
      text-align: center;
      border-radius: 12px;
      padding: 14px;
      position: fixed;
      left: 50%;
      bottom: 40px;
      transform: translateX(-50%);
      z-index: 9999;
      font-weight: 500;
      opacity: 0;
      transition: all 0.4s ease;
      box-shadow: 0 4px 14px rgba(0, 0, 0, 0.3);
    }

    #snackbar.show {
      visibility: visible;
      opacity: 1;
      bottom: 60px;
    }

    #snackbar.success { background-color: #2ecc71; }
    #snackbar.error { background-color: #e74c3c; }
  </style>
</head>

<body class="ff-bg">

  <!-- HEADER sticky -->
  <header id="siteHeader" class="ff-header sticky-top">
    <div class="container d-flex align-items-center gap-3 py-2">
      <a href="<?= $BASE ?>/index.php" class="d-flex align-items-center gap-2 text-decoration-none">
        <img src="<?= $BASE ?>/img/logo.svg" alt="4everFootball" style="height:34px">
      </a>

      <form class="ms-auto me-auto w-50 d-none d-md-flex" role="search">
        <div class="input-group ff-search">
          <span class="input-group-text">🔎</span>
          <input type="search" class="form-control" placeholder="Buscar en 4everFootball…">
        </div>
      </form>

      <div>
        <a href="<?= $BASE ?>/logout.php" class="btn btn-outline-light btn-sm">Cerrar sesión</a>
      </div>
    </div>
  </header>

  <main class="container py-4 pb-5">

    <section class="glass-card p-3 p-md-4 mb-3">
      <div class="d-flex justify-content-between align-items-center">
        <h1 class="ff-title mb-0">Panel de aprobaciones</h1>
      </div>
    </section>

    <div id="adminMsg" class="small mb-3" role="alert" aria-live="polite"></div>

    <!-- FILTROS -->
    <section class="glass-card p-3 p-md-4 mb-3">
      <form id="filterForm" class="row g-2 align-items-end">
        <div class="col-12 col-md-4">
          <label class="form-label" for="q">Buscar</label>
          <input id="q" type="search" class="form-control" placeholder="Título, autor…">
        </div>
        <div class="col-6 col-md-2">
          <label class="form-label" for="cat">Categoría</label>
          <select id="cat" class="form-select">
            <option value="">Todas</option>
            <option value="jugadas">Jugadas</option>
            <option value="entrevistas">Entrevistas</option>
            <option value="estadisticas">Estadísticas</option>
            <option value="sedes">Sedes</option>
          </select>
        </div>
        <div class="col-6 col-md-2">
          <label class="form-label" for="sede">Sede/Año</label>
          <select id="sede" class="form-select">
            <option value="">Todas</option>
            <option value="1986">México 1986</option>
            <option value="1994">USA 1994</option>
            <option value="2014">Brasil 2014</option>
            <option value="2018">Rusia 2018</option>
            <option value="2022">Qatar 2022</option>
          </select>
        </div>
        <div class="col-6 col-md-2">
          <label class="form-label" for="estado">Estado</label>
          <select id="estado" class="form-select">
            <option value="">Todos</option>
            <option value="revision">En revisión</option>
            <option value="aprobado">Aprobado</option>
            <option value="rechazado">Rechazado</option>
          </select>
        </div>
        <div class="col-6 col-md-2">
          <label class="form-label" for="orden">Orden</label>
          <select id="orden" class="form-select">
            <option value="reciente">Más reciente</option>
            <option value="antiguo">Más antiguo</option>
            <option value="titulo">Título (A–Z)</option>
          </select>
        </div>

        <div class="col-12 d-flex justify-content-between gap-2">
          <div class="d-flex gap-2">
            <button id="bulkApprove" type="button" class="btn btn-login">Aprobar seleccionados</button>
            <button id="bulkReject" type="button" class="btn btn-outline-light">Rechazar seleccionados</button>
          </div>
          <div class="d-flex gap-2">
            <button class="btn btn-login" type="submit">Aplicar filtros</button>
            <button id="resetBtn" class="btn btn-outline-light" type="button">Limpiar</button>
          </div>
        </div>
      </form>
    </section>

    <!-- TABLA -->
    <section class="glass-card p-0">
      <div class="table-responsive">
        <table class="table table-dark table-hover align-middle mb-0 ff-table">
          <thead>
            <tr>
              <th style="width:36px;"><input id="checkAll" class="form-check-input" type="checkbox"></th>
              <th>Título</th>
              <th class="d-none d-md-table-cell">Autor</th>
              <th>Categoría</th>
              <th class="d-none d-sm-table-cell">Sede/Año</th>
              <th class="d-none d-lg-table-cell">Fecha</th>
              <th>Estado</th>
              <th class="text-end">Acciones</th>
            </tr>
          </thead>
          <tbody id="tbody">
            <!-- Rows generadas por JS -->
          </tbody>
        </table>
      </div>

      <!-- Footer tabla -->
      <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 p-3">
        <div class="text-secondary small">
          <span id="countSel">0</span> seleccionados ·
          Mostrando <span id="countPage">0</span> de <span id="countTotal">0</span>
        </div>
        <div class="d-flex align-items-center gap-2">
          <div class="text-secondary small me-2">Página <span id="pageNum">1</span></div>
          <button id="prevBtn" class="btn btn-outline-light btn-sm">« Anterior</button>
          <button id="nextBtn" class="btn btn-outline-light btn-sm">Siguiente »</button>
        </div>
      </div>
    </section>

  </main>

  <!-- MODAL -->
  <div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content bg-dark text-white border border-light-subtle">
        <div class="modal-header">
          <h5 class="modal-title" id="previewLabel">Vista previa</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <div id="modalMedia" class="ff-modal-media ff-empty mb-3"></div>
          <div class="d-flex flex-wrap gap-2 mb-2">
            <span id="modalEstado" class="ff-status-badge">Estado</span>
            <span id="modalCat" class="ff-status-badge">Categoría</span>
            <span id="modalSede" class="ff-status-badge">Sede</span>
          </div>
          <h3 id="modalTitle" class="h5 mb-1">Título</h3>
          <div id="modalMeta" class="text-secondary small mb-2">Autor · Fecha</div>
          <p id="modalDesc" class="mb-0">Descripción…</p>
        </div>
        <div class="modal-footer">
          <button id="modalApprove" type="button" class="btn btn-login">Aprobar</button>
          <button id="modalReject" type="button" class="btn btn-outline-light">Rechazar</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Snackbar -->
  <div id="snackbar"></div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="<?= $BASE ?>/js/aprobaciones.js"></script>

</body>
</html>
