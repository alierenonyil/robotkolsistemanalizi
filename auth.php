<?php
// ============================================================
// Robot Kol - Oturum / Yetkilendirme yardimcilari
// ============================================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/db.php';

/**
 * Kullaniciyi dogrular ve oturumu acar.
 * @return bool true = basarili, false = hatali kimlik
 */
function login($kullanici_adi, $sifre) {
    $stmt = db()->prepare("
        SELECT id, kullanici_adi, sifre_hash, ad_soyad, rol, aktif
        FROM users
        WHERE kullanici_adi = ?
        LIMIT 1
    ");
    $stmt->execute([$kullanici_adi]);
    $u = $stmt->fetch();

    if (!$u || !$u['aktif']) return false;
    if (!password_verify($sifre, $u['sifre_hash'])) return false;

    $_SESSION['user_id']       = (int)$u['id'];
    $_SESSION['kullanici_adi'] = $u['kullanici_adi'];
    $_SESSION['ad_soyad']      = $u['ad_soyad'];
    $_SESSION['rol']           = $u['rol'];

    db()->prepare("UPDATE users SET son_giris = NOW() WHERE id = ?")
        ->execute([$u['id']]);

    return true;
}

/** Oturumu kapatir ve index'e yonlendirir. */
function logout() {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $p["path"], $p["domain"], $p["secure"], $p["httponly"]);
    }
    session_destroy();
    header('Location: index.php');
    exit;
}

/** Oturum yoksa login.php'ye atar. Korunmasi gereken sayfalarin basinda cagrilir. */
function checkAuth() {
    if (empty($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
}

/** Aktif kullaniciyi dondurur (oturum yoksa null). */
function currentUser() {
    if (empty($_SESSION['user_id'])) return null;
    return [
        'id'            => $_SESSION['user_id'],
        'kullanici_adi' => $_SESSION['kullanici_adi'] ?? '',
        'ad_soyad'      => $_SESSION['ad_soyad']      ?? '',
        'rol'           => $_SESSION['rol']           ?? '',
    ];
}

/** Kullanici admin mi? */
function isAdmin() {
    return ($_SESSION['rol'] ?? '') === 'admin';
}
