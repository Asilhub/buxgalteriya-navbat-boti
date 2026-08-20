<?php

require_once __DIR__ . '/src/Config.php';
require_once __DIR__ . '/src/Database.php';
require_once __DIR__ . '/src/Telegram.php';
require_once __DIR__ . '/src/Lang.php';
require_once __DIR__ . '/src/Keyboards.php';
require_once __DIR__ . '/src/PdfGenerator.php';
require_once __DIR__ . '/src/Report.php';
require_once __DIR__ . '/src/BotHandler.php';

\App\Config::init();

// Webhook setup (brauzerdan ochilganda ?setup= bilan sozlash)
if (isset($_GET['setup'])) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'bots.mindup.uz';
    $script = explode('?', $_SERVER['REQUEST_URI'] ?? '/buxgalteriya/index.php')[0];
    $webhookUrl = "https://{$host}{$script}";

    $res = \App\Telegram::apiRequest('setWebhook', [
        'url'             => $webhookUrl,
        'allowed_updates' => ['message', 'callback_query']
    ]);

    \App\Telegram::setupCommands();

    header('Content-Type: application/json');
    echo json_encode([
        'status'   => 'Webhook muvaffaqiyatli o\'rnatildi',
        'url'      => $webhookUrl,
        'telegram' => $res
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

// Telegram Webhook yangilanishlari
$input = file_get_contents('php://input');
if (!empty($input)) {
    $update = json_decode($input, true);
    if ($update) {
        \App\BotHandler::handle($update);
    }
    echo "OK";
    exit;
}

echo "<h3>🤖 Buxgalteriya Navbat Boti Webhook Faol</h3>";
