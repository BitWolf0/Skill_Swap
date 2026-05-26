'use strict';

class SearchPageHandler {
  constructor() {
    this.currentRole = this.detectRole();
    this.cards = [];
    this.filteredCards = [];
    
    this.init();
  }

  detectRole() {
    const path = window.location.pathname;
    if (path.includes('pages_stagiaire')) return 'stagiaire';
    if (path.includes('pages_mentor')) return 'mentor';
    if (path.includes('formateur_pages')) return 'formateur';
    return 'unknown';
  }

  init() {
    // Get all cards from the grid
    this.cards = Array.from(document.querySelectorAll('[role="listitem"]'));
    this.filteredCards = [...this.cards];
    
    // Attach event listeners to all filter inputs
    this.attachFilterListeners();
    this.attachSortListener();
    this.attachSearchListener();
    this.attachActionButtons();
  }

  attachFilterListeners() {
    const filterSelects = document.querySelectorAll('[id^="filter-"]');
    const filterToggles = document.querySelectorAll('.filter-toggle input[type="checkbox"]');
    
    filterSelects.forEach(select => {
      select.addEventListener('change', () => this.applyFilters());
    });
    
    filterToggles.forEach(checkbox => {
      checkbox.addEventListener('change', () => this.applyFilters());
    });
  }

  attachSearchListener() {
    const searchInputs = document.querySelectorAll('#search-mentor, #search-request');
    searchInputs.forEach(input => {
      input.addEventListener('input', (e) => this.performSearch(e.target.value));
    });
  }

  attachSortListener() {
    const sortSelect = document.getElementById('sort-by');
    if (sortSelect) {
      sortSelect.addEventListener('change', () => this.applySorting());
    }
  }

  attachActionButtons() {
    const actionButtons = document.querySelectorAll('.btn-demander');
    actionButtons.forEach(btn => {
      btn.addEventListener('click', (e) => {
        const card = e.target.closest('[role="listitem"]');
        if (card && this.currentRole === 'stagiaire') {
          const name = card.querySelector('h3')?.textContent || '';
          const url = `nouvelle_demande.html?mentor=${encodeURIComponent(name)}`;
          window.location.href = url;
        } else if (card && this.currentRole === 'mentor') {
          showToast('Demande acceptée! Vous pouvez maintenant communiquer avec le stagiaire.', 'success');
          btn.disabled = true;
          btn.textContent = 'Engagé';
        }
      });
    });
  }

  performSearch(query) {
    const searchTerm = query.toLowerCase().trim();
    
    if (searchTerm === '') {
      this.filteredCards = [...this.cards];
    } else {
      this.filteredCards = this.cards.filter(card => {
        const name = card.querySelector('h3')?.textContent.toLowerCase() || '';
        const desc = card.querySelector('.mentor-desc')?.textContent.toLowerCase() || '';
        const badges = Array.from(card.querySelectorAll('.mentor-badge')).map(b => b.textContent.toLowerCase()).join(' ');
        
        return name.includes(searchTerm) || desc.includes(searchTerm) || badges.includes(searchTerm);
      });
    }
    
    this.applyFilters();
  }

  applyFilters() {
    let result = [...this.filteredCards];
    
    if (this.currentRole === 'stagiaire') {
      result = this.applyStageFilters(result);
    } else if (this.currentRole === 'mentor') {
      result = this.applyMentorFilters(result);
    }
    
    this.applySorting();
    this.displayResults(result);
    this.updateResultsCount(result.length);
  }

  applyStageFilters(cards) {
    const skillFilter = document.getElementById('filter-skill')?.value || '';
    const levelFilter = document.getElementById('filter-level')?.value || '';
    const ratingFilter = parseFloat(document.getElementById('filter-rating')?.value || 0);
    const availableOnly = document.getElementById('filter-available')?.checked || false;
    const responsiveOnly = document.getElementById('filter-responsive')?.checked || false;
    
    return cards.filter(card => {
      // Skill filter - check if skill appears in badges
      if (skillFilter) {
        const badges = Array.from(card.querySelectorAll('.mentor-badge')).map(b => b.textContent.toLowerCase());
        if (!badges.some(b => b.includes(skillFilter.toLowerCase()))) {
          return false;
        }
      }
      
      // Level filter
      if (levelFilter) {
        const cardLevel = card.getAttribute('data-level') || '';
        if (cardLevel !== levelFilter) {
          return false;
        }
      }
      
      // Rating filter
      if (ratingFilter > 0) {
        const ratingText = card.querySelector('.mentor-info span')?.textContent || '';
        const ratingMatch = ratingText.match(/(\d+\.?\d*)/);
        if (ratingMatch) {
          const cardRating = parseFloat(ratingMatch[1]);
          if (cardRating < ratingFilter) {
            return false;
          }
        }
      }
      
      // Available only filter
      if (availableOnly) {
        const availability = card.getAttribute('data-availability') || '';
        if (availability !== 'disponible') {
          return false;
        }
      }
      
      // Responsive only filter (response time < 2h)
      if (responsiveOnly) {
        const metaText = card.querySelector('.mentor-meta')?.textContent || '';
        if (!metaText.includes('1h') && !metaText.includes('2h')) {
          return false;
        }
      }
      
      return true;
    });
  }

