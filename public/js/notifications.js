// Notification System - Immediate execution version
(function() {
    'use strict';
    
    console.log('=== NOTIFICATIONS.JS LOADING ===');
    
    function initNotifications() {
        console.log('initNotifications called');
        console.log('initialNotifications:', typeof initialNotifications !== 'undefined' ? initialNotifications : 'UNDEFINED');
        console.log('APP_URL:', typeof APP_URL !== 'undefined' ? APP_URL : 'UNDEFINED');
        
        const notificationBell = document.getElementById('notificationBell');
        const notificationDropdown = document.getElementById('notificationDropdown');
        const notificationCount = document.getElementById('notificationCount');
        const notificationList = document.getElementById('notificationList');
        const markAllAsReadBtn = document.getElementById('markAllAsReadBtn');

        console.log('Elements found:', {
            bell: !!notificationBell,
            dropdown: !!notificationDropdown,
            count: !!notificationCount,
            list: !!notificationList,
            markAllBtn: !!markAllAsReadBtn
        });

        // Check if elements exist
        if (!notificationBell || !notificationDropdown || !notificationCount || !notificationList || !markAllAsReadBtn) {
            console.error('Notification elements not found - one or more elements missing');
            console.error('Missing elements:', {
                bell: !notificationBell,
                dropdown: !notificationDropdown,
                count: !notificationCount,
                list: !notificationList,
                markAllBtn: !markAllAsReadBtn
            });
            return;
        }

        let isDropdownOpen = false;
        let currentNotifications = typeof initialNotifications !== 'undefined' ? initialNotifications : [];
        let pollInterval = null;
        
        console.log('Current notifications count:', currentNotifications.length);
        console.log('Current notifications:', currentNotifications);

        function updateNotificationUI(notifications) {
            console.log('updateNotificationUI called with', notifications.length, 'notifications');
            currentNotifications = notifications;
            
            if (notifications.length > 0) {
                notificationCount.textContent = notifications.length;
                notificationCount.classList.remove('hidden');
                console.log('Notification count badge updated:', notifications.length);
            } else {
                notificationCount.classList.add('hidden');
                console.log('Notification count badge hidden');
            }

            notificationList.innerHTML = '';
            if (notifications.length === 0) {
                notificationList.innerHTML = '<p class="text-center text-gray-500 py-4">No new notifications</p>';
                console.log('No notifications - showing empty message');
            } else {
                console.log('Building notification items...');
                notifications.forEach((notification, index) => {
                    console.log('Adding notification', index + 1, ':', notification.message);
                    const notificationItem = document.createElement('div');
                    notificationItem.className = 'p-4 border-b border-gray-200 hover:bg-gray-50 cursor-pointer transition-colors';
                    notificationItem.dataset.id = notification.id;

                    let icon = 'fas fa-info-circle text-blue-500';
                    if (notification.type === 'inventory') {
                        icon = 'fas fa-exclamation-triangle text-yellow-500';
                    } else if (notification.type === 'system') {
                        icon = 'fas fa-cogs text-gray-500';
                    }

                    notificationItem.innerHTML = `
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <i class="${icon} text-xl"></i>
                            </div>
                            <div class="ml-3 flex-1">
                                <p class="text-sm text-gray-700">${escapeHtml(notification.message)}</p>
                                <p class="text-xs text-gray-500 mt-1">${formatDate(notification.created_at)}</p>
                            </div>
                        </div>
                    `;
                    notificationList.appendChild(notificationItem);
                });
                console.log('Notification items added, innerHTML length:', notificationList.innerHTML.length);
            }
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diffMs = now - date;
            const diffMins = Math.floor(diffMs / 60000);
            const diffHours = Math.floor(diffMs / 3600000);
            const diffDays = Math.floor(diffMs / 86400000);

            if (diffMins < 1) return 'Just now';
            if (diffMins < 60) return `${diffMins} minute${diffMins > 1 ? 's' : ''} ago`;
            if (diffHours < 24) return `${diffHours} hour${diffHours > 1 ? 's' : ''} ago`;
            if (diffDays < 7) return `${diffDays} day${diffDays > 1 ? 's' : ''} ago`;
            return date.toLocaleString();
        }

        function fetchNotifications() {
            const appUrl = window.APP_URL || '';
            console.log('Fetching notifications from:', appUrl + '/notifications');
            
            fetch(appUrl + '/notifications')
                .then(response => {
                    console.log('Fetch response status:', response.status);
                    return response.json();
                })
                .then(notifications => {
                    console.log('Fetched', notifications.length, 'notifications');
                    const previousCount = currentNotifications.length;
                    const newCount = notifications.length;
                    
                    updateNotificationUI(notifications);
                    
                    // Show visual feedback if new notifications arrived
                    if (newCount > previousCount && !isDropdownOpen) {
                        notificationBell.classList.add('animate-bounce');
                        setTimeout(() => {
                            notificationBell.classList.remove('animate-bounce');
                        }, 1000);
                    }
                })
                .catch(error => {
                    console.error('Error fetching notifications:', error);
                });
        }

        notificationBell.addEventListener('click', (e) => {
            console.log('Bell clicked');
            e.stopPropagation();
            isDropdownOpen = !isDropdownOpen;
            console.log('Dropdown open:', isDropdownOpen);
            
            if (isDropdownOpen) {
                notificationDropdown.classList.remove('hidden');
                notificationDropdown.classList.remove('opacity-0');
                notificationDropdown.classList.remove('invisible');
                notificationDropdown.classList.add('opacity-100');
                console.log('Dropdown shown');
                // Show current notifications immediately
                updateNotificationUI(currentNotifications);
                // Then fetch fresh notifications in the background
                fetchNotifications();
            } else {
                notificationDropdown.classList.add('opacity-0');
                notificationDropdown.classList.add('invisible');
                setTimeout(() => {
                    notificationDropdown.classList.add('hidden');
                }, 300);
                console.log('Dropdown hidden');
            }
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (isDropdownOpen && !notificationDropdown.contains(e.target) && !notificationBell.contains(e.target)) {
                isDropdownOpen = false;
                notificationDropdown.classList.add('opacity-0');
                notificationDropdown.classList.add('invisible');
                setTimeout(() => {
                    notificationDropdown.classList.add('hidden');
                }, 300);
                console.log('Dropdown closed by outside click');
            }
        });

        notificationList.addEventListener('click', (e) => {
            const notificationItem = e.target.closest('[data-id]');
            if (notificationItem) {
                const notificationId = notificationItem.dataset.id;
                const appUrl = window.APP_URL || '';
                console.log('Marking notification as read:', notificationId);
                
                fetch(appUrl + `/notifications/mark_as_read?id=${notificationId}`)
                    .then(() => {
                        notificationItem.style.opacity = '0';
                        setTimeout(() => {
                            notificationItem.remove();
                            const currentCount = currentNotifications.length - 1;
                            currentNotifications = currentNotifications.filter(n => n.id != notificationId);
                            
                            if (currentCount === 0) {
                                notificationCount.classList.add('hidden');
                                notificationList.innerHTML = '<p class="text-center text-gray-500 py-4">No new notifications</p>';
                            } else {
                                notificationCount.textContent = currentCount;
                            }
                        }, 300);
                    })
                    .catch(error => {
                        console.error('Error marking notification as read:', error);
                    });
            }
        });

        markAllAsReadBtn.addEventListener('click', () => {
            const appUrl = window.APP_URL || '';
            console.log('Marking all notifications as read');
            
            fetch(appUrl + '/notifications/mark_all_as_read')
                .then(() => {
                    currentNotifications = [];
                    notificationList.innerHTML = '<p class="text-center text-gray-500 py-4">No new notifications</p>';
                    notificationCount.classList.add('hidden');
                    console.log('All notifications marked as read');
                })
                .catch(error => {
                    console.error('Error marking all notifications as read:', error);
                });
        });

        // Update the UI with the initial notifications
        console.log('Calling initial updateNotificationUI with', currentNotifications.length, 'notifications');
        updateNotificationUI(currentNotifications);
        console.log('Initial UI update complete');

        // Start polling for new notifications every 30 seconds
        pollInterval = setInterval(fetchNotifications, 30000);
        console.log('Polling started (30 second interval)');

        // Clean up interval when page is unloaded
        window.addEventListener('beforeunload', () => {
            if (pollInterval) {
                clearInterval(pollInterval);
            }
        });
        
        console.log('=== NOTIFICATION SYSTEM INITIALIZED ===');
    }

    // Try to initialize immediately if DOM is ready, otherwise wait
    if (document.readyState === 'loading') {
        console.log('DOM still loading, waiting for DOMContentLoaded');
        document.addEventListener('DOMContentLoaded', initNotifications);
    } else {
        console.log('DOM already loaded, initializing immediately');
        initNotifications();
    }
})();
