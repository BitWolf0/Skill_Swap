'use strict';

/**
 * Search Modal Component
 * Reusable modal for quick search across the platform
 * Triggered by header search bar on any page
 */

class SearchModal {
  constructor() {
    this.modal = null;
    this.searchInput = null;
    this.resultsContainer = null;
    this.userRole = this.detectUserRole();
    this.init();
  }

  init() {
    this.createModal();
    this.attachListeners();
  }

  detectUserRole() {
    // Detect role from URL path
    const path = window.location.pathname;
    if (path.includes('/pages_stagiaire/')) return 'stagiaire';
    if (path.includes('/pages_mentor/')) return 'mentor';
    if (path.includes('/formateur_pages/')) return 'formateur';
    if (path.includes('/pages_admin/')) return 'admin';
    return 'unknown';
  }

  createModal() {
    // Create modal HTML
    const modalHTML = `
      <div class="search-modal" id="search-modal" role="dialog" aria-labelledby="search-modal-title" hidden>
        <div class="search-modal-overlay" id="search-modal-overlay"></div>
        <div class="search-modal-container">
          <div class="search-modal-header">
            <input type="search" 
                   id="search-modal-input" 
                   class="search-modal-input" 
                   placeholder="Rechercher..."
                   aria-label="Champ de recherche" />
            <button class="search-modal-close" id="search-modal-close" aria-label="Fermer">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
              </svg>
            </button>
          </div>
          
          <div class="search-modal-filters">
            <select id="search-filter-type" class="search-filter-select" aria-label="Type de recherche">
              <option value="all">Tous</option>
              ${this.userRole === 'stagiaire' ? `
                <option value="mentors">Mentors</option>
                <option value="skills">Comp&eacute;tences</option>
                <option value="help">Demandes d'aide</option>
              ` : this.userRole === 'mentor' ? `
                <option value="requests">Demandes</option>
                <option value="trainees">Stagiaires</option>
                <option value="skills">Comp&eacute;tences</option>
              ` : `
                <option value="users">Utilisateurs</option>
                <option value="skills">Comp&eacute;tences</option>
              `}
            </select>
            
            <select id="search-filter-level" class="search-filter-select" aria-label="Niveau">
              <option value="">Tous les niveaux</option>
              <option value="debutant">D&eacute;butant</option>
              <option value="intermediaire">Interm&eacute;diaire</option>
              <option value="expert">Expert</option>
            </select>
          </div>
          
          <div class="search-modal-results" id="search-modal-results">
            <p class="search-modal-placeholder">Commencez à taper pour chercher...</p>
          </div>
        </div>
      </div>
    `;

    // Insert modal into DOM
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    this.modal = document.getElementById('search-modal');
    this.searchInput = document.getElementById('search-modal-input');
    this.resultsContainer = document.getElementById('search-modal-results');
    
    // Initialize results container with placeholder
    this.resultsContainer.innerHTML = '<p class="search-modal-placeholder">Commencez à taper pour chercher...</p>';
  }

