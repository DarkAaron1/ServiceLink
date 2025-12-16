import Pusher from 'pusher-js';

const key = process.env.REVERB_APP_KEY || 'jjyl5ogl3nilew3gkiap';
const host = process.env.REVERB_HOST || 'localhost';
const port = process.env.REVERB_PORT || 8080;
const scheme = process.env.REVERB_SCHEME || 'http';

console.log('Connecting to Reverb ws://'+host+':'+port+" with key " + key);

const pusher = new Pusher(key, {
    cluster: '',
    wsHost: host,
    wsPort: Number(port),
    wssPort: Number(port),
    enabledTransports: ['ws', 'wss'],
    forceTLS: scheme === 'https',
    disableStats: true,
    auth: {
        headers: {}
    }
});

const channel = pusher.subscribe('cocina');
channel.bind('NuevoPedidoCreado', function(data) {
    console.log('Event received (no-dot):', JSON.stringify(data));
});
channel.bind('.NuevoPedidoCreado', function(data) {
    console.log('Event received (dot):', JSON.stringify(data));
});

pusher.connection.bind('connected', () => console.log('Pusher connected'));
pusher.connection.bind('error', (err) => console.error('Pusher error', err));

// keep process alive
setInterval(()=>{}, 1000);
