<?php

namespace App;

class Telegram {
    public static function apiRequest($method, $parameters = []) {
        $url = Config::$apiUrl . $method;
        $options = [
            'http' => [
                'header'  => "Content-Type: application/json\r\n",
                'method'  => 'POST',
                'content' => json_encode($parameters),
                'timeout' => 10,
                'ignore_errors' => true
            ]
        ];

        $context = stream_context_create($options);
        $result = @file_get_contents($url, false, $context);
        return json_decode($result, true);
    }

    public static function sendMessage($chatId, $text, $replyMarkup = null, $extra = []) {
        $payload = [
            'chat_id'    => $chatId,
            'text'       => $text,
            'parse_mode' => 'HTML'
        ];
        if ($replyMarkup) {
            $payload['reply_markup'] = $replyMarkup;
        }
        if (!empty($extra)) {
            foreach ($extra as $k => $v) {
                $payload[$k] = $v;
            }
        }
        return self::apiRequest('sendMessage', $payload);
    }

    public static function copyMessage($chatId, $fromChatId, $messageId, $replyMarkup = null, $extra = []) {
        $payload = [
            'chat_id'      => $chatId,
            'from_chat_id' => $fromChatId,
            'message_id'   => $messageId
        ];
        if ($replyMarkup) {
            $payload['reply_markup'] = $replyMarkup;
        }
        if (!empty($extra)) {
            foreach ($extra as $k => $v) {
                $payload[$k] = $v;
            }
        }
        return self::apiRequest('copyMessage', $payload);
    }

    public static function sendPhoto($chatId, $photo, $caption = '', $replyMarkup = null, $extra = []) {
        $payload = [
            'chat_id'    => $chatId,
            'photo'      => $photo,
            'caption'    => $caption,
            'parse_mode' => 'HTML'
        ];
        if ($replyMarkup) {
            $payload['reply_markup'] = $replyMarkup;
        }
        if (!empty($extra)) {
            foreach ($extra as $k => $v) {
                $payload[$k] = $v;
            }
        }
        return self::apiRequest('sendPhoto', $payload);
    }

    public static function sendVideo($chatId, $video, $caption = '', $replyMarkup = null, $extra = []) {
        $payload = [
            'chat_id'    => $chatId,
            'video'      => $video,
            'caption'    => $caption,
            'parse_mode' => 'HTML'
        ];
        if ($replyMarkup) {
            $payload['reply_markup'] = $replyMarkup;
        }
        if (!empty($extra)) {
            foreach ($extra as $k => $v) {
                $payload[$k] = $v;
            }
        }
        return self::apiRequest('sendVideo', $payload);
    }

    public static function sendDocumentFileId($chatId, $fileId, $caption = '', $replyMarkup = null, $extra = []) {
        $payload = [
            'chat_id'    => $chatId,
            'document'   => $fileId,
            'caption'    => $caption,
            'parse_mode' => 'HTML'
        ];
        if ($replyMarkup) {
            $payload['reply_markup'] = $replyMarkup;
        }
        if (!empty($extra)) {
            foreach ($extra as $k => $v) {
                $payload[$k] = $v;
            }
        }
        return self::apiRequest('sendDocument', $payload);
    }

    public static function editMessageText($chatId, $messageId, $text, $replyMarkup = null) {
        $payload = [
            'chat_id'    => $chatId,
            'message_id' => $messageId,
            'text'       => $text,
            'parse_mode' => 'HTML'
        ];
        if ($replyMarkup !== null) {
            $payload['reply_markup'] = $replyMarkup;
        }
        return self::apiRequest('editMessageText', $payload);
    }

    public static function answerCallbackQuery($callbackQueryId, $text = null, $showAlert = false) {
        $payload = [
            'callback_query_id' => $callbackQueryId,
            'show_alert'        => $showAlert
        ];
        if ($text !== null) {
            $payload['text'] = $text;
        }
        return self::apiRequest('answerCallbackQuery', $payload);
    }

    public static function sendDocument($chatId, $filePath, $caption = '', $extra = []) {
        $boundary = '--------------------------' . microtime(true);
        $url = Config::$apiUrl . "sendDocument";

        $filename = basename($filePath);
        $fileContent = file_get_contents($filePath);

        $body = "--{$boundary}\r\n";
        $body .= "Content-Disposition: form-data; name=\"chat_id\"\r\n\r\n{$chatId}\r\n";

        if (!empty($caption)) {
            $body .= "--{$boundary}\r\n";
            $body .= "Content-Disposition: form-data; name=\"caption\"\r\n\r\n{$caption}\r\n";
            $body .= "--{$boundary}\r\n";
            $body .= "Content-Disposition: form-data; name=\"parse_mode\"\r\n\r\nHTML\r\n";
        }

        if (!empty($extra)) {
            foreach ($extra as $k => $v) {
                $valStr = is_array($v) ? json_encode($v) : $v;
                $body .= "--{$boundary}\r\n";
                $body .= "Content-Disposition: form-data; name=\"{$k}\"\r\n\r\n{$valStr}\r\n";
            }
        }

        $body .= "--{$boundary}\r\n";
        $body .= "Content-Disposition: form-data; name=\"document\"; filename=\"{$filename}\"\r\n";
        $body .= "Content-Type: application/octet-stream\r\n\r\n";
        $body .= $fileContent . "\r\n";
        $body .= "--{$boundary}--\r\n";

        $options = [
            'http' => [
                'header'  => "Content-Type: multipart/form-data; boundary={$boundary}\r\n" .
                             "Content-Length: " . strlen($body) . "\r\n",
                'method'  => 'POST',
                'content' => $body,
                'timeout' => 30,
                'ignore_errors' => true
            ]
        ];

        $context = stream_context_create($options);
        $result = @file_get_contents($url, false, $context);
        return json_decode($result, true);
    }

    public static function setupCommands() {
        // 1. Shaxsiy chatlar uchun buyruqlar
        $privateCommands = [
            ['command' => 'start', 'description' => 'Asosiy menyu / Главное меню'],
            ['command' => 'myqueue', 'description' => 'Mening navbatim / Моя очередь'],
            ['command' => 'help', 'description' => 'Yordam / Помощь']
        ];
        self::apiRequest('setMyCommands', [
            'commands' => $privateCommands,
            'scope' => ['type' => 'all_private_chats']
        ]);
        self::apiRequest('setMyCommands', [
            'commands' => $privateCommands,
            'scope' => ['type' => 'default']
        ]);

        // 2. Guruhlar uchun buyruqlar
        $groupCommands = [
            ['command' => 'start', 'description' => 'Navbat olish', 'is_ephemeral' => true],
            ['command' => 'myqueue', 'description' => 'Mening navbatim', 'is_ephemeral' => true],
            ['command' => 'news', 'description' => 'Xabarnoma yuborish (Rassilka)'],
            ['command' => 'stat', 'description' => 'Kunlik statistika & KPI', 'is_ephemeral' => true],
            ['command' => 'report', 'description' => 'PDF hisobot', 'is_ephemeral' => true]
        ];
        return self::apiRequest('setMyCommands', [
            'commands' => $groupCommands,
            'scope' => ['type' => 'all_group_chats']
        ]);
    }
}
