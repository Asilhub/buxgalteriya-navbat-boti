<?php

namespace App;

class PdfGenerator {
    public static function generateQueueReportPdf($db, $outputFile) {
        // 1. Agar tizimda Google Chrome yoki Chromium bo'lsa, HTML dan super sifatli PDF qilamiz
        $chromePath = exec('which google-chrome || which chromium || true');
        if (!empty($chromePath)) {
            $htmlFile = sys_get_temp_dir() . '/rep_' . time() . '_' . rand(100, 999) . '.html';
            $html = self::buildHtmlReport($db);
            file_put_contents($htmlFile, $html);
            exec("{$chromePath} --headless --disable-gpu --no-sandbox --print-to-pdf=" . escapeshellarg($outputFile) . " " . escapeshellarg($htmlFile) . " 2>&1");
            @unlink($htmlFile);
            if (file_exists($outputFile) && filesize($outputFile) > 0) {
                return $outputFile;
            }
        }

        // 2. Agar Chrome bo'lmasa, toza PHP da to'g'ridan-to'g'ri PDF binarini yasaymiz
        return self::generatePurePhpPdf($db, $outputFile);
    }

    public static function buildHtmlReport($db) {
        $dateNow = date('d.m.Y H:i');
        $total = count($db['queue'] ?? []);
        $done = 0; $pending = 0; $cancelled = 0; $inProgress = 0;
        $ratings = [];

        foreach (($db['queue'] ?? []) as $q) {
            if ($q['status'] === 'done') $done++;
            elseif ($q['status'] === 'pending') $pending++;
            elseif ($q['status'] === 'cancelled') $cancelled++;
            elseif ($q['status'] === 'in_progress') $inProgress++;
            if (!empty($q['rating'])) $ratings[] = (int)$q['rating'];
        }
        $avgRating = count($ratings) > 0 ? round(array_sum($ratings) / count($ratings), 1) . ' / 5.0' : '-';

        $html = '<!DOCTYPE html>
<html lang="uz">
<head>
<meta charset="utf-8">
<style>
  @page { size: A4 landscape; margin: 10mm; }
  body { font-family: "DejaVu Sans", "Segoe UI", Arial, sans-serif; color: #1e293b; margin: 0; padding: 0; font-size: 11px; }
  .header { display: flex; justify-content: space-between; border-bottom: 2px solid #2563eb; padding-bottom: 10px; margin-bottom: 12px; }
  .header h1 { margin: 0; font-size: 18px; color: #1e3a8a; }
  .stats-grid { display: flex; gap: 8px; margin-bottom: 14px; }
  .stat-card { flex: 1; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 8px; text-align: center; }
  .stat-card .val { font-size: 16px; font-weight: bold; color: #0f172a; margin-top: 2px; }
  .stat-card .lbl { font-size: 9.5px; color: #64748b; text-transform: uppercase; font-weight: bold; }
  .stat-card.success .val { color: #16a34a; }
  .stat-card.warning .val { color: #d97706; }
  .stat-card.danger .val { color: #dc2626; }
  .stat-card.blue .val { color: #2563eb; }
  table { width: 100%; border-collapse: collapse; }
  th { background-color: #f1f5f9; color: #334155; font-weight: bold; text-align: left; padding: 7px 5px; border: 1px solid #cbd5e1; font-size: 9.5px; text-transform: uppercase; }
  td { padding: 6px 5px; border: 1px solid #e2e8f0; font-size: 10px; vertical-align: middle; }
  tr:nth-child(even) { background-color: #f8fafc; }
  .badge { display: inline-block; padding: 2px 5px; border-radius: 3px; font-size: 9px; font-weight: bold; }
  .badge-done { background: #dcfce7; color: #15803d; }
  .badge-pending { background: #fef9c3; color: #a16207; }
  .badge-progress { background: #e0e7ff; color: #4338ca; }
  .badge-cancelled { background: #fee2e2; color: #b91c1c; }
  .footer { margin-top: 15px; font-size: 9.5px; color: #94a3b8; text-align: center; border-top: 1px solid #e2e8f0; padding-top: 6px; }
</style>
</head>
<body>
<div class="header">
  <div>
    <h1>🏢 BUXGALTERIYA NAVBAT HISOBOTI</h1>
    <div style="font-size:10.5px; color:#64748b;">Elektron arizalar va xizmatlar monitoringi</div>
  </div>
  <div style="text-align: right;">
    <div style="font-weight: bold; color: #1e3a8a;">Sana: ' . $dateNow . '</div>
    <div style="font-size:10px; color:#64748b;">Avtomatik PDF hisobot</div>
  </div>
</div>
<div class="stats-grid">
  <div class="stat-card blue"><div class="lbl">Jami arizalar</div><div class="val">' . $total . ' ta</div></div>
  <div class="stat-card success"><div class="lbl">Bajarildi</div><div class="val">' . $done . ' ta</div></div>
  <div class="stat-card warning"><div class="lbl">Kutilmoqda / Jarayonda</div><div class="val">' . ($pending + $inProgress) . ' ta</div></div>
  <div class="stat-card danger"><div class="lbl">Bekor qilindi</div><div class="val">' . $cancelled . ' ta</div></div>
  <div class="stat-card"><div class="lbl">O‘rtacha baho</div><div class="val">' . $avgRating . '</div></div>
</div>
<table>
  <thead>
    <tr>
      <th style="width: 30px; text-align: center;">№</th>
      <th style="width: 70px; text-align: center;">Holat</th>
      <th>Xizmat turi</th>
      <th>Mijoz</th>
      <th>Telefon</th>
      <th>STIR / Izoh</th>
      <th>Mas‘ul xodim</th>
      <th style="width: 45px; text-align: center;">Baho</th>
      <th>Yaratilgan vaqt</th>
      <th>Bajarilgan vaqt</th>
    </tr>
  </thead>
  <tbody>';

        $statusBadgeMap = [
            'done'        => '<span class="badge badge-done">Bajarildi</span>',
            'pending'     => '<span class="badge badge-pending">Kutilmoqda</span>',
            'in_progress' => '<span class="badge badge-progress">Jarayonda</span>',
            'cancelled'   => '<span class="badge badge-cancelled">Bekor</span>'
        ];

        foreach (array_reverse($db['queue'] ?? []) as $item) {
            $servTitle = Lang::getServiceTitle($item['service'] ?? '', 'uz_lat');
            $stBadge = $statusBadgeMap[$item['status'] ?? ''] ?? $item['status'];
            $stars = !empty($item['rating']) ? str_repeat('★', (int)$item['rating']) : '-';
            $clientDesc = htmlspecialchars($item['name'] ?? '');
            if (!empty($item['username']) && $item['username'] !== 'Мавжуд эмас') {
                $clientDesc .= '<br><span style="color:#64748b; font-size:9px;">' . htmlspecialchars($item['username']) . '</span>';
            }
            $opDesc = htmlspecialchars($item['operator_name'] ?? '-');
            if (!empty($item['cancel_reason'])) {
                $opDesc .= '<br><span style="color:#b91c1c; font-size:9px;">' . htmlspecialchars($item['cancel_reason']) . '</span>';
            }

            $html .= '<tr>
      <td style="text-align: center; font-weight: bold;">#' . ($item['queue_number'] ?? '') . '</td>
      <td style="text-align: center;">' . $stBadge . '</td>
      <td><b>' . htmlspecialchars($servTitle) . '</b></td>
      <td>' . $clientDesc . '</td>
      <td><code>' . htmlspecialchars($item['phone'] ?? '') . '</code></td>
      <td>' . htmlspecialchars($item['comment'] ?? '-') . '</td>
      <td>' . $opDesc . '</td>
      <td style="text-align: center; color: #eab308; font-weight: bold;">' . $stars . '</td>
      <td><span style="font-size:9px; color:#475569;">' . ($item['created_at'] ?? '') . '</span></td>
      <td><span style="font-size:9px; color:#475569;">' . ($item['done_at'] ?? '-') . '</span></td>
    </tr>';
        }

        $html .= '  </tbody>
</table>
<div class="footer">Ushbu hisobot Telegram Buxgalteriya Navbat Boti (@ArzonUC_robot) tomonidan yaratildi.</div>
</body>
</html>';

        return $html;
    }

    private static function generatePurePhpPdf($db, $outputFile) {
        $dateNow = date('d.m.Y H:i');
        $lines = [];
        $lines[] = "BUXGALTERIYA ELEKTRON NAVBAT HISOBOTI";
        $lines[] = "Sana: " . $dateNow . " | Jami arizalar: " . count($db['queue'] ?? []);
        $lines[] = str_repeat("=", 80);
        $lines[] = sprintf("%-5s | %-12s | %-25s | %-15s | %-15s", "№", "Holat", "Xizmat turi", "Telefon", "Mas'ul xodim");
        $lines[] = str_repeat("-", 80);

        $statusMap = [
            'done'        => 'Bajarildi',
            'pending'     => 'Kutilmoqda',
            'in_progress' => 'Jarayonda',
            'cancelled'   => 'Bekor qilindi'
        ];

        foreach (array_reverse($db['queue'] ?? []) as $q) {
            $serv = substr(Lang::getServiceTitle($q['service'] ?? '', 'uz_lat'), 0, 24);
            $st = $statusMap[$q['status'] ?? ''] ?? $q['status'];
            $op = substr($q['operator_name'] ?? '-', 0, 14);
            $lines[] = sprintf("#%-4d | %-12s | %-25s | %-15s | %-15s", (int)($q['queue_number'] ?? 0), $st, $serv, $q['phone'] ?? '', $op);
        }
        $lines[] = str_repeat("=", 80);

        $buffer = "%PDF-1.4\n";
        $objCount = 0;
        $offsets = [];

        $addObj = function($content) use (&$buffer, &$objCount, &$offsets) {
            $objCount++;
            $offsets[$objCount] = strlen($buffer);
            $buffer .= "{$objCount} 0 obj\n" . $content . "\nendobj\n";
            return $objCount;
        };

        $fontObj = $addObj("<< /Type /Font /Subtype /Type1 /BaseFont /Courier >>");

        $streamContent = "BT /F1 9 Tf 35 560 Td 11 TL\n";
        foreach ($lines as $line) {
            $cleanLine = preg_replace('/[^\x20-\x7E]/', '', $line);
            $streamContent .= "(" . addcslashes($cleanLine, "()\\") . ") '\n";
        }
        $streamContent .= "ET";
        $streamLen = strlen($streamContent);

        $contentObj = $addObj("<< /Length {$streamLen} >>\nstream\n{$streamContent}\nendstream");
        $pageObj = $addObj("<< /Type /Page /Parent 3 0 R /MediaBox [0 0 842 595] /Contents {$contentObj} 0 R /Resources << /Font << /F1 {$fontObj} 0 R >> >> >>");
        $pagesObj = $addObj("<< /Type /Pages /Kids [{$pageObj} 0 R] /Count 1 >>");
        $catalogObj = $addObj("<< /Type /Catalog /Pages {$pagesObj} 0 R >>");

        $xrefOffset = strlen($buffer);
        $buffer .= "xref\n0 " . ($objCount + 1) . "\n0000000000 65535 f \n";
        for ($i = 1; $i <= $objCount; $i++) {
            $buffer .= sprintf("%010d 00000 n \n", $offsets[$i]);
        }
        $buffer .= "trailer\n<< /Size " . ($objCount + 1) . " /Root {$catalogObj} 0 R >>\nstartxref\n{$xrefOffset}\n%%EOF";

        file_put_contents($outputFile, $buffer);
        return $outputFile;
    }
}
