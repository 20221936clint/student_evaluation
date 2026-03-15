/**
 * Session Guard - Prevents access to protected pages via browser back/forward buttons after logout
 * Include this script on every protected dashboard page
 */
(function() {
    'use strict';

    // If user just logged in, clear the flag and skip the immediate logout check
    // This prevents the race condition where login.php's inline script sets 'logged_out'
    // but the redirect happens before session_guard can detect the valid session
    var justLoggedIn = false;
    if (sessionStorage.getItem('just_logged_in') === 'true') {
        justLoggedIn = true;
        sessionStorage.removeItem('just_logged_in');
        sessionStorage.removeItem('logged_out');
        sessionStorage.setItem('on_protected_page', 'true');
        // Skip the immediate check - we trust the login was successful
        // The async checks below will still verify the session periodically
    } else if (sessionStorage.getItem('logged_out') === 'true') {
        // IMMEDIATE CHECK: If user was logged out, redirect right away
        // Do NOT remove the flag here - only login.js clears it on successful login
        // User has logged out - redirect to login immediately
        // Use replace so they can't use forward button to come back
        window.location.replace(getLoginPath());
        // Stop all script execution
        throw new Error('Session expired - redirecting to login');
    }

    // Use synchronous XMLHttpRequest to verify session BEFORE page renders
    // This blocks the page from showing any content if session is invalid
    function checkSessionSync() {
        // If user just logged in, skip the sync check to avoid race condition
        if (sessionStorage.getItem('on_protected_page') === 'true') {
            // We have a valid session indicator, still verify but don't force logout on network errors
            try {
                var xhr = new XMLHttpRequest();
                var checkPath = getBasePath() + 'data/check_session.php';
                // Synchronous request - blocks until response
                xhr.open('GET', checkPath, false);
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                xhr.withCredentials = true;
                xhr.send();
                
                if (xhr.status === 200) {
                    try {
                        var response = JSON.parse(xhr.responseText);
                        if (!response.authenticated) {
                            forceLogout();
                        }
                    } catch (e) {
                        // JSON parse error - don't force logout, could be server issue
                        console.warn('Session check: Could not parse response');
                    }
                } else if (xhr.status === 401) {
                    forceLogout();
                }
                // For other status codes (0, 500, etc.), don't force logout
            } catch (e) {
                // Network error - don't force logout if we have session indicator
                console.warn('Session check: Network error', e.message);
            }
            return;
        }

        try {
            var xhr = new XMLHttpRequest();
            var checkPath = getBasePath() + 'data/check_session.php';
            // Synchronous request - blocks until response
            xhr.open('GET', checkPath, false);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.withCredentials = true;
            xhr.send();
            
            if (xhr.status === 200) {
                try {
                    var response = JSON.parse(xhr.responseText);
                    if (!response.authenticated) {
                        forceLogout();
                    }
                } catch (e) {
                    forceLogout();
                }
            } else {
                forceLogout();
            }
        } catch (e) {
            // If the request itself fails (network error), force logout for safety
            if (e.message !== 'Session expired - redirecting to login') {
                forceLogout();
            }
        }
    }

    // Use async XMLHttpRequest for subsequent checks (pageshow, visibility)
    function checkSessionAsync() {
        var xhr = new XMLHttpRequest();
        var checkPath = getBasePath() + 'data/check_session.php';
        xhr.open('GET', checkPath, true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    try {
                        var response = JSON.parse(xhr.responseText);
                        if (!response.authenticated) {
                            forceLogout();
                        }
                    } catch (e) {
                        forceLogout();
                    }
                } else if (xhr.status === 401) {
                    forceLogout();
                }
            }
        };
        xhr.send();
    }

    function forceLogout() {
        // Set the flag so any future page loads also redirect
        sessionStorage.setItem('logged_out', 'true');
        window.location.replace(getLoginPath());
    }

    // Determine the base path to the project root
    function getBasePath() {
        var path = window.location.pathname;
        // Handle sub-pages (3 levels deep: /Door/role/pages/)
        if (path.indexOf('/pages/') !== -1) {
            return '../../../';
        }
        // Handle dashboard pages (2 levels deep: /Door/role/)
        if (path.indexOf('/Door/admin/') !== -1 ||
            path.indexOf('/Door/program_head/') !== -1 ||
            path.indexOf('/Door/instructor/') !== -1) {
            return '../../';
        }
        return '../';
    }

    // Get the login page path
    function getLoginPath() {
        var path = window.location.pathname;
        // Handle sub-pages (3 levels deep)
        if (path.indexOf('/pages/') !== -1) {
            return '../../login.php';
        }
        // Handle dashboard pages (2 levels deep)
        if (path.indexOf('/Door/admin/') !== -1 ||
            path.indexOf('/Door/program_head/') !== -1 ||
            path.indexOf('/Door/instructor/') !== -1) {
            return '../login.php';
        }
        return './Door/login.php';
    }

    // INITIAL CHECK: Verify session with server synchronously (blocks page render)
    // Skip the sync check if user just logged in to avoid race conditions
    if (!justLoggedIn) {
        checkSessionSync();
    }

    // Check on pageshow event (fires when page is loaded from bfcache via back/forward)
    window.addEventListener('pageshow', function(event) {
        if (event.persisted) {
            // Page was loaded from back/forward cache - must re-check
            checkSessionAsync();
        }
    });

    // Also check when page becomes visible again (tab switching)
    document.addEventListener('visibilitychange', function() {
        if (!document.hidden) {
            checkSessionAsync();
        }
    });

    // Prevent the page from being stored in bfcache
    window.addEventListener('unload', function() {
        // This empty handler prevents bfcache in most browsers
    });

})();