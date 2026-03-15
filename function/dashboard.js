// ========================================
// Dashboard JavaScript - Faculty Evaluation System
// ========================================

document.addEventListener('DOMContentLoaded', function() {
    // Sidebar Toggle for Mobile
    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.getElementById('sidebar');
    
    if (menuToggle && sidebar) {
        menuToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            sidebar.classList.toggle('open');
        });
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(e) {
            if (window.innerWidth <= 992) {
                if (!sidebar.contains(e.target) && !menuToggle.contains(e.target)) {
                    sidebar.classList.remove('open');
                }
            }
        });
        
        // Close sidebar on window resize if going to desktop
        window.addEventListener('resize', function() {
            if (window.innerWidth > 992) {
                sidebar.classList.remove('open');
            }
        });
    }
    
    // Active nav item highlighting based on current page
    const currentPage = window.location.pathname.split('/').pop();
    const navItems = document.querySelectorAll('.sidebar-nav-item');
    
    navItems.forEach(function(item) {
        const href = item.getAttribute('href');
        const hrefPage = href ? href.split('/').pop() : '';
        
        // Remove all active classes first
        item.classList.remove('active');
        
        // Set active class based on current page
        if (currentPage === hrefPage) {
            item.classList.add('active');
        }
        // Handle dashboard.php as the overview page
        if (currentPage === 'dashboard.php' && hrefPage === 'dashboard.php') {
            item.classList.add('active');
        }
    });
    
    // Toast notification helper
    window.showToast = function(message) {
        // Remove existing toast if any
        const existingToast = document.querySelector('.toast');
        if (existingToast) {
            existingToast.remove();
        }
        
        const toast = document.createElement('div');
        toast.className = 'toast';
        toast.innerHTML = '<span class="toast-icon"><i class="fas fa-check-circle"></i></span><span class="toast-message">' + message + '</span>';
        document.body.appendChild(toast);
        
        // Show toast
        setTimeout(function() {
            toast.classList.add('show');
        }, 100);
        
        // Hide toast after 3 seconds
        setTimeout(function() {
            toast.classList.remove('show');
            setTimeout(function() {
                toast.remove();
            }, 300);
        }, 3000);
    };
    
    // Search functionality for student tables
    const searchInput = document.querySelector('.eval-search-input');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const tableRows = document.querySelectorAll('.eval-table tbody tr');
            
            tableRows.forEach(function(row) {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
    
    // Filter functionality for course dropdowns
    const filterSelect = document.querySelector('.eval-filter-select');
    if (filterSelect) {
        filterSelect.addEventListener('change', function() {
            const selectedValue = this.value;
            const tableRows = document.querySelectorAll('.eval-table tbody tr');
            const feedbackItems = document.querySelectorAll('.feedback-item');
            
            // Filter table rows
            tableRows.forEach(function(row) {
                if (selectedValue === 'All Courses' || selectedValue === '') {
                    row.style.display = '';
                } else {
                    const text = row.textContent;
                    if (text.includes(selectedValue.split(' - ')[0])) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                }
            });
            
            // Filter feedback items
            feedbackItems.forEach(function(item) {
                if (selectedValue === 'All Courses' || selectedValue === '') {
                    item.style.display = '';
                } else {
                    const courseName = item.querySelector('.feedback-course');
                    if (courseName && courseName.textContent.includes(selectedValue.split(' - ')[1])) {
                        item.style.display = '';
                    } else {
                        item.style.display = 'none';
                    }
                }
            });
        });
    }
    
    // Animate progress bars on page load
    const progressBars = document.querySelectorAll('.progress');
    progressBars.forEach(function(bar) {
        const width = bar.style.width;
        bar.style.width = '0%';
        setTimeout(function() {
            bar.style.width = width;
        }, 500);
    });
});