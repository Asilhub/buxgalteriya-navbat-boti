<?php

namespace App;

use PDO;
use Exception;

class Database {
    private static $pdo = null;

    public static function getPdo() {
        if (self::$pdo !== null) {
            return self::$pdo;
        }

        $dbPath = Config::$dbPath;
        $dir = dirname($dbPath);
        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }

        try {
            self::$pdo = new PDO("sqlite:" . $dbPath);
            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            // WAL (Write-Ahead Logging) rejimi - bir vaqtda yuzlab so'rovlar uchun tezkor
            self::$pdo->exec("PRAGMA journal_mode = WAL;");
            self::$pdo->exec("PRAGMA synchronous = NORMAL;");

            self::createTables();
        } catch (Exception $e) {
            error_log("SQLite Connection Error: " . $e->getMessage());
        }

        return self::$pdo;
    }

    private static function createTables() {
        $pdo = self::$pdo;
        if (!$pdo) return;

        // 1. Users jadvali
        $pdo->exec("CREATE TABLE IF NOT EXISTS users (
            user_id INTEGER PRIMARY KEY,
            name TEXT,
            username TEXT,
            phone TEXT,
            lang TEXT DEFAULT 'uz_lat',
            step TEXT DEFAULT 'main',
            menu TEXT DEFAULT 'main',
            pending_service TEXT,
            updated_at TEXT,
            created_at TEXT
        );");

        // 2. Queue (Navbatlar) jadvali
        $pdo->exec("CREATE TABLE IF NOT EXISTS queue (
            id TEXT PRIMARY KEY,
            queue_number INTEGER,
            user_id INTEGER,
            name TEXT,
            username TEXT,
            phone TEXT,
            service TEXT,
            comment TEXT,
            file_id TEXT,
            file_type TEXT,
            status TEXT DEFAULT 'pending',
            group_msg_id INTEGER,
            operator_id INTEGER,
            operator_name TEXT,
            cancel_reason TEXT,
            rating INTEGER,
            date TEXT,
            created_at TEXT,
            taken_at TEXT,
            done_at TEXT
        );");

        // 3. Drafts (Rassilka qoralamalari) jadvali
        $pdo->exec("CREATE TABLE IF NOT EXISTS drafts (
            id TEXT PRIMARY KEY,
            chat_id INTEGER,
            message_id INTEGER,
            text TEXT,
            file_id TEXT,
            file_type TEXT,
            caption TEXT,
            sender_id INTEGER,
            created_at TEXT
        );");

        // Indekslar
        $pdo->exec("CREATE INDEX IF NOT EXISTS idx_queue_user ON queue(user_id);");
        $pdo->exec("CREATE INDEX IF NOT EXISTS idx_queue_date ON queue(date);");
        $pdo->exec("CREATE INDEX IF NOT EXISTS idx_queue_status ON queue(status);");
        $pdo->exec("CREATE INDEX IF NOT EXISTS idx_queue_group_msg ON queue(group_msg_id);");
    }

    public static function getUser($userId) {
        $pdo = self::getPdo();
        if (!$pdo) return [];

        $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = :uid LIMIT 1");
        $stmt->execute([':uid' => $userId]);
        $row = $stmt->fetch();
        return $row ?: [];
    }

    public static function saveUser($userId, $data) {
        $pdo = self::getPdo();
        if (!$pdo) return;

        $existing = self::getUser($userId);
        $timeNow = date('Y-m-d H:i:s');

        if (empty($existing)) {
            $stmt = $pdo->prepare("INSERT INTO users (user_id, name, username, phone, lang, step, menu, pending_service, updated_at, created_at)
                VALUES (:user_id, :name, :username, :phone, :lang, :step, :menu, :pending_service, :updated_at, :created_at)");
            $stmt->execute([
                ':user_id'         => $userId,
                ':name'            => $data['name'] ?? 'Mijoz',
                ':username'        => $data['username'] ?? '',
                ':phone'           => $data['phone'] ?? null,
                ':lang'            => $data['lang'] ?? 'uz_lat',
                ':step'            => $data['step'] ?? 'main',
                ':menu'            => $data['menu'] ?? 'main',
                ':pending_service' => $data['pending_service'] ?? null,
                ':updated_at'      => $timeNow,
                ':created_at'      => $timeNow
            ]);
        } else {
            $fields = [];
            $params = [':user_id' => $userId, ':updated_at' => $timeNow];

            foreach ($data as $key => $val) {
                if ($key !== 'user_id' && $key !== 'created_at') {
                    $fields[] = "{$key} = :{$key}";
                    $params[":{$key}"] = $val;
                }
            }
            $fields[] = "updated_at = :updated_at";

            $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE user_id = :user_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
        }
    }

    public static function getAllUsers() {
        $pdo = self::getPdo();
        if (!$pdo) return [];

        $stmt = $pdo->query("SELECT * FROM users");
        $rows = $stmt->fetchAll();
        $res = [];
        foreach ($rows as $r) {
            $res[$r['user_id']] = $r;
        }
        return $res;
    }

    public static function createNewQueue($userId, $name, $username, $phone, $serviceKey, $comment = '', $fileId = null, $fileType = null) {
        $pdo = self::getPdo();
        $today = date('Y-m-d');
        $timeNow = date('Y-m-d H:i:s');

        // Bugungi navbatlar soni
        $stmt = $pdo->prepare("SELECT COUNT(*) as cnt FROM queue WHERE date = :tdate");
        $stmt->execute([':tdate' => $today]);
        $cntRow = $stmt->fetch();
        $queueNumber = ($cntRow['cnt'] ?? 0) + 1;

        $queueId = 'q_' . time() . '_' . rand(100, 999);

        $stmt = $pdo->prepare("INSERT INTO queue (
            id, queue_number, user_id, name, username, phone, service, comment, file_id, file_type,
            status, group_msg_id, operator_id, operator_name, cancel_reason, rating, date, created_at, taken_at, done_at
        ) VALUES (
            :id, :queue_number, :user_id, :name, :username, :phone, :service, :comment, :file_id, :file_type,
            'pending', NULL, NULL, NULL, NULL, NULL, :date, :created_at, NULL, NULL
        )");

        $stmt->execute([
            ':id'           => $queueId,
            ':queue_number' => $queueNumber,
            ':user_id'      => $userId,
            ':name'         => $name,
            ':username'     => $username,
            ':phone'        => $phone,
            ':service'      => $serviceKey,
            ':comment'      => $comment,
            ':file_id'      => $fileId,
            ':file_type'    => $fileType,
            ':date'         => $today,
            ':created_at'   => $timeNow
        ]);

        return self::getQueueById($queueId);
    }

    public static function getQueueById($queueId) {
        $pdo = self::getPdo();
        if (!$pdo) return null;

        $stmt = $pdo->prepare("SELECT * FROM queue WHERE id = :qid LIMIT 1");
        $stmt->execute([':qid' => $queueId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function getQueueByGroupMsgId($msgId) {
        $pdo = self::getPdo();
        if (!$pdo || empty($msgId)) return null;

        $stmt = $pdo->prepare("SELECT * FROM queue WHERE group_msg_id = :gmsg LIMIT 1");
        $stmt->execute([':gmsg' => $msgId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function updateQueue($queueId, $fields) {
        $pdo = self::getPdo();
        if (!$pdo) return null;

        $updates = [];
        $params = [':id' => $queueId];

        foreach ($fields as $key => $val) {
            $updates[] = "{$key} = :{$key}";
            $params[":{$key}"] = $val;
        }

        if (empty($updates)) {
            return self::getQueueById($queueId);
        }

        $sql = "UPDATE queue SET " . implode(', ', $updates) . " WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return self::getQueueById($queueId);
    }

    public static function getDb() {
        $pdo = self::getPdo();
        if (!$pdo) {
            return ['users' => [], 'queue' => []];
        }

        $users = self::getAllUsers();
        $stmt = $pdo->query("SELECT * FROM queue ORDER BY created_at ASC");
        $queueRows = $stmt->fetchAll();

        $queue = [];
        foreach ($queueRows as $q) {
            $queue[$q['id']] = $q;
        }

        return [
            'users' => $users,
            'queue' => $queue
        ];
    }

    public static function saveBroadcastDraft($draftId, $data) {
        $pdo = self::getPdo();
        if (!$pdo) return;

        $stmt = $pdo->prepare("INSERT OR REPLACE INTO drafts (id, chat_id, message_id, text, file_id, file_type, caption, sender_id, created_at)
            VALUES (:id, :chat_id, :message_id, :text, :file_id, :file_type, :caption, :sender_id, :created_at)");

        $stmt->execute([
            ':id'         => $draftId,
            ':chat_id'    => $data['chat_id'] ?? null,
            ':message_id' => $data['message_id'] ?? null,
            ':text'       => $data['text'] ?? '',
            ':file_id'    => $data['file_id'] ?? null,
            ':file_type'  => $data['file_type'] ?? 'text',
            ':caption'    => $data['caption'] ?? '',
            ':sender_id'  => $data['sender_id'] ?? null,
            ':created_at' => $data['created_at'] ?? date('Y-m-d H:i:s')
        ]);
    }

    public static function getBroadcastDraft($draftId) {
        $pdo = self::getPdo();
        if (!$pdo) return null;

        $stmt = $pdo->prepare("SELECT * FROM drafts WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $draftId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function deleteBroadcastDraft($draftId) {
        $pdo = self::getPdo();
        if (!$pdo) return;

        $stmt = $pdo->prepare("DELETE FROM drafts WHERE id = :id");
        $stmt->execute([':id' => $draftId]);
    }
}
