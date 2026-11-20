import './bootstrap';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

const realtime = window.QQQRealtime;
if (realtime?.enabled && realtime.key) {
    window.Pusher = Pusher;
    window.Echo = new Echo({
        broadcaster: 'reverb',
        key: realtime.key,
        wsHost: window.location.hostname,
        wsPort: realtime.port,
        wssPort: realtime.port,
        forceTLS: realtime.secure,
        enabledTransports: [realtime.secure ? 'wss' : 'ws'],
        auth: {headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? ''}},
    });

    const capacity = document.getElementById('event-capacity');
    if (capacity) {
        window.Echo.channel(`events.${capacity.dataset.eventId}`)
            .listen('.ticket.counts.changed', ({registered, capacity: maximum}) => {
                const percent = maximum > 0 ? Math.min(100, Math.round(registered / maximum * 100)) : 0;
                const remaining = maximum > 0 ? Math.max(0, maximum - registered) : 0;
                document.getElementById('capacity-text').textContent = `${registered} / ${maximum} (${percent}%)`;
                document.getElementById('remaining-text').textContent = `Còn ${remaining} vé`;
                const bar = document.getElementById('capacity-progress-bar');
                bar.style.width = `${percent}%`;
                bar.setAttribute('aria-valuenow', percent);
                bar.classList.toggle('bg-danger', percent >= 100);
                bar.classList.toggle('bg-warning', percent >= 80 && percent < 100);
                bar.classList.toggle('bg-dark', percent < 80);
                const button = document.getElementById('register-action-btn');
                if (button) {
                    button.disabled = maximum > 0 && remaining === 0;
                    button.querySelector('span').textContent = button.disabled ? 'Đã hết vé' : 'Đăng ký tham gia ngay';
                }
            });
    }

    const checkinCount = document.getElementById('checkin-live-count');
    if (checkinCount) {
        window.Echo.private('admin.checkins').listen('.ticket.checked.in', (data) => {
            checkinCount.textContent = data.checkedInTotal;
            const feed = document.getElementById('checkin-live-feed');
            feed.textContent = `${data.attendeeName} vừa check-in thành công.`;
            const cell = document.getElementById(`reg-status-cell-${data.ticketId}`);
            if (cell) {
                cell.textContent = 'Đã tham gia';
            }
        });
    }
}
