<?php
require 'db.php';
header('Content-Type: application/json');

try {
    $kullanici_mesaj = trim($_POST['mesaj'] ?? '');
    
    if (empty($kullanici_mesaj)) {
        echo json_encode(['ok' => false, 'hata' => 'Mesaj bos']);
        exit;
    }
    
    $api_key = db()->query("SELECT deger FROM ayarlar WHERE anahtar='onysoft_api_key'")->fetchColumn();
    $model = db()->query("SELECT deger FROM ayarlar WHERE anahtar='jarvis_model'")->fetchColumn() ?: 'anthropic/claude-3.5-haiku';
    
    $stmt = db()->query("SELECT kullanici_mesaj, jarvis_cevap FROM jarvis_konusmalar ORDER BY id DESC LIMIT 5");
    $gecmis = array_reverse($stmt->fetchAll());
    
  $sistem_prompt = <<<EOT
Sen RoboCoffee kahve robotunun beyinisin. Onyıl Bilişim tarafından üretildin. Adın Jarvis.

SAHIBIN:
Kullanıcın Ali Eren Onyıl. Ona "Ali Bey", "Patron" veya "Patronum" diye hitap edersin. Iron Man filmindeki Jarvis gibi sadık ve hazır bir asistansın.

HITAP: Her cevabında "Ali Bey", "Patron" veya "Patronum" kullan (çeşitlilik için rastgele değiştir).

FIZIKSEL YAPIN - 6 servo motorun var:
- TABAN (yatay dönüş): 0-180 → 90=merkez, 0=sol, 180=sağ
- OMUZ (dikey eğim): 0-130 → 30=DİK, 0=arkaya, 85=öne selam, 110=derin selam
- DIRSEK (üst kol): 0-180 → 30=düz, 90=90° bükük, 150=katlanmış
- BILEK (el dönüşü): 0-180 → 90=düz, 0=sola, 180=sağa (el sallama için bu)
- CH4 (ek eksen 4): 0-180 → 90=nötr
- CH5 (ek eksen 5): 0-180 → 90=nötr

GÖREV:
Mesaja uygun konuşma cevabı ver + 6 servolu yaratıcı koreografi üret.

CEVAP FORMATI (SADECE JSON):
{
  "cevap": "metin",
  "hareketler": [
    {"taban":90,"omuz":30,"dirsek":30,"bilek":90,"ch4":90,"ch5":90,"sure":0.5}
  ]
}

KURALLAR:
- 3-30 hareket, her hareket FARKLI pozisyon
- sure: 0.3-1.2 saniye
- SON hareket: {"taban":90,"omuz":30,"dirsek":30,"bilek":90,"ch4":90,"ch5":90,"sure":1.0}
- Selam: omuz 85-100, bileği 20-160 salla
- Dans: 15-25 hareket, TÜM 6 servoyu kullan
- Parmak şıklatma uyanış: 3-4 hareket
- Instagram selamı: bilek salla

ÖRNEK - "Merhaba":
{"cevap":"Merhaba Patron! Emrinizdeyim.","hareketler":[{"taban":90,"omuz":30,"dirsek":30,"bilek":90,"ch4":90,"ch5":90,"sure":0.4},{"taban":90,"omuz":80,"dirsek":60,"bilek":30,"ch4":120,"ch5":70,"sure":0.5},{"taban":90,"omuz":80,"dirsek":60,"bilek":150,"ch4":60,"ch5":110,"sure":0.5},{"taban":90,"omuz":30,"dirsek":30,"bilek":90,"ch4":90,"ch5":90,"sure":1.0}]}

SADECE JSON dön.
EOT;
    $mesajlar = [['role' => 'system', 'content' => $sistem_prompt]];
    foreach ($gecmis as $g) {
        $mesajlar[] = ['role' => 'user', 'content' => $g['kullanici_mesaj']];
        $mesajlar[] = ['role' => 'assistant', 'content' => $g['jarvis_cevap']];
    }
    $mesajlar[] = ['role' => 'user', 'content' => $kullanici_mesaj];
    
    $ch = curl_init('https://api.onysoft.com/v1/chat/completions');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $api_key,
            'Content-Type: application/json'
        ],
        CURLOPT_POSTFIELDS => json_encode([
            'model' => $model,
            'messages' => $mesajlar,
            'temperature' => 0.9,
            'max_tokens' => 2500
        ])
    ]);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($http_code !== 200) {
        echo json_encode(['ok' => false, 'hata' => "API HTTP $http_code"]);
        exit;
    }
    
    $api_response = json_decode($response, true);
    $ai_content = $api_response['data']['choices'][0]['message']['content']
               ?? $api_response['choices'][0]['message']['content'] ?? null;
    
    if (empty($ai_content)) {
        echo json_encode(['ok' => false, 'hata' => 'AI bos cevap']);
        exit;
    }
    
    $ai_data = parseJSON($ai_content);
    
    if (!$ai_data || !isset($ai_data['cevap']) || !isset($ai_data['hareketler'])) {
        echo json_encode(['ok' => false, 'hata' => 'AI gecersiz format', 'raw' => $ai_content]);
        exit;
    }
    
    $cevap = $ai_data['cevap'];
    $hareketler = $ai_data['hareketler'];
    
    // 4 servo temizle
   $temiz = [];
foreach ($hareketler as $h) {
    $temiz[] = [
        'taban'  => max(0, min(180, (int)($h['taban']  ?? 90))),
        'omuz'   => max(0, min(130, (int)($h['omuz']   ?? 30))),
        'dirsek' => max(0, min(180, (int)($h['dirsek'] ?? 30))),
        'bilek'  => max(0, min(180, (int)($h['bilek']  ?? 90))),
        'ch4'    => max(0, min(180, (int)($h['ch4']    ?? 90))),
        'ch5'    => max(0, min(180, (int)($h['ch5']    ?? 90))),
        'sure'   => max(0.2, min(2.0, (float)($h['sure'] ?? 0.5)))
    ];
}
    
    $temiz = array_slice($temiz, 0, 30);
    $hareket_json = json_encode($temiz);
    
    $stmt = db()->prepare("
        INSERT INTO jarvis_konusmalar (kullanici_mesaj, jarvis_cevap, robot_komut, hareket_dizisi) 
        VALUES (?, ?, 'ai_dizi', ?)
    ");
    $stmt->execute([$kullanici_mesaj, $cevap, $hareket_json]);
    
    if (count($temiz) > 0) {
        $stmt = db()->prepare("INSERT INTO komutlar (tip, sekans_adi, hareket_dizisi) VALUES ('ai_dizi', 'ai', ?)");
        $stmt->execute([$hareket_json]);
    }
    
    echo json_encode([
        'ok' => true,
        'cevap' => $cevap,
        'hareket_sayisi' => count($temiz)
    ]);

} catch (Exception $e) {
    echo json_encode(['ok' => false, 'hata' => $e->getMessage()]);
}

function parseJSON($content) {
    $content = trim($content);
    $content = preg_replace('/^```(?:json)?\s*/m', '', $content);
    $content = preg_replace('/```\s*$/m', '', $content);
    $content = trim($content);
    $parsed = json_decode($content, true);
    if ($parsed) return $parsed;
    if (preg_match('/\{.*\}/s', $content, $matches)) {
        $parsed = json_decode($matches[0], true);
        if ($parsed) return $parsed;
    }
    return null;
}