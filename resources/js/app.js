import './bootstrap';
import Alpine from 'alpinejs';
import Sortable from 'sortablejs';
import Chart from 'chart.js/auto';

window.Alpine = Alpine;
window.Sortable = Sortable;
window.Chart = Chart;

window.notificationBell = function () {
    return {
        open: false,
        notifications: [],
        unreadCount: 0,
        allRead: true,
        async fetchNotifications() {
            try {
                const res = await fetch('/notifications', {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();
                this.notifications = data.notifications;
                this.unreadCount = data.unread_count;
                this.allRead = data.unread_count === 0;
            } catch (e) {
                console.error('Failed to fetch notifications', e);
            }
        },
        async toggleReadAll() {
            const url = this.allRead ? '/notifications/mark-all-unread' : '/notifications/mark-all-read';
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            try {
                await fetch(url, {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': token, 'X-Requested-With': 'XMLHttpRequest' }
                });
                await this.fetchNotifications();
            } catch (e) {
                console.error('Failed to toggle notifications', e);
            }
        }
    };
};

Alpine.start();
