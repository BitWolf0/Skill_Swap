'use strict';

/* ── Role detection ──────────────────────────────────── */

const IS_STAGIAIRE = window.location.pathname.includes('/pages_stagiaire/');

const DEMO_USER = IS_STAGIAIRE
  ? { id: 1, name: 'Sophie Martin', role: 'Stagiaire', initials: 'SM' }
  : { id: 1, name: 'Sophie Martin', role: 'Mentor', initials: 'SM' };

/* ── Demo data ──────────────────────────────────────── */

const DEMO_CONVERSATIONS = IS_STAGIAIRE ? [
  {
    id: 1,
    participant: { name: 'Sophie Martin', role: 'Mentor', initials: 'SM', color: 'blue' },
    requestTitle: 'Comprendre les Promesses en JavaScript',
    lastMessage: 'Avec plaisir ! N\'hésitez pas si vous avez d\'autres questions.',
    timestamp: '2026-05-28T14:35:00',
    unread: 1,
    status: 'in_progress'
  },
  {
    id: 2,
    participant: { name: 'Jean Dupuis',  role: 'Formateur', initials: 'JD', color: 'green' },
    requestTitle: 'Problème avec les hooks React',
    lastMessage: 'Je vous ai partagé un exemple de code, regardez-le.',
    timestamp: '2026-05-27T11:00:00',
    unread: 0,
    status: 'active'
  },
  {
    id: 3,
    participant: { name: 'Marc Laurent', role: 'Mentor', initials: 'ML', color: 'orange' },
    requestTitle: 'Optimisation requête SQL',
    lastMessage: 'N\'oubliez pas d\'ajouter un index sur la colonne de jointure.',
    timestamp: '2026-05-26T09:30:00',
    unread: 0,
    status: 'completed'
  },
  {
    id: 4,
    participant: { name: 'Alice Martin', role: 'Mentor', initials: 'AM', color: 'purple' },
    requestTitle: 'Git - Résolution de conflits',
    lastMessage: 'Je suis disponible pour vous aider, dites-moi quand.',
    timestamp: '2026-05-28T17:00:00',
    unread: 2,
    status: 'in_progress'
  }
] : [
  {
    id: 1,
    participant: { name: 'Marie Dupont',  role: 'Stagiaire', initials: 'MD', color: 'green' },
    requestTitle: 'Comprendre les Promesses en JavaScript',
    lastMessage: 'Merci beaucoup pour vos explications, j\'ai enfin compris le concept !',
    timestamp: '2026-05-28T14:30:00',
    unread: 2,
    status: 'in_progress'
  },
  {
    id: 2,
    participant: { name: 'Thomas Martin', role: 'Stagiaire', initials: 'TM', color: 'blue' },
    requestTitle: 'Problème avec les hooks React',
    lastMessage: 'D\'accord, je vais essayer avec useEffect et je vous tiens au courant.',
    timestamp: '2026-05-27T09:15:00',
    unread: 0,
    status: 'completed'
  },
  {
    id: 3,
    participant: { name: 'Sophie Bernard', role: 'Stagiaire', initials: 'SB', color: 'orange' },
    requestTitle: 'Optimisation requête SQL',
    lastMessage: 'La jointure avec l\'index m\'a fait gagner 80% de temps !',
    timestamp: '2026-05-26T11:30:00',
    unread: 0,
    status: 'completed'
  },
  {
    id: 4,
    participant: { name: 'Lucas Petit',   role: 'Stagiaire', initials: 'LP', color: 'pink' },
    requestTitle: 'Git - Résolution de conflits',
    lastMessage: 'Je bloque sur un conflit dans le fichier package.json',
    timestamp: '2026-05-28T16:45:00',
    unread: 1,
    status: 'in_progress'
  },
  {
    id: 5,
    participant: { name: 'Julie Bernard', role: 'Mentor', initials: 'JB', color: 'purple' },
    requestTitle: 'React Native - Navigation',
    lastMessage: 'On pourrait faire un point en visio demain si tu veux.',
    timestamp: '2026-05-25T18:00:00',
    unread: 0,
    status: 'active'
  }
];

