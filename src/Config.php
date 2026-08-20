<?php

namespace App;

class Config {
    public static $botToken;
    public static $groupId;
    public static $apiUrl;
    public static $dbPath;
    public static $adminIds = [];

    public static function init() {
        date_default_timezone_set('Asia/Tashkent');

        $envPath = dirname(__DIR__) . '/.env';
        if (file_exists($envPath)) {
            $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                $line = trim($line);
                if (strpos($line, '#') === 0) continue;
                if (strpos($line, '=') !== false) {
                    list($key, $val) = explode('=', $line, 2);
                    $key = trim($key);
                    $val = trim($val);
                    putenv("{$key}={$val}");
                    $_ENV[$key] = $val;
                }
            }
        }

        self::$botToken = getenv('BOT_TOKEN') ?: getenv('BotToken') ?: '7463730449:AAGPX88P7-pL2fBE-XOVX1VkzLMTt2Oiyvo';
        self::$groupId  = getenv('GROUP_ID') ?: '-1004367218267';
        self::$apiUrl   = "https://api.telegram.org/bot" . self::$botToken . "/";
        self::$dbPath   = dirname(__DIR__) . '/data/database.sqlite';

        $adminStr = getenv('ADMIN_IDS') ?: '1127939579';
        self::$adminIds = array_filter(array_map('trim', explode(',', $adminStr)));

        $dataDir = dirname(__DIR__) . '/data';
        if (!is_dir($dataDir)) {
            mkdir($dataDir, 0777, true);
        }
    }

    public static function isAdmin($userId) {
        if (in_array((string)$userId, self::$adminIds)) {
            return true;
        }

        // Buxgalteriya guruhidagi barcha a'zolarni admin deb hisoblash
        if (!empty(self::$groupId)) {
            $res = Telegram::apiRequest('getChatMember', [
                'chat_id' => self::$groupId,
                'user_id' => $userId
            ]);
            if (!empty($res['ok'])) {
                $status = $res['result']['status'] ?? '';
                if (in_array($status, ['creator', 'administrator', 'member', 'restricted'])) {
                    return true;
                }
            }
        }

        return false;
    }

    public static function isWorkingHours() {
        $dayOfWeek = (int)date('N'); // 1 (Mon) - 7 (Sun)
        $hour = (int)date('H');
        return ($dayOfWeek >= 1 && $dayOfWeek <= 6 && $hour >= 9 && $hour < 18);
    }
}
