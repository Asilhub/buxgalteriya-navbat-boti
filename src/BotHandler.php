<?php

namespace App;

class BotHandler {
    public static function handle($update) {
        // 1. CALLBACK QUERY
        if (isset($update['callback_query'])) {
            self::handleCallback($update['callback_query']);
            return;
        }

        // 2. MESSAGE
        if (isset($update['message'])) {
            self::handleMessage($update['message']);
            return;
        }
    }

    private static function handleCallback($cb) {
        $cbId = $cb['id'];
        $from = $cb['from'];
        $fromId = $from['id'];
        $fromName = htmlspecialchars(trim(($from['first_name'] ?? '') . ' ' . ($from['last_name'] ?? '')));
        if (empty($fromName)) $fromName = 'Ходим';
        $data = $cb['data'] ?? '';
        $message = $cb['message'] ?? null;
        $chatId = $message['chat']['id'] ?? null;
        $messageId = $message['message_id'] ?? null;

        // 1.1. ⚡ Қабул қилиш: take_{queueId}
        if (strpos($data, 'take_') === 0) {
            $queueId = substr($data, 5);
            $item = Database::getQueueById($queueId);

            if (!$item) {
                Telegram::answerCallbackQuery($cbId, '⚠️ Ариза топилмади!', true);
                return;
            }

            if ($item['status'] === 'in_progress') {
                Telegram::answerCallbackQuery($cbId, "⚠️ Бу ариза аллақачон {$item['operator_name']} томонидан қабул қилинган!", true);
                return;
            }

            if ($item['status'] === 'done') {
                Telegram::answerCallbackQuery($cbId, '⚠️ Бу ариза аллақачон бажарилган!', true);
                return;
            }

            $updated = Database::updateQueue($queueId, [
                'status'        => 'in_progress',
                'operator_id'   => $fromId,
                'operator_name' => $fromName,
                'taken_at'      => date('Y-m-d H:i:s')
            ]);

            Telegram::answerCallbackQuery($cbId, "✅ Сиз #{$item['queue_number']}-аризани қабул қилдингиз!\n\nМижозга автоматик хабарнома юборилди.", true);

            if ($chatId && $messageId) {
                Telegram::editMessageText(
                    $chatId,
                    $messageId,
                    Report::formatGroupQueueCard($updated),
                    Keyboards::getGroupQueue($queueId, 'in_progress')
                );
            }

            $client = Database::getUser($item['user_id']);
            $clientLang = $client['lang'] ?? 'uz_lat';
            $clientServiceTitle = Lang::getServiceTitle($item['service'], $clientLang);
            $clientNotice = Lang::t('operator_taken', $clientLang, [
                'queue_number' => $item['queue_number'],
                'operator'     => $fromName,
                'service'      => $clientServiceTitle
            ]);
            Telegram::sendMessage($item['user_id'], $clientNotice);
            return;
        }

        // 1.2. ✅ Бажарилди: done_{queueId}
        if (strpos($data, 'done_') === 0) {
            $queueId = substr($data, 5);
            $item = Database::getQueueById($queueId);

            if (!$item) {
                Telegram::answerCallbackQuery($cbId, '⚠️ Ариза топилмади!', true);
                return;
            }

            if ($item['status'] === 'done') {
                Telegram::answerCallbackQuery($cbId, '⚠️ Бу ариза аллақачон бажарилган!', true);
                return;
            }

            $updated = Database::updateQueue($queueId, [
                'status'        => 'done',
                'operator_id'   => $item['operator_id'] ?: $fromId,
                'operator_name' => $item['operator_name'] ?: $fromName,
                'done_at'       => date('Y-m-d H:i:s')
            ]);

            Telegram::answerCallbackQuery($cbId, "🎉 #{$item['queue_number']}-ариза муваффақиятли бажарилди!\n\nМижозга баҳолаш таклифи юборилди.", true);

            if ($chatId && $messageId) {
                Telegram::editMessageText(
                    $chatId,
                    $messageId,
                    Report::formatGroupQueueCard($updated),
                    Keyboards::getGroupQueue($queueId, 'done')
                );
            }

            $client = Database::getUser($item['user_id']);
            $clientLang = $client['lang'] ?? 'uz_lat';
            $clientServiceTitle = Lang::getServiceTitle($item['service'], $clientLang);
            $clientNotice = Lang::t('operator_done', $clientLang, [
                'queue_number' => $item['queue_number'],
                'service'      => $clientServiceTitle
            ]);
            Telegram::sendMessage($item['user_id'], $clientNotice, Keyboards::getClientRating($queueId));
            return;
        }

        // 1.3. 🚫 Бекор қилиш сабабини танлаш: askcancel_{queueId}
        if (strpos($data, 'askcancel_') === 0) {
            $queueId = substr($data, 10);
            Telegram::answerCallbackQuery($cbId, 'Илтимос, бекор қилиш сабабини танланг:');

            if ($chatId && $messageId) {
                Telegram::editMessageText(
                    $chatId,
                    $messageId,
                    "🚫 <b>Бекор қилиш сабабини танланг:</b>",
                    Keyboards::getCancelReasons($queueId)
                );
            }
            return;
        }

        // 1.4. ⬅️ Сабабдан қайтиш: backqueue_{queueId}
        if (strpos($data, 'backqueue_') === 0) {
            $queueId = substr($data, 10);
            $item = Database::getQueueById($queueId);
            if ($item && $chatId && $messageId) {
                Telegram::answerCallbackQuery($cbId);
                Telegram::editMessageText(
                    $chatId,
                    $messageId,
                    Report::formatGroupQueueCard($item),
                    Keyboards::getGroupQueue($queueId, $item['status'])
                );
            }
            return;
        }

        // 1.5. 🚫 Аниқ сабаб билан бекор қилиш: cancelr_{queueId}_{reason}
        if (strpos($data, 'cancelr_') === 0) {
            $parts = explode('_', $data);
            if (count($parts) >= 3) {
                $reasonKey = end($parts);
                $queueId = $parts[1] . '_' . $parts[2] . '_' . $parts[3];

                $reasonMap = [
                    'stir'  => 'СТИР (ИНН) хато киритилган',
                    'debt'  => 'Қарздорлик мавжудлиги сабабли',
                    'docs'  => 'Ҳужжатлар етарли эмас',
                    'other' => 'Техник сабабга кўра'
                ];
                $reasonText = $reasonMap[$reasonKey] ?? 'Техник сабабга кўра';

                $item = Database::getQueueById($queueId);
                if (!$item) {
                    Telegram::answerCallbackQuery($cbId, '⚠️ Ариза топилмади!', true);
                    return;
                }

                $updated = Database::updateQueue($queueId, [
                    'status'        => 'cancelled',
                    'cancel_reason' => $reasonText,
                    'operator_id'   => $item['operator_id'] ?: $fromId,
                    'operator_name' => $item['operator_name'] ?: $fromName,
                    'done_at'       => date('Y-m-d H:i:s')
                ]);

                Telegram::answerCallbackQuery($cbId, "❌ #{$item['queue_number']}-ариза бекор қилинди ва мижозга сабаби етказилди.", true);

                if ($chatId && $messageId) {
                    Telegram::editMessageText(
                        $chatId,
                        $messageId,
                        Report::formatGroupQueueCard($updated),
                        Keyboards::getGroupQueue($queueId, 'cancelled')
                    );
                }

                $client = Database::getUser($item['user_id']);
                $clientLang = $client['lang'] ?? 'uz_lat';
                $clientServiceTitle = Lang::getServiceTitle($item['service'], $clientLang);
                $clientNotice = Lang::t('operator_cancelled', $clientLang, [
                    'queue_number' => $item['queue_number'],
                    'service'      => $clientServiceTitle,
                    'reason'       => $reasonText
                ]);
                Telegram::sendMessage($item['user_id'], $clientNotice);
            }
            return;
        }

        // 1.6. 🚀 Xabarnoma tarqatishni tasdiqlash: sendbc_{draftId}
        if (strpos($data, 'sendbc_') === 0) {
            $draftId = substr($data, 7);
            $draft = Database::getBroadcastDraft($draftId);

            if (!$draft) {
                Telegram::answerCallbackQuery($cbId, '⚠️ Xabarnoma muddati o\'tgan yoki topilmadi!', true);
                return;
            }

            Telegram::answerCallbackQuery($cbId, '🚀 Yuborilmoqda...');

            $allUsers = Database::getAllUsers();
            $sentCount = 0;

            foreach ($allUsers as $uId => $uData) {
                if (!empty($uData['phone'])) {
                    $res = null;
                    if (!empty($draft['file_id'])) {
                        if ($draft['file_type'] === 'photo') {
                            $res = Telegram::sendPhoto($uId, $draft['file_id'], $draft['caption'] ?? '');
                        } elseif ($draft['file_type'] === 'video') {
                            $res = Telegram::sendVideo($uId, $draft['file_id'], $draft['caption'] ?? '');
                        } elseif ($draft['file_type'] === 'document') {
                            $res = Telegram::sendDocumentFileId($uId, $draft['file_id'], $draft['caption'] ?? '');
                        }
                    } elseif (!empty($draft['text'])) {
                        $res = Telegram::sendMessage($uId, $draft['text']);
                    } else {
                        $res = Telegram::copyMessage($uId, $draft['chat_id'], $draft['message_id']);
                    }

                    if (!empty($res['ok'])) {
                        $sentCount++;
                    }
                    usleep(40000);
                }
            }

            Database::deleteBroadcastDraft($draftId);

            if ($chatId && $messageId) {
                Telegram::editMessageText(
                    $chatId,
                    $messageId,
                    "✅ <b>Xabarnoma muvaffaqiyatli tarqatildi!</b>\n\nJami <b>{$sentCount} ta</b> mijozga to'liq yetkazildi. 🚀"
                );
            }
            return;
        }

        // 1.7. ❌ Xabarnomani bekor qilish: cancelbc_{draftId}
        if (strpos($data, 'cancelbc_') === 0) {
            $draftId = substr($data, 9);
            Database::deleteBroadcastDraft($draftId);
            Telegram::answerCallbackQuery($cbId, '❌ Bekor qilindi');

            if ($chatId && $messageId) {
                Telegram::editMessageText(
                    $chatId,
                    $messageId,
                    "❌ <b>Xabarnoma yuborish bekor qilindi va hech kimga yuborilmadi.</b>"
                );
            }
            return;
        }

        // 1.8. ⭐ Рейтинг баҳолаш: rate_{queueId}_{stars}
        if (strpos($data, 'rate_') === 0) {
            $parts = explode('_', $data);
            if (count($parts) >= 3) {
                $queueId = $parts[1] . '_' . $parts[2] . '_' . $parts[3];
                $stars = (int)end($parts);

                $item = Database::getQueueById($queueId);
                if ($item) {
                    $updated = Database::updateQueue($queueId, ['rating' => $stars]);
                    $client = Database::getUser($item['user_id']);
                    $clientLang = $client['lang'] ?? 'uz_lat';

                    Telegram::answerCallbackQuery($cbId, "⭐ " . $stars . "/5");

                    $starStr = str_repeat('⭐', $stars);
                    if ($chatId && $messageId) {
                        $thankMsg = Lang::t('rating_thanks', $clientLang, [
                            'stars'  => $starStr,
                            'rating' => $stars
                        ]);
                        Telegram::editMessageText($chatId, $messageId, $thankMsg);
                    }

                    if (!empty($item['group_msg_id'])) {
                        Telegram::editMessageText(
                            Config::$groupId,
                            $item['group_msg_id'],
                            Report::formatGroupQueueCard($updated),
                            Keyboards::getGroupQueue($queueId, $updated['status'])
                        );
                    }
                }
            }
            return;
        }

        Telegram::answerCallbackQuery($cbId);
    }

