// ============================================
// Dashboard JavaScript Functions
// ============================================

const Dashboard = {
    // Initialize dashboard
    init: function() {
        this.setupTabButtons();
        this.setupSidebarNavigation();
        this.setupEventListeners();
    },

    // Setup tab button functionality
    setupTabButtons: function() {
        const tabButtons = document.querySelectorAll('.tab-button');
        const tabContents = document.querySelectorAll('.tab-content');

        tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                const tabName = this.getAttribute('data-tab');

                // Remove active class from all buttons and contents
                tabButtons.forEach(btn => btn.classList.remove('active'));
                tabContents.forEach(content => content.classList.remove('active'));

                // Add active class to clicked button and corresponding content
                this.classList.add('active');
                const activeContent = document.getElementById(tabName);
                if (activeContent) {
                    activeContent.classList.add('active');
                }
            });
        });
    },

    // Setup sidebar navigation
    setupSidebarNavigation: function() {
        const sidebarLinks = document.querySelectorAll('.sidebar-nav a');
        const currentPage = window.location.pathname.split('/').pop() || 'index.php';

        sidebarLinks.forEach(link => {
            const href = link.getAttribute('href');
            if (href === currentPage || href.includes(currentPage)) {
                link.classList.add('active');
            }

            link.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                if (href && !href.startsWith('#')) {
                    // Remove active class from all links
                    sidebarLinks.forEach(l => l.classList.remove('active'));
                    // Add active class to clicked link
                    this.classList.add('active');
                }
            });
        });
    },

    // Setup event listeners
    setupEventListeners: function() {
        // Delete confirmation
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('btn-delete')) {
                if (!confirm('Are you sure you want to delete this item?')) {
                    e.preventDefault();
                }
            }
        });
    },

    // Show/hide content
    toggleContent: function(contentId) {
        const content = document.getElementById(contentId);
        if (content) {
            content.classList.toggle('hidden');
        }
    },

    // Show content
    showContent: function(contentId) {
        const content = document.getElementById(contentId);
        if (content) {
            content.classList.remove('hidden');
        }
    },

    // Hide content
    hideContent: function(contentId) {
        const content = document.getElementById(contentId);
        if (content) {
            content.classList.add('hidden');
        }
    },

    // Switch tab
    switchTab: function(tabName) {
        const button = document.querySelector(`[data-tab="${tabName}"]`);
        if (button) {
            button.click();
        }
    }
};

// Initialize dashboard when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    Dashboard.init();
});
