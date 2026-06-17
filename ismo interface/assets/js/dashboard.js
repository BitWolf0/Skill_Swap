/**
 * ISMO-SkillSwap — Dashboard JS
 * Consistent with login.js patterns (toast, interaction helpers)
 */

'use strict';

/* ──────────────────────────────────────────────
   DOM References
────────────────────────────────────────────── */
const profileBtn = document.getElementById('btn-profile');
const profileDropdown = document.getElementById('profile-dropdown');
const btnPublish = document.getElementById('btn-publish');
const btnPassportPdf = document.getElementById('btn-passport-pdf');
const toastContainer = document.getElementById('toast-container');
const navItems = document.querySelectorAll('.nav-item');
const searchInput = document.getElementById('search-input');
const progressFill = document.getElementById('progress-fill');

// API base: works from any page depth
const API_PATH = window.location.pathname.includes('/pages_') || window.location.pathname.includes('/formateur_pages/')
  ? '../backend/api' : 'backend/api';

// Mobile sidebar elements
const sidebar = document.getElementById('sidebar');
const btnMenu = document.getElementById('btn-menu');
const sidebarOverlay = document.getElementById('sidebar-overlay');

// Sidebar: visible by default on desktop, hidden on mobile (≤820px)
if (window.innerWidth <= 820) {
  sidebar?.classList.remove('open');
}

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
  if (window.innerWidth <= 820) {
    sidebarOverlay?.classList.add('show');
    document.body.style.overflow = 'hidden';
  }
}

function closeSidebar() {
  sidebar?.classList.remove('open');
  sidebarOverlay?.classList.remove('show');
  document.body.style.overflow = '';
}

btnMenu?.addEventListener('click', (e) => {
  e.stopPropagation();
  const isOpen = sidebar?.classList.contains('open');
  isOpen ? closeSidebar() : openSidebar();
});
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
  window.location.href = 'nouvelle_demande.php';
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
   Search — Enter redirects to recherche.php
────────────────────────────────────────────── */
searchInput?.addEventListener('keydown', (e) => {
  if (e.key === 'Enter') {
    e.preventDefault();
    const q = searchInput.value.trim();
    if (q.length > 0) {
      window.location.href = `recherche.php?q=${encodeURIComponent(q)}`;
    }
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
   Notification button + panel
────────────────────────────────────────────── */
const notifPanel = document.getElementById('notif-panel');
const notifList = document.getElementById('notif-list');
const markAllBtn = document.getElementById('notif-mark-all');
const notifBtn = document.getElementById('btn-notif');

function notifIcon(type) {
  const icons = { proposition: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 12c0 2.5-2 5-5 5h-2l-4 3v-3H9c-3 0-5-2-5-5s2-5 5-5h1"/><path d="M16 7l2-2 4 4-4 4-2-2"/><path d="M8 12l-2 2-4-4 4-4 2 2"/></svg>', acceptee: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>', resolu: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/><path d="M4 22h16"/><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"/><path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"/><path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"/></svg>', refusee: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>', retiree: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/></svg>', badge: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg>' };
  return icons[type] || '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>';
}
function notifIconClass(type) {
  return 'icon-' + (type || 'default');
}
function timeAgo(dateStr) {
  const diff = Date.now() - new Date(dateStr).getTime();
  const mins = Math.floor(diff / 60000);
  if (mins < 1) return 'À l\'instant';
  if (mins < 60) return `Il y a ${mins} min`;
  const hrs = Math.floor(mins / 60);
  if (hrs < 24) return `Il y a ${hrs}h`;
  const days = Math.floor(hrs / 24);
  if (days < 7) return `Il y a ${days}j`;
  return new Date(dateStr).toLocaleDateString('fr-FR');
}

async function loadNotifs() {
  try {
    const resp = await fetch(API_PATH + '/notifications.php');
    const notifs = await resp.json();
    if (!Array.isArray(notifs)) return;
    const unreadCount = notifs.filter(n => !n.is_read).length;
    const dot = document.querySelector('.notif-dot');
    if (dot) dot.style.display = unreadCount > 0 ? '' : 'none';
    notifList.innerHTML = '';
    if (notifs.length === 0) {
      notifList.innerHTML = '<div class="notif-empty">Aucune notification</div>';
      return;
    }
    for (const n of notifs) {
      const item = document.createElement('a');
      item.className = 'notif-item' + (n.is_read ? '' : ' unread');
      item.href = '#';
      item.innerHTML = `
        <div class="notif-icon ${notifIconClass(n.notification_type)}">${notifIcon(n.notification_type)}</div>
        <div class="notif-content">
          <span class="nf-title">${n.title || ''}</span>
          <span class="nf-desc">${n.message || ''}</span>
          <span class="nf-time">${timeAgo(n.created_at)}</span>
        </div>
      `;
      item.addEventListener('click', async function(e) {
        e.preventDefault();
        if (!n.is_read) {
          try {
            await fetch(API_PATH + '/notifications.php?id=' + n.notification_id, { method: 'PUT', headers: { 'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '' } });
          } catch (e) { console.warn('Mark read failed', e); }
        }
        notifPanel.hidden = true;
      });
      notifList.appendChild(item);
    }
  } catch (e) { console.warn('Load notifs failed', e); }
}

notifBtn?.addEventListener('click', async function(e) {
  e.stopPropagation();
  const isOpen = !notifPanel.hidden;
  document.querySelectorAll('.profile-dropdown').forEach(d => d.hidden = true);
  if (isOpen) { notifPanel.hidden = true; return; }
  await loadNotifs();
  notifPanel.hidden = false;
});

markAllBtn?.addEventListener('click', async function(e) {
  e.stopPropagation();
  try {
    await fetch(API_PATH + '/notifications.php?all=1', { method: 'PUT', headers: { 'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '' } });
    const dot = document.querySelector('.notif-dot');
    if (dot) dot.style.display = 'none';
    await loadNotifs();
  } catch (e) { console.warn('Mark all read failed', e); }
});

document.addEventListener('click', function() {
  if (notifPanel && !notifPanel.hidden) notifPanel.hidden = true;
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
   Responsive sidebar: debounced resize handler
────────────────────────────────────────────── */
let resizeTimer;
window.addEventListener('resize', () => {
  clearTimeout(resizeTimer);
  resizeTimer = setTimeout(() => {
    const w = window.innerWidth;
    if (w <= 820) {
      sidebar?.classList.remove('open');
      sidebarOverlay?.classList.remove('show');
      document.body.style.overflow = '';
    } else {
      sidebar?.classList.add('open');
    }
  }, 150);
});

/* ──────────────────────────────────────────────
   Initialisation
────────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', () => {
  // Sidebar starts open on desktop
  if (window.innerWidth > 820) {
    sidebar?.classList.add('open');
  }

  animateProgressBar();
  // Welcome toast - only show on first visit
  if (!localStorage.getItem('welcomeShown')) {
    setTimeout(() => {
      showToast('Bienvenue, Sophie ! <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l1.5 5.5L19 10l-5.5 1.5L12 17l-1.5-5.5L5 10l5.5-1.5L12 3z"/><line x1="19" y1="17" x2="19" y2="21"/><line x1="17" y1="19" x2="21" y2="19"/></svg>', 'success', 4000);
    }, 600);
    localStorage.setItem('welcomeShown', 'true');
  }
});
