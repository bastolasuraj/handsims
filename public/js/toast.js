/**
 * Toast Notification System
 * Displays messages in a bubble at the bottom right corner
 * Auto-dismisses after 5 seconds
 */

(function() {
    'use strict';
    
    console.log('Toast.js loaded');

    class ToastManager {
        constructor() {
            this.container = null;
            this.toasts = [];
            this.init();
        }

        init() {
            console.log('Initializing toast container...');
            // Create toast container if it doesn't exist
            if (!document.querySelector('.toast-container')) {
                this.container = document.createElement('div');
                this.container.className = 'toast-container';
                document.body.appendChild(this.container);
                console.log('Toast container created and appended to body');
            } else {
                this.container = document.querySelector('.toast-container');
                console.log('Toast container already exists');
            }
        }

        /**
         * Show a toast notification
         * @param {string} message - The message to display
         * @param {string} type - Type of toast: 'success', 'error', 'warning', 'info'
         * @param {number} duration - Duration in milliseconds (default: 5000)
         */
        show(message, type, duration) {
            type = type || 'info';
            duration = duration || 5000;
            
            console.log('Showing toast:', type, message);
            
            // Ensure container exists
            if (!this.container || !document.body.contains(this.container)) {
                console.log('Container missing, reinitializing...');
                this.init();
            }
            
            const toastEl = this.createToast(message, type);
            this.container.appendChild(toastEl);
            this.toasts.push(toastEl);

            // Auto dismiss after duration
            const timeoutId = setTimeout(() => {
                this.dismiss(toastEl);
            }, duration);

            // Store timeout ID for manual dismissal
            toastEl.dataset.timeoutId = timeoutId;

            return toastEl;
        }

        createToast(message, type) {
            const toastEl = document.createElement('div');
            toastEl.className = 'toast toast-' + type;
            
            // Get icon based on type
            const icon = this.getIcon(type);
            
            toastEl.innerHTML = 
                '<div class="toast-icon">' +
                    '<i class="' + icon + '"></i>' +
                '</div>' +
                '<div class="toast-content">' + message + '</div>' +
                '<div class="toast-close">' +
                    '<i class="fas fa-times"></i>' +
                '</div>' +
                '<div class="toast-progress" style="background-color: ' + this.getProgressColor(type) + '"></div>';

            // Add click handler for close button
            const closeBtn = toastEl.querySelector('.toast-close');
            const self = this;
            closeBtn.addEventListener('click', function() {
                self.dismiss(toastEl);
            });

            // Trigger animation after adding to DOM
            setTimeout(() => {
                toastEl.classList.add('toast-show');
            }, 10);

            return toastEl;
        }

        getIcon(type) {
            const icons = {
                success: 'fas fa-check-circle',
                error: 'fas fa-exclamation-circle',
                warning: 'fas fa-exclamation-triangle',
                info: 'fas fa-info-circle'
            };
            return icons[type] || icons.info;
        }

        getProgressColor(type) {
            const colors = {
                success: '#10b981',
                error: '#ef4444',
                warning: '#f59e0b',
                info: '#2563eb'
            };
            return colors[type] || colors.info;
        }

        dismiss(toastEl) {
            // Clear timeout if exists
            if (toastEl.dataset.timeoutId) {
                clearTimeout(parseInt(toastEl.dataset.timeoutId));
            }

            // Add hiding class for animation
            toastEl.classList.add('hiding');

            // Remove from DOM after animation
            const self = this;
            setTimeout(function() {
                if (toastEl.parentNode) {
                    toastEl.parentNode.removeChild(toastEl);
                }
                // Remove from toasts array
                const index = self.toasts.indexOf(toastEl);
                if (index > -1) {
                    self.toasts.splice(index, 1);
                }
            }, 300);
        }

        dismissAll() {
            const self = this;
            this.toasts.forEach(function(toastEl) {
                self.dismiss(toastEl);
            });
        }

        success(message, duration) {
            return this.show(message, 'success', duration);
        }

        error(message, duration) {
            return this.show(message, 'error', duration);
        }

        warning(message, duration) {
            return this.show(message, 'warning', duration);
        }

        info(message, duration) {
            return this.show(message, 'info', duration);
        }
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initToast);
    } else {
        // DOM is already ready
        initToast();
    }

    function initToast() {
        console.log('Toast system initializing...');
        
        // Create global instance
        window.toast = new ToastManager();
        
        console.log('Toast system initialized');
        
        // Check for session messages in data attributes
        const body = document.body;
        
        console.log('Body dataset:', body.dataset);
        
        let hasMessages = false;
        
        if (body.dataset.successMessage) {
            console.log('Success message found:', body.dataset.successMessage);
            window.toast.success(body.dataset.successMessage);
            delete body.dataset.successMessage;
            hasMessages = true;
        }
        
        if (body.dataset.errorMessage) {
            console.log('Error message found:', body.dataset.errorMessage);
            window.toast.error(body.dataset.errorMessage);
            delete body.dataset.errorMessage;
            hasMessages = true;
        }
        
        if (body.dataset.warningMessage) {
            console.log('Warning message found:', body.dataset.warningMessage);
            window.toast.warning(body.dataset.warningMessage);
            delete body.dataset.warningMessage;
            hasMessages = true;
        }
        
        if (body.dataset.infoMessage) {
            console.log('Info message found:', body.dataset.infoMessage);
            window.toast.info(body.dataset.infoMessage);
            delete body.dataset.infoMessage;
            hasMessages = true;
        }
        
        // Clear session messages via AJAX to prevent showing again on refresh
        if (hasMessages) {
            fetch(window.location.href, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Clear-Messages': 'true'
                },
                body: 'clear_messages=1'
            }).catch(function(err) {
                console.log('Could not clear messages:', err);
            });
        }
    }
})();