const MENTOR_MESSAGES = {
  1: [
    { id: 1, senderId: 2, text: 'Bonjour Sophie, je n\'arrive pas à comprendre le concept des Promesses en JavaScript. Pouvez-vous m\'aider ?', timestamp: '2026-05-27T10:00:00' },
    { id: 2, senderId: 1, text: 'Bonjour Marie ! Bien sûr, commençons par les bases. Une Promise est un objet qui représente l\'achèvement ou l\'échec d\'une opération asynchrone.', timestamp: '2026-05-27T10:05:00' },
    { id: 3, senderId: 2, text: 'D\'accord, et comment on crée une Promise ?', timestamp: '2026-05-27T10:10:00' },
    { id: 4, senderId: 1, text: 'On utilise le constructeur `new Promise()` avec une fonction qui reçoit `resolve` et `reject`. Par exemple :\n\n```js\nconst maPromise = new Promise((resolve, reject) => {\n  if (succes) resolve(\'OK\');\n  else reject(\'Erreur\');\n});\n```', timestamp: '2026-05-27T10:15:00' },
    { id: 5, senderId: 2, text: 'Ah d\'accord ! Et avec async/await c\'est plus simple non ?', timestamp: '2026-05-27T10:20:00' },
    { id: 6, senderId: 1, text: 'Exactement ! `async/await` est du sucre syntaxique. Une fonction `async` retourne toujours une Promise, et `await` attend sa résolution.', timestamp: '2026-05-27T10:25:00' },
    { id: 7, senderId: 2, text: 'Merci beaucoup pour vos explications, j\'ai enfin compris le concept !', timestamp: '2026-05-28T14:30:00' },
    { id: 8, senderId: 1, text: 'Avec plaisir ! N\'hésitez pas si vous avez d\'autres questions.', timestamp: '2026-05-28T14:35:00' }
  ],
  4: [
    { id: 1, senderId: 4, text: 'Bonjour Sophie, j\'ai un conflit sur package.json après un merge. Je n\'arrive pas à le résoudre.', timestamp: '2026-05-28T16:00:00' },
    { id: 2, senderId: 1, text: 'Bonjour Lucas, pas de panique. Est-ce que vous pouvez me montrer les marqueurs de conflit ?', timestamp: '2026-05-28T16:05:00' },
    { id: 3, senderId: 4, text: 'Oui, j\'ai ça :\n\n<<<<<<< HEAD\n"version": "2.0.0"\n=======\n"version": "1.5.0"\n>>>>>>> develop\n\nJe bloque sur un conflit dans le fichier package.json', timestamp: '2026-05-28T16:45:00' }
  ]
};

const STAGIAIRE_MESSAGES = {
  1: [
    { id: 1, senderId: 1, text: 'Bonjour Sophie, je n\'arrive pas à comprendre le concept des Promesses en JavaScript. Pouvez-vous m\'aider ?', timestamp: '2026-05-27T10:00:00' },
    { id: 2, senderId: 2, text: 'Bonjour Marie ! Bien sûr, commençons par les bases. Une Promise est un objet qui représente l\'achèvement ou l\'échec d\'une opération asynchrone.', timestamp: '2026-05-27T10:05:00' },
    { id: 3, senderId: 1, text: 'D\'accord, et comment on crée une Promise ?', timestamp: '2026-05-27T10:10:00' },
    { id: 4, senderId: 2, text: 'On utilise le constructeur `new Promise()` avec une fonction qui reçoit `resolve` et `reject`. Par exemple :\n\n```js\nconst maPromise = new Promise((resolve, reject) => {\n  if (succes) resolve(\'OK\');\n  else reject(\'Erreur\');\n});\n```', timestamp: '2026-05-27T10:15:00' },
    { id: 5, senderId: 1, text: 'Ah d\'accord ! Et avec async/await c\'est plus simple non ?', timestamp: '2026-05-27T10:20:00' },
    { id: 6, senderId: 2, text: 'Exactement ! `async/await` est du sucre syntaxique. Une fonction `async` retourne toujours une Promise, et `await` attend sa résolution.', timestamp: '2026-05-27T10:25:00' },
    { id: 7, senderId: 1, text: 'Merci beaucoup pour vos explications, j\'ai enfin compris le concept !', timestamp: '2026-05-28T14:30:00' },
    { id: 8, senderId: 2, text: 'Avec plaisir ! N\'hésitez pas si vous avez d\'autres questions.', timestamp: '2026-05-28T14:35:00' }
  ],
  4: [
    { id: 1, senderId: 1, text: 'Bonjour Sophie, j\'ai un conflit sur package.json après un merge. Je n\'arrive pas à le résoudre.', timestamp: '2026-05-28T16:00:00' },
    { id: 2, senderId: 4, text: 'Bonjour Lucas, pas de panique. Est-ce que vous pouvez me montrer les marqueurs de conflit ?', timestamp: '2026-05-28T16:05:00' },
    { id: 3, senderId: 1, text: 'Oui, j\'ai ça :\n\n<<<<<<< HEAD\n"version": "2.0.0"\n=======\n"version": "1.5.0"\n>>>>>>> develop\n\nJe bloque sur un conflit dans le fichier package.json', timestamp: '2026-05-28T16:45:00' }
  ]
};

