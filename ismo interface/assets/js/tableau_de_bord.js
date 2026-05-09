// Dashboard Interactivity Script

document.addEventListener('DOMContentLoaded', function() {
    initializeNavigation();
    initializeRoleButtons();
    initializeValidationActions();
    initializeTaskCheckboxes();
    initializeViewAllButton();
    initializeNotification();
});

/**
 * Initialize navigation between sections
 */
function initializeNavigation() {
    const navItems = document.querySelectorAll('.nav-item');
    
    navItems.forEach(item => {
        item.addEventListener('click', function() {
            // Remove active class from all items
            navItems.forEach(nav => nav.classList.remove('active'));
            // Add active class to clicked item
            this.classList.add('active');
            
            const section = this.dataset.section;
            handleSectionChange(section);
        });
    });
}

/**
 * Handle section changes
 */
function handleSectionChange(section) {
    console.log('Section changed to:', section);
    
    // Here you can implement logic to show/hide different sections
    // For now, just log the action
    switch(section) {
        case 'dashboard':
            console.log('Loading dashboard');
            break;
        case 'validation':
            console.log('Loading validation');
            break;
        case 'statistics':
            console.log('Loading statistics');
            break;
        case 'catalog':
            console.log('Loading catalog');
            break;
    }
}

/**
 * Initialize role button switching
 */
function initializeRoleButtons() {
    const roleButtons = document.querySelectorAll('.role-btn');
    
    roleButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            // Remove active class from all buttons
            roleButtons.forEach(b => b.classList.remove('active'));
            // Add active class to clicked button
            this.classList.add('active');
            
            const role = this.textContent.trim();
            handleRoleChange(role);
        });
    });
}

/**
 * Handle role changes
 */
function handleRoleChange(role) {
    console.log('Role changed to:', role);
    
    // Store the selected role
    localStorage.setItem('selectedRole', role);
    
    // Here you would typically make an API call to switch roles
    // and reload the dashboard with the new role's data
}

/**
 * Initialize validation action buttons
 */
function initializeValidationActions() {
    const validateButtons = document.querySelectorAll('.btn-success');
    const refuseButtons = document.querySelectorAll('.btn-danger');
    
    validateButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const card = this.closest('.validation-card');
            const name = card.querySelector('.card-header h3').textContent;
            const skill = card.querySelector('.skill-tag').textContent;
            
            handleValidation(name, skill, true);
            animateCardRemoval(card);
        });
    });
    
    refuseButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const card = this.closest('.validation-card');
            const name = card.querySelector('.card-header h3').textContent;
            const skill = card.querySelector('.skill-tag').textContent;
            
            handleValidation(name, skill, false);
            animateCardRemoval(card);
        });
    });
}

/**
 * Handle validation action
 */
function handleValidation(name, skill, isApproved) {
    const action = isApproved ? 'approved' : 'refused';
    console.log(`Validation ${action} for ${name} - ${skill}`);
    
    // Here you would make an API call to save the validation
    // For now, just show a notification
    showNotification(
        `Compétence ${action === 'approved' ? 'validée' : 'refusée'}`,
        `${name} - ${skill}`,
        isApproved ? 'success' : 'error'
    );
}

/**
 * Animate card removal
 */
function animateCardRemoval(card) {
    card.style.transition = 'all 0.3s ease';
    card.style.opacity = '0';
    card.style.transform = 'translateX(20px)';
    
    setTimeout(() => {
        card.remove();
        checkEmptyState();
    }, 300);
}

/**
 * Check if there are no more validation cards
 */
function checkEmptyState() {
    const validationItems = document.querySelector('.validation-items');
    if (validationItems && validationItems.children.length === 0) {
        const emptyMessage = document.createElement('div');
        emptyMessage.style.cssText = 'padding: 40px; text-align: center; color: #6B7280;';
        emptyMessage.innerHTML = '<p style="font-size: 16px; margin: 0;">Aucune validation en attente</p>';
        validationItems.appendChild(emptyMessage);
    }
}

/**
 * Initialize task checkbox functionality
 */
