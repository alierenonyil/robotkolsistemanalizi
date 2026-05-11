<?php
require 'db.php';
header('Content-Type: application/json');

$action = $_REQUEST['action'] ?? '';

try {
 if ($action === 'hareket') {
    $taban  = max(0, min(180, (int)($_POST['taban']  ?? 90)));
    $omuz   = max(0, min(130, (int)($_POST['omuz']   ?? 30)));
    $dirsek = max(0, min(180, (int)($_POST['dirsek'] ?? 30)));
    $bilek  = max(0, min(180, (int)($_POST['bilek']  ?? 90)));
    $ch4    = max(0, min(180, (int)($_POST['ch4']    ?? 90)));
    $ch5    = max(0, min(180, (int)($_POST['ch5']    ?? 90)));
    
    $stmt = db()->prepare("
        INSERT INTO komutlar (tip, taban_aci, omuz_aci, dirsek_aci, bilek_aci, ch4_aci, ch5_aci) 
        VALUES ('hareket', ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$taban, $omuz, $dirsek, $bilek, $ch4, $ch5]);
        
        echo json_encode(['ok' => true, 'id' => db()->lastInsertId()]);
        exit;
    }
    
    // Durum
    if ($action === 'durum') {
        $poz = db()->query("SELECT * FROM servo_pozisyon WHERE id=1")->fetch();
        $pi = db()->query("SELECT *, TIMESTAMPDIFF(SECOND, son_ping, NOW()) AS gecen FROM pi_durum WHERE id=1")->fetch();
        $bekleyen = db()->query("SELECT COUNT(*) FROM komutlar WHERE durum='bekliyor'")->fetchColumn();
        
        echo json_encode([
            'ok' => true,
            'pozisyon' => $poz,
            'pi_baglanti' => ($pi['gecen'] !== null && $pi['gecen'] < 5) ? 'canli' : 'offline',
            'bekleyen_komut' => (int)$bekleyen
        ]);
        exit;
    }
    
    // Pi bekleyen komutlari cekiyor
    if ($action === 'pi_cek') {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        db()->prepare("UPDATE pi_durum SET son_ping=NOW(), ip_adresi=? WHERE id=1")->execute([$ip]);
        
       $stmt = db()->query("
    SELECT id, tip, taban_aci, omuz_aci, dirsek_aci, bilek_aci, ch4_aci, ch5_aci, sekans_adi, hareket_dizisi
    FROM komutlar 
    WHERE durum='bekliyor' 
    ORDER BY id ASC 
    LIMIT 10
");
        $komutlar = $stmt->fetchAll();
        
        echo json_encode(['ok' => true, 'komutlar' => $komutlar]);
        exit;
    }
    
    // Pi komutu tamamladı
    if ($action === 'pi_tamamla') {
        $id = (int)($_POST['id'] ?? 0);
        $basarili = ($_POST['basarili'] ?? '1') === '1';
        $hata = $_POST['hata'] ?? null;
        $taban = (int)($_POST['taban'] ?? 90);
        $omuz = (int)($_POST['omuz'] ?? 30);
        
        db()->prepare("UPDATE komutlar SET durum=?, hata_mesaji=?, calisma=NOW() WHERE id=?")
            ->execute([$basarili ? 'calisti' : 'hata', $hata, $id]);
        
        if ($basarili) {
            db()->prepare("UPDATE servo_pozisyon SET taban_aci=?, omuz_aci=? WHERE id=1")
                ->execute([$taban, $omuz]);
        }
        
        echo json_encode(['ok' => true]);
        exit;
    }
    
    echo json_encode(['ok' => false, 'hata' => 'Gecersiz action']);

} catch (Exception $e) {
    echo json_encode(['ok' => false, 'hata' => $e->getMessage()]);
}