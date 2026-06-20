'use strict';
/* ═══════════════════════════════════════════════════════════════
   ISMO-SkillSwap — app.js
   ═══════════════════════════════════════════════════════════════ */

const THEME_COLORS = {
  red500: '#EF4444',
  orange500: '#F97316',
  green500: '#22C55E',
  green600: '#16A34A',
};

/* ── PARTICLE BACKGROUND ─────────────────────────────────────── */
(function initParticles() {
  const canvas = document.getElementById('particles-canvas');
  const ctx    = canvas.getContext('2d');
  let W, H, particles, mouse;

  const COUNT = 180;
  const MAX_DIST = 180;

  mouse = { x: -9999, y: -9999 };

  function resize() {
    W = canvas.width  = window.innerWidth;
    H = canvas.height = window.innerHeight;
  }

  function createParticles() {
    particles = Array.from({ length: COUNT }, () => ({
      x:     Math.random() * W,
      y:     Math.random() * H,
      vx:    (Math.random() - .5) * .35,
      vy:    (Math.random() - .5) * .35,
      r:     Math.random() * 1.5 + .8,
      alpha: Math.random() * .15 + .25,
    }));
  }

  function draw() {
    ctx.clearRect(0, 0, W, H);

    // Lines
    for (let i = 0; i < particles.length; i++) {
      for (let j = i + 1; j < particles.length; j++) {
        const dx = particles[i].x - particles[j].x;
        const dy = particles[i].y - particles[j].y;
        const d  = Math.sqrt(dx * dx + dy * dy);
        if (d < MAX_DIST) {
          const alpha = .12 * (1 - d / MAX_DIST);
          ctx.beginPath();
          ctx.strokeStyle = `rgba(255,255,255,${alpha})`;
          ctx.lineWidth   = .5;
          ctx.moveTo(particles[i].x, particles[i].y);
          ctx.lineTo(particles[j].x, particles[j].y);
          ctx.stroke();
        }
      }
    }

    // Dots
    particles.forEach(p => {
      // Glow
      const grad = ctx.createRadialGradient(p.x, p.y, 0, p.x, p.y, p.r * 5);
      grad.addColorStop(0, 'rgba(255,255,255,0.12)');
      grad.addColorStop(1, 'rgba(255,255,255,0)');
      ctx.beginPath();
      ctx.arc(p.x, p.y, p.r * 4, 0, Math.PI * 2);
      ctx.fillStyle = grad;
      ctx.fill();

      // Core dot
      ctx.beginPath();
      ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
      ctx.fillStyle = 'rgba(255,255,255,' + p.alpha + ')';
      ctx.fill();

      // Mouse repulsion
      const mdx = p.x - mouse.x;
      const mdy = p.y - mouse.y;
      const md  = Math.sqrt(mdx * mdx + mdy * mdy);
      if (md < 100 && md > 0) {
        const force = (100 - md) / 100 * 0.5;
        p.x += (mdx / md) * force;
        p.y += (mdy / md) * force;
      }

      p.x += p.vx;
      p.y += p.vy;
      if (p.x < 0 || p.x > W) p.vx *= -1;
      if (p.y < 0 || p.y > H) p.vy *= -1;
    });

    requestAnimationFrame(draw);
  }

  document.addEventListener('mousemove', e => { mouse.x = e.clientX; mouse.y = e.clientY; });
  document.addEventListener('mouseleave', () => { mouse.x = -9999; mouse.y = -9999; });
  window.addEventListener('resize', () => { resize(); createParticles(); });
  resize();
  createParticles();
  draw();
})();


/* ── COUNTER ANIMATION ───────────────────────────────────────── */
function animateCounter(el, target, duration = 1800, suffix = '') {
  const start = performance.now();
  const isFloat = target % 1 !== 0;

  function step(now) {
    const elapsed  = Math.min((now - start) / duration, 1);
    const eased    = 1 - Math.pow(1 - elapsed, 3); // ease-out-cubic
    const current  = isFloat
      ? (eased * target).toFixed(1)
      : Math.round(eased * target);
    el.firstChild
      ? (el.firstChild.textContent = current)
      : (el.textContent = current + suffix);
    if (elapsed < 1) requestAnimationFrame(step);
  }
  requestAnimationFrame(step);
}

window.addEventListener('load', () => {
  setTimeout(() => {
    const users = document.getElementById('stat-users');
    const helps = document.getElementById('stat-helps');
    const rate  = document.getElementById('stat-rate');

    if (users) {
      const target = parseInt(users.dataset.target, 10) || 0;
      animateCounter(users, target, 1600);
    }
    if (helps) {
      const target = parseInt(helps.dataset.target, 10) || 0;
      animateCounter(helps, target, 2000);
    }
    if (rate) {
      const target = parseInt(rate.dataset.target, 10) || 0;
      let start = performance.now();
      const duration = 1800;
      function stepRate(now) {
        const elapsed = Math.min((now - start) / duration, 1);
        const eased   = 1 - Math.pow(1 - elapsed, 3);
        const current = Math.round(eased * target);
        rate.innerHTML = `${current}<small>%</small>`;
        if (elapsed < 1) requestAnimationFrame(stepRate);
      }
      requestAnimationFrame(stepRate);
    }
  }, 400);
});