  applyMentorFilters(cards) {
    const skillFilter = document.getElementById('filter-skill')?.value || '';
    const levelFilter = document.getElementById('filter-level')?.value || '';
    const urgencyFilter = document.getElementById('filter-urgency')?.value || '';
    const newRequestsOnly = document.getElementById('filter-new-requests')?.checked || false;
    
    return cards.filter(card => {
      // Skill filter
      if (skillFilter) {
        const badges = Array.from(card.querySelectorAll('.mentor-badge')).map(b => b.textContent.toLowerCase());
        if (!badges.some(b => b.includes(skillFilter.toLowerCase()))) {
          return false;
        }
      }
      
      // Level (difficulty) filter
      if (levelFilter) {
        const cardLevel = card.getAttribute('data-level') || '';
        if (cardLevel !== levelFilter) {
          return false;
        }
      }
      
      // Urgency filter
      if (urgencyFilter) {
        const cardUrgency = card.getAttribute('data-urgency') || '';
        if (cardUrgency !== urgencyFilter) {
          return false;
        }
      }
      
      // New requests only
      if (newRequestsOnly) {
        const metaText = card.querySelector('.mentor-meta')?.textContent || '';
        if (!metaText.includes('il y a 1h') && !metaText.includes('il y a 2h')) {
          return false;
        }
      }
      
      return true;
    });
  }

  applySorting() {
    const sortValue = document.getElementById('sort-by')?.value || 'relevance';
    const cardsToSort = Array.from(this.filteredCards);
    
    if (this.currentRole === 'stagiaire') {
      this.sortStageResults(cardsToSort, sortValue);
    } else if (this.currentRole === 'mentor') {
      this.sortMentorResults(cardsToSort, sortValue);
    }
    
    this.filteredCards = cardsToSort;
  }

  sortStageResults(cards, sortBy) {
    switch (sortBy) {
      case 'rating':
        cards.sort((a, b) => {
          const ratingA = this.extractRating(a);
          const ratingB = this.extractRating(b);
          return ratingB - ratingA;
        });
        break;
      
      case 'response-time':
        cards.sort((a, b) => {
          const timeA = this.extractResponseTime(a);
          const timeB = this.extractResponseTime(b);
          return timeA - timeB;
        });
        break;
      
      case 'helps':
        cards.sort((a, b) => {
          const helpsA = this.extractHelpCount(a);
          const helpsB = this.extractHelpCount(b);
          return helpsB - helpsA;
        });
        break;
      
      case 'relevance':
      default:
        // Keep original order
        break;
    }
  }

  sortMentorResults(cards, sortBy) {
    switch (sortBy) {
      case 'urgency':
        // Sort by urgency level: high > medium > low
        const urgencyOrder = { high: 0, medium: 1, low: 2 };
        cards.sort((a, b) => {
          const urgencyA = urgencyOrder[a.getAttribute('data-urgency') || 'low'] || 2;
          const urgencyB = urgencyOrder[b.getAttribute('data-urgency') || 'low'] || 2;
          return urgencyA - urgencyB;
        });
        break;
      
      case 'matching':
        // Sort by number of matching badges
        cards.sort((a, b) => {
          const badgesA = a.querySelectorAll('.mentor-badge').length;
          const badgesB = b.querySelectorAll('.mentor-badge').length;
          return badgesB - badgesA;
        });
        break;
      
      case 'date':
      default:
        // Keep original order
        break;
    }
  }

  extractRating(card) {
    const ratingText = card.querySelector('.mentor-info span')?.textContent || '';
    const match = ratingText.match(/(\d+\.?\d*)/);
    return match ? parseFloat(match[1]) : 0;
  }

  extractResponseTime(card) {
    const metaText = card.querySelector('.mentor-meta')?.textContent || '';
    if (metaText.includes('1h')) return 1;
    if (metaText.includes('2h')) return 2;
    if (metaText.includes('3h')) return 3;
    if (metaText.includes('4h')) return 4;
    return 999;
  }

  extractHelpCount(card) {
    const metaText = card.querySelector('.mentor-meta')?.textContent || '';
    const match = metaText.match(/(\d+)\s+aides?/);
    return match ? parseInt(match[1]) : 0;
  }

  displayResults(cardsToDisplay) {
    const grid = document.querySelector('[role="list"].mentor-grid, [role="list"].requests-grid');
    if (!grid) return;
    
    // Hide all cards
    this.cards.forEach(card => {
      card.style.display = 'none';
    });
    
    // Show filtered cards
    cardsToDisplay.forEach(card => {
      card.style.display = '';
    });
  }

  updateResultsCount(count) {
    const countElement = document.getElementById('results-count');
    if (countElement) {
      const label = this.currentRole === 'stagiaire' ? 'mentor' : 'demande';
      countElement.innerHTML = `Affichage de <strong>${count}</strong> ${label}${count > 1 ? 's' : ''}`;
    }
  }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
  new SearchPageHandler();
});
