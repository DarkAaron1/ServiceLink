<?php
require __DIR__ . '/../vendor/autoload.php';

use Pusher\Pusher;

$opts = [
    'host' => getenv('REVERB_HOST') ?: 'localhost',
    'port' => getenv('REVERB_PORT') ?: 8080,
    'scheme' => getenv('REVERB_SCHEME') ?: 'http',
    'useTLS' => false,
];
$pusher = new Pusher(getenv('REVERB_APP_KEY'), getenv('REVERB_APP_SECRET'), getenv('REVERB_APP_ID'), $opts);
try {
    $result = $pusher->trigger('cocina', 'NuevoPedidoCreado', ['test' => 'ok via pusher-php']);
    var_dump($result);
} catch (Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
    if (method_exists($e, 'getCode')) {
        echo "Code: " . $e->getCode() . "\n";
    }
}

