// ============================
// HAIL EVENTS - MAIN JAVASCRIPT
// ============================

// Debounce function for search
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Search events with AJAX
const searchInput = document.getElementById('search-input');
if (searchInput) {
    searchInput.addEventListener('input', debounce(function() {
        const keyword = this.value;
        if (keyword.length > 2) {
            fetch('api/search.php?q=' + encodeURIComponent(keyword))
                .then(response => response.json())
                .then(data => {
                    console.log('Search results:', data);
                    // Display suggestions
                })
                .catch(error => console.error('Error:', error));
        }
    }, 300));
}

// Filter events
function filterEvents() {
    const category = document.getElementById('filter-category')?.value || '';
    const date = document.getElementById('filter-date')?.value || '';
    const priceType = document.getElementById('filter-price')?.value || '';

    const params = new URLSearchParams();
    if (category) params.append('category', category);
    if (date) params.append('date', date);
    if (priceType) params.append('price', priceType);

    window.location.href = 'events.php?' + params.toString();
}

// Add event to favorites
function addToFavorites(eventId) {
    fetch('api/save-event.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ event_id: eventId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Event added to favorites!');
            document.getElementById('favorite-btn-' + eventId).classList.add('active');
        }
    })
    .catch(error => console.error('Error:', error));
}

// Remove event from favorites
function removeFromFavorites(eventId) {
    fetch('api/unsave-event.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ event_id: eventId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Event removed from favorites!');
            document.getElementById('favorite-btn-' + eventId).classList.remove('active');
        }
    })
    .catch(error => console.error('Error:', error));
}

// Calendar navigation
function previousMonth() {
    const currentDate = new Date(document.getElementById('calendar-month').dataset.date);
    currentDate.setMonth(currentDate.getMonth() - 1);
    loadCalendar(currentDate);
}

function nextMonth() {
    const currentDate = new Date(document.getElementById('calendar-month').dataset.date);
    currentDate.setMonth(currentDate.getMonth() + 1);
    loadCalendar(currentDate);
}

function loadCalendar(date) {
    const year = date.getFullYear();
    const month = date.getMonth() + 1;
    window.location.href = 'calendar.php?year=' + year + '&month=' + month;
}

// Form validation
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return true;

    let isValid = true;
    const inputs = form.querySelectorAll('input, textarea, select');

    inputs.forEach(input => {
        if (input.hasAttribute('required') && !input.value.trim()) {
            input.parentElement.classList.add('error');
            isValid = false;
        } else {
            input.parentElement.classList.remove('error');
        }
    });

    return isValid;
}

// Show notification
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = 'alert alert-' + type;
    notification.textContent = message;
    notification.style.position = 'fixed';
    notification.style.top = '20px';
    notification.style.right = '20px';
    notification.style.zIndex = '1000';
    notification.style.minWidth = '300px';

    document.body.appendChild(notification);

    setTimeout(() => {
        notification.remove();
    }, 5000);
}

// Format date
function formatDate(dateString) {
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('en-US', options);
}

// Format time
function formatTime(timeString) {
    const options = { hour: '2-digit', minute: '2-digit' };
    return new Date('2000-01-01 ' + timeString).toLocaleTimeString('en-US', options);
}

// Toggle dropdown menu
function toggleDropdown(id) {
    const dropdown = document.getElementById(id);
    if (dropdown) {
        dropdown.classList.toggle('hidden');
    }
}

// Close all dropdowns when clicking outside
document.addEventListener('click', function(event) {
    const dropdowns = document.querySelectorAll('[id$="-dropdown"]');
    dropdowns.forEach(dropdown => {
        if (!dropdown.contains(event.target) && !event.target.matches('[data-toggle]')) {
            dropdown.classList.add('hidden');
        }
    });
});

// Smooth scroll to element
function smoothScroll(elementId) {
    const element = document.getElementById(elementId);
    if (element) {
        element.scrollIntoView({ behavior: 'smooth' });
    }
}

// Initialize tooltips
function initTooltips() {
    const tooltips = document.querySelectorAll('[data-tooltip]');
    tooltips.forEach(element => {
        element.addEventListener('mouseenter', function() {
            const tooltip = document.createElement('div');
            tooltip.className = 'tooltip';
            tooltip.textContent = this.getAttribute('data-tooltip');
            tooltip.style.position = 'absolute';
            tooltip.style.background = 'rgba(0, 0, 0, 0.8)';
            tooltip.style.color = 'white';
            tooltip.style.padding = '8px 12px';
            tooltip.style.borderRadius = '6px';
            tooltip.style.fontSize = '12px';
            tooltip.style.whiteSpace = 'nowrap';
            tooltip.style.zIndex = '1000';
            
            document.body.appendChild(tooltip);
            
            const rect = this.getBoundingClientRect();
            tooltip.style.top = (rect.top - tooltip.offsetHeight - 10) + 'px';
            tooltip.style.left = (rect.left + rect.width / 2 - tooltip.offsetWidth / 2) + 'px';
        });

        element.addEventListener('mouseleave', function() {
            const tooltip = document.querySelector('.tooltip');
            if (tooltip) tooltip.remove();
        });
    });
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    initTooltips();
});

// Export functions for use in other scripts
window.HailEvents = {
    debounce,
    filterEvents,
    addToFavorites,
    removeFromFavorites,
    validateForm,
    showNotification,
    formatDate,
    formatTime,
    toggleDropdown,
    smoothScroll
};
