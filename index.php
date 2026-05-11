<?php
// Tanitim sayfasi - kimlik dogrulama gerekmiyor
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Robot Kol - Kahve Bardağı Taşıyan Robot Kolu</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, 'Segoe UI', Arial, sans-serif;
            background: radial-gradient(ellipse at top, #0c1e3a 0%, #050a1a 70%);
            min-height: 100vh; padding: 20px; color: white; line-height: 1.6;
        }
        .container { max-width: 720px; margin: 0 auto; }
        .hero {
            text-align: center; padding: 40px 20px 30px;
        }
        .logo { font-size: 4em; margin-bottom: 10px; }
        h1 {
            font-size: 2.4em; margin-bottom: 8px;
            background: linear-gradient(90deg, #60a5fa, #22d3ee, #a78bfa);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .alt { opacity: 0.7; font-size: 1em; margin-bottom: 24px; }
        .btn-grup { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }
        .btn {
            padding: 14px 24px;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            color: white; border: none; border-radius: 10px;
            font-size: 1em; font-weight: 700; cursor: pointer;
            text-decoration: none; display: inline-block;
            transition: transform 0.1s;
        }
        .btn:active { transform: scale(0.97); }
        .btn.ghost {
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.15);
        }
        .kart {
            background: rgba(255,255,255,0.05);
            backdrop-filter: blur(20px);
            border-radius: 16px;
            padding: 22px 24px;
            margin-bottom: 16px;
            border: 1px solid rgba(255,255,255,0.1);
        }
        h2 { font-size: 1.2em; margin-bottom: 12px; color: #93c5fd; }
        .ozellikler { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        .ozellik {
            background: rgba(0,0,0,0.25);
            padding: 12px; border-radius: 10px;
            font-size: 0.9em;
        }
        .ozellik strong { color: #a78bfa; display: block; margin-bottom: 4px; }
        ul { padding-left: 20px; font-size: 0.92em; opacity: 0.9; }
        ul li { margin-bottom: 6px; }
        .ekip {
            display: grid; grid-template-columns: 1fr 1fr; gap: 8px;
            font-size: 0.88em;
        }
        .ekip > div {
            background: rgba(0,0,0,0.25);
            padding: 8px 12px; border-radius: 8px;
        }
        .ekip span { display: block; opacity: 0.6; font-size: 0.78em; }
        footer {
            text-align: center; padding: 30px 0 10px;
            opacity: 0.5; font-size: 0.8em;
        }
        @media (max-width: 480px) {
            .ozellikler, .ekip { grid-template-columns: 1fr; }
            h1 { font-size: 1.8em; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="hero">
            <div class="logo">🦾</div>
            <h1>Robot Kol</h1>
            <div class="alt">Kahve Bardağı Taşıyan Web Kontrollü Robot Kolu</div>
            <div class="btn-grup">
                <a class="btn" href="login.php">Kontrol Paneline Giriş</a>
            </div>
        </div>

        <div class="kart">
            <h2>📌 Proje Hakkında</h2>
            <p>
                Robot Kol, Raspberry Pi 4 üzerinde çalışan ve web arayüzü üzerinden
                uzaktan kontrol edilen 6 servolu bir robot kol sistemidir. Yetkili
                kullanıcılar (yönetici / operatör) tarayıcıdan kola komut göndererek
                bir kahve bardağını mevcut konumundan başka bir konuma taşıyabilir.
            </p>
        </div>

        <div class="kart">
            <h2>⚙️ Sistem Özellikleri</h2>
            <div class="ozellikler">
                <div class="ozellik"><strong>Donanım</strong>Raspberry Pi 4 + PCA9685</div>
                <div class="ozellik"><strong>Servolar</strong>2× MG995 + 4× SG90</div>
                <div class="ozellik"><strong>Backend</strong>PHP + MySQL</div>
                <div class="ozellik"><strong>Pi ↔ Sunucu</strong>HTTP polling (1 sn)</div>
                <div class="ozellik"><strong>Roller</strong>Yönetici / Operatör</div>
                <div class="ozellik"><strong>Hosting</strong>Onyıl Bilişim</div>
            </div>
        </div>

        <div class="kart">
            <h2>🎯 Yapılabilenler</h2>
            <ul>
                <li>Her eklemin açısı 0–180° aralığında slider ile manuel ayarlanır</li>
                <li>Kahve bardağı taşıma için hazır test pozisyonları gönderilir</li>
                <li>Robot durumu (canlı / offline / kuyruk) anlık izlenir</li>
                <li>Tüm komutlar veritabanında durum ve zaman damgaları ile loglanır</li>
                <li>Hatalar admin paneline yansır ve kayıt altına alınır</li>
            </ul>
        </div>

        <div class="kart">
            <h2>👥 Proje Ekibi</h2>
            <div class="ekip">
                <div>Ali Eren Onyıl<span>20242425034</span></div>
                <div>Murat Ege Ertürk<span>20242425020</span></div>
                <div>Arda Savaş<span>20242425039</span></div>
                <div>Muhammed Burak Arıkan<span>20242425003</span></div>
            </div>
        </div>

        <div class="kart">
            <h2>🎓 Ders Bilgisi</h2>
            <p style="font-size:0.9em">
                İzmir Ekonomi Üniversitesi - Bilgisayar Programcılığı<br>
                Sistem Analizi ve Tasarımı - Grup Ödevi
            </p>
        </div>

        <footer>
            © 2026 Robot Kol — Onyıl Bilişim ortaklığında geliştirilmiştir
        </footer>
    </div>
</body>
</html>
