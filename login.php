<?php
  $BASE = '/4everFootball';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Iniciar sesión | 4everFootball</title>

 
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <link rel="stylesheet" href="<?= $BASE ?>/css/iniciaRegister.css?v=42">
</head>
<body class="ff-bg">

  <div class="container py-4">
    <div class="row min-vh-100 align-items-center g-5">

    
      <div class="col-12 col-md-6 text-center text-md-start">
        <div class="brand-wrap">
          <img src="<?= $BASE ?>/img/logo.svg" alt="4everFootball" class="brand-logo">
          <p class="brand-tagline">
            Las noticias más emocionantes del fútbol, <span class="em">por siempre</span>.
          </p>
        </div>
      </div>

     
      <div class="col-12 col-md-6 d-flex justify-content-center">
        <main class="auth-card glass-card card shadow p-4 w-100">
          <h1 class="ff-title text-center mb-3">Iniciar sesión</h1>

          <div id="message" class="small mb-3" role="alert" aria-live="polite"></div>

          <form id="loginForm" action="<?= $BASE ?>/api/login.php" method="post" novalidate autocomplete="off">
            <input type="text" name="fake_user" autocomplete="username" style="position:absolute;left:-9999px;top:-9999px;width:0;height:0;opacity:0;">
            <input type="password" name="fake_pass" autocomplete="new-password" style="position:absolute;left:-9999px;top:-9999px;width:0;height:0;opacity:0;">


            <div class="mb-3">
              <label for="email" class="form-label">Correo electrónico</label>
              <input type="email" class="form-control" id="email" name="email" required autocomplete="off"
                     autocapitalize="none" spellcheck="false" inputmode="email" aria-describedby="emailHelp">
              <div id="emailHelp" class="form-text">Ej. nombre@correo.com</div>
              <div class="invalid-feedback">Ingresa un correo válido.</div>
            </div>

            <div class="mb-3">
              <label for="password" class="form-label">Contraseña</label>
              <div class="input-group">
                <input type="password" class="form-control" id="password" name="password"
                       required minlength="8" autocomplete="off" aria-describedby="pwdHelp">

                <button type="button" class="btn btn-outline-light eye-btn px-3" id="togglePwd"
                        aria-label="Mostrar contraseña" aria-pressed="false">
                  <!-- Icono eye -->
                  <svg class="icon icon-eye" width="18" height="18" viewBox="0 0 24 24" fill="none">
                    <path d="M12 5c5.7 0 10 5.25 10 7s-4.3 7-10 7S2 13.75 2 12 6.3 5 12 5Z" stroke="currentColor" stroke-width="1.8"/>
                    <circle cx="12" cy="12" r="3.2" stroke="currentColor" stroke-width="1.8"/>
                  </svg>
                 
                  <svg class="icon icon-eye-off d-none" width="18" height="18" viewBox="0 0 24 24" fill="none">
                    <path d="M3 3l18 18" stroke="currentColor" stroke-width="1.8"/>
                    <path d="M12 5c5.7 0 10 5.25 10 7 0 .95-.58 2.3-1.7 3.64M6 7.4C3.9 9 2 11.1 2 12c0 1.75 4.3 7 10 7 1.6 0 3.08-.3 4.4-.85" stroke="currentColor" stroke-width="1.8"/>
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
    
    function setupEyeToggle(inputId, btnId){
      const input = document.getElementById(inputId);
      const btn   = document.getElementById(btnId);
      if (!input || !btn) return;

      const eyeOn  = btn.querySelector('.icon-eye');
      const eyeOff = btn.querySelector('.icon-eye-off');

      btn.addEventListener('click', () => {
        const showing = input.type === 'text';
        input.type = showing ? 'password' : 'text';
        eyeOn.classList.toggle('d-none', !showing);
        eyeOff.classList.toggle('d-none', showing);
        btn.setAttribute('aria-pressed', String(!showing));
        btn.setAttribute('aria-label', showing ? 'Mostrar contraseña' : 'Ocultar contraseña');
      });
    }
    setupEyeToggle('password','togglePwd');
  </script>

  <script>
    
    (() => {
      const form      = document.getElementById('loginForm');
      const emailEl   = document.getElementById('email');
      const pwdEl     = document.getElementById('password');
      const msgEl     = document.getElementById('message');
      const submitBtn = document.getElementById('submitBtn');

      const emailHelp = document.getElementById('emailHelp');
      const pwdHelp   = document.getElementById('pwdHelp');

      const showMsg = (text, type = 'danger') => {
        msgEl.className = `alert alert-${type} small`;
        msgEl.textContent = text;
      };
      const clearMsg = () => { msgEl.className = 'small mb-3'; msgEl.textContent = ''; };

      const setLoading = (v) => {
        submitBtn.disabled = v;
        if (v) {
          submitBtn.dataset.originalText = submitBtn.textContent;
          submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>Ingresando…';
        } else {
          submitBtn.textContent = submitBtn.dataset.originalText || 'Ingresar';
        }
      };

      let submittedOnce = false;
      function validateAndMark() {
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
          const params = new URLSearchParams();
          params.set('email', emailEl.value.trim());
          params.set('password', pwdEl.value);

          const resp = await fetch(form.action, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: params.toString(),
            credentials: 'same-origin'
          });

          const txt = await resp.text();
          let data = null;
          try { data = JSON.parse(txt); } catch {}

          if (!resp.ok || !data || !data.ok) {
            const msg = (data && data.msg) ? data.msg : 'Correo o contraseña incorrectos.';
            showMsg(msg);
            return;
          }

          if (data.user) {
            try { localStorage.setItem('ff_user', JSON.stringify(data.user)); } catch {}
          }

          showMsg(data.msg || 'Inicio de sesión exitoso.', 'success');
          window.location.href = '<?= $BASE ?>/index.php';
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
