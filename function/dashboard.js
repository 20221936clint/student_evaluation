document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.getElementById('sidebar');
    const navItems = document.querySelectorAll('.sidebar-nav-item');
    const viewAllLinks = document.querySelectorAll('.view-all');
    const pageTitle = document.getElementById('pageTitle');

    // Page titles
    const pageTitles = {
        'overview': 'Dashboard',
        'evaluations': 'Evaluations',
        'instructors': 'Instructors',
        'courses': 'Courses',
        'departments': 'Departments',
        'reports': 'Reports',
        'settings': 'Settings'
    };

    // Toggle sidebar on mobile
    if (menuToggle) {
        menuToggle.addEventListener('click', function() {
            sidebar.classList.toggle('open');
        });
    }

    // Handle navigation
    function navigateToPage(page) {
        // Hide all pages
        document.querySelectorAll('.page-content').forEach(page => {
            page.classList.add('page-hidden');
        });
        
        // Show selected page
        const selectedPage = document.getElementById('page-' + page);
        if (selectedPage) {
            selectedPage.classList.remove('page-hidden');
        }
        
        // Update page title
        if (pageTitle && pageTitles[page]) {
            pageTitle.textContent = pageTitles[page];
        }
        
        // Update nav active state
        navItems.forEach(item => {
            item.classList.remove('active');
            if (item.getAttribute('data-page') === page) {
                item.classList.add('active');
            }
        });
        
        // Close sidebar on mobile
        if (window.innerWidth <= 992) {
            sidebar.classList.remove('open');
        }
    }

    // Nav item clicks
    navItems.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const page = this.getAttribute('data-page');
            if (page) {
                navigateToPage(page);
            }
        });
    });

    // View all links
    viewAllLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const page = this.getAttribute('data-page');
            if (page) {
                navigateToPage(page);
            }
        });
    });

    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(e) {
        if (window.innerWidth <= 992 && 
            sidebar && 
            sidebar.classList.contains('open') &&
            !sidebar.contains(e.target) &&
            !menuToggle.contains(e.target)) {
            sidebar.classList.remove('open');
        }
    });

    // Handle window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth > 992 && sidebar) {
            sidebar.classList.remove('open');
        }
    });

    // Add fade-in animation to cards on page load
    const cards = document.querySelectorAll('.stat-card, .content-card, .welcome-banner');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        setTimeout(() => {
            card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
});
