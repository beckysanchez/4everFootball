<?php
  session_start();
  $BASE = '/4everFootball';

  // CSRF simple
  if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
  }
  $CSRF = $_SESSION['csrf'];

  // Soporta ?next=/ruta-a-volver (fallback al inicio)
  $next = isset($_GET['next']) && is_string($_GET['next']) ? $_GET['next'] : "$BASE/index.php";
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Iniciar sesión | 4everFootball</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Tus estilos -->
  <link rel="stylesheet" href="<?= $BASE ?>/css/iniciaRegister.css?v=43">
</head>

<body class="ff-bg">
  <div class="container py-4">
    <div class="row min-vh-100 align-items-center g-5">

      <!-- IZQUIERDA: logo + frase -->
      <div class="col-12 col-md-6 text-center text-md-start">
        <div class="brand-wrap">
          <img src="<?= $BASE ?>/img/logo.svg" alt="4everFootball" class="brand-logo">
          <p class="brand-tagline">
            Las noticias más emocionantes del fútbol, <span class="em">por siempre</span>.
          </p>
        </div>
      </div>


            <!-- DERECHA: tarjeta de login -->
      <div class="col-12 col-md-6 d-flex justify-content-center">
        <main class="auth-card glass-card card shadow p-4 w-100">
          <h1 class="ff-title text-center mb-3">Iniciar sesión</h1>

          <div id="message" class="small mb-3" role="alert" aria-live="polite"></div>

          <form id="loginForm" action="<?= $BASE ?>/api/login.php" method="post" novalidate autocomplete="off">
            <!-- Seguridad y retorno -->
            <input type="hidden" name="csrf" value="<?= htmlspecialchars($CSRF) ?>">
            <input type="hidden" name="next" value="<?= htmlspecialchars($next) ?>">

            <div class="mb-3">
              <label for="email" class="form-label">Correo electrónico</label>
              <input type="email" class="form-control" id="email" name="email" required
                     autocomplete="username" autocapitalize="none" spellcheck="false"
                     inputmode="email" aria-describedby="emailHelp">
              <div id="emailHelp" class="form-text">Ej. nombre@correo.com</div>
              <div class="invalid-feedback">Ingresa un correo válido.</div>
            </div>

            <div class="mb-3">
              <label for="password" class="form-label">Contraseña</label>
              <div class="input-group">
                <input type="password" class="form-control" id="password" name="password"
                       required minlength="8" autocomplete="current-password" aria-describedby="pwdHelp">

                <button type="button" class="btn btn-outline-light eye-btn px-3" id="togglePwd"
                        aria-label="Mostrar contraseña" aria-pressed="false">
                  <!-- eye -->
                  <svg class="icon icon-eye" width="18" height="18" viewBox="0 0 24 24" fill="none">
                    <path d="M12 5c5.7 0 10 5.25 10 7s-4.3 7-10 7S2 13.75 2 12 6.3 5 12 5Z"
                          stroke="currentColor" stroke-width="1.8"/>
                    <circle cx="12" cy="12" r="3.2" stroke="currentColor" stroke-width="1.8"/>
                  </svg>
                  <!-- eye-off -->
                  <svg class="icon icon-eye-off d-none" width="18" height="18" viewBox="0 0 24 24" fill="none">
                    <path d="M3 3l18 18" stroke="currentColor" stroke-width="1.8"/>
                    <path d="M12 5c5.7 0 10 5.25 10 7 0 .95-.58 2.3-1.7 3.64M6 7.4C3.9 9 2 11.1 2 12c0 1.75 4.3 7 10 7 1.6 0 3.08-.3 4.4-.85"
                          stroke="currentColor" stroke-width="1.8"/>
                    <circle cx="12" cy="12" r="3.2" stroke="currentColor" stroke-width="1.8"/>
                  </svg>
                </button>
              </div>
              <div id="pwdHelp" class="form-text">Mínimo 8 caracteres.</div>
              <div class="invalid-feedback">La contraseña debe tener al menos 8 caracteres.</div>
            </div>

            <button type="submit" class="btn btn-login w-100 mb-3" id="submitBtn">Ingresar</button>
            <div class="ff-divider my-3" aria-hidden="true"><span>o</span></div>
            <a class="btn btn-register w-100" href="<?= $BASE ?>/register.php">Crear cuenta nueva</a>
          </form>
        </main>
      </div>
    </div>
  </div>


  <script>
  // Toggle contraseña
  (function setupEyeToggle(){
    const input = document.getElementById('password');
    const btn   = document.getElementById('togglePwd');
    if (!input || !btn) return;
    const eyeOn  = btn.querySelector('.icon-eye');
    const eyeOff = btn.querySelector('.icon-eye-off');
    btn.addEventListener('click', () => {
      const showing = (input.type === 'text');
      input.type = showing ? 'password' : 'text';
      eyeOn.classList.toggle('d-none', !showing);
      eyeOff.classList.toggle('d-none', showing);
      btn.setAttribute('aria-pressed', String(!showing));
      btn.setAttribute('aria-label', showing ? 'Mostrar contraseña' : 'Ocultar contraseña');
    });
  })();