  attachListeners() {
    // Open modal on header search bar click
    const headerSearch = document.getElementById('search-input');
    if (headerSearch) {
      headerSearch.addEventListener('focus', () => this.open());
      headerSearch.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
          this.open();
          e.preventDefault();
        }
      });
    }

    // Keyboard shortcuts
    document.addEventListener('keydown', (e) => {
      if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        this.toggle();
      }
      if (e.key === 'Escape') {
        this.close();
      }
    });

    // Modal controls
    document.getElementById('search-modal-close').addEventListener('click', () => this.close());
    document.getElementById('search-modal-overlay').addEventListener('click', () => this.close());

    // Search input
    this.searchInput.addEventListener('input', (e) => this.performSearch(e.target.value));

    // Filter changes
    document.getElementById('search-filter-type').addEventListener('change', () => {
      if (this.searchInput.value) this.performSearch(this.searchInput.value);
    });
    document.getElementById('search-filter-level').addEventListener('change', () => {
      if (this.searchInput.value) this.performSearch(this.searchInput.value);
    });
  }

  open() {
    this.modal.removeAttribute('hidden');
    this.searchInput.focus();
  }

  close() {
    this.modal.setAttribute('hidden', '');
    this.searchInput.value = '';
    this.resultsContainer.innerHTML = '<p class="search-modal-placeholder">Commencez à taper pour chercher...</p>';
  }

  toggle() {
    if (this.modal.hasAttribute('hidden')) {
      this.open();
    } else {
      this.close();
    }
  }

  performSearch(query) {
    if (!query.trim()) {
      this.resultsContainer.innerHTML = '<p class="search-modal-placeholder">Commencez à taper pour chercher...</p>';
      return;
    }

    const filterType = document.getElementById('search-filter-type').value;
    const filterLevel = document.getElementById('search-filter-level').value;

    // Simulate search results (in production, this would call an API)
    const results = this.mockSearch(query, filterType, filterLevel);
    
    if (results.length === 0) {
      this.resultsContainer.innerHTML = '<p class="search-modal-no-results">Aucun résultat trouvé</p>';
      return;
    }

    this.resultsContainer.innerHTML = results.map(result => this.renderResult(result)).join('');
  }

  mockSearch(query, type, level) {
    // Mock data - replace with actual API call
    const mockData = {
      stagiaire: {
        mentors: [
          { id: 1, name: 'Lea Bernard', skill: 'React', level: 'expert', rating: 4.9, available: true, type: 'mentor' },
          { id: 2, name: 'Karim Dufour', skill: 'Node.js', level: 'intermediaire', rating: 4.7, available: false, type: 'mentor' },
          { id: 3, name: 'Sofia Martin', skill: 'SQL', level: 'expert', rating: 4.8, available: true, type: 'mentor' },
        ],
        skills: [
          { id: 1, name: 'React', category: 'Frontend', count: 24 },
          { id: 2, name: 'SQL', category: 'Database', count: 18 },
          { id: 3, name: 'Node.js', category: 'Backend', count: 15 },
        ],
      },
      mentor: {
        requests: [
          { id: 1, title: 'Aide React Hooks', trainee: 'Sophie M.', level: 'intermediaire', urgent: false, type: 'request' },
          { id: 2, title: 'Debug API REST', trainee: 'Marc T.', level: 'debutant', urgent: true, type: 'request' },
        ],
        trainees: [
          { id: 1, name: 'Sophie Martin', skill: 'React', level: 'debutant', available: true, type: 'trainee' },
          { id: 2, name: 'Marc Thomas', skill: 'Node.js', level: 'intermediaire', available: true, type: 'trainee' },
        ],
      },
    };

    const roleData = mockData[this.userRole] || mockData.stagiaire;
    let dataSource = [];

    if (type === 'all') {
      dataSource = Object.values(roleData).flat();
    } else {
      dataSource = roleData[type] || [];
    }

    // Filter by query and level
    return dataSource.filter(item => {
      const matchesQuery = (item.name || item.title || item.skill || '').toLowerCase().includes(query.toLowerCase());
      const matchesLevel = !level || item.level === level;
      return matchesQuery && matchesLevel;
    }).slice(0, 8);
  }

  renderResult(result) {
    const { type, name, title, skill, level, rating, available, urgent } = result;

    if (type === 'mentor') {
      return `
        <div class="search-result-item mentor-result">
          <div class="result-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></div>
          <div class="result-content">
            <div class="result-title">${name}</div>
            <div class="result-meta">${skill} · ${level}</div>
          </div>
          <div class="result-badge">${rating}/5</div>
          <a href="recherche.php?mentor=${result.id}" class="result-link" aria-label="Voir ${name}"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg></a>
        </div>
      `;
    }

    if (type === 'request') {
      return `
        <div class="search-result-item request-result">
          <div class="result-icon">${urgent ? '<svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor" stroke="none"><circle cx="12" cy="12" r="10"/></svg>' : '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/></svg>'}</div>
          <div class="result-content">
            <div class="result-title">${title}</div>
            <div class="result-meta">${level} · ${result.trainee}</div>
          </div>
          <div class="result-badge">${urgent ? 'Urgent' : 'Normal'}</div>
          <a href="marketplace.php?request=${result.id}" class="result-link" aria-label="Voir demande"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg></a>
        </div>
      `;
    }

    if (type === 'trainee') {
      return `
        <div class="search-result-item trainee-result">
          <div class="result-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg></div>
          <div class="result-content">
            <div class="result-title">${name}</div>
            <div class="result-meta">${skill} · ${level}</div>
          </div>
          <div class="result-badge">${available ? '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Disponible' : '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg> Indisponible'}</div>
          <a href="../pages_stagiaire/profile.php" class="result-link" aria-label="Voir profil"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg></a>
        </div>
      `;
    }

    if (type === 'skill') {
      return `
        <div class="search-result-item skill-result">
          <div class="result-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18h6"/><path d="M10 22h4"/><path d="M15.09 14c.18-.98.65-1.74 1.41-2.5A4.65 4.65 0 0 0 18 8 6 6 0 0 0 6 8c0 1 .23 2.23 1.5 3.5A4.61 4.61 0 0 1 8.91 14"/></svg></div>
          <div class="result-content">
            <div class="result-title">${name}</div>
            <div class="result-meta">${result.category} · ${result.count} mentors</div>
          </div>
          <a href="recherche.php?skill=${name}" class="result-link" aria-label="Rechercher ${name}"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg></a>
        </div>
      `;
    }

    return '';
  }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
  window.searchModalInstance = new SearchModal();
});
