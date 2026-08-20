<?php

namespace App;

class Report {
    public static function formatGroupQueueCard($item) {
        $statusMap = [
            'pending'     => '⏳ Кутилмоқда',
            'in_progress' => '👨‍💻 Жараёнда',
            'done'        => '✅ Бажарилди',
            'cancelled'   => '🚫 Бекор қилинди'
        ];
        $statusText = $statusMap[$item['status']] ?? $item['status'];

        $serviceTitle = Lang::getServiceTitle($item['service'], 'uz_cyr');

        $card = "⚡ <b>ЯНГИ БУЮРТМА / НАВБАТ: #{$item['queue_number']}</b>\n";
        $card .= "━━━━━━━━━━━━━━━━━━━━\n";
        $card .= "👤 <b>Мижоз:</b> <a href=\"tg://user?id={$item['user_id']}\">{$item['name']}</a> ({$item['username']})\n";
        $card .= "📞 <b>Телефон:</b> <code>{$item['phone']}</code>\n";
        $card .= "🛠 <b>Хизмат тури:</b> <b>{$serviceTitle}</b>\n";
        if (!empty($item['comment'])) {
            $card .= "📝 <b>СТИР / Изоҳ:</b> <code>" . htmlspecialchars($item['comment']) . "</code>\n";
        }
        if (!empty($item['file_id'])) {
            $fType = ($item['file_type'] === 'photo') ? '🖼 Реквизит расми' : '📄 Ҳужжат (PDF/Word)';
            $card .= "📎 <b>Илова қилинган файл:</b> <i>{$fType}</i> ✅\n";
        }
        $card .= "⏰ <b>Вақт:</b> <code>{$item['created_at']}</code>\n";
        $card .= "📊 <b>Ҳолат:</b> <b>{$statusText}</b>\n";

        if (!empty($item['operator_name'])) {
            $card .= "👤 <b>Масъул ходим:</b> <b>{$item['operator_name']}</b>\n";
        }
        if (!empty($item['cancel_reason'])) {
            $card .= "🚫 <b>Бекор қилиш сабаби:</b> <i>{$item['cancel_reason']}</i>\n";
        }
        if (!empty($item['rating'])) {
            $stars = str_repeat('⭐', (int)$item['rating']);
            $card .= "🌟 <b>Мижоз баҳоси:</b> {$stars} (<b>{$item['rating']}/5</b>)\n";
        }
        $card .= "━━━━━━━━━━━━━━━━━━━━";

        return $card;
    }

    public static function generateStatsMessage() {
        $db = Database::getDb();
        $today = date('Y-m-d');
        
        $todayTotal = 0;
        $todayPending = 0;
        $todayInProgress = 0;
        $todayDone = 0;
        $todayCancelled = 0;
        $ratings = [];
        $staffStats = [];

        $allTotal = count($db['queue'] ?? []);

        foreach (($db['queue'] ?? []) as $item) {
            $itemDate = substr($item['created_at'] ?? '', 0, 10);
            if ($itemDate === $today) {
                $todayTotal++;
                if ($item['status'] === 'pending') $todayPending++;
                elseif ($item['status'] === 'in_progress') $todayInProgress++;
                elseif ($item['status'] === 'done') $todayDone++;
                elseif ($item['status'] === 'cancelled') $todayCancelled++;
            }
            if (!empty($item['operator_name']) && $item['status'] === 'done') {
                $op = $item['operator_name'];
                if (!isset($staffStats[$op])) {
                    $staffStats[$op] = ['done' => 0, 'ratings' => []];
                }
                $staffStats[$op]['done']++;
                if (!empty($item['rating'])) {
                    $staffStats[$op]['ratings'][] = (int)$item['rating'];
                }
            }
            if (!empty($item['rating'])) {
                $ratings[] = (int)$item['rating'];
            }
        }

        $avgRating = count($ratings) > 0 ? round(array_sum($ratings) / count($ratings), 1) : 'Ҳозирча йўқ';

        $msg = "📊 <b>БУХГАЛТЕРИЯ НАВБАТ ВА ХОДИМЛАР СТАТИСТИКАСИ</b>\n";
        $msg .= "━━━━━━━━━━━━━━━━━━━━\n";
        $msg .= "📅 <b>Сана:</b> <code>" . date('d.m.Y') . "</code>\n\n";
        $msg .= "📥 <b>Бугунги жами аризалар:</b> <code>{$todayTotal} та</code>\n";
        $msg .= "⏳ <b>Кутилмоқда:</b> <code>{$todayPending} та</code>\n";
        $msg .= "👨‍💻 <b>Жараёнда:</b> <code>{$todayInProgress} та</code>\n";
        $msg .= "✅ <b>Бажарилди:</b> <code>{$todayDone} та</code>\n";
        $msg .= "🚫 <b>Бекор қилинди:</b> <code>{$todayCancelled} та</code>\n\n";

        if (!empty($staffStats)) {
            $msg .= "👥 <b>ХОДИМЛАР САМАРАДОРЛИГИ (KPI):</b>\n";
            foreach ($staffStats as $staffName => $sData) {
                $stAvg = count($sData['ratings']) > 0 ? round(array_sum($sData['ratings']) / count($sData['ratings']), 1) . ' ⭐' : '-';
                $msg .= "• <b>{$staffName}:</b> <code>{$sData['done']} та бажарилган</code> (Рейтинг: {$stAvg})\n";
            }
            $msg .= "\n";
        }

        $msg .= "📦 <b>Жами аризаlar:</b> <code>{$allTotal} та</code>\n";
        $msg .= "⭐ <b>Ўртача мижоз баҳоси:</b> <b>{$avgRating}</b>\n";
        $msg .= "━━━━━━━━━━━━━━━━━━━━\n";
        $msg .= "💡 <i>Тўлиқ PDF ҳисоботни олиш учун <b>/report</b> буйруғини юборинг.</i>";

        return $msg;
    }

    public static function exportQueueToPdf() {
        $db = Database::getDb();
        $tempPdf = sys_get_temp_dir() . '/Buxgalteriya_Navbat_Hisoboti_' . date('Y-m-d_Hi') . '.pdf';
        return PdfGenerator::generateQueueReportPdf($db, $tempPdf);
    }
}