// Auto-open signup tab when requested via hash or query param
(() => {
  try {
    const params = new URLSearchParams(window.location.search);
    const show = params.get('show');
    if (window.location.hash === '#signup' || show === 'signup') {
      // Defer to ensure DOM is ready
      setTimeout(() => switchTab('signup'), 80);
    }
  } catch (e) {
    // ignore
  }
})();


/* ── TAB SWITCHER ────────────────────────────────────────────── */
function switchTab(tab) {
  const loginBtn   = document.getElementById('tab-login');
  const signupBtn  = document.getElementById('tab-signup');
  const loginPanel = document.getElementById('panel-login');
  const signupPanel= document.getElementById('panel-signup');
  const indicator  = document.querySelector('.tab-indicator');

  if (tab === 'login') {
    loginBtn.classList.add('active');
    loginBtn.setAttribute('aria-selected', 'true');
    signupBtn.classList.remove('active');
    signupBtn.setAttribute('aria-selected', 'false');

    loginPanel.classList.add('active');
    loginPanel.hidden = false;
    signupPanel.classList.remove('active');
    signupPanel.hidden = true;

    indicator.classList.remove('right');
  } else {
    signupBtn.classList.add('active');
    signupBtn.setAttribute('aria-selected', 'true');
    loginBtn.classList.remove('active');
    loginBtn.setAttribute('aria-selected', 'false');

    signupPanel.classList.add('active');
    signupPanel.hidden = false;
    loginPanel.classList.remove('active');
    loginPanel.hidden = true;

    indicator.classList.add('right');
  }
}


/* ── PASSWORD TOGGLE ─────────────────────────────────────────── */
function togglePassword(inputId, btn) {
  const input = document.getElementById(inputId);
  const isHidden = input.type === 'password';
  input.type = isHidden ? 'text' : 'password';
  btn.setAttribute('aria-label', isHidden ? 'Masquer le mot de passe' : 'Afficher le mot de passe');
  btn.classList.toggle('show', isHidden);

  // Swap icon
  btn.querySelector('.eye-icon').innerHTML = isHidden
    ? `<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>`
    : `<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>`;
}


/* ── PASSWORD STRENGTH ───────────────────────────────────────── */
const signupPw = document.getElementById('signup-password');
if (signupPw) {
  signupPw.addEventListener('input', () => {
    const val = signupPw.value;
    let score = 0;
    if (val.length >= 8)  score++;
    if (val.length >= 12) score++;
    if (/[A-Z]/.test(val)) score++;
    if (/[0-9]/.test(val)) score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;

    const fill  = document.getElementById('strength-fill');
    const label = document.getElementById('strength-label');

    const levels = [
      { pct: '0%',   color: THEME_COLORS.red500, text: '' },
      { pct: '25%',  color: THEME_COLORS.red500, text: 'Très faible' },
      { pct: '45%',  color: THEME_COLORS.orange500, text: 'Faible' },
      { pct: '65%',  color: THEME_COLORS.orange500, text: 'Moyen' },
      { pct: '82%',  color: THEME_COLORS.green500, text: 'Fort' },
      { pct: '100%', color: THEME_COLORS.green600, text: 'Très fort' },
    ];

    const lvl = levels[score] || levels[0];
    fill.style.width      = val ? lvl.pct : '0%';
    fill.style.background = lvl.color;
    label.textContent     = val ? lvl.text : '';
    label.style.color     = lvl.color;
  });
}


/* ── FORM VALIDATION HELPERS ─────────────────────────────────── */
function setError(inputEl, errorEl, msg) {
  inputEl.classList.add('error');
  inputEl.classList.remove('success');
  if (errorEl) errorEl.textContent = msg;
}

function setSuccess(inputEl, errorEl) {
  inputEl.classList.remove('error');
  inputEl.classList.add('success');
  if (errorEl) errorEl.textContent = '';
}

function clearState(inputEl, errorEl) {
  inputEl.classList.remove('error', 'success');
  if (errorEl) errorEl.textContent = '';
}

function validateEmail(email) {
  return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email) && email.endsWith('@ofppt-edu.ma');
}


/* ── LOGIN FORM ──────────────────────────────────────────────── */
const loginForm = document.getElementById('login-form');
if (loginForm) {
  loginForm.addEventListener('submit', async e => {
    e.preventDefault();
    const emailEl    = document.getElementById('login-email');
    const passwordEl = document.getElementById('login-password');
    const emailErr   = document.getElementById('login-email-error');
    const passwordErr= document.getElementById('login-password-error');
    const btn        = document.getElementById('btn-login');

    let valid = true;

    if (!validateEmail(emailEl.value.trim())) {
      setError(emailEl, emailErr, 'Utilisez un email @ofppt-edu.ma valide.');
      valid = false;
    } else {
      setSuccess(emailEl, emailErr);
    }

    if (passwordEl.value.length < 6) {
      setError(passwordEl, passwordErr, 'Le mot de passe doit contenir au moins 6 caractères.');
      valid = false;
    } else {
      setSuccess(passwordEl, passwordErr);
    }

    if (!valid) return;

    btn.classList.add('loading');
    btn.disabled = true;

    try {
      const resp = await fetch(loginForm.action, {
        method: 'POST',
        body: new URLSearchParams(new FormData(loginForm))
      });
      const result = await resp.json();

      if (result.success && result.redirect) {
        showToast('Connexion réussie ! Redirection...', 'success');
        setTimeout(() => { window.location.href = result.redirect; }, 500);
      } else {
        showToast(result.error || 'Erreur de connexion', 'error');
      }
    } catch (err) {
      showToast('Erreur de connexion au serveur', 'error');
    }

    btn.classList.remove('loading');
    btn.disabled = false;
  });
}


