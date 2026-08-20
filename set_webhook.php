<?php

// .env faylidan sozlamalarni o'qish
if (file_exists(__DIR__ . '/.env')) {
    $envLines = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($envLines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($key, $val) = explode('=', $line, 2);
            putenv(trim($key) . '=' . trim($val));
            $_ENV[trim($key)] = trim($val);
        }
    }
}

$botToken = getenv('BOT_TOKEN');

if (empty($botToken) || $botToken === 'YOUR_BOT_TOKEN_HERE') {
    echo "❌ Xatolik: .env faylida BOT_TOKEN ko'rsatilmagan!\n";
    echo "Iltimos, avval .env fayliga bot tokeningizni kiriting.\n";
    exit(1);
}

$apiUrl = "https://api.telegram.org/bot{$botToken}/";

$action = $argv[1] ?? 'info';
$webhookUrl = $argv[2] ?? '';

if ($action === 'set') {
    if (empty($webhookUrl)) {
        echo "❌ Xatolik: Webhook URL ko'rsatilmadi!\n";
        echo "Foydalanish: php set_webhook.php set https://sizning-domeningiz.com/bot.php\n";
        exit(1);
    }

    $url = $apiUrl . "setWebhook?url=" . urlencode($webhookUrl);
    $response = file_get_contents($url);
    echo "📌 Webhook o'rnatish natijasi:\n" . json_encode(json_decode($response), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

} elseif ($action === 'delete') {
    $url = $apiUrl . "deleteWebhook";
    $response = file_get_contents($url);
    echo "🗑 Webhook o'chirish natijasi:\n" . json_encode(json_decode($response), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

} else {
    // info
    $url = $apiUrl . "getWebhookInfo";
    $response = file_get_contents($url);
    echo "ℹ️ Joriy Webhook holati:\n" . json_encode(json_decode($response), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
    echo "\nBuyruqlar:\n";
    echo "  Webhook o'rnatish:  php set_webhook.php set https://URL/bot.php\n";
    echo "  Webhook o'chirish:  php set_webhook.php delete\n";
    echo "  Webhook ma'lumoti:  php set_webhook.php info\n";
}
