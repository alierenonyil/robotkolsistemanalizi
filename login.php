<?php
require_once __DIR__ . '/auth.php';

// Zaten girisliyse panele yonlendir
if (!empty($_SESSION['user_id'])) {
    header('Location: panel.php');
    exit;
}

$hata = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = trim($_POST['kullanici_adi'] ?? '');
    $p = $_POST['sifre'] ?? '';
    if ($u === '' || $p === '') {
        $hata = 'Kullanici adi ve sifre zorunlu.';
    } elseif (login($u, $p)) {
        header('Location: panel.php');
        exit;
    } else {
        $hata = 'Kullanici adi veya sifre hatali.';
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Robot Kol - Giriş</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, 'Segoe UI', Arial, sans-serif;
            background: radial-gradient(ellipse at top, #0c1e3a 0%, #050a1a 70%);
            min-height: 100vh; display: flex; align-items: center; justify-content: center;
            padding: 20px; color: white;
        }
        .kart {
            width: 100%; max-width: 420px;
            background: rgba(255,255,255,0.05);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 32px 28px;
            border: 1px solid rgba(255,255,255,0.1);
        }
        h1 {
            text-align: center; margin-bottom: 6px; font-size: 1.8em;
            background: linear-gradient(90deg, #60a5fa, #22d3ee, #a78bfa);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .alt { text-align: center; opacity: 0.6; margin-bottom: 24px; font-size: 0.85em; }
        label { display: block; font-size: 0.85em; margin-bottom: 6px; opacity: 0.85; }
        input[type=text], input[type=password] {
            width: 100%; padding: 12px 14px; margin-bottom: 14px;
            background: rgba(0,0,0,0.3); color: white;
            border: 1px solid rgba(255,255,255,0.15); border-radius: 10px;
            font-size: 0.95em; outline: none;
        }
        input:focus { border-color: #3b82f6; }
        .btn {
            width: 100%; padding: 14px;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            color: white; border: none; border-radius: 10px;
            font-size: 1em; font-weight: 700; cursor: pointer;
            transition: transform 0.1s;
        }
        .btn:active { transform: scale(0.98); }
        .hata {
            background: rgba(239,68,68,0.15);
            border: 1px solid rgba(239,68,68,0.4);
            color: #fca5a5;
            padding: 10px 12px; border-radius: 8px;
            margin-bottom: 14px; font-size: 0.85em; text-align: center;
        }
        .geri {
            display: block; text-align: center; margin-top: 16px;
            color: #93c5fd; text-decoration: none; font-size: 0.85em;
        }
        .geri:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="kart">
        <h1>🦾 Robot Kol</h1>
        <div class="alt">Kontrol Paneli Girişi</div>

        <?php if ($hata): ?>
            <div class="hata"><?= htmlspecialchars($hata, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <form method="post" autocomplete="off">
            <label for="kullanici_adi">Kullanıcı Adı</label>
            <input type="text" id="kullanici_adi" name="kullanici_adi"
                   value="<?= htmlspecialchars($_POST['kullanici_adi'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                   autofocus required>

            <label for="sifre">Şifre</label>
            <input type="password" id="sifre" name="sifre" required>

            <button type="submit" class="btn">Giriş Yap</button>
        </form>

        <a class="geri" href="index.php">← Tanıtım sayfasına dön</a>
    </div>
</body>
</html>