/* ── SIGNUP FORM ─────────────────────────────────────────────── */
const signupForm = document.getElementById('signup-form');
if (signupForm) {
  signupForm.addEventListener('submit', async e => {
    e.preventDefault();
    const emailEl = document.getElementById('signup-email');
    const emailErr= document.getElementById('signup-email-error');
    const pwEl    = document.getElementById('signup-password');
    const terms   = document.getElementById('terms');
    const btn     = document.getElementById('btn-signup');

    let valid = true;

    if (!validateEmail(emailEl.value.trim())) {
      setError(emailEl, emailErr, 'Utilisez un email @ofppt-edu.ma valide.');
      valid = false;
    } else {
      setSuccess(emailEl, emailErr);
    }

    if (pwEl.value.length < 8) {
      showToast('Le mot de passe doit contenir au moins 8 caractères.', 'error');
      valid = false;
    }

    if (!terms.checked) {
      showToast("Veuillez accepter les conditions d'utilisation.", 'error');
      valid = false;
    }

    if (!valid) return;

    btn.classList.add('loading');
    btn.disabled = true;

    try {
      const resp = await fetch(signupForm.action, {
        method: 'POST',
        body: new URLSearchParams(new FormData(signupForm))
      });
      const result = await resp.json();

      if (result.success) {
        showToast(result.message || 'Compte créé !', 'success');
        if (result.redirect) {
          setTimeout(() => { window.location.href = result.redirect; }, 500);
        }
      } else {
        showToast(result.error || 'Erreur lors de l\'inscription', 'error');
      }
    } catch (err) {
      showToast('Erreur de connexion au serveur', 'error');
    }

    btn.classList.remove('loading');
    btn.disabled = false;
  });
}


/* ── REAL-TIME VALIDATION ────────────────────────────────────── */
['login-email', 'signup-email'].forEach(id => {
  const el  = document.getElementById(id);
  const err = document.getElementById(`${id}-error`);
  if (!el) return;
  el.addEventListener('blur', () => {
    if (el.value && !validateEmail(el.value)) {
      setError(el, err, 'Seul l\'email @ofppt-edu.ma est accepté.');
    } else if (el.value) {
      setSuccess(el, err);
    }
  });
  el.addEventListener('input', () => {
    if (el.classList.contains('error') && validateEmail(el.value)) {
      setSuccess(el, err);
    }
  });
});


/* ── TOAST ───────────────────────────────────────────────────── */
function showToast(message, type = 'info', duration = 3000) {
  const container = document.getElementById('toast-container');
  if (!container) return;
  const toast = document.createElement('div');
  toast.className = `toast toast-${type}`;
  toast.setAttribute('role', 'status');
  toast.setAttribute('aria-live', 'polite');

  const icon = type === 'success'
    ? `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>`
    : `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>`;

  toast.innerHTML = `<span class="toast-icon" aria-hidden="true">${icon}</span><span>${message}</span>`;
  container.appendChild(toast);

  setTimeout(() => {
    toast.classList.add('toast-exit');
    toast.addEventListener('animationend', () => toast.remove(), { once: true });
  }, duration);
}


/* ── UTILS ───────────────────────────────────────────────────── */
function delay(ms) {
  return new Promise(resolve => setTimeout(resolve, ms));
}


/* ── RIPPLE EFFECT on primary button ────────────────────────── */
document.querySelectorAll('.btn-primary').forEach(btn => {
  btn.addEventListener('click', function(e) {
    if (this.disabled) return;
    const rect  = this.getBoundingClientRect();
    const x     = e.clientX - rect.left;
    const y     = e.clientY - rect.top;
    const ripple = document.createElement('span');
    ripple.style.cssText = `
      position:absolute;
      width:200px;height:200px;
      left:${x - 100}px;top:${y - 100}px;
      border-radius:50%;
      background:rgba(255,255,255,.2);
      transform:scale(0);
      animation:ripple-anim .6s linear;
      pointer-events:none;
    `;
    if (!document.getElementById('ripple-style')) {
      const style = document.createElement('style');
      style.id = 'ripple-style';
      style.textContent = '@keyframes ripple-anim{to{transform:scale(4);opacity:0}}';
      document.head.appendChild(style);
    }
    this.appendChild(ripple);
    setTimeout(() => ripple.remove(), 700);
  });
});
