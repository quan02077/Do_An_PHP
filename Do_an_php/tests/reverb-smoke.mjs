import { spawnSync } from 'node:child_process';
import WebSocket from 'ws';

const port = process.env.REVERB_PORT || '8080';
const key = process.env.REVERB_APP_KEY || 'qqq-local-key';
const socket = new WebSocket(`ws://127.0.0.1:${port}/app/${key}?protocol=7&client=js&version=8.6.0&flash=false`);
const timer = setTimeout(() => {
    console.error('Timed out waiting for Reverb event');
    socket.close();
    process.exitCode = 1;
}, 10000);

socket.addEventListener('message', (message) => {
    const packet = JSON.parse(message.data);
    if (packet.event === 'pusher:connection_established') {
        socket.send(JSON.stringify({event: 'pusher:subscribe', data: {channel: 'events.123'}}));
    } else if (packet.event === 'pusher_internal:subscription_succeeded') {
        const result = spawnSync(process.env.PHP_BINARY || 'php', [
            '-r',
            "require 'vendor/autoload.php'; $app = require 'bootstrap/app.php'; $app->make(Illuminate\\Contracts\\Console\\Kernel::class)->bootstrap(); event(new App\\Events\\TicketCountsChanged(123, 7, 10, 2));",
        ], {encoding: 'utf8', env: process.env});
        if (result.status !== 0) {
            clearTimeout(timer);
            console.error(result.stderr || result.stdout);
            socket.close();
            process.exitCode = 1;
        }
    } else if (packet.event === 'ticket.counts.changed') {
        const data = typeof packet.data === 'string' ? JSON.parse(packet.data) : packet.data;
        clearTimeout(timer);
        if (data.eventId !== 123 || data.registered !== 7 || data.capacity !== 10) {
            console.error('Unexpected payload:', data);
            process.exitCode = 1;
        } else {
            console.log('Reverb WebSocket delivered ticket counts:', data);
        }
        socket.close();
    }
});

socket.addEventListener('error', (error) => {
    clearTimeout(timer);
    console.error(error.message, error.cause);
    process.exitCode = 1;
});