    private static function handleMessage($message) {
        $chatId = $message['chat']['id'] ?? null;
        $messageId = $message['message_id'] ?? null;
        $userId = $message['from']['id'] ?? $chatId;
        $firstName = htmlspecialchars($message['from']['first_name'] ?? 'Мижоз');
        $username = isset($message['from']['username']) ? '@' . $message['from']['username'] : 'Мавжуд эмас';
        $text = trim($message['text'] ?? ($message['caption'] ?? ''));
        $chatType = $message['chat']['type'] ?? 'private';
        $isGroup = ($chatType === 'group' || $chatType === 'supergroup' || $chatId < 0);
        $ephemeralMsgId = $message['ephemeral_message_id'] ?? ($message['reply_parameters']['ephemeral_message_id'] ?? null);

        // Hujjat yoki Rasm/Video tekshiruvi
        $document = $message['document'] ?? null;
        $photo = $message['photo'] ?? null;
        $video = $message['video'] ?? null;
        $replyToMessage = $message['reply_to_message'] ?? null;

        $ephemeralExtra = [];
        if ($isGroup) {
            $ephemeralExtra['receiver_user_id'] = $userId;
            $replyParams = [];
            if ($messageId) $replyParams['message_id'] = $messageId;
            if ($ephemeralMsgId) $replyParams['ephemeral_message_id'] = $ephemeralMsgId;
            if (!empty($replyParams)) $ephemeralExtra['reply_parameters'] = $replyParams;
        }

        $user = Database::getUser($userId);
        $lang = $user['lang'] ?? 'uz_lat';
        $isAdmin = Config::isAdmin($userId);

        // =========================================================
        // ГУРУҲ ХАБАРЛАРИ (STAFF & EPHEMERAL)
        // =========================================================
        if ($isGroup) {
            // 1. Buxgalter tayyor hujjatni REPLY qilib tashlaganda (Direct Reply Delivery)
            if ($replyToMessage && ($document || $photo || $video || !empty($text))) {
                $replyMsgId = $replyToMessage['message_id'] ?? null;
                $targetQueue = Database::getQueueByGroupMsgId($replyMsgId);

                if ($targetQueue && $targetQueue['status'] !== 'done') {
                    $opName = htmlspecialchars(trim(($message['from']['first_name'] ?? '') . ' ' . ($message['from']['last_name'] ?? '')));
                    if (empty($opName)) $opName = 'Ходим';

                    $updated = Database::updateQueue($targetQueue['id'], [
                        'status'        => 'done',
                        'operator_id'   => $userId,
                        'operator_name' => $opName,
                        'done_at'       => date('Y-m-d H:i:s')
                    ]);

                    Telegram::editMessageText(
                        $chatId,
                        $replyMsgId,
                        Report::formatGroupQueueCard($updated),
                        Keyboards::getGroupQueue($targetQueue['id'], 'done')
                    );

                    $client = Database::getUser($targetQueue['user_id']);
                    $clientLang = $client['lang'] ?? 'uz_lat';
                    $captionNotice = Lang::t('doc_delivered', $clientLang, [
                        'queue_number' => $targetQueue['queue_number']
                    ]);

                    if ($document) {
                        Telegram::sendDocumentFileId($targetQueue['user_id'], $document['file_id'], $captionNotice, Keyboards::getClientRating($targetQueue['id']));
                    } elseif ($photo) {
                        $largestPhoto = end($photo);
                        Telegram::sendPhoto($targetQueue['user_id'], $largestPhoto['file_id'], $captionNotice, Keyboards::getClientRating($targetQueue['id']));
                    } elseif ($video) {
                        Telegram::sendVideo($targetQueue['user_id'], $video['file_id'], $captionNotice, Keyboards::getClientRating($targetQueue['id']));
                    } else {
                        $customNotice = "📄 <b>Tayyor hujjat / Javob:</b>\n\n" . htmlspecialchars($text) . "\n\n" . $captionNotice;
                        Telegram::sendMessage($targetQueue['user_id'], $customNotice, Keyboards::getClientRating($targetQueue['id']));
                    }

                    Telegram::sendMessage($chatId, "✅ <b>#{$targetQueue['queue_number']}-ariza uchun tayyor hujjat mijozga to'g'ridan-to'g'ri yetkazildi va ariza yakunlandi!</b>", null, [
                        'reply_parameters' => ['message_id' => $messageId]
                    ]);
                    return;
                }
            }

            // 2. /news yoki /send (Yangilik tayyorlash va tasdiqlash so'rash)
            if (strpos($text, '/news') === 0 || strpos($text, '/send') === 0) {
                $newsContent = trim(preg_replace('/^\/(news|send)(@\w+)?/i', '', $text));
                
                if (empty($newsContent) && !$photo && !$video && !$document) {
                    Telegram::sendMessage($chatId, "ℹ️ <b>Xabarnoma yuborish formati:</b>\n<code>/news Hurmatli tadbirkorlar! ...</code>\n\n<i>Yoki rasm/video/hujjat bilan birga <b>/news</b> yozib yuboring.</i>", null, [
                        'reply_parameters' => ['message_id' => $messageId]
                    ]);
                    return;
                }

                $draftId = 'bc_' . time() . '_' . rand(100, 999);
                $fileId = $photo ? end($photo)['file_id'] : ($video ? $video['file_id'] : ($document ? $document['file_id'] : null));
                $fileType = $photo ? 'photo' : ($video ? 'video' : ($document ? 'document' : 'text'));

                Database::saveBroadcastDraft($draftId, [
                    'chat_id'    => $chatId,
                    'message_id' => $messageId,
                    'text'       => $newsContent,
                    'file_id'    => $fileId,
                    'file_type'  => $fileType,
                    'caption'    => $newsContent,
                    'sender_id'  => $userId,
                    'created_at' => date('Y-m-d H:i:s')
                ]);

                $allUsers = Database::getAllUsers();
                $recipientsCount = count(array_filter($allUsers, function($u) { return !empty($u['phone']); }));

                $confirmText = "⚠️ <b>XABARNOMANI TEKSHIRIB TASDIQLANG!</b>\n";
                $confirmText .= "━━━━━━━━━━━━━━━━━━━━\n";
                $confirmText .= "Yuqoridagi xabar barcha <b>{$recipientsCount} ta mijozga</b> (matn, rasm, video yoki fayl ko'rinishida) to'g'ridan-to'g'ri yuboriladi.\n\n";
                $confirmText .= "<i>Hammasi to'g'rimi? Yuborishni tasdiqlaysizmi?</i>";

                Telegram::sendMessage($chatId, $confirmText, Keyboards::getBroadcastConfirmKeyboard($draftId), [
                    'reply_parameters' => ['message_id' => $messageId]
                ]);
                return;
            }

            // 3. /stat
            if ($text === '/stat' || $text === '/stats' || strpos($text, '/stat@') === 0) {
                $statsMsg = Report::generateStatsMessage();
                Telegram::sendMessage($chatId, $statsMsg, null, $ephemeralExtra);
                return;
            }

            // 4. /report
            if ($text === '/report' || $text === '/pdf' || $text === '/excel' || strpos($text, '/report@') === 0) {
                Telegram::sendMessage($chatId, "⏳ <b>PDF hisobot tayyorlanmoqda...</b>", null, $ephemeralExtra);
                $pdfFile = Report::exportQueueToPdf();
                if ($pdfFile) {
                    Telegram::sendDocument($chatId, $pdfFile, "📊 <b>Buxgalteriya Navbat PDF Hisoboti</b>\nSana: " . date('d.m.Y H:i'), $ephemeralExtra);
                    @unlink($pdfFile);
                } else {
                    Telegram::sendMessage($chatId, "⚠️ PDF hisobot yaratishda xatolik yuz berdi.", null, $ephemeralExtra);
                }
                return;
            }

            // 5. /myqueue
            if ($text === '/myqueue' || strpos($text, '/myqueue@') === 0) {
                $db = Database::getDb();
                $today = date('Y-m-d');
                $userQueues = [];
                foreach ($db['queue'] as $item) {
                    if ($item['user_id'] == $userId && ($item['date'] === $today || $item['status'] === 'pending' || $item['status'] === 'in_progress')) {
                        $userQueues[] = $item;
                    }
                }
                if (empty($userQueues)) {
                    $msg = Lang::t('no_queue', $lang);
                } else {
                    $msg = Lang::t('my_queues_title', $lang);
                    foreach (array_reverse($userQueues) as $q) {
                        $st = Lang::t('status_' . ($q['status'] ?? 'pending'), $lang);
                        $servTitle = Lang::getServiceTitle($q['service'], $lang);
                        $msg .= "🎫 <b>Navbat:</b> #<b>{$q['queue_number']}</b>\n🛠 <b>Xizmat:</b> {$servTitle}\n📊 <b>Holat:</b> <b>{$st}</b>\n──────────\n";
                    }
                }
                Telegram::sendMessage($chatId, $msg, null, $ephemeralExtra);
                return;
            }

            if (strpos($text, '/start') === 0 || strpos($text, '/navbat') === 0) {
                $kb = [
                    'inline_keyboard' => [
                        [
                            ['text' => '🤖 Shaxsiy chatda navbat olish', 'url' => "https://t.me/ArzonUC_robot?start=start"]
                        ]
                    ]
                ];
                Telegram::sendMessage($chatId, "ℹ️ <b>Buxgalteriya xizmatlariga navbat olish:</b>", $kb, $ephemeralExtra);
                return;
            }

            return;
        }

        // =========================================================
        // ШАХСИЙ ЧАТ (ЛИЧКА)
        // =========================================================

        // ADMIN PANEL MENYUSI
        if ($isAdmin && ($text === '👑 Admin Panel' || $text === '/admin')) {
            Database::saveUser($userId, ['step' => 'admin_panel', 'menu' => 'admin']);
            $adminMsg = "👑 <b>BUXGALTERIYA BOTI - ADMIN PANEL</b>\n";
            $adminMsg .= "━━━━━━━━━━━━━━━━━━━━\n";
            $adminMsg .= "Xush kelibsiz, Admin!\nQuyidagi menyu orqali botni to'liq boshqarishingiz mumkin:";
            Telegram::sendMessage($chatId, $adminMsg, Keyboards::getAdminPanel());
            return;
        }

        // ADMIN BUYRUQLARI
        if ($isAdmin) {
            // 1. Xabarnoma yuborish bosqichini boshlash
            if ($text === '📢 Xabarnoma yuborish (Rassilka)') {
                Database::saveUser($userId, ['step' => 'admin_wait_broadcast']);
                $bcMsg = "📢 <b>XABARNOMA YUBORISH BO'LIMI:</b>\n";
                $bcMsg .= "━━━━━━━━━━━━━━━━━━━━\n";
                $bcMsg .= "Barcha mijozlarga yubormoqchi bo'lgan xabaringizni yozib yuboring.\n\n";
                $bcMsg .= "💡 <i>Siz oddiy matn, rasmli xabar, video yoki fayl yuborishingiz mumkin.</i>";
                Telegram::sendMessage($chatId, $bcMsg, Keyboards::getCancelBroadcast());
                return;
            }

            // 2. Xabarnomani bekor qilish
            if ($text === '❌ Xabarnomani bekor qilish') {
                Database::saveUser($userId, ['step' => 'admin_panel']);
                Telegram::sendMessage($chatId, "❌ <i>Xabarnoma tayyorlash bekor qilindi.</i>", Keyboards::getAdminPanel());
                return;
            }

            // 3. Admin xabarnoma matnini yuborganda
            if (($user['step'] ?? '') === 'admin_wait_broadcast' && ($text || $photo || $video || $document)) {
                $draftId = 'bc_' . time() . '_' . rand(100, 999);
                $fileId = $photo ? end($photo)['file_id'] : ($video ? $video['file_id'] : ($document ? $document['file_id'] : null));
                $fileType = $photo ? 'photo' : ($video ? 'video' : ($document ? 'document' : 'text'));

                Database::saveBroadcastDraft($draftId, [
                    'chat_id'    => $chatId,
                    'message_id' => $messageId,
                    'text'       => $text,
                    'file_id'    => $fileId,
                    'file_type'  => $fileType,
                    'caption'    => $text,
                    'sender_id'  => $userId,
                    'created_at' => date('Y-m-d H:i:s')
                ]);

                Database::saveUser($userId, ['step' => 'admin_panel']);

                $allUsers = Database::getAllUsers();
                $recipientsCount = count(array_filter($allUsers, function($u) { return !empty($u['phone']); }));

                $confirmText = "⚠️ <b>XABARNOMANI TEKSHIRIB TASDIQLANG!</b>\n";
                $confirmText .= "━━━━━━━━━━━━━━━━━━━━\n";
                $confirmText .= "Yuqoridagi xabar barcha <b>{$recipientsCount} ta mijozga</b> to'liq yetkaziladi.\n\n";
                $confirmText .= "<i>Xabarni tekshirib ko'ring va tasdiqlang:</i>";

                Telegram::sendMessage($chatId, $confirmText, Keyboards::getBroadcastConfirmKeyboard($draftId), [
                    'reply_parameters' => ['message_id' => $messageId]
                ]);
                return;
            }

            // 4. Statistika
            if ($text === '📊 Kunlik Statistika & KPI') {
                $statsMsg = Report::generateStatsMessage();
                Telegram::sendMessage($chatId, $statsMsg, Keyboards::getAdminPanel());
                return;
            }

            // 5. PDF hisobot
            if ($text === '📥 PDF hisobot yuklash' || $text === '📥 Excel hisobot yuklash') {
                Telegram::sendMessage($chatId, "⏳ <b>PDF hisobot tayyorlanmoqda...</b>");
                $pdfFile = Report::exportQueueToPdf();
                if ($pdfFile) {
                    Telegram::sendDocument($chatId, $pdfFile, "📊 <b>Buxgalteriya Navbat PDF Hisoboti</b>\nSana: " . date('d.m.Y H:i'), []);
                    @unlink($pdfFile);
                } else {
                    Telegram::sendMessage($chatId, "⚠️ PDF hisobot yaratishda xatolik yuz berdi.", Keyboards::getAdminPanel());
                }
                return;
            }

            // 6. Foydalanuvchilar soni
            if ($text === '👥 Foydalanuvchilar soni') {
                $allUsers = Database::getAllUsers();
                $totalUsers = count($allUsers);
                $withPhone = count(array_filter($allUsers, function($u) { return !empty($u['phone']); }));

                $uzLat = 0; $uzCyr = 0; $ru = 0; $en = 0;
                foreach ($allUsers as $u) {
                    $l = $u['lang'] ?? 'uz_lat';
                    if ($l === 'uz_lat') $uzLat++;
                    elseif ($l === 'uz_cyr') $uzCyr++;
                    elseif ($l === 'ru') $ru++;
                    elseif ($l === 'en') $en++;
                }

                $userStats = "👥 <b>FOYDALANUVCHILAR STATISTIKASI</b>\n";
                $userStats .= "━━━━━━━━━━━━━━━━━━━━\n";
                $userStats .= "👤 <b>Jami foydalanuvchilar:</b> <code>{$totalUsers} ta</code>\n";
                $userStats .= "📱 <b>Telefon raqamini kiritganlar:</b> <code>{$withPhone} ta</code>\n\n";
                $userStats .= "🌐 <b>Tillar bo'yicha taqsimot:</b>\n";
                $userStats .= "• 🇺🇿 O'zbekcha (Lotin): <code>{$uzLat} ta</code>\n";
                $userStats .= "• 🇺🇿 Ўзбекча (Кирилл): <code>{$uzCyr} ta</code>\n";
                $userStats .= "• 🇷🇺 Русский: <code>{$ru} ta</code>\n";
                $userStats .= "• 🇬🇧 English: <code>{$en} ta</code>\n";
                $userStats .= "━━━━━━━━━━━━━━━━━━━━";

                Telegram::sendMessage($chatId, $userStats, Keyboards::getAdminPanel());
                return;
            }
        }

        // ТИЛ ТАНЛАШ
        $langMap = [
            "🇺🇿 O'zbekcha" => 'uz_lat',
            "🇺🇿 Ўзбекcha"  => 'uz_lat',
            "🇺🇿 Ўзбекча"  => 'uz_cyr',
            "🇷🇺 Русский"   => 'ru',
            "🇬🇧 English"   => 'en'
        ];

        if (isset($langMap[$text])) {
            $newLang = $langMap[$text];
            $lang = $newLang;

            if (empty($user['phone'])) {
                Database::saveUser($userId, [
                    'user_id'  => $userId,
                    'name'     => $firstName,
                    'username' => $username,
                    'lang'     => $newLang,
                    'step'     => 'wait_phone',
                    'menu'     => 'main'
                ]);
                $msg = Lang::t('lang_changed', $lang) . "\n\n" . Lang::t('ask_phone', $lang, ['name' => $firstName]);
                Telegram::sendMessage($chatId, $msg, Keyboards::getContact($lang));
            } else {
                Database::saveUser($userId, [
                    'lang' => $newLang,
                    'step' => 'main',
                    'menu' => 'main'
                ]);
                $msg = Lang::t('lang_changed', $lang) . "\n\n" . Lang::t('welcome', $lang, ['name' => $firstName, 'phone' => $user['phone']]);
                Telegram::sendMessage($chatId, $msg, Keyboards::getMain($lang, $isAdmin));
            }
            return;
        }

        if ($text === '/stat' || $text === '/stats') {
            $statsMsg = Report::generateStatsMessage();
            Telegram::sendMessage($chatId, $statsMsg);
            return;
        }

        if ($text === '/report' || $text === '/pdf' || $text === '/excel') {
            Telegram::sendMessage($chatId, "⏳ <b>PDF hisobot tayyorlanmoqda...</b>");
            $pdfFile = Report::exportQueueToPdf();
            if ($pdfFile) {
                Telegram::sendDocument($chatId, $pdfFile, "📊 <b>Buxgalteriya Navbat PDF Hisoboti</b>\nSana: " . date('d.m.Y H:i'));
                @unlink($pdfFile);
            } else {
                Telegram::sendMessage($chatId, "⚠️ PDF hisobot yaratishda xatolik yuz berdi.");
            }
            return;
        }

        // /start
        if ($text === '/start') {
            if (empty($user['lang'])) {
                Database::saveUser($userId, [
                    'user_id'    => $userId,
                    'name'       => $firstName,
                    'username'   => $username,
                    'phone'      => null,
                    'lang'       => null,
                    'step'       => 'choose_lang',
                    'menu'       => 'main'
                ]);
                $msg = "🌐 <b>Iltimos, tilni tanlang / Тилни танланг / Выберите язык / Choose language:</b>";
                Telegram::sendMessage($chatId, $msg, Keyboards::getLanguage());
                return;
            }

            if (!empty($user['phone'])) {
                Database::saveUser($userId, ['menu' => 'main', 'step' => 'main']);
                $msg = Lang::t('welcome', $lang, ['name' => $firstName, 'phone' => $user['phone']]);
                Telegram::sendMessage($chatId, $msg, Keyboards::getMain($lang, $isAdmin));
            } else {
                Database::saveUser($userId, ['step' => 'wait_phone']);
                $msg = Lang::t('ask_phone', $lang, ['name' => $firstName]);
                Telegram::sendMessage($chatId, $msg, Keyboards::getContact($lang));
            }
            return;
        }

        // Contact
        if (isset($message['contact'])) {
            $phone = $message['contact']['phone_number'];
            if (strpos($phone, '+') !== 0) $phone = '+' . $phone;

            Database::saveUser($userId, [
                'user_id'  => $userId,
                'name'     => $firstName,
                'username' => $username,
                'phone'    => $phone,
                'step'     => 'main',
                'menu'     => 'main'
            ]);

            $msg = Lang::t('phone_saved', $lang, ['phone' => $phone]);
            Telegram::sendMessage($chatId, $msg, Keyboards::getMain($lang, $isAdmin));
            return;
        }

        // Sozlamalar
        if ($text === '⚙️ Sozlamalar' || $text === '⚙️ Созламалар' || $text === '⚙️ Настройки' || $text === '⚙️ Settings' || $text === '⚙️ Созламалар / Рақам') {
            Database::saveUser($userId, ['menu' => 'settings']);
            $msg = Lang::t('settings_menu', $lang);
            Telegram::sendMessage($chatId, $msg, Keyboards::getSettings($lang));
            return;
        }

        // Tilni o'zgartirish
        if ($text === '🌐 Tilni o\'zgartirish' || $text === '🌐 Тилни ўзгартириш' || $text === '🌐 Сменить язык' || $text === '🌐 Change language') {
            Database::saveUser($userId, ['step' => 'choose_lang']);
            $msg = Lang::t('choose_lang', $lang);
            Telegram::sendMessage($chatId, $msg, Keyboards::getLanguage());
            return;
        }

        // Raqamni o'zgartirish
        if ($text === '📱 Raqamni o\'zgartirish' || $text === '📱 Рақамни ўзгартириш' || $text === '📱 Сменить номер' || $text === '📱 Change phone number' || $text === '📱 Телефонни ўзгартириш') {
            Database::saveUser($userId, ['step' => 'wait_phone']);
            $msg = Lang::t('ask_new_phone', $lang);
            Telegram::sendMessage($chatId, $msg, Keyboards::getContact($lang));
            return;
        }

        // Telefon kutilayotganda
        if (!empty($user) && (($user['step'] ?? '') === 'wait_phone' || empty($user['phone']))) {
            $cleanPhone = preg_replace('/[^\d+]/', '', $text);
            if (strlen($cleanPhone) >= 9) {
                if (strpos($cleanPhone, '+') !== 0 && strlen($cleanPhone) == 12) {
                    $cleanPhone = '+' . $cleanPhone;
                } elseif (strlen($cleanPhone) == 9) {
                    $cleanPhone = '+998' . $cleanPhone;
                }

                Database::saveUser($userId, [
                    'phone'    => $cleanPhone,
                    'step'     => 'main',
                    'name'     => $firstName,
                    'username' => $username,
                    'menu'     => 'main'
                ]);

                $msg = Lang::t('phone_saved', $lang, ['phone' => $cleanPhone]);
                Telegram::sendMessage($chatId, $msg, Keyboards::getMain($lang, $isAdmin));
                return;
            } else {
                $msg = Lang::t('phone_invalid', $lang);
                Telegram::sendMessage($chatId, $msg, Keyboards::getContact($lang));
                return;
            }
        }

        // Ma'lumot va yordam
        if ($text === "ℹ️ Ma'lumot va yordam" || $text === "ℹ️ Маълумот ва ёрдам" || $text === "ℹ️ Информация и помощь" || $text === "ℹ️ Information & Help" || $text === "/help") {
            $infoMsg = Lang::t('info_text', $lang);
            Telegram::sendMessage($chatId, $infoMsg, Keyboards::getMain($lang, $isAdmin));
            return;
        }

        // Mening navbatim
        if ($text === '📊 Mening navbatim' || $text === '📊 Менинг навбатим' || $text === '📊 Моя очередь' || $text === '📊 My queue' || $text === '/myqueue') {
            $db = Database::getDb();
            $today = date('Y-m-d');
            $userQueues = [];

            foreach ($db['queue'] as $item) {
                if ($item['user_id'] == $userId && ($item['date'] === $today || $item['status'] === 'pending' || $item['status'] === 'in_progress')) {
                    $userQueues[] = $item;
                }
            }

            if (empty($userQueues)) {
                $msg = Lang::t('no_queue', $lang);
                Telegram::sendMessage($chatId, $msg, Keyboards::getMain($lang, $isAdmin));
                return;
            }

            $allPending = [];
            foreach ($db['queue'] as $item) {
                if ($item['status'] === 'pending' && ($item['date'] ?? '') === $today) {
                    $allPending[] = $item['queue_number'];
                }
            }

            $msg = Lang::t('my_queues_title', $lang);

            foreach (array_reverse($userQueues) as $q) {
                $st = Lang::t('status_' . ($q['status'] ?? 'pending'), $lang);
                $servTitle = Lang::getServiceTitle($q['service'], $lang);
                $msg .= "🎫 <b>Navbat / Очередь:</b> #<b>{$q['queue_number']}</b>\n";
                $msg .= "🛠 <b>Xizmat / Услуga:</b> {$servTitle}\n";
                $msg .= "📊 <b>Holat / Статус:</b> <b>{$st}</b>\n";
                if (!empty($q['operator_name'])) {
                    $msg .= "👤 <b>Mutaxassis / Специалист:</b> <b>{$q['operator_name']}</b>\n";
                }
                if ($q['status'] === 'pending') {
                    $aheadCount = 0;
                    foreach ($allPending as $pNum) {
                        if ($pNum < $q['queue_number']) $aheadCount++;
                    }
                    if ($aheadCount > 0) {
                        $msg .= Lang::t('ahead_count', $lang, ['count' => $aheadCount]);
                    } else {
                        $msg .= Lang::t('you_are_first', $lang);
                    }
                }
                $msg .= "⏰ <b>Vaqt:</b> <code>{$q['created_at']}</code>\n";
                $msg .= "────────────────────\n";
            }
            $msg .= "━━━━━━━━━━━━━━━━━━━━";

            Telegram::sendMessage($chatId, $msg, Keyboards::getMain($lang, $isAdmin));
            return;
        }

        // Orqaga / Asosiy menyu
        if ($text === '⬅️ Orqaga' || $text === '⬅️ Орқага' || $text === '⬅️ Назад' || $text === '⬅️ Back' || $text === '🏠 Asosiy menyu' || $text === '🏠 Асосий меню' || $text === '🏠 Главное меню' || $text === '🏠 Main menu') {
            Database::saveUser($userId, [
                'menu'            => 'main',
                'step'            => 'main',
                'pending_service' => null
            ]);
            $msg = Lang::t('select_service_prompt', $lang);
            Telegram::sendMessage($chatId, $msg, Keyboards::getMain($lang, $isAdmin));
            return;
        }

        // Shartnoma
        if ($text === '📄 Shartnoma olish' || $text === '📄 Шартнома олиш' || $text === '📄 Оформить договор' || $text === '📄 Contract' || $text === '📄 Shartnoma' || $text === '📄 Шартнома') {
            Database::saveUser($userId, ['menu' => 'shartnoma']);
            $msg = Lang::t('select_system', $lang, ['service' => Lang::t('btn_contract', $lang)]);
            Telegram::sendMessage($chatId, $msg, Keyboards::getShartnoma($lang));
            return;
        }

        // Hisob-faktura
        if ($text === '🧾 Hisob-faktura' || $text === '🧾 Ҳисоб-фактура' || $text === '🧾 Счет-фактура' || $text === '🧾 Invoice') {
            Database::saveUser($userId, ['menu' => 'faktura']);
            $msg = Lang::t('select_system', $lang, ['service' => Lang::t('btn_invoice', $lang)]);
            Telegram::sendMessage($chatId, $msg, Keyboards::getFaktura($lang));
            return;
        }

        // Ishonchnoma
        if ($text === '📑 Ishonchnoma' || $text === '📑 Ишончнома' || $text === '📑 Доверенность' || $text === '📑 Power of Attorney') {
            Database::saveUser($userId, ['menu' => 'ishonchnoma']);
            $msg = Lang::t('select_system', $lang, ['service' => Lang::t('btn_attorney', $lang)]);
            Telegram::sendMessage($chatId, $msg, Keyboards::getIshonchnoma($lang));
            return;
        }

        // Xizmatlar xaritasi
        if (isset(Lang::$serviceMap[$text])) {
            $serviceKey = Lang::$serviceMap[$text];
            Database::saveUser($userId, [
                'pending_service' => $serviceKey,
                'step'            => 'wait_comment'
            ]);

            $selectedServiceTitle = Lang::getServiceTitle($serviceKey, $lang);
            $promptMsg = Lang::t('ask_stir', $lang, ['service' => $selectedServiceTitle]);
            Telegram::sendMessage($chatId, $promptMsg, Keyboards::getSkipComment($lang));
            return;
        }

        // STIR / Izoh / Fayl biriktirish
        if (!empty($user) && ($user['step'] ?? '') === 'wait_comment') {
            $serviceKey = $user['pending_service'] ?? 'contract_didox';
            $isSkip = ($text === "⏭ O'tkazib yuborish" || $text === "⏭ Ўтказиб юбориш" || $text === "⏭ Пропустить" || $text === "⏭ Skip");
            
            $fileId = null;
            $fileType = null;

            if ($document) {
                $fileId = $document['file_id'];
                $fileType = 'document';
            } elseif ($photo) {
                $largestPhoto = end($photo);
                $fileId = $largestPhoto['file_id'];
                $fileType = 'photo';
            }

            $comment = $isSkip ? '' : $text;

            Database::saveUser($userId, [
                'step'            => 'main',
                'pending_service' => null,
                'menu'            => 'main'
            ]);

            $phone = $user['phone'] ?? 'Kiritilmagan';

            // 1. Navbat yaratish
            $queueItem = Database::createNewQueue($userId, $firstName, $username, $phone, $serviceKey, $comment, $fileId, $fileType);
            $queueNumber = $queueItem['queue_number'];
            $queueId = $queueItem['id'];
            $timeNow = date('d.m.Y H:i:s');

            // 2. Guruhga yuborish
            $groupCardText = Report::formatGroupQueueCard($queueItem);
            $groupReplyMarkup = Keyboards::getGroupQueue($queueId, 'pending');
            $groupRes = Telegram::sendMessage(Config::$groupId, $groupCardText, $groupReplyMarkup);

            if (!empty($groupRes['result']['message_id'])) {
                $groupMsgId = $groupRes['result']['message_id'];
                Database::updateQueue($queueId, ['group_msg_id' => $groupMsgId]);

                if ($fileId && $fileType === 'document') {
                    Telegram::sendDocumentFileId(Config::$groupId, $fileId, "📎 <b>#{$queueNumber}-ariza uchun ilova qilingan hujjat</b>", null, [
                        'reply_parameters' => ['message_id' => $groupMsgId]
                    ]);
                } elseif ($fileId && $fileType === 'photo') {
                    Telegram::sendPhoto(Config::$groupId, $fileId, "📎 <b>#{$queueNumber}-ariza uchun ilova qilingan rekvizit rasmi</b>", null, [
                        'reply_parameters' => ['message_id' => $groupMsgId]
                    ]);
                }
            }

            // 3. Mijozga chipta
            $stirLine = !empty($comment) ? "📝 <b>STIR / Izoh:</b> <code>" . htmlspecialchars($comment) . "</code>\n" : "";
            $fileAttachLine = !empty($fileId) ? "📎 <b>Ilova qilingan fayl:</b> <i>Qabul qilindi ✅</i>\n" : "";
            $clientServiceTitle = Lang::getServiceTitle($serviceKey, $lang);

            $clientMsg = Lang::t('ticket_title', $lang, [
                'queue_number' => $queueNumber,
                'service'      => $clientServiceTitle,
                'stir'         => $stirLine,
                'file_attach'  => $fileAttachLine,
                'phone'        => $phone,
                'time'         => $timeNow
            ]);

            if (!Config::isWorkingHours()) {
                $clientMsg .= Lang::t('working_hours_notice', $lang);
            } else {
                $clientMsg .= Lang::t('ticket_wait_text', $lang);
            }

            Telegram::sendMessage($chatId, $clientMsg, Keyboards::getMain($lang, $isAdmin));

            echo date('[H:i:s] ') . "✅ Yangi navbat #{$queueNumber} | Mijoz: {$firstName} | Xizmat: {$clientServiceTitle} | Guruhga yuborildi.\n";
            return;
        }

        $msg = Lang::t('select_service_prompt', $lang);
        Telegram::sendMessage($chatId, $msg, Keyboards::getMain($lang, $isAdmin));
    }
}
