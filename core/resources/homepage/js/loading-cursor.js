/*
 *
 *  * Copyright (c) 2022-2025 Bearsampp
 *  * License: GNU General Public License version 3 or later; see LICENSE.txt
 *  * Website: https://bearsampp.com
 *  * Github: https://github.com/Bearsampp
 *
 */

// Set loading cursor immediately - don't wait for DOMContentLoaded
(function() {
    // Set cursor immediately on script load
    document.documentElement.classList.add('loading-cursor');
    
    // Function to show loading cursor and overlay
    window.showLoadingState = function() {
        document.documentElement.classList.add('loading-cursor');
        if (document.body) document.body.classList.add('loading-cursor');
        
        // Create and show loading overlay if body exists
        if (document.body) {
            const overlay = document.createElement('div');
            overlay.className = 'loading-overlay';
            overlay.innerHTML = '<div class="waitloader"></div>';
            document.body.appendChild(overlay);
            return overlay;
        }
        return null;
    };

    // Function to hide loading cursor and overlay
    window.hideLoadingState = function(overlay) {
        document.documentElement.classList.remove('loading-cursor');
        if (document.body) document.body.classList.remove('loading-cursor');
        
        // Remove overlay if it exists
        if (overlay && overlay.parentNode) {
            overlay.parentNode.removeChild(overlay);
        }
    };

    // Remove loading cursor when page is fully loaded
    window.addEventListener('load', function() {
        document.documentElement.classList.remove('loading-cursor');
        if (document.body) document.body.classList.remove('loading-cursor');
        
        // If there's an overlay, remove it
        const existingOverlay = document.querySelector('.loading-overlay');
        if (existingOverlay) {
            existingOverlay.parentNode.removeChild(existingOverlay);
        }
    });

    // Function for AJAX requests with loading state
    window.fetchWithLoading = function(url, options = {}) {
        const overlay = showLoadingState();
        
        return fetch(url, options)
            .then(response => {
                hideLoadingState(overlay);
                return response;
            })
            .catch(error => {
                hideLoadingState(overlay);
                throw error;
            });
    };

    // Set up event handlers when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        // Add event listeners to all localhost links
        const localhostLinks = document.querySelectorAll('a[href^="http://localhost"], a[href^="https://localhost"], a[href^="/"]');
        
        localhostLinks.forEach(link => {
            link.addEventListener('click', function(event) {
                // Don't show loading state for links opening in new tabs/windows
                if (!event.ctrlKey && !event.metaKey && link.target !== '_blank') {
                    showLoadingState();
                }
            });
        });

        // Intercept XMLHttpRequest to show loading cursor
        const originalXhrOpen = XMLHttpRequest.prototype.open;
        const originalXhrSend = XMLHttpRequest.prototype.send;
        let activeRequests = 0;
        let loadingOverlay = null;

        XMLHttpRequest.prototype.open = function() {
            this._isLocalhost = false;
            const url = arguments[1];
            
            // Check if the URL is for localhost
            if (typeof url === 'string' && (url.indexOf('localhost') !== -1 || url.startsWith('/'))) {
                this._isLocalhost = true;
            }
            
            return originalXhrOpen.apply(this, arguments);
        };

        XMLHttpRequest.prototype.send = function() {
            if (this._isLocalhost) {
                activeRequests++;
                
                if (activeRequests === 1) {
                    loadingOverlay = showLoadingState();
                }
                
                // Add event listeners to track when the request completes
                this.addEventListener('load', decrementRequests);
                this.addEventListener('error', decrementRequests);
                this.addEventListener('abort', decrementRequests);
            }
            
            return originalXhrSend.apply(this, arguments);
        };

        function decrementRequests() {
            if (activeRequests > 0) {
                activeRequests--;
                
                if (activeRequests === 0 && loadingOverlay) {
                    hideLoadingState(loadingOverlay);
                    loadingOverlay = null;
                }
            }
        }
    });

    // Handle page unload to show loading cursor
    window.addEventListener('beforeunload', function() {
        document.documentElement.classList.add('loading-cursor');
        if (document.body) document.body.classList.add('loading-cursor');
    });
})();
