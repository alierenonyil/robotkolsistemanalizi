<?php
require_once __DIR__ . '/auth.php';
checkAuth();

// Cikis istegi
if (isset($_GET['cikis'])) {
    logout();
}

$kullanici = currentUser();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Robot Kol - Kontrol Paneli</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, 'Segoe UI', Arial, sans-serif;
            background: radial-gradient(ellipse at top, #0c1e3a 0%, #050a1a 70%);
            min-height: 100vh; padding: 20px; color: white;
        }
        .container { max-width: 500px; margin: 0 auto; }
        h1 {
            text-align: center; margin-bottom: 4px; font-size: 2em;
            background: linear-gradient(90deg, #60a5fa, #22d3ee, #a78bfa);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .subtitle { text-align: center; opacity: 0.6; margin-bottom: 12px; font-size: 0.85em; }

        .ust-bar {
            display: flex; justify-content: space-between; align-items: center;
            padding: 8px 14px; margin-bottom: 14px;
            background: rgba(0,0,0,0.3); border-radius: 10px;
            font-size: 0.8em;
        }
        .ust-bar .kullanici-bilgi { opacity: 0.85; }
        .ust-bar .rol-rozet {
            display: inline-block; padding: 2px 8px;
            background: rgba(168,85,247,0.4); border-radius: 4px;
            font-size: 0.78em; margin-left: 6px;
        }
        .ust-bar .rol-rozet.admin { background: rgba(239,68,68,0.4); }
        .cikis-btn {
            background: rgba(239,68,68,0.2); color: #fca5a5;
            border: 1px solid rgba(239,68,68,0.3); border-radius: 6px;
            padding: 4px 10px; font-size: 0.8em; cursor: pointer;
            text-decoration: none;
        }
        .cikis-btn:hover { background: rgba(239,68,68,0.3); }

        .kart {
            background: rgba(255,255,255,0.05);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 20px;
            margin-bottom: 16px;
            border: 1px solid rgba(255,255,255,0.1);
        }

        .jarvis-kart { text-align: center; padding: 30px 20px; }
        .jarvis-daire {
            width: 160px; height: 160px; border-radius: 50%;
            margin: 0 auto 20px;
            background: radial-gradient(circle, #1e40af, #0c1e3a);
            border: 3px solid #3b82f6;
            display: flex; align-items: center; justify-content: center;
            font-size: 3em;
            transition: all 0.3s;
        }
        .jarvis-daire.dinliyor {
            border-color: #ef4444;
            box-shadow: 0 0 40px #ef4444, 0 0 80px rgba(239,68,68,0.4);
            animation: pulse 1s infinite;
        }
        .jarvis-daire.dusunuyor {
            border-color: #fbbf24;
            box-shadow: 0 0 40px #fbbf24;
            animation: donme 2s infinite linear;
        }
        .jarvis-daire.konusuyor {
            border-color: #10b981;
            box-shadow: 0 0 40px #10b981;
            animation: pulse 0.6s infinite;
        }
        .jarvis-daire.uyaniyor {
            border-color: #a78bfa;
            box-shadow: 0 0 60px #a78bfa;
            animation: uyan 0.5s ease-out;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        @keyframes donme { 100% { transform: rotate(360deg); } }
        @keyframes uyan {
            0% { transform: scale(0.8); }
            50% { transform: scale(1.15); }
            100% { transform: scale(1); }
        }

        .durum-yazi { font-size: 1.1em; margin-bottom: 8px; font-weight: 600; }
        .alt-durum { opacity: 0.7; font-size: 0.85em; min-height: 20px; }

        .btn-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 16px; }
        .btn {
            padding: 14px; background: rgba(255,255,255,0.1);
            color: white; border: none; border-radius: 10px;
            font-size: 0.95em; font-weight: 600;
            cursor: pointer; transition: all 0.2s;
        }
        .btn:active { transform: scale(0.97); }
        .btn.aktif {
            background: linear-gradient(135deg, #ef4444, #f97316);
            animation: pulse 1s infinite;
        }
        .btn-primary {
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            grid-column: span 2;
            padding: 18px; font-size: 1.1em; font-weight: 700;
        }

        .sohbet { max-height: 280px; overflow-y: auto; padding-right: 8px; }
        .mesaj {
            padding: 10px 14px; border-radius: 14px; margin-bottom: 8px;
            max-width: 85%; word-wrap: break-word; font-size: 0.9em;
            animation: fadeIn 0.3s;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .mesaj.kullanici {
            background: rgba(59,130,246,0.3);
            margin-left: auto;
            border-bottom-right-radius: 4px;
        }
        .mesaj.jarvis {
            background: rgba(255,255,255,0.1);
            border-bottom-left-radius: 4px;
        }
        .mesaj.sistem {
            background: rgba(168,85,247,0.2);
            text-align: center;
            margin: 0 auto;
            font-size: 0.8em;
            font-style: italic;
        }
        .hareket-sayi {
            display: inline-block; margin-top: 4px;
            padding: 2px 8px; background: rgba(168,85,247,0.4);
            border-radius: 4px; font-size: 0.7em;
        }

        .durum-bari {
            display: flex; justify-content: space-around;
            padding: 10px; background: rgba(0,0,0,0.3);
            border-radius: 10px; font-size: 0.75em;
        }
        .durum-bari > div { text-align: center; }
        .durum-deger { font-weight: 700; color: #60a5fa; }
        .pi-nokta {
            display: inline-block; width: 8px; height: 8px;
            border-radius: 50%; margin-right: 4px;
        }
        .pi-nokta.canli { background: #10b981; animation: yanip 2s infinite; }
        .pi-nokta.offline { background: #ef4444; }
        @keyframes yanip { 50% { opacity: 0.3; } }

        .hotword-badge {
            display: inline-block; padding: 4px 10px;
            background: rgba(168,85,247,0.3);
            border-radius: 12px; font-size: 0.75em;
            margin-top: 8px;
        }
        .hotword-badge.aktif {
            background: #a78bfa;
            color: #1e1b4b;
            animation: yanip 1s infinite;
        }

        .toggle-bar {
            padding: 10px; text-align: center;
            background: rgba(0,0,0,0.3); border-radius: 8px;
            cursor: pointer; font-size: 0.85em; margin-bottom: 10px;
        }
        .icerik-gizli { display: none; }
        .icerik-gizli.acik { display: block; }
        .slider-grup { margin-bottom: 12px; }
        label { display: flex; justify-content: space-between; margin-bottom: 6px; font-size: 0.85em; }
        input[type=range] {
            width: 100%; height: 6px; border-radius: 3px;
            background: rgba(255,255,255,0.2); outline: none;
            -webkit-appearance: none;
        }
        input[type=range]::-webkit-slider-thumb {
            -webkit-appearance: none; width: 20px; height: 20px;
            border-radius: 50%; background: #3b82f6; cursor: pointer;
        }

        /* Bardak test butonlari */
        .bardak-grid {
            display: grid; grid-template-columns: 1fr 1fr; gap: 8px;
        }
        .bardak-btn {
            padding: 14px 10px; background: rgba(34,211,238,0.12);
            border: 1px solid rgba(34,211,238,0.3);
            color: white; border-radius: 10px;
            font-size: 0.85em; font-weight: 600;
            cursor: pointer; transition: all 0.15s;
            text-align: left; line-height: 1.3;
        }
        .bardak-btn:hover { background: rgba(34,211,238,0.22); }
        .bardak-btn:active { transform: scale(0.97); }
        .bardak-btn .adim {
            display: block; opacity: 0.6; font-size: 0.7em;
            margin-bottom: 2px; letter-spacing: 0.5px;
        }
        .bardak-btn.eve-don {
            grid-column: span 2;
            background: rgba(168,85,247,0.15);
            border-color: rgba(168,85,247,0.4);
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Ust bar: kullanici + cikis -->
        <div class="ust-bar">
            <div class="kullanici-bilgi">
                👤 <?= htmlspecialchars($kullanici['ad_soyad'] ?: $kullanici['kullanici_adi'], ENT_QUOTES, 'UTF-8') ?>
                <span class="rol-rozet <?= $kullanici['rol'] === 'admin' ? 'admin' : '' ?>">
                    <?= $kullanici['rol'] === 'admin' ? 'Yönetici' : 'Operatör' ?>
                </span>
            </div>
            <a class="cikis-btn" href="?cikis=1">Çıkış</a>
        </div>

        <h1>🦾 Robot Kol</h1>
        <div class="subtitle">Kontrol Paneli - Jarvis Modu</div>

        <!-- Jarvis merkez -->
        <div class="kart jarvis-kart">
            <div class="jarvis-daire" id="jarvisDaire">🤖</div>
            <div class="durum-yazi" id="durumYazi">Başlatmak için butona basın</div>
            <div class="alt-durum" id="altDurum"></div>
            <div id="hotwordBadge" style="display:none" class="hotword-badge">
                👂 Uyandırıcı dinleniyor: "Jarvis"
            </div>

            <div class="btn-grid">
                <button class="btn btn-primary" id="btnSurekli" onclick="toggleSurekli()">
                    🎤 Sürekli Dinlemeyi Başlat
                </button>
                <button class="btn" id="btnTek" onclick="tekSeferDinle()">
                    🎙 Tek Seferlik
                </button>
                <button class="btn" onclick="konusmayiDurdur()">⏹ Sustur</button>
            </div>
        </div>

        <!-- Sohbet -->
        <div class="kart">
            <h3 style="margin-bottom:12px">💬 Sohbet</h3>
            <div class="sohbet" id="sohbet">
                <div class="mesaj sistem">
                    "Sürekli Dinleme" butonuna bas → sonra konuş.<br>
                    Uyandırıcı kelime: <b>"Jarvis"</b>
                </div>
            </div>
        </div>

        <!-- Pi durum -->
        <div class="kart" style="padding:12px">
            <div class="durum-bari">
                <div>
                    <span class="pi-nokta offline" id="piNokta"></span>
                    <span id="piDurum">Pi: offline</span>
                </div>
                <div>Pozisyon: <span class="durum-deger" id="mevcutPoz">—</span></div>
                <div>Kuyruk: <span class="durum-deger" id="bekleyenKomut">0</span></div>
            </div>
        </div>

        <!-- BARDAK TASIMA TEST HAREKETLERI (FR-04) -->
        <div class="kart" style="padding:12px">
            <div class="toggle-bar" onclick="document.getElementById('bardakIcerik').classList.toggle('acik')">
                ☕ Kahve Bardağı Taşıma - Test Hareketleri (FR-04)
            </div>
            <div class="icerik-gizli acik" id="bardakIcerik">
                <div class="bardak-grid">
                    <button class="bardak-btn" onclick="bardakAdim('uzan')">
                        <span class="adim">ADIM 1</span>🤲 Bardağa Uzan
                    </button>
                    <button class="bardak-btn" onclick="bardakAdim('kavra')">
                        <span class="adim">ADIM 2</span>✊ Bardağı Kavra
                    </button>
                    <button class="bardak-btn" onclick="bardakAdim('kaldir')">
                        <span class="adim">ADIM 3</span>⬆️ Bardağı Kaldır
                    </button>
                    <button class="bardak-btn" onclick="bardakAdim('tasi')">
                        <span class="adim">ADIM 4</span>↪️ Hedefe Taşı
                    </button>
                    <button class="bardak-btn" onclick="bardakAdim('indir')">
                        <span class="adim">ADIM 5</span>⬇️ Bardağı İndir
                    </button>
                    <button class="bardak-btn" onclick="bardakAdim('birak')">
                        <span class="adim">ADIM 6</span>🖐 Bardağı Bırak
                    </button>
                    <button class="bardak-btn eve-don" onclick="bardakAdim('eve_don')">
                        🏠 Başlangıç Pozisyonuna Dön
                    </button>
                </div>
            </div>
        </div>

        <!-- Manuel kontrol (gizli) -->
        <div class="kart" style="padding:12px">
            <div class="toggle-bar" onclick="document.getElementById('manuelIcerik').classList.toggle('acik')">
                ⚙️ Manuel Kontrol (6 servo)
            </div>
            <div class="icerik-gizli" id="manuelIcerik">
                <div class="slider-grup">
                    <label>Taban: <span id="tabanDeger">90°</span></label>
                    <input type="range" id="taban" min="0" max="180" value="90">
                </div>
                <div class="slider-grup">
                    <label>Omuz: <span id="omuzDeger">30°</span></label>
                    <input type="range" id="omuz" min="0" max="130" value="30">
                </div>
                <div class="slider-grup">
                    <label>Dirsek: <span id="dirsekDeger">30°</span></label>
                    <input type="range" id="dirsek" min="0" max="180" value="30">
                </div>
                <div class="slider-grup">
                    <label>Bilek: <span id="bilekDeger">90°</span></label>
                    <input type="range" id="bilek" min="0" max="180" value="90">
                </div>
                <div class="slider-grup">
                    <label>CH4 (ekstra): <span id="ch4Deger">90°</span></label>
                    <input type="range" id="ch4" min="0" max="180" value="90">
                </div>
                <div class="slider-grup">
                    <label>CH5 (ekstra): <span id="ch5Deger">90°</span></label>
                    <input type="range" id="ch5" min="0" max="180" value="90">
                </div>
                <button class="btn btn-primary" onclick="hareketGonder()" style="grid-column:auto">Gönder</button>
            </div>
        </div>
    </div>

<script>
const $ = id => document.getElementById(id);

// ============ MANUEL SLIDER'LAR ============
['taban','omuz','dirsek','bilek','ch4','ch5'].forEach(id => {
    $(id).oninput = e => $(id+'Deger').textContent = e.target.value + '°';
});

async function hareketGonder() {
    const params = new URLSearchParams({
        action: 'hareket',
        taban: $('taban').value,
        omuz: $('omuz').value,
        dirsek: $('dirsek').value,
        bilek: $('bilek').value,
        ch4: $('ch4').value,
        ch5: $('ch5').value
    });
    await fetch('api.php', {method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body: params.toString()});
}

// ============ BARDAK TASIMA TEST POZISYONLARI (FR-04) ============
// CH4/CH5 dar guvenli aralik (80-100): 80=acik gripper, 100=kapali (varsayim, kalibrasyonla netlesir)
const BARDAK_POZ = {
    eve_don: { taban: 90, omuz: 30, dirsek: 30,  bilek: 90, ch4: 90, ch5: 90,  ad: 'Eve dönülüyor' },
    uzan:    { taban: 90, omuz: 85, dirsek: 120, bilek: 90, ch4: 90, ch5: 80,  ad: 'Bardağa uzanıyor (gripper açık)' },
    kavra:   { taban: 90, omuz: 85, dirsek: 120, bilek: 90, ch4: 90, ch5: 100, ad: 'Bardak kavranıyor' },
    kaldir:  { taban: 90, omuz: 50, dirsek: 80,  bilek: 90, ch4: 90, ch5: 100, ad: 'Bardak kaldırılıyor' },
    tasi:    { taban: 30, omuz: 50, dirsek: 80,  bilek: 90, ch4: 90, ch5: 100, ad: 'Bardak hedefe taşınıyor' },
    indir:   { taban: 30, omuz: 85, dirsek: 120, bilek: 90, ch4: 90, ch5: 100, ad: 'Bardak indiriliyor' },
    birak:   { taban: 30, omuz: 85, dirsek: 120, bilek: 90, ch4: 90, ch5: 80,  ad: 'Bardak bırakılıyor (gripper açık)' },
};

async function bardakAdim(anahtar) {
    const p = BARDAK_POZ[anahtar];
    if (!p) return;

    // Slider'lari da senkronla (gorsel geri bildirim)
    ['taban','omuz','dirsek','bilek','ch4','ch5'].forEach(k => {
        $(k).value = p[k];
        $(k+'Deger').textContent = p[k] + '°';
    });

    const params = new URLSearchParams({
        action: 'hareket',
        taban: p.taban, omuz: p.omuz, dirsek: p.dirsek,
        bilek: p.bilek, ch4: p.ch4, ch5: p.ch5
    });

    try {
        const r = await fetch('api.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: params.toString()
        });
        const d = await r.json();
        if (d.ok) {
            mesajEkle(`☕ ${p.ad}`, 'sistem');
        } else {
            mesajEkle('Hata: ' + (d.hata || 'bilinmeyen'), 'sistem');
        }
    } catch (e) {
        mesajEkle('Bağlantı hatası', 'sistem');
    }
}

// ============ KONUŞMA TANIMA ============
let tanima = null;
let surekliMod = false;
let tekSeferMod = false;
let isleniyor = false;
let hotwordBekleniyor = false;  // true=uyandirici bekle, false=mesaj dinle
let dinlemeAktif = false;
let birikenMetin = '';
let sessizlikTimer = null;
let aktivlesmeZamani = 0;

const HOTWORD_REGEX = /(jarvis|cervis|carvis|cervıs|çarvıs|çervis)/i;
const MESAJ_BEKLEME_SURESI = 15000;  // 15 saniye
const SESSIZLIK_SURESI = 1800;       // 1.8 saniye

const SR = window.SpeechRecognition || window.webkitSpeechRecognition;
if (SR) {
    tanima = new SR();
    tanima.lang = 'tr-TR';
    tanima.continuous = true;
    tanima.interimResults = true;

    tanima.onstart = () => {
        dinlemeAktif = true;
        console.log('STT basladi. hotword:', hotwordBekleniyor);
    };

    tanima.onresult = e => {
        if (isleniyor) return;

        let final = '', ara = '';
        for (let i = e.resultIndex; i < e.results.length; i++) {
            if (e.results[i].isFinal) final += e.results[i][0].transcript;
            else ara += e.results[i][0].transcript;
        }

        const tumMetin = (final + ara).trim();

        // --------- HOTWORD MODU ---------
        if (surekliMod && hotwordBekleniyor) {
            if (HOTWORD_REGEX.test(tumMetin)) {
                console.log('Hotword yakalandi:', tumMetin);
                hotwordAktif();
            } else {
                $('altDurum').textContent = tumMetin ? `(bekliyor) "${tumMetin}"` : '';
            }
            return;
        }

        // --------- NORMAL DINLEME MODU ---------
        if (tumMetin) {
            let temizMetin = tumMetin;
            if (Date.now() - aktivlesmeZamani < 2500) {
                temizMetin = tumMetin.replace(HOTWORD_REGEX, '').trim();
            }
            $('altDurum').textContent = `"${temizMetin}"`;
        }

        if (final) {
            let temizFinal = final;
            if (Date.now() - aktivlesmeZamani < 3000) {
                temizFinal = final.replace(HOTWORD_REGEX, '').trim();
            }

            if (temizFinal) {
                birikenMetin += ' ' + temizFinal;
                birikenMetin = birikenMetin.trim();
            }

            clearTimeout(sessizlikTimer);
            sessizlikTimer = setTimeout(() => {
                if (birikenMetin && !isleniyor) {
                    const mesaj = birikenMetin;
                    birikenMetin = '';
                    hotwordBekleniyor = false;
                    jarviseGonder(mesaj);
                }
            }, SESSIZLIK_SURESI);
        }
    };

    tanima.onerror = e => {
        console.warn('STT hata:', e.error);
        dinlemeAktif = false;
        if (e.error === 'not-allowed') {
            durumGuncelle('', '❌ Mikrofon izni verilmedi', 'Sayfa izinlerinden ver');
            surekliMod = false;
            $('btnSurekli').textContent = '🎤 Sürekli Dinlemeyi Başlat';
            $('btnSurekli').classList.remove('aktif');
        }
    };

    tanima.onend = () => {
        dinlemeAktif = false;
        console.log('STT durdu. surekli:', surekliMod, 'isleniyor:', isleniyor);

        if (surekliMod) {
            setTimeout(() => {
                if (surekliMod && !dinlemeAktif) {
                    try {
                        tanima.start();
                    } catch(e) {
                        console.warn('STT restart hatasi:', e.message);
                        setTimeout(() => {
                            if (surekliMod && !dinlemeAktif) {
                                try { tanima.start(); } catch(e2) {}
                            }
                        }, 500);
                    }
                }
            }, 200);
        } else if (tekSeferMod) {
            tekSeferMod = false;
            $('btnTek').classList.remove('aktif');
        }
    };
} else {
    $('btnSurekli').textContent = 'Tarayıcı desteklemiyor';
    $('btnSurekli').disabled = true;
    $('btnTek').disabled = true;
}

function toggleSurekli() {
    if (!tanima) return;

    if (surekliMod) {
        surekliMod = false;
        hotwordBekleniyor = false;
        birikenMetin = '';
        clearTimeout(sessizlikTimer);
        try { tanima.stop(); } catch(e) {}
        $('btnSurekli').textContent = '🎤 Sürekli Dinlemeyi Başlat';
        $('btnSurekli').classList.remove('aktif');
        $('hotwordBadge').style.display = 'none';
        durumGuncelle('', 'Kapalı', '');
    } else {
        surekliMod = true;
        hotwordBekleniyor = true;
        birikenMetin = '';
        try { tanima.start(); } catch(e) { console.error(e); }
        $('btnSurekli').textContent = '⏹ Sürekli Dinlemeyi Durdur';
        $('btnSurekli').classList.add('aktif');
        $('hotwordBadge').style.display = 'inline-block';
        $('hotwordBadge').classList.add('aktif');
        durumGuncelle('', '👂 Uyandırıcı kelime bekleniyor', '"Jarvis" de');
        mesajEkle('Sürekli dinleme açık. "Jarvis" diyerek uyandır, sonra konuş.', 'sistem');
    }
}

function hotwordAktif() {
    hotwordBekleniyor = false;
    aktivlesmeZamani = Date.now();
    birikenMetin = '';
    clearTimeout(sessizlikTimer);

    $('hotwordBadge').classList.remove('aktif');
    $('jarvisDaire').className = 'jarvis-daire uyaniyor';
    setTimeout(() => {
        $('jarvisDaire').className = 'jarvis-daire dinliyor';
    }, 400);
    durumGuncelle('dinliyor', '🎤 Dinliyorum Patron...', 'Mesajınızı söyleyin');

    seslendir('Evet patron?', true);

    setTimeout(() => {
        if (surekliMod && !isleniyor && !hotwordBekleniyor && !birikenMetin) {
            console.log('Mesaj gelmedi, hotword moduna donuyor');
            hotwordModunaDon();
        }
    }, MESAJ_BEKLEME_SURESI);
}

function hotwordModunaDon() {
    if (!surekliMod) return;
    hotwordBekleniyor = true;
    birikenMetin = '';
    $('hotwordBadge').style.display = 'inline-block';
    $('hotwordBadge').classList.add('aktif');
    durumGuncelle('', '👂 Uyandırıcı bekliyor', '"Jarvis" diyerek tekrar konuş');
}

function tekSeferDinle() {
    if (!tanima || surekliMod) return;
    tekSeferMod = true;
    hotwordBekleniyor = false;
    aktivlesmeZamani = Date.now();
    birikenMetin = '';
    try { tanima.start(); } catch(e) {}
    $('btnTek').classList.add('aktif');
    durumGuncelle('dinliyor', '🎤 Dinliyorum...', 'Tek seferlik');
}

// ============ JARVIS'E GONDER ============
async function jarviseGonder(mesaj) {
    if (isleniyor) return;
    if (!mesaj || mesaj.length < 2) {
        console.log('Bos mesaj, iptal');
        if (surekliMod) hotwordModunaDon();
        return;
    }

    isleniyor = true;
    mesajEkle(mesaj, 'kullanici');
    durumGuncelle('dusunuyor', '🧠 Düşünüyorum...', '');

    try {
        const r = await fetch('jarvis.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'mesaj=' + encodeURIComponent(mesaj)
        });
        const d = await r.json();

        if (d.ok) {
            mesajEkle(d.cevap, 'jarvis', d.hareket_sayisi);
            durumGuncelle('konusuyor', '🗣 Konuşuyorum...', `🤖 ${d.hareket_sayisi} hareket`);
            seslendir(d.cevap);
        } else {
            mesajEkle('Hata: ' + (d.hata || 'bilinmeyen'), 'sistem');
            durumGuncelle('', 'Hata!', d.hata || '');
            isleniyor = false;
            if (surekliMod) hotwordModunaDon();
        }
    } catch (e) {
        mesajEkle('Bağlantı hatası', 'sistem');
        durumGuncelle('', 'Bağlantı hatası', '');
        isleniyor = false;
        if (surekliMod) hotwordModunaDon();
    }
}

// ============ SES (TTS) ============
let suAnkiAudio = null;

function seslendir(metin, kisa = false) {
    konusmayiDurdur();

    const url = 'tts.php?metin=' + encodeURIComponent(metin);
    const audio = new Audio(url);
    suAnkiAudio = audio;

    audio.onended = () => {
        suAnkiAudio = null;
        if (!kisa) {
            isleniyor = false;
            if (surekliMod) {
                setTimeout(hotwordModunaDon, 300);
            } else {
                durumGuncelle('', 'Hazır', '');
            }
        }
    };

    audio.onerror = () => {
        console.log('TTS basarisiz, browser fallback');
        browserTTS(metin, kisa);
    };

    audio.play().catch(() => browserTTS(metin, kisa));
}

function browserTTS(metin, kisa) {
    const s = window.speechSynthesis;
    s.cancel();
    const u = new SpeechSynthesisUtterance(metin);
    u.lang = 'tr-TR';
    u.rate = 1.0;
    u.pitch = 1.05;

    const sesler = s.getVoices();
    const tr = sesler.find(v => v.lang === 'tr-TR' && v.name.toLowerCase().includes('google'))
             || sesler.find(v => v.lang === 'tr-TR')
             || sesler.find(v => v.lang.startsWith('tr'));
    if (tr) u.voice = tr;

    u.onend = () => {
        if (!kisa) {
            isleniyor = false;
            if (surekliMod) {
                setTimeout(hotwordModunaDon, 300);
            } else {
                durumGuncelle('', 'Hazır', '');
            }
        }
    };
    s.speak(u);
}

function konusmayiDurdur() {
    if (suAnkiAudio) {
        suAnkiAudio.pause();
        suAnkiAudio = null;
    }
    window.speechSynthesis.cancel();
}

// ============ ARAYUZ ============
function mesajEkle(metin, tip, hareketSayisi) {
    const d = document.createElement('div');
    d.className = 'mesaj ' + tip;
    d.textContent = metin;
    if (hareketSayisi && hareketSayisi > 0) {
        const s = document.createElement('div');
        s.className = 'hareket-sayi';
        s.textContent = `🤖 ${hareketSayisi} hareket`;
        d.appendChild(s);
    }
    $('sohbet').appendChild(d);
    $('sohbet').scrollTop = $('sohbet').scrollHeight;
}

function durumGuncelle(sinif, ana, alt) {
    $('jarvisDaire').className = 'jarvis-daire ' + sinif;
    $('durumYazi').textContent = ana;
    $('altDurum').textContent = alt;
}

// ============ PI DURUM ============
async function piDurumGuncelle() {
    try {
        const r = await fetch('api.php?action=durum');
        const d = await r.json();
        if (d.ok) {
            const canli = d.pi_baglanti === 'canli';
            $('piNokta').className = 'pi-nokta ' + (canli ? 'canli' : 'offline');
            $('piDurum').textContent = 'Pi: ' + (canli ? 'canlı' : 'offline');
            $('mevcutPoz').textContent = `T${d.pozisyon.taban_aci}° O${d.pozisyon.omuz_aci}°`;
            $('bekleyenKomut').textContent = d.bekleyen_komut;
        }
    } catch (e) {}
}
piDurumGuncelle();
setInterval(piDurumGuncelle, 2000);

window.speechSynthesis.onvoiceschanged = () => {};
</script>
</body>
</html>
