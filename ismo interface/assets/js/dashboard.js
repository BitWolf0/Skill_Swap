/**
 * ISMO-SkillSwap — Dashboard JS
 * Consistent with login.js patterns (toast, interaction helpers)
 */

'use strict';

/* ──────────────────────────────────────────────
   DOM References
────────────────────────────────────────────── */
const profileBtn      = document.getElementById('btn-profile');
const profileDropdown = document.getElementById('profile-dropdown');
const btnPublish      = document.getElementById('btn-publish');
const btnPassportPdf  = document.getElementById('btn-passport-pdf');
const toastContainer  = document.getElementById('toast-container');
const navItems        = document.querySelectorAll('.nav-item');
const searchInput     = document.getElementById('search-input');
const progressFill    = document.getElementById('progress-fill');

// Mobile sidebar elements
const sidebar         = document.getElementById('sidebar');
const btnMenu         = document.getElementById('btn-menu');
const sidebarOverlay  = document.getElementById('sidebar-overlay');

/* ──────────────────────────────────────────────
   Profile Dropdown Toggle
────────────────────────────────────────────── */
function openDropdown() {
  profileDropdown.hidden = false;
  profileBtn.setAttribute('aria-expanded', 'true');
  // Trap focus to first item
  const firstItem = profileDropdown.querySelector('.dropdown-item');
  if (firstItem) firstItem.focus();
}

function closeDropdown() {
  profileDropdown.hidden = true;
  profileBtn.setAttribute('aria-expanded', 'false');
}

profileBtn.addEventListener('click', (e) => {
  e.stopPropagation();
  const isOpen = !profileDropdown.hidden;
  isOpen ? closeDropdown() : openDropdown();
});

// Close on outside click
document.addEventListener('click', (e) => {
  if (!profileDropdown.hidden && !profileDropdown.contains(e.target)) {
    closeDropdown();
  }
});

// Close on Escape key
document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape' && !profileDropdown.hidden) {
    closeDropdown();
    profileBtn.focus();
  }
});

/* ──────────────────────────────────────────────
   Mobile Sidebar Toggle
────────────────────────────────────────────── */
function openSidebar() {
  sidebar?.classList.add('open');
  sidebarOverlay?.classList.add('show');
  document.body.style.overflow = 'hidden'; // Prevent scrolling under sidebar
}

function closeSidebar() {
  sidebar?.classList.remove('open');
  sidebarOverlay?.classList.remove('show');
  document.body.style.overflow = '';
}

btnMenu?.addEventListener('click', openSidebar);
sidebarOverlay?.addEventListener('click', closeSidebar);

// Close sidebar on Escape key
document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape' && sidebar?.classList.contains('open')) {
    closeSidebar();
  }
});

/* ──────────────────────────────────────────────
   Sidebar Navigation
────────────────────────────────────────────── */
navItems.forEach(item => {
  item.addEventListener('click', (e) => {
    // Only handle # links as active switcher, not real navigation
    if (item.getAttribute('href') === '#') {
      e.preventDefault();
    }
    navItems.forEach(n => {
      n.classList.remove('active');
      n.removeAttribute('aria-current');
    });
    item.classList.add('active');
    item.setAttribute('aria-current', 'page');

    const label = item.querySelector('.nav-label')?.textContent?.trim();
    if (label) showToast(`Navigation : ${label}`, 'info');
  });
});

/* ──────────────────────────────────────────────
   Publish Button
────────────────────────────────────────────── */
btnPublish?.addEventListener('click', () => {
  showToast('Formulaire de demande bientôt disponible !', 'info');
});

/* ──────────────────────────────────────────────
   Passport PDF Button
────────────────────────────────────────────── */
btnPassportPdf?.addEventListener('click', () => {
  showToast('Génération du PDF en cours…', 'success');
});

/* ──────────────────────────────────────────────
   Request Cards — hover-lift already via CSS,
   add click feedback
────────────────────────────────────────────── */
document.querySelectorAll('.request-card').forEach(card => {
  card.addEventListener('click', () => {
    const title = card.querySelector('.request-title')?.textContent?.trim();
    if (title) showToast(`Ouverture : ${title}`, 'info');
  });
  // Keyboard accessibility
  card.setAttribute('tabindex', '0');
  card.addEventListener('keydown', (e) => {
    if (e.key === 'Enter' || e.key === ' ') {
      e.preventDefault();
      card.click();
    }
  });
});

/* ──────────────────────────────────────────────
   Search — live hint
────────────────────────────────────────────── */
let searchTimer;
searchInput?.addEventListener('input', () => {
  clearTimeout(searchTimer);
  const q = searchInput.value.trim();
  if (q.length > 2) {
    searchTimer = setTimeout(() => {
      showToast(`Recherche : « ${q} »`, 'info');
    }, 600);
  }
});

// ⌘K / Ctrl+K shortcut
document.addEventListener('keydown', (e) => {
  if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
    e.preventDefault();
    searchInput?.focus();
    searchInput?.select();
  }
});

/* ──────────────────────────────────────────────
   Notification button
────────────────────────────────────────────── */
document.getElementById('btn-notif')?.addEventListener('click', () => {
  showToast('Vous avez 3 nouvelles notifications', 'info');
});

/* ──────────────────────────────────────────────
   Progress bar — animated on page load
────────────────────────────────────────────── */
function animateProgressBar() {
  if (!progressFill) return;
  const target = progressFill.style.width;
  progressFill.style.width = '0%';
  requestAnimationFrame(() => {
    setTimeout(() => {
      progressFill.style.width = target;
    }, 200);
  });
}

/* ──────────────────────────────────────────────
   Toast Notification Helper
   (mirrors the same pattern from login.js)
────────────────────────────────────────────── */
function showToast(message, type = 'info', duration = 3000) {
  const toast = document.createElement('div');
  toast.className = `toast toast-${type}`;
  toast.setAttribute('role', 'status');
  toast.setAttribute('aria-live', 'polite');

  const icon = type === 'success'
    ? `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>`
    : `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>`;

  toast.innerHTML = `${icon}<span>${message}</span>`;
  toastContainer.appendChild(toast);

  // Auto-dismiss
  setTimeout(() => {
    toast.classList.add('toast-exit');
    toast.addEventListener('animationend', () => toast.remove(), { once: true });
  }, duration);
}

/* ──────────────────────────────────────────────
   Initialisation
────────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', () => {
  animateProgressBar();
  // Welcome toast
  setTimeout(() => {
    showToast('Bienvenue, Sophie ! ✨', 'success', 4000);
  }, 600);
});
