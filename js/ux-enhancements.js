/**
 * UX Enhancements
 * This file provides progressive enhancements to improve user experience
 */

(function() {
    'use strict';

    // Wait for DOM to be ready
    document.addEventListener('DOMContentLoaded', function() {
        
        // Add loading states to form submissions
        initFormLoadingStates();
        
        // Enhance checkbox interactions
        enhanceCheckboxes();
        
        // Add smooth scroll behavior
        initSmoothScroll();
        
        // Add confirmation for destructive actions
        initConfirmations();
        
        // Enhance external links
        enhanceExternalLinks();
        
        // Add keyboard shortcuts
        initKeyboardShortcuts();
    });

    /**
     * Add loading states to form buttons on submit
     */
    function initFormLoadingStates() {
        const forms = document.querySelectorAll('form');
        
        forms.forEach(function(form) {
            form.addEventListener('submit', function(e) {
                const submitButton = form.querySelector('button[type="submit"]');
                
                if (submitButton && !submitButton.classList.contains('loading')) {
                    submitButton.classList.add('loading');
                    submitButton.disabled = true;
                    
                    // Re-enable if form validation fails (though native validation will prevent submit)
                    setTimeout(function() {
                        if (!form.checkValidity()) {
                            submitButton.classList.remove('loading');
                            submitButton.disabled = false;
                        }
                    }, 100);
                }
            });
        });
    }

    /**
     * Enhance checkbox interactions with visual feedback
     */
    function enhanceCheckboxes() {
        const checkboxForms = document.querySelectorAll('form input[type="checkbox"][onchange*="submit"]');
        
        checkboxForms.forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                // Add a subtle animation
                const parent = checkbox.closest('td') || checkbox.parentElement;
                if (parent) {
                    parent.style.opacity = '0.6';
                    setTimeout(function() {
                        parent.style.opacity = '1';
                    }, 300);
                }
            });
        });
    }

    /**
     * Initialize smooth scroll for anchor links
     */
    function initSmoothScroll() {
        // Already handled by CSS, but we can add programmatic scrolling if needed
        const anchorLinks = document.querySelectorAll('a[href^="#"]');
        
        anchorLinks.forEach(function(link) {
            link.addEventListener('click', function(e) {
                const href = link.getAttribute('href');
                if (href && href !== '#') {
                    const target = document.querySelector(href);
                    if (target) {
                        e.preventDefault();
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                }
            });
        });
    }

    /**
     * Add confirmation dialogs for potentially destructive actions
     */
    function initConfirmations() {
        // Could be used for delete buttons if they existed
        const deleteLinks = document.querySelectorAll('a[href*="supprimer"], a[href*="delete"]');
        
        deleteLinks.forEach(function(link) {
            link.addEventListener('click', function(e) {
                if (!confirm('Êtes-vous sûr de vouloir supprimer cet élément ?')) {
                    e.preventDefault();
                }
            });
        });
    }

    /**
     * Enhance external links with visual indicator
     */
    function enhanceExternalLinks() {
        const externalLinks = document.querySelectorAll('a[target="_blank"]');
        
        externalLinks.forEach(function(link) {
            // Ensure proper security attributes
            if (!link.hasAttribute('rel')) {
                link.setAttribute('rel', 'noopener noreferrer');
            }
            
            // Add visual feedback on click
            link.addEventListener('click', function() {
                link.style.opacity = '0.7';
                setTimeout(function() {
                    link.style.opacity = '1';
                }, 200);
            });
        });
    }

    /**
     * Add keyboard shortcuts for common actions
     */
    function initKeyboardShortcuts() {
        document.addEventListener('keydown', function(e) {
            // Alt + L: Go to lists page (if user is logged in)
            if (e.altKey && e.key === 'l') {
                const listLink = document.querySelector('a[href*="/pages/listes/"]');
                if (listLink) {
                    e.preventDefault();
                    window.location.href = listLink.href;
                }
            }
            
            // Alt + C: Go to account page (if user is logged in)
            if (e.altKey && e.key === 'c') {
                const accountLink = document.querySelector('a[href*="/pages/compte/"]');
                if (accountLink) {
                    e.preventDefault();
                    window.location.href = accountLink.href;
                }
            }
            
            // Escape key: Close alerts
            if (e.key === 'Escape') {
                const alerts = document.querySelectorAll('.alert .btn-close');
                alerts.forEach(function(closeBtn) {
                    closeBtn.click();
                });
            }
        });
            }
        });
    }

})();