const DEMO_MESSAGES = IS_STAGIAIRE ? STAGIAIRE_MESSAGES : MENTOR_MESSAGES;

/* ── Helpers ─────────────────────────────────────────── */

function formatTime(iso) {
  const d = new Date(iso);
  const now = new Date();
  const sameDay = d.toDateString() === now.toDateString();
  const yesterday = new Date(now);
  yesterday.setDate(yesterday.getDate() - 1);
  const isYesterday = d.toDateString() === yesterday.toDateString();

  const hours = d.getHours().toString().padStart(2, '0');
  const mins = d.getMinutes().toString().padStart(2, '0');

  if (sameDay) return `${hours}:${mins}`;
  if (isYesterday) return `Hier ${hours}:${mins}`;

  const day = d.getDate().toString().padStart(2, '0');
  const month = (d.getMonth() + 1).toString().padStart(2, '0');
  return `${day}/${month}`;
}

/* ── Inbox page ──────────────────────────────────────── */

function renderConversations(list) {
  const container = document.getElementById('conv-list-container');
  if (!container) return;

  if (!list || list.length === 0) {
    container.innerHTML = `
      <div class="empty-state">
        <div class="empty-state-icon">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
            stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
          </svg>
        </div>
        <div class="empty-state-title">Aucune conversation</div>
        <div class="empty-state-desc">Vous n'avez pas encore de messages. ${IS_STAGIAIRE ? 'Posez une demande d\'aide sur le Marketplace' : 'Proposez votre aide sur le Marketplace'} pour démarrer une conversation.</div>
      </div>`;
    return;
  }

  container.innerHTML = list.map(c => {
    const time = formatTime(c.timestamp);
    const unreadBadge = c.unread > 0 ? `<span class="conv-badge">${c.unread}</span>` : '';
    const unreadClass = c.unread > 0 ? 'unread' : '';
    const previewClass = c.unread > 0 ? 'unread-text' : '';
    const href = `conversation.html?id=${c.id}`;

    return `
      <a href="${href}" class="conv-item ${unreadClass}">
        <div class="conv-avatar ${c.participant.color}">${c.participant.initials}</div>
        <div class="conv-body">
          <div class="conv-top">
            <span class="conv-name">${c.participant.name}</span>
            <span class="conv-time">${time}</span>
          </div>
          <div class="conv-title">${c.requestTitle}</div>
          <div class="conv-preview ${previewClass}">${c.lastMessage}</div>
        </div>
        ${unreadBadge}
      </a>`;
  }).join('');
}

function filterConversations() {
  const q = (document.getElementById('inbox-search')?.value || '').toLowerCase().trim();
  const filtered = q
    ? DEMO_CONVERSATIONS.filter(c =>
        c.participant.name.toLowerCase().includes(q) ||
        c.requestTitle.toLowerCase().includes(q) ||
        c.lastMessage.toLowerCase().includes(q))
    : DEMO_CONVERSATIONS;
  renderConversations(filtered);
}

function initInbox() {
  renderConversations(DEMO_CONVERSATIONS);

  const searchInput = document.getElementById('inbox-search');
  if (searchInput) {
    searchInput.addEventListener('input', filterConversations);
  }
}

/* ── Conversation thread page ───────────────────────── */

function getConvId() {
  const params = new URLSearchParams(window.location.search);
  return parseInt(params.get('id'), 10);
}

function loadConversation() {
  const convId = getConvId();
  const conv = DEMO_CONVERSATIONS.find(c => c.id === convId);

  if (!conv) {
    const container = document.getElementById('messages-container');
    if (container) {
      container.innerHTML = `
        <div class="empty-state">
          <div class="empty-state-icon">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
              stroke-linecap="round" stroke-linejoin="round">
              <circle cx="11" cy="11" r="8" /><line x1="21" y1="21" x2="16.65" y2="16.65" />
            </svg>
          </div>
          <div class="empty-state-title">Conversation introuvable</div>
          <div class="empty-state-desc">Cette conversation n'existe pas ou a été supprimée.</div>
        </div>`;
      return;
    }
  }

  /* Header */
  const headerTitle = document.getElementById('conv-header-title');
  const headerSub = document.getElementById('conv-header-sub');
  if (headerTitle) headerTitle.textContent = conv.requestTitle;
  if (headerSub) headerSub.textContent = `Avec ${conv.participant.name} · ${conv.participant.role}`;

  /* Messages */
  const messages = DEMO_MESSAGES[convId] || [];
  renderMessages(messages);

  /* Sidebar */
  const sidebarName = document.getElementById('sidebar-participant-name');
  const sidebarRole = document.getElementById('sidebar-participant-role');
  const sidebarInitials = document.getElementById('sidebar-participant-initials');
  const sidebarAvatar = document.getElementById('sidebar-participant-avatar');
  if (sidebarName) sidebarName.textContent = conv.participant.name;
  if (sidebarRole) sidebarRole.textContent = conv.participant.role;
  if (sidebarInitials) sidebarInitials.textContent = conv.participant.initials;
  if (sidebarAvatar) sidebarAvatar.className = `participant-avatar ${conv.participant.color}`;
}

function renderMessages(messages) {
  const container = document.getElementById('messages-container');
  if (!container) return;

  if (!messages || messages.length === 0) {
    container.innerHTML = `
      <div class="empty-state">
        <div class="empty-state-icon">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
            stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
          </svg>
        </div>
        <div class="empty-state-title">Aucun message</div>
        <div class="empty-state-desc">Le début de la conversation apparaîtra ici.</div>
      </div>`;
    return;
  }

  messages.sort((a, b) => new Date(a.timestamp) - new Date(b.timestamp));

  let html = '';
  let lastDate = '';

  messages.forEach((msg, i) => {
    const msgDate = new Date(msg.timestamp).toDateString();

    if (msgDate !== lastDate) {
      const label = new Date(msg.timestamp).toLocaleDateString('fr-FR', { weekday: 'long', day: 'numeric', month: 'long' });
      html += `
        <div class="msg-divider">
          <span class="msg-divider-line"></span>
          <span class="msg-divider-label">${label}</span>
          <span class="msg-divider-line"></span>
        </div>`;
      lastDate = msgDate;
    }

    const isSent = msg.senderId === DEMO_USER.id;
    const side = isSent ? 'sent' : 'received';
    const participant = DEMO_CONVERSATIONS.find(c => c.id === getConvId())?.participant;
    const initials = isSent ? DEMO_USER.initials : (participant?.initials || '?');
    const color = isSent ? 'blue' : (participant?.color || 'green');
    const author = isSent ? 'Moi' : (participant?.name.split(' ')[0] || '');
    const time = formatTime(msg.timestamp);

    /* simple markdown-ish: code blocks */
    let text = msg.text;
    text = text.replace(/```(\w*)\n?([\s\S]*?)```/g, '<code class="msg-code">$2</code>');
    text = text.replace(/\n/g, '<br>');

    html += `
      <div class="msg ${side}">
        <div class="msg-avatar ${color}">${initials}</div>
        <div class="msg-body">
          <div class="msg-meta">
            <span class="msg-author">${author}</span>
          </div>
          <div class="msg-bubble">${text}</div>
          <span class="msg-time-inline">${time}</span>
        </div>
      </div>`;
  });

  container.innerHTML = html;
  container.scrollTop = container.scrollHeight;
}

function initConversation() {
  loadConversation();

  const sendBtn = document.getElementById('btn-send');
  const input = document.getElementById('msg-input');

  function updateSendBtn() {
    if (sendBtn) sendBtn.disabled = !input.value.trim();
  }

  function sendMessage() {
    const text = input.value.trim();
    if (!text) return;

    const messages = DEMO_MESSAGES[getConvId()] || [];
    messages.push({
      id: Date.now(),
      senderId: DEMO_USER.id,
      text: text,
      timestamp: new Date().toISOString()
    });
    DEMO_MESSAGES[getConvId()] = messages;

    input.value = '';
    updateSendBtn();
    renderMessages(messages);

    /* Show toast */
    const toastContainer = document.getElementById('toast-container');
    if (toastContainer && window.showToast) {
      window.showToast('Message envoyé', 'success');
    }
  }

  updateSendBtn();
  if (sendBtn) sendBtn.addEventListener('click', sendMessage);
  if (input) {
    input.addEventListener('input', updateSendBtn);
    input.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        sendMessage();
      }
    });
  }
}

/* ── Init ────────────────────────────────────────────── */

document.addEventListener('DOMContentLoaded', () => {
  if (document.getElementById('conv-list-container')) {
    initInbox();
  }
  if (document.getElementById('messages-container')) {
    initConversation();
  }
});