</script>

<script>
  // AJAX login + validaciones
  (() => {
    const form      = document.getElementById('loginForm');
    const emailEl   = document.getElementById('email');
    const pwdEl     = document.getElementById('password');
    const msgEl     = document.getElementById('message');
    const submitBtn = document.getElementById('submitBtn');
    const emailHelp = document.getElementById('emailHelp');
    const pwdHelp   = document.getElementById('pwdHelp');

    const showMsg = (t, type='danger') => { msgEl.className = `alert alert-${type} small`; msgEl.textContent = t; };
    const clearMsg= () => { msgEl.className = 'small mb-3'; msgEl.textContent = ''; };
    const setLoading = (v) => {
      submitBtn.disabled = v;
      if (v) { submitBtn.dataset.originalText = submitBtn.textContent;
               submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>Ingresando…';
      } else { submitBtn.textContent = submitBtn.dataset.originalText || 'Ingresar'; }
    };

    let submittedOnce = false;
    function validateAndMark(){
      [emailEl, pwdEl].forEach(el => el.classList.remove('is-invalid'));
      [emailHelp, pwdHelp].forEach(el => el.classList.remove('text-danger'));
      let ok = true;
      if (!emailEl.checkValidity()) { ok = false; emailEl.classList.add('is-invalid'); if (submittedOnce) emailHelp.classList.add('text-danger'); }
      if (!pwdEl.checkValidity())   { ok = false; pwdEl.classList.add('is-invalid');   if (submittedOnce) pwdHelp.classList.add('text-danger'); }
      return ok;
    }
    [emailEl, pwdEl].forEach(el => {
      el.addEventListener('input', () => { if (submittedOnce) validateAndMark(); else el.classList.remove('is-invalid'); });
      el.addEventListener('change', () => { if (submittedOnce) validateAndMark(); else el.classList.remove('is-invalid'); });
    });

    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      submittedOnce = true;
      clearMsg();
      if (!validateAndMark()) { showMsg('Corrige los campos marcados.'); return; }

      setLoading(true);
      try {
        // Construir parámetros (incluye csrf y next del form)
        const params = new URLSearchParams(new FormData(form));

        const resp = await fetch(form.action, {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: params.toString(),
          credentials: 'same-origin'
        });

        const text = await resp.text();
        let data = null; try{ data = JSON.parse(text); }catch{}

        if (!resp.ok || !data || !data.ok) {
          showMsg((data && (data.error || data.msg)) || 'Correo o contraseña incorrectos.');
          return;
        }

        if (data.user) {
          try { localStorage.setItem('ff_user', JSON.stringify(data.user)); } catch {}
        }

        const next = (data.next && typeof data.next === 'string')
                   ? data.next
                   : (form.querySelector('input[name="next"]')?.value || '<?= $BASE ?>/index.php');

        showMsg(data.msg || 'Inicio de sesión exitoso.', 'success');
        window.location.href = next;
      } catch (err) {
        console.error(err);
        showMsg('Hubo un problema. Intenta más tarde.');
      } finally {
        setLoading(false);
      }
    });
  })();
</script>
</body>
</html>