function initializeTaskCheckboxes() {
    const taskCheckboxes = document.querySelectorAll('.task-checkbox');
    
    taskCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const taskItem = this.closest('.task-item');
            const taskTitle = taskItem.querySelector('.task-title').textContent;
            
            if (this.checked) {
                taskItem.style.opacity = '0.6';
                taskTitle.style.textDecoration = 'line-through';
                showNotification('Tâche complétée', taskTitle, 'success');
            } else {
                taskItem.style.opacity = '1';
                taskTitle.style.textDecoration = 'none';
            }
        });
    });
}

/**
 * Initialize view all button
 */
function initializeViewAllButton() {
    const viewAllBtn = document.querySelector('.btn-view-all');
    
    if (viewAllBtn) {
        viewAllBtn.addEventListener('click', function() {
            console.log('View all tasks clicked');
            // Navigate to full tasks page or expand section
            // This could be implemented based on your routing system
        });
    }
}

/**
 * Initialize notification button
 */
function initializeNotification() {
    const notificationBtn = document.querySelector('.notification-btn');
    
    if (notificationBtn) {
        notificationBtn.addEventListener('click', function() {
            console.log('Notification icon clicked');
            // This would typically open a notification panel or redirect to notifications page
        });
    }
}

/**
 * Show notification message
 */
function showNotification(title, message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <strong>${title}</strong>
            <p>${message}</p>
        </div>
        <button class="notification-close">×</button>
    `;
    
    // Add styles if not already present
    if (!document.querySelector('#notification-styles')) {
        const style = document.createElement('style');
        style.id = 'notification-styles';
        style.textContent = `
            .notification {
                position: fixed;
                bottom: 20px;
                right: 20px;
                background-color: white;
                border-radius: 8px;
                padding: 16px 20px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                gap: 16px;
                max-width: 350px;
                z-index: 1000;
                animation: slideIn 0.3s ease;
            }
            
            @keyframes slideIn {
                from {
                    transform: translateX(400px);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
            
            @keyframes slideOut {
                from {
                    transform: translateX(0);
                    opacity: 1;
                }
                to {
                    transform: translateX(400px);
                    opacity: 0;
                }
            }
            
            .notification.hidden {
                animation: slideOut 0.3s ease forwards;
            }
            
            .notification-content {
                flex: 1;
            }
            
            .notification-content strong {
                display: block;
                margin-bottom: 4px;
                color: #111827;
            }
            
            .notification-content p {
                margin: 0;
                font-size: 13px;
                color: #6B7280;
            }
            
            .notification-success {
                border-left: 4px solid #10B981;
            }
            
            .notification-success .notification-content strong {
                color: #10B981;
            }
            
            .notification-error {
                border-left: 4px solid #EF4444;
            }
            
            .notification-error .notification-content strong {
                color: #EF4444;
            }
            
            .notification-info {
                border-left: 4px solid #3B82F6;
            }
            
            .notification-info .notification-content strong {
                color: #3B82F6;
            }
            
            .notification-close {
                background: none;
                border: none;
                font-size: 24px;
                color: #9CA3AF;
                cursor: pointer;
                padding: 0;
                width: 24px;
                height: 24px;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: color 0.3s ease;
            }
            
            .notification-close:hover {
                color: #6B7280;
            }
        `;
        document.head.appendChild(style);
    }
    
    // Add to page
    document.body.appendChild(notification);
    
    // Close button handler
    const closeBtn = notification.querySelector('.notification-close');
    closeBtn.addEventListener('click', function() {
        notification.classList.add('hidden');
        setTimeout(() => notification.remove(), 300);
    });
    
    // Auto-close after 4 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.classList.add('hidden');
            setTimeout(() => notification.remove(), 300);
        }
    }, 4000);
}

/**
 * Enhanced interaction logging
 */
function logInteraction(action, details = {}) {
    const timestamp = new Date().toLocaleTimeString();
    console.log(`[${timestamp}] ${action}`, details);
    
    // This could be sent to analytics or logged server-side
}

// Add smooth scroll behavior
document.documentElement.style.scrollBehavior = 'smooth';

// Handle search bar interaction
const searchBar = document.querySelector('.search-bar');
if (searchBar) {
    searchBar.addEventListener('input', function(e) {
        const searchTerm = e.target.value.trim();
        if (searchTerm.length > 0) {
            logInteraction('Search', { term: searchTerm });
            // You could implement live search here
        }
    });
}

// Add keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + K to focus search
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        if (searchBar) {
            searchBar.focus();
        }
    }
});

console.log('Dashboard initialized successfully');
