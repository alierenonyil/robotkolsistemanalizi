<?php
require 'db.php';

$metin = $_GET['metin'] ?? '';

if (empty($metin)) {
    http_response_code(400);
    exit('Metin bos');
}

// ============ TURKCE TELAFFUZ DUZELTICI ============
function turkceTelaffuz($metin) {
    // Ingilizce kelimeleri Turkce okumaya zorla
    $ceviri = [
        // Sosyal medya
        '/\bInstagram\b/i'  => 'İnstagram',
        '/\bFacebook\b/i'   => 'Feysbuk',
        '/\bTwitter\b/i'    => 'Tuitır',
        '/\bYouTube\b/i'    => 'Yutup',
        '/\bTikTok\b/i'     => 'Tiktok',
        '/\bWhatsApp\b/i'   => 'Vatsap',
        '/\bLinkedIn\b/i'   => 'Linkedin',
        '/\bTelegram\b/i'   => 'Telegram',
        '/\bSnapchat\b/i'   => 'Snepçet',
        
        // Teknoloji
        '/\bGoogle\b/i'     => 'Gugıl',
        '/\bApple\b/i'      => 'Epıl',
        '/\bMicrosoft\b/i'  => 'Maykrosoft',
        '/\bAndroid\b/i'    => 'Endroyd',
        '/\bRaspberry\b/i'  => 'Razberi',
        '/\bArduino\b/i'    => 'Arduino',
        '/\bGitHub\b/i'     => 'Githab',
        '/\bPython\b/i'     => 'Paytın',
        '/\bJavaScript\b/i' => 'Cavaskript',
        '/\bPHP\b/'         => 'Pe He Pe',
        '/\bSQL\b/i'        => 'Sikuel',
        '/\bAPI\b/'         => 'Ey Pi Ay',
        '/\bURL\b/'         => 'Yu Ar El',
        '/\bHTML\b/'        => 'Eyç Ti Em El',
        '/\bCSS\b/'         => 'Si Es Es',
        '/\bUSB\b/'         => 'Yu Es Bi',
        '/\bAI\b/'          => 'Yapay zeka',
        '/\bGPT\b/'         => 'Ci Pi Ti',
        '/\bIoT\b/i'        => 'Ay O Ti',
        '/\bWi-Fi\b/i'      => 'Vay Fay',
        '/\bWifi\b/i'       => 'Vay Fay',
        '/\bPC\b/'          => 'Pi Si',
        '/\bCPU\b/'         => 'Si Pi Yu',
        '/\bGPU\b/'         => 'Ci Pi Yu',
        '/\bRAM\b/'         => 'Ram',
        
        // Kullanici adi / proje
        '/\bJarvis\b/i'     => 'Carvis',
        '/\bRoboCoffee\b/i' => 'Robo kofi',
        
        // Oneli durumlar - biraz boshluk ekle ki dogru okunsun
        '/\bOnyil\b/i'      => 'On yıl',
        '/\bOnySoft\b/i'    => 'On y soft',
    ];
    
    foreach ($ceviri as $pattern => $replacement) {
        $metin = preg_replace($pattern, $replacement, $metin);
    }
    
    return $metin;
}

$metin_temiz = turkceTelaffuz($metin);

$api_key = db()->query("SELECT deger FROM ayarlar WHERE anahtar='onysoft_api_key'")->fetchColumn();
$tts_model = db()->query("SELECT deger FROM ayarlar WHERE anahtar='tts_model'")->fetchColumn() ?: 'elevenlabs/text-to-speech-turbo-2-5';
$voice_id = db()->query("SELECT deger FROM ayarlar WHERE anahtar='tts_voice'")->fetchColumn() ?: 'XrExE9yKIg1WjnnlVkGX';

// ============ ELEVENLABS TTS ============
$ch = curl_init('https://api.onysoft.com/v1/audio/speech');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ' . $api_key,
        'Content-Type: application/json'
    ],
    CURLOPT_POSTFIELDS => json_encode([
        'model' => $tts_model,
        'input' => $metin_temiz,
        'voice' => $voice_id,
        'response_format' => 'mp3'
    ])
]);

$audio = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Eger ElevenLabs basarisiz olursa OpenAI TTS'e fallback
if ($http_code !== 200 || strlen($audio) < 500) {
    $ch = curl_init('https://api.onysoft.com/v1/audio/speech');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $api_key,
            'Content-Type: application/json'
        ],
        CURLOPT_POSTFIELDS => json_encode([
            'model' => 'tts-1',
            'input' => $metin_temiz,
            'voice' => 'nova',
            'response_format' => 'mp3'
        ])
    ]);
    $audio = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
}

if ($http_code !== 200 || strlen($audio) < 500) {
    http_response_code(204);
    exit;
}

header('Content-Type: audio/mpeg');
header('Content-Length: ' . strlen($audio));
header('Cache-Control: public, max-age=3600');
echo $audio;