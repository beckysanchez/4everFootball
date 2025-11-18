<?php
  session_start();
  $BASE = '/4everFootball';

  // CSRF simple
  if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
  }
  $CSRF = $_SESSION['csrf'];

  // ?next=/ruta-a-volver (opcional)
  $next = isset($_GET['next']) && is_string($_GET['next']) ? $_GET['next'] : "$BASE/index.php";
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Crear cuenta | 4everFootball</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Estilos específicos de login/registro -->
  <link rel="stylesheet" href="<?= $BASE ?>/css/iniciaRegister.css?v=108">
</head>

<body class="ff-bg">
  <div class="container py-4">
    <div class="row min-vh-100 align-items-center g-5">

      <!-- IZQUIERDA: logo + frase -->
      <div class="col-12 col-lg-6 text-center text-lg-start">
        <div class="brand-wrap">
          <img src="<?= $BASE ?>/img/logo.svg" alt="4everFootball" class="brand-logo">
          <p class="brand-tagline mt-2">Únete a la comunidad y comparte tu pasión por el fútbol.</p>
        </div>
      </div>

            <!-- DERECHA: tarjeta registro -->
      <div class="col-12 col-lg-6 d-flex justify-content-center">
        <main class="auth-card auth-card--wide glass-card card shadow p-4 w-100">
          <h1 class="ff-title text-center mb-3">Crear cuenta</h1>
          <div id="regMessage" class="small mb-3" role="alert" aria-live="polite"></div>

          <form id="registerForm" class="reg-grid" action="<?= $BASE ?>/api/register.php"
                method="post" enctype="multipart/form-data" novalidate>
            <!-- Seguridad y retorno -->
            <input type="hidden" name="csrf" value="<?= htmlspecialchars($CSRF) ?>">
            <input type="hidden" name="next" value="<?= htmlspecialchars($next) ?>">

            <!-- IZQUIERDA -->
            <section class="reg-left">
              <!-- Nombre -->
              <div class="reg-row-single">
                <div>
                  <label class="form-label" for="first_name">Nombre</label>
                  <input class="form-control" id="first_name" name="first_name" required autocomplete="given-name">
                  <div class="invalid-feedback">Escribe tu nombre.</div>
                </div>
              </div>
              <!-- Apellido paterno -->
              <div class="reg-row-single">
                <div>
                  <label class="form-label" for="last_name_p">Apellido paterno</label>
                  <input class="form-control" id="last_name_p" name="last_name_p" required autocomplete="family-name">
                  <div class="invalid-feedback">Escribe tu apellido paterno.</div>
                </div>
              </div>
              <!-- Apellido materno -->
              <div class="reg-row-single">
                <div>
                  <label class="form-label" for="last_name_m">Apellido materno</label>
                  <input class="form-control" id="last_name_m" name="last_name_m" required autocomplete="additional-name">
                  <div class="invalid-feedback">Escribe tu apellido materno.</div>
                </div>
              </div>
              <!-- Fecha de nacimiento (máx hoy - 12 años) -->
              <div class="reg-row-single">
                <div>
                  <label class="form-label" for="birth_date">Fecha de nacimiento</label>
                  <input class="form-control" type="date" id="birth_date" name="birth_date"
                         required max="<?= date('Y-m-d', strtotime('-12 years')) ?>">
                  <div class="form-text hint-inline">Debes tener 12 años o más.</div>
                  <div class="invalid-feedback">Verifica tu fecha (mínimo 12 años).</div>
                </div>
              </div>
              <!-- Género -->
              <div class="reg-row-single">
                <div>
                  <label class="form-label" for="gender">Género</label>
                  <select class="form-select" id="gender" name="gender" required>
                    <option value="" disabled selected>Selecciona…</option>
                    <option value="M">Masculino</option>
                    <option value="F">Femenino</option>
                    <option value="X">Otro / Prefiero no decir</option>
                  </select>
                  <div class="invalid-feedback">Selecciona una opción.</div>
                </div>
              </div>
              <!-- País de nacimiento -->
              <div class="reg-row-single">
                <div>
                  <label class="form-label" for="country">País de nacimiento</label>
                  <input class="form-control" list="country-list" id="country" name="country" required placeholder="Ej. México">
                  <datalist id="country-list">
                    <option value="México"></option><option value="Argentina"></option><option value="España"></option>
                    <option value="Estados Unidos"></option><option value="Colombia"></option><option value="Chile"></option>
                    <option value="Perú"></option><option value="Brasil"></option><option value="Uruguay"></option>
                  </datalist>
                  <div class="invalid-feedback">Indica tu país.</div>
                </div>
              </div>
              <!-- Nacionalidad -->
              <div class="reg-row-single">
                <div>
                  <label class="form-label" for="nationality">Nacionalidad</label>
                  <input class="form-control" id="nationality" name="nationality" required placeholder="Ej. Mexicana">
                  <div class="invalid-feedback">Indica tu nacionalidad.</div>
                </div>
              </div>
            </section>


                        <!-- DERECHA -->
            <aside class="reg-right">
              <div class="idbox">
                <div class="label mb-2">Foto de perfil (formato credencial)</div>
                <div class="id-photo">
                  <img id="photoPreviewImg" src="" alt="Previsualización" hidden>
                  <span id="photoEmpty">Sin imagen</span>
                </div>
                <input class="form-control" type="file" id="photo" name="photo"
                       accept="image/jpeg,image/png,image/webp" required>
                <div class="sub mt-2">JPG/PNG/WEBP · máx. 2 MB</div>
                <div class="invalid-feedback">Sube una imagen válida (≤ 2 MB).</div>
              </div>

              <!-- Correo + Contraseña -->
              <div class="reg-row-dual">
                <div>
                  <label class="form-label" for="email">Correo electrónico</label>
                  <input class="form-control" type="email" id="email" name="email" required
                         autocomplete="email" placeholder="tucorreo@ejemplo.com">
                  <div class="invalid-feedback">Ingresa un correo válido.</div>
                </div>
                <div>
                  <label class="form-label" for="password">Contraseña</label>
                  <div class="input-group">
                    <input class="form-control" type="password" id="password" name="password"
                           required minlength="8"
                           pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z0-9]).{8,}$">
                    <button type="button" class="btn btn-outline-light eye-btn px-3" id="togglePwd"
                            aria-label="Mostrar contraseña" aria-pressed="false">
                      <!-- eye -->
                      <svg class="icon icon-eye" width="18" height="18" viewBox="0 0 24 24" fill="none">
                        <path d="M12 5c5.7 0 10 5.25 10 7s-4.3 7-10 7S2 13.75 2 12 6.3 5 12 5Z" stroke="currentColor" stroke-width="1.8"/>
                        <circle cx="12" cy="12" r="3.2" stroke="currentColor" stroke-width="1.8"/>
                      </svg>
                      <!-- eye-off -->
                      <svg class="icon icon-eye-off d-none" width="18" height="18" viewBox="0 0 24 24" fill="none">
                        <path d="M3 3l18 18" stroke="currentColor" stroke-width="1.8"/>
                        <path d="M12 5c5.7 0 10 5.25 10 7 0 .95-.58 2.3-1.7 3.64M6 7.4C3.9 9 2 11.1 2 12c0 1.75 4.3 7 10 7 1.6 0 3.08-.3 4.4-.85" stroke="currentColor" stroke-width="1.8"/>
                        <circle cx="12" cy="12" r="3.2" stroke="currentColor" stroke-width="1.8"/>
                      </svg>
                    </button>
                  </div>
                  <div class="form-text hint-inline">Mínimo 8, 1 mayúscula, 1 minúscula, 1 número y 1 carácter especial.</div>
                  <div class="invalid-feedback">La contraseña no cumple los requisitos.</div>
                </div>
              </div>
            </aside>

            <!-- ACCIONES -->
            <div class="reg-actions">
              <button type="submit" class="btn btn-register" id="submitBtn">Crear cuenta</button>
              <a class="btn btn-login-alt" href="<?= $BASE ?>/login.php" aria-label="Ir a la pantalla de iniciar sesión">Iniciar sesión</a>
            </div>
          </form>
        </main>
      </div>
    </div>
  </div>

<script>
document.addEventListener("DOMContentLoaded", () => {

  // ========== TOGGLE CONTRASEÑA ==========
  (function setupEyeToggle(){
    const input = document.getElementById('password');
    const btn   = document.getElementById('togglePwd');
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
  })();

  // ========== VALIDACIÓN DE PAÍS Y NACIONALIDAD ==========
  const countryInput      = document.getElementById("country");
  const nationalityInput  = document.getElementById("nationality");

  let lastCountryInfo = null;
  let nationalityWasAutofilled = false;

  const normalize = s =>
    (s || "")
      .toString()
      .trim()
      .toLowerCase()
      .normalize("NFD")
      .replace(/[\u0300-\u036f]/g, "");

  const titleCase = s =>
    (s || "").toLowerCase().replace(/\b\p{L}+/gu, w => w[0].toUpperCase() + w.slice(1));

  async function fetchCountryInfo(query) {
    if (!query) return null;
    try {
      const url = `https://restcountries.com/v3.1/name/${encodeURIComponent(query)}?fields=name,translations,demonyms`;
      const res = await fetch(url);
      const data = await res.json();
      if (!Array.isArray(data) || !data.length) return null;

      const qNorm = normalize(query);
      let best = data.find(c =>
        [c?.name?.common, c?.name?.official, c?.translations?.spa?.common, c?.translations?.spa?.official]
          .filter(Boolean).map(normalize).includes(qNorm)
      ) || data[0];

      const spaM = best?.demonyms?.spa?.m || "";
      const spaF = best?.demonyms?.spa?.f || "";
      const engM = best?.demonyms?.eng?.m || "";
      const engF = best?.demonyms?.eng?.f || "";

      return {
        countryCommon: best?.translations?.spa?.common || best?.name?.common || query,
        demonyms: [spaM, spaF, engM, engF].filter(Boolean)
      };
    } catch {
      return null;
    }
  }

  const fallbackTranslations = {
    "mexican": "Mexicano", "argentine": "Argentino", "argentinian": "Argentino",
    "chilean": "Chileno", "uruguayan": "Uruguayo", "brazilian": "Brasileño",
    "peruvian": "Peruano", "colombian": "Colombiano", "spanish": "Español",
    "american": "Estadounidense", "french": "Francés", "italian": "Italiano",
    "german": "Alemán", "canadian": "Canadiense", "paraguayan": "Paraguayo",
    "venezuelan": "Venezolano", "ecuadorian": "Ecuatoriano", "bolivian": "Boliviano"
  };

  function traducirAGentilicioEspanol(eng) {
    if (!eng) return "";
    const key = normalize(eng);
    return fallbackTranslations[key] || eng;
  }

  function markValid(el, ok, msg = "") {
    el.classList.toggle("is-valid",   !!ok);
    el.classList.toggle("is-invalid", !ok);
    el.setCustomValidity(ok ? "" : msg);
  }

  function nationalityMatches(value, info) {
    if (!info || !info.demonyms?.length) return false;
    const v = normalize(value);
    // Acepta masculino/femenino y versión traducida
    return info.demonyms.some(d => {
      const n = normalize(d);
      return v === n || v === normalize(traducirAGentilicioEspanol(d)) ||
             v.replace(/a$/, "o") === n.replace(/a$/, "o") || v.replace(/o$/, "a") === n.replace(/o$/, "a");
    });
  }

  function autofillNationality(info) {
    if (!info || !info.demonyms?.length) return;
    let preferSpa = info.demonyms.find(d => /[áéíóúñ]/i.test(d) || /o$|a$/i.test(d)) || info.demonyms[0];
    preferSpa = traducirAGentilicioEspanol(preferSpa);
    nationalityInput.value = titleCase(preferSpa);
    nationalityWasAutofilled = true;
  }

  async function handleCountryChange() {
    const val = countryInput.value.trim();
    if (!val) {
      lastCountryInfo = null;
      markValid(countryInput, false, "Indica tu país.");
      markValid(nationalityInput, false, "Selecciona primero un país.");
      return;
    }

    lastCountryInfo = await fetchCountryInfo(val);
    markValid(countryInput, !!lastCountryInfo, lastCountryInfo ? "" : "País no reconocido.");

    if (!lastCountryInfo) {
      markValid(nationalityInput, false, "Selecciona primero un país válido.");
      return;
    }

    if (!nationalityInput.value.trim() || nationalityWasAutofilled) {
      autofillNationality(lastCountryInfo);
    }

    const natOk = nationalityMatches(nationalityInput.value, lastCountryInfo);
    markValid(nationalityInput, natOk, "El gentilicio no coincide con el país.");
  }

  async function handleNationalityInput() {
    const val = nationalityInput.value.trim();
    if (!val) {
      nationalityWasAutofilled = false;
      markValid(nationalityInput, false, "Indica tu nacionalidad.");
      return;
    }
    nationalityWasAutofilled = false;

    // Si el país está vacío, buscarlo por gentilicio
    if (!countryInput.value.trim()) {
      for (const [eng, esp] of Object.entries(fallbackTranslations)) {
        if (normalize(val) === normalize(esp)) {
          const country = Object.keys(fallbackTranslations).find(k => fallbackTranslations[k] === esp);
          const reverse = await fetchCountryInfo(esp);
          if (reverse) {
            countryInput.value = reverse.countryCommon;
            lastCountryInfo = reverse;
            markValid(countryInput, true);
            break;
          }
        }
      }
    }

    if (!lastCountryInfo) {
      markValid(nationalityInput, false, "Selecciona o escribe un país válido.");
      return;
    }

    const ok = nationalityMatches(val, lastCountryInfo);
    markValid(nationalityInput, ok, "El gentilicio no coincide con el país.");
  }

  countryInput.addEventListener("input",  handleCountryChange);
  countryInput.addEventListener("blur",   handleCountryChange);
  nationalityInput.addEventListener("input", handleNationalityInput);
  nationalityInput.addEventListener("blur",  handleNationalityInput);

  // ========== FORMULARIO ==========
  (function(){
    const form   = document.getElementById('registerForm');
    const msgEl  = document.getElementById('regMessage');
    const submit = document.getElementById('submitBtn');

    const showMsg  = (t, type='danger') => { msgEl.className = `alert alert-${type} small`; msgEl.textContent = t; };
    const clearMsg = () => { msgEl.className = 'small mb-3'; msgEl.textContent = ''; };
    const setLoading = (v) => { if (submit){ submit.disabled = v; submit.textContent = v ? 'Creando…' : 'Crear cuenta'; } };

    function isAtLeast12(birth){
      if(!birth) return false;
      const bd = new Date(birth);
      const today = new Date();
      const limit = new Date(today.getFullYear() - 12, today.getMonth(), today.getDate());
      return bd <= limit;
    }

    function validatePhoto() {
      const input = document.getElementById('photo');
      if (!input.files || !input.files[0]) { input.setCustomValidity('Requerida'); return false; }
      const f = input.files[0];
      const validTypes = ['image/jpeg','image/png','image/webp'];
      const okType = validTypes.includes(f.type);
      const okSize = f.size <= 2 * 1024 * 1024;
      input.setCustomValidity((okType && okSize) ? '' : 'Archivo inválido');
      return okType && okSize;
    }

    document.getElementById('photo')?.addEventListener('change', (e) => {
      const f = e.target.files?.[0];
      const img = document.getElementById('photoPreviewImg');
      const empty = document.getElementById('photoEmpty');
      if (f && ['image/jpeg','image/png','image/webp'].includes(f.type)) {
        img.src = URL.createObjectURL(f);
        img.hidden = false; empty.hidden = true;
      } else {
        img.hidden = true; empty.hidden = false; img.src = '';
      }
      validatePhoto();
    });

    document.getElementById('birth_date')?.addEventListener('change', (e)=>{
      e.target.setCustomValidity(isAtLeast12(e.target.value) ? '' : 'Eres menor de 12');
    });

    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      clearMsg();

      validatePhoto();
      const birthEl = document.getElementById('birth_date');
      if (birthEl && !isAtLeast12(birthEl.value)) birthEl.setCustomValidity('Eres menor de 12');
      else if (birthEl) birthEl.setCustomValidity('');

      if (!form.checkValidity()) {
        form.classList.add('was-validated');
        showMsg('Revisa los campos marcados.');
        return;
      }

      setLoading(true);
      try{
        const fd   = new FormData(form);
        const resp = await fetch(form.action, { method:'POST', body: fd, credentials:'same-origin' });
        const data = await resp.json().catch(()=>null);

        if (resp.ok && data && (data.ok === true || data.status === 'ok')) {
          showMsg(data.msg || 'Cuenta creada correctamente.', 'success');
          if (data.user) try{ localStorage.setItem('ff_user', JSON.stringify(data.user)); }catch{}
          const next = (data.next && typeof data.next === 'string')
                     ? data.next
                     : (form.querySelector('input[name="next"]')?.value || '<?= $BASE ?>/index.php');
          location.href = next;
        } else {
          showMsg((data && (data.msg || data.error)) || 'No se pudo crear la cuenta.');
        }
      } catch (err) {
        console.error(err);
        showMsg('Hubo un problema. Intenta más tarde.');
      } finally {
        setLoading(false);
      }
    });
  })();
});
</script>


</body>
</html>
