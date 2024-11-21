document.addEventListener('DOMContentLoaded', function() {
    const notificationBtn = document.querySelector('.notification-btn');
    const notificationContent = document.querySelector('.notification-content');
    
    notificationBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        notificationContent.classList.toggle('show');
        
        // Mark notifications as read
        const unreadNotifications = document.querySelectorAll('.notification-item.unread');
        unreadNotifications.forEach(notification => {
            const notificationId = notification.dataset.id;
            markNotificationAsRead(notificationId);
            notification.classList.remove('unread');
        });
        
        // Remove notification badge
        const badge = document.querySelector('.notification-badge');
        if (badge) badge.remove();
    });
    
    // Close notifications when clicking outside
    document.addEventListener('click', function(e) {
        if (!notificationContent.contains(e.target) && !notificationBtn.contains(e.target)) {
            notificationContent.classList.remove('show');
        }
    });
});

async function markNotificationAsRead(notificationId) {
    try {
        const response = await fetch('../actions/mark_notification_read.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ notification_id: notificationId })
        });
        
        if (!response.ok) {
            throw new Error('Failed to mark notification as read');
        }
    } catch (error) {
        console.error('Error:', error);
    }
} 