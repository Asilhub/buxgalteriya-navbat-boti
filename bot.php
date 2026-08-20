<?php

require_once __DIR__ . '/src/Config.php';
require_once __DIR__ . '/src/Database.php';
require_once __DIR__ . '/src/Telegram.php';
require_once __DIR__ . '/src/Lang.php';
require_once __DIR__ . '/src/Keyboards.php';
require_once __DIR__ . '/src/PdfGenerator.php';
require_once __DIR__ . '/src/Report.php';
require_once __DIR__ . '/src/BotHandler.php';

use App\Config;
use App\Telegram;
use App\BotHandler;

// Konfiguratsiyani yuklash
Config::init();

// CLI DA ISHGA TUSHIRISH (LONG POLLING)
if (php_sapi_name() === 'cli') {
    $botInfoRaw = @file_get_contents(Config::$apiUrl . 'getMe');
    $botInfo = json_decode($botInfoRaw, true);

    if (!empty($botInfo['ok'])) {
        $botUser = $botInfo['result']['username'] ?? 'Bot';
        $botName = $botInfo['result']['first_name'] ?? 'Bot';
        echo "====================================================\n";
        echo "🤖 Buxgalteriya Navbat Boti (Modern Modular Architecture)\n";
        echo "👤 Bot: {$botName} (@{$botUser})\n";
        echo "👥 Guruh ID: " . Config::$groupId . "\n";
        echo "🟢 Telegramdan xabarlar kutilmoqda...\n";
        echo "====================================================\n\n";
    } else {
        echo "⚠️ Ogohlantirish: Bot tokeni bilan ulanishda muammo bo'ldi.\n";
    }

    Telegram::setupCommands();
    echo "⚡ Buyruqlar sozlandi!\n";

    @file_get_contents(Config::$apiUrl . 'deleteWebhook');

    $offset = 0;
    while (true) {
        $response = @file_get_contents(Config::$apiUrl . "getUpdates?offset={$offset}&timeout=30");
        if ($response === false) {
            sleep(2);
            continue;
        }

        $data = json_decode($response, true);
        if (!empty($data['ok']) && !empty($data['result'])) {
            foreach ($data['result'] as $update) {
                $offset = $update['update_id'] + 1;
                BotHandler::handle($update);
            }
        }
        usleep(100000);
    }
}

// WEBHOOK REJIMI (HTTPS)
$updateRaw = file_get_contents('php://input');
if (!empty($updateRaw)) {
    $update = json_decode($updateRaw, true);
    BotHandler::handle($update);
    http_response_code(200);
    echo json_encode(['status' => 'ok']);
} else {
    header('Content-Type: text/html; charset=utf-8');
    echo "<h2>🤖 Buxgalteriya Navbat Boti (Modern Modular Architecture) ishlamoqda!</h2>";
}
