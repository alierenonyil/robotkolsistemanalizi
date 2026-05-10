# 14. Test Senaryoları ve Sonuçları

## 14.1 Test Ortamı

| Bileşen | Sürüm / Yapılandırma |
|---|---|
| Sunucu | `robocoffee.onyapp.com.tr` (Onyapp Hosting, PHP 8.4, MariaDB 10.11) |
| Tarayıcı | Chrome 130+, Firefox 132+ (masaüstü + mobil) |
| Donanım | Raspberry Pi 4 (4 GB), PCA9685 @ I²C 0x40, 6 servo |
| Pi yazılımı | `dinleyici.py` (Python 3, polling 1 sn) |
| Veritabanı | 6 tablo: users, komutlar, pi_durum, servo_pozisyon, ayarlar, jarvis_konusmalar |
| Test tarihi | _________ |
| Test eden | _________ |

---

## 14.2 Test Senaryoları

### TC-01 — Tanıtım Sayfasının Açılması

| | |
|---|---|
| **İlgili Gereksinim** | NFR-07 (Tarayıcı uyumluluğu) |
| **Ön Koşul** | Web sunucu çalışır durumda; kullanıcı oturum açmamış |
| **Adımlar** | 1. Tarayıcıdan `https://robocoffee.onyapp.com.tr/` adresine gidilir<br>2. Sayfa yüklenir; içeriği gözlemlenir |
| **Beklenen Sonuç** | Sayfa 2 sn içinde yüklenir; "🦾 Robot Kol" başlığı, proje açıklaması, ekip listesi (4 kişi), "Kontrol Paneline Giriş" butonu görünür |
| **Ekran Görüntüsü** | `screenshots/TC-01_tanitim.png` |

---

### TC-02 — Oturumsuz Panel Erişimi (Yetkisiz Yönlendirme)

| | |
|---|---|
| **İlgili Gereksinim** | FR-05, NFR-03 (Güvenlik) |
| **Ön Koşul** | Hiçbir kullanıcı oturum açmamış (tarayıcı çerezi temiz) |
| **Adımlar** | 1. Tarayıcıdan doğrudan `panel.php` adresine gidilir<br>2. URL ve sayfa içeriği gözlemlenir |
| **Beklenen Sonuç** | Tarayıcı otomatik olarak `login.php` adresine yönlendirilir; panel içeriği görüntülenmez |
| **Ekran Görüntüsü** | `screenshots/TC-02_auth_redirect.png` |

---

### TC-03 — Hatalı Şifre ile Giriş Denemesi

| | |
|---|---|
| **İlgili Gereksinim** | FR-05 (Negatif senaryo) |
| **Ön Koşul** | `login.php` sayfası açık |
| **Adımlar** | 1. Kullanıcı adı: `admin`<br>2. Şifre: `yanlissifre123`<br>3. "Giriş Yap" butonuna tıklanır |
| **Beklenen Sonuç** | "Kullanici adi veya sifre hatali" mesajı kırmızı uyarı kutusunda görünür; `panel.php`'ye geçilmez; oturum açılmaz |
| **Ekran Görüntüsü** | `screenshots/TC-03_yanlis_sifre.png` |

---

### TC-04 — Başarılı Yönetici Girişi

| | |
|---|---|
| **İlgili Gereksinim** | FR-05, FR-06 (Yönetici rolü) |
| **Ön Koşul** | `users` tablosunda admin kullanıcısı tanımlı ve aktif |
| **Adımlar** | 1. Kullanıcı adı: `admin`<br>2. Şifre: `Admin2026!`<br>3. "Giriş Yap" butonuna tıklanır |
| **Beklenen Sonuç** | `panel.php` açılır; üst barda "👤 Sistem Yoneticisi" ve **kırmızı "Yönetici"** rozeti görünür; veritabanı `son_giris` alanı güncellenir |
| **Ekran Görüntüsü** | `screenshots/TC-04_admin_giris.png` |

---

### TC-05 — Operatör Rolü ile Giriş

| | |
|---|---|
| **İlgili Gereksinim** | FR-05, FR-06 (Operatör rolü) |
| **Ön Koşul** | `users` tablosunda operator kullanıcısı tanımlı |
| **Adımlar** | 1. Kullanıcı adı: `operator`<br>2. Şifre: `Operator2026!`<br>3. "Giriş Yap" butonuna tıklanır |
| **Beklenen Sonuç** | `panel.php` açılır; üst barda **mor "Operatör"** rozeti görünür; tüm komut gönderme fonksiyonları erişilebilir |
| **Ekran Görüntüsü** | `screenshots/TC-05_operator_giris.png` |

---

### TC-06 — Çıkış (Logout)

| | |
|---|---|
| **İlgili Gereksinim** | FR-05 (Oturum sonlandırma) |
| **Ön Koşul** | Kullanıcı panele giriş yapmış durumda |
| **Adımlar** | 1. Üst bardan "Çıkış" butonuna tıklanır<br>2. `index.php`'ye düşülür<br>3. Tarayıcının geri butonuyla `panel.php` geri çağrılır |
| **Beklenen Sonuç** | Çıkış sonrası `index.php` açılır; oturum sonlanır; panel.php'ye dönüş denemesi `login.php`'ye yönlendirilir |
| **Ekran Görüntüsü** | `screenshots/TC-06_cikis.png` |

---

### TC-07 — Manuel Slider ile Komut Gönderimi (FR-01)

| | |
|---|---|
| **İlgili Gereksinim** | FR-01, FR-02, FR-03, NFR-01 (1 sn polling) |
| **Ön Koşul** | Pi canlı (yeşil nokta), kullanıcı panelde, "Manuel Kontrol (6 servo)" açık |
| **Adımlar** | 1. Slider'lar şu değerlere ayarlanır: Taban=120, Omuz=60, Dirsek=80, Bilek=120, CH4=90, CH5=90<br>2. "Gönder" butonuna tıklanır<br>3. Pi terminal çıktısı + fiziksel kol hareketi gözlemlenir<br>4. Sağ üstteki "Pozisyon" göstergesi izlenir |
| **Beklenen Sonuç** | Komut 1 sn içinde Pi tarafından çekilir; kol fiziksel olarak hedef pozisyona ulaşır; Pi terminalinde `[HAREKET] t=120 o=60 d=80 b=120 c4=90 c5=90` mesajı; "Pozisyon" göstergesi `T120° O60°` olarak güncellenir |
| **Ekran Görüntüsü** | `screenshots/TC-07_manuel_komut.png` |

---

### TC-08 — Servo Açı Limit Sınaması (Sınır Değer Testi)

| | |
|---|---|
| **İlgili Gereksinim** | NFR-05 (Dayanıklılık), backend validasyonu |
| **Ön Koşul** | Panel açık, geliştirici konsolu (F12) açık |
| **Adımlar** | 1. Omuz slider'ı 130 (maksimum) yapılır, gönderilir → kabul edilmeli<br>2. Tarayıcı konsolundan `curl` veya `fetch` ile **omuz=200** içeren bir POST isteği gönderilir<br>3. Veritabanından son komut kaydı kontrol edilir<br>4. CH5 için 180 değeri gönderilir (kapsam: dar aralık 80-100) |
| **Beklenen Sonuç** | 130 değeri başarıyla işlenir. 200 değeri backend (`api.php`) tarafından 130'a; CH5=180 değeri Pi tarafında 100'e clamp edilir. Kol mekanik sınırın ötesine zorlanmaz |
| **Ekran Görüntüsü** | `screenshots/TC-08_limit_sinama.png` |

---

### TC-09 — Pi Canlı Durum İzleme (FR-07)

| | |
|---|---|
| **İlgili Gereksinim** | FR-07, NFR-02 (Güvenilirlik) |
| **Ön Koşul** | `dinleyici.py` Pi'de çalışır durumda, panel açık |
| **Adımlar** | 1. Panel sayfası 5 sn boyunca izlenir<br>2. Sağ üstteki Pi durum noktası ve metni gözlemlenir<br>3. Tarayıcı geliştirici araçları → Network sekmesinde `api.php?action=durum` çağrıları doğrulanır |
| **Beklenen Sonuç** | Yeşil yanıp sönen nokta görünür; metin "Pi: canlı"; durum sorgusu 2 sn'de bir tekrar eder; Pi her 1 sn'de bir `pi_cek` çağrısı yapar |
| **Ekran Görüntüsü** | `screenshots/TC-09_pi_canli.png` |

---

### TC-10 — Pi Offline Algılama

| | |
|---|---|
| **İlgili Gereksinim** | NFR-02 (5 sn eşik), FR-07 |
| **Ön Koşul** | Pi çalışır durumda, panel açık |
| **Adımlar** | 1. Pi terminalinde `Ctrl+C` ile `dinleyici.py` durdurulur<br>2. 6 sn beklenir<br>3. Panel'deki Pi durum göstergesi gözlemlenir<br>4. Tekrar `python3 dinleyici.py` ile başlatılır<br>5. 2 sn sonra panel kontrol edilir |
| **Beklenen Sonuç** | 5 sn sonrası: nokta kırmızıya döner, metin "Pi: offline" olur. Pi yeniden başlatılınca: 2 sn içinde tekrar yeşil "Pi: canlı" durumuna geçer |
| **Ekran Görüntüsü** | `screenshots/TC-10_pi_offline.png` |

---

### TC-11 — Kahve Bardağı Taşıma Tam Akışı (FR-04)

| | |
|---|---|
| **İlgili Gereksinim** | FR-04, FR-09 |
| **Ön Koşul** | Pi canlı, kol başlangıç pozisyonunda (90/30/30/90/90/90), boş bir bardak başlangıç noktasına yerleştirilmiş |
| **Adımlar** | "Kahve Bardağı Taşıma - Test Hareketleri" panelinde sırasıyla 7 butona, her biri arasında 3 sn bekleyerek, tıklanır:<br>1. 🤲 Bardağa Uzan<br>2. ✊ Bardağı Kavra<br>3. ⬆️ Bardağı Kaldır<br>4. ↪️ Hedefe Taşı<br>5. ⬇️ Bardağı İndir<br>6. 🖐 Bardağı Bırak<br>7. 🏠 Başlangıç Pozisyonuna Dön |
| **Beklenen Sonuç** | Her adımda kol ilgili pozisyona ulaşır; sohbet panelinde "☕ Bardağa uzanıyor", "☕ Bardak kavranıyor" gibi onay mesajları görünür; bardak fiziksel olarak başlangıçtan hedef konuma taşınmış olur; son adım sonrası kol başlangıç pozisyonunda olur |
| **Ekran Görüntüsü** | `screenshots/TC-11a_baslangic.png`, `TC-11b_kavrama.png`, `TC-11c_tasima.png`, `TC-11d_birakma.png` |

---

### TC-12 — Anlık Pozisyon Göstergesinin Güncellenmesi

| | |
|---|---|
| **İlgili Gereksinim** | FR-07 (Anlık izleme) |
| **Ön Koşul** | Pi canlı, panel açık |
| **Adımlar** | 1. Slider'lardan Taban=45, Omuz=80 ayarlanır<br>2. "Gönder" tıklanır<br>3. Pi'nin komutu çekmesi beklenir<br>4. Sağ üstteki "Pozisyon" göstergesi izlenir<br>5. Veritabanında `servo_pozisyon` tablosu sorgulanır |
| **Beklenen Sonuç** | Komut tamamlandıktan en geç 2 sn sonra "Pozisyon: T45° O80°" görünür; `servo_pozisyon` tablosunda `taban_aci=45`, `omuz_aci=80` ve güncel `son_guncelleme` zaman damgası bulunur |
| **Ekran Görüntüsü** | `screenshots/TC-12_pozisyon.png` |

---

### TC-13 — Komut Kuyruğu Yönetimi

| | |
|---|---|
| **İlgili Gereksinim** | FR-02, FR-03 (Polling) |
| **Ön Koşul** | Pi geçici olarak durdurulmuş (`Ctrl+Z` ile arka plana atılmış veya `kill`'lenmiş) |
| **Adımlar** | 1. Pi durdurulur (komutlar işlenmesin)<br>2. 5 sn içinde 3 farklı slider komutu hızla gönderilir<br>3. Panel'deki "Kuyruk" sayacı izlenir<br>4. `fg` ile Pi tekrar başlatılır<br>5. Kuyruğun azalışı izlenir |
| **Beklenen Sonuç** | Pi durdurulmuşken kuyruk sayacı 0→1→2→3 olarak artar; Pi başlatılınca komutlar **gönderim sırasıyla** (FIFO) işlenir; kuyruk 3→2→1→0'a düşer |
| **Ekran Görüntüsü** | `screenshots/TC-13_kuyruk.png` |

---

### TC-14 — Veritabanına Komut Kaydı (FR-08)

| | |
|---|---|
| **İlgili Gereksinim** | FR-08 (Loglama) |
| **Ön Koşul** | phpMyAdmin erişimi var, `komutlar` tablosu boş veya bilinen son ID kayıtlı |
| **Adımlar** | 1. Panel'den bir hareket komutu gönderilir (örn t=100, o=50)<br>2. phpMyAdmin'de `SELECT * FROM komutlar ORDER BY id DESC LIMIT 1` sorgusu çalıştırılır<br>3. Pi komut işleyene kadar beklenir<br>4. Aynı sorgu tekrar çalıştırılır |
| **Beklenen Sonuç** | İlk sorgu: yeni satır görünür; `tip='hareket'`, `taban_aci=100`, `omuz_aci=50`, `durum='bekliyor'`, `olusturma` dolu, `calisma` NULL.<br>İkinci sorgu: aynı satır; `durum='calisti'`, `calisma` zaman damgası dolmuş |
| **Ekran Görüntüsü** | `screenshots/TC-14a_bekliyor.png`, `TC-14b_calisti.png` |

---

### TC-15 — I²C Hata Kurtarma (Hata Yönetimi)

| | |
|---|---|
| **İlgili Gereksinim** | FR-10 (Hata yönetimi), NFR-02 |
| **Ön Koşul** | `dinleyici.py`'ye I²C kurtarma yaması uygulanmış; Pi çalışıyor |
| **Adımlar** | 1. Pi terminali izlenir<br>2. Pi-PCA9685 arasındaki SDA kablosu ~3 sn süreyle çıkarılır<br>3. Bu süre içinde panel'den bir komut gönderilir<br>4. Kablo geri takılır<br>5. Yeni komut gönderilir |
| **Beklenen Sonuç** | Kablo çıkıkken: terminalde `[I2C HATA #1] [Errno 5]` mesajları; veritabanında `durum='hata'` ve `hata_mesaji` dolu kayıt. 3 hata sonrası `[KURTARMA] I2C bus yenileniyor...` ve `[KURTARMA] PCA9685 yeniden baslatildi` mesajları. Kablo takıldıktan sonra yeni komut başarılı olarak işlenir |
| **Ekran Görüntüsü** | `screenshots/TC-15_i2c_kurtarma.png` |

---

## 14.3 Test Sonuç Özet Tablosu

> Manuel test çalıştırıldıktan sonra her satıra **Başarılı / Başarısız** yazılır, varsa not eklenir.

| TC No | Test Adı | İlgili FR/NFR | Sonuç | Test Tarihi | Notlar |
|:---:|---|:---:|:---:|:---:|---|
| TC-01 | Tanıtım Sayfasının Açılması | NFR-07 | ☐ | _____ | |
| TC-02 | Oturumsuz Panel Erişimi | FR-05, NFR-03 | ☐ | _____ | |
| TC-03 | Hatalı Şifre ile Giriş | FR-05 | ☐ | _____ | |
| TC-04 | Başarılı Yönetici Girişi | FR-05, FR-06 | ☐ | _____ | |
| TC-05 | Operatör Rolü ile Giriş | FR-05, FR-06 | ☐ | _____ | |
| TC-06 | Çıkış (Logout) | FR-05 | ☐ | _____ | |
| TC-07 | Manuel Slider ile Komut | FR-01, FR-02, FR-03 | ☐ | _____ | |
| TC-08 | Servo Açı Limit Sınaması | NFR-05 | ☐ | _____ | |
| TC-09 | Pi Canlı Durum İzleme | FR-07 | ☐ | _____ | |
| TC-10 | Pi Offline Algılama | NFR-02, FR-07 | ☐ | _____ | |
| TC-11 | Bardak Taşıma Tam Akışı | FR-04, FR-09 | ☐ | _____ | |
| TC-12 | Anlık Pozisyon Göstergesi | FR-07 | ☐ | _____ | |
| TC-13 | Komut Kuyruğu Yönetimi | FR-02, FR-03 | ☐ | _____ | |
| TC-14 | Veritabanına Komut Kaydı | FR-08 | ☐ | _____ | |
| TC-15 | I²C Hata Kurtarma | FR-10, NFR-02 | ☐ | _____ | |

**Toplam:** 15 test senaryosu &nbsp;&nbsp;|&nbsp;&nbsp; **Başarılı:** _____ &nbsp;&nbsp;|&nbsp;&nbsp; **Başarısız:** _____ &nbsp;&nbsp;|&nbsp;&nbsp; **Başarı Oranı:** _____ %

---

## 14.4 Ekran Görüntüsü Klasör Yapısı

Test sırasında çekilen ekran görüntüleri aşağıdaki yapıda düzenlenmelidir:

```
docs/test_screenshots/
├── TC-01_tanitim.png
├── TC-02_auth_redirect.png
├── TC-03_yanlis_sifre.png
├── TC-04_admin_giris.png
├── TC-05_operator_giris.png
├── TC-06_cikis.png
├── TC-07_manuel_komut.png
├── TC-08_limit_sinama.png
├── TC-09_pi_canli.png
├── TC-10_pi_offline.png
├── TC-11a_baslangic.png
├── TC-11b_kavrama.png
├── TC-11c_tasima.png
├── TC-11d_birakma.png
├── TC-12_pozisyon.png
├── TC-13_kuyruk.png
├── TC-14a_bekliyor.png
├── TC-14b_calisti.png
└── TC-15_i2c_kurtarma.png
```

> **Notlar:**
> - Veritabanı kontrolleri için **phpMyAdmin** ekran görüntüleri kullanılmalı
> - Pi tarafı testleri için **terminal SSH ekranı** + **fiziksel kol fotoğrafı** birleştirilmeli
> - Mobil test için Chrome Developer Tools "Device Toolbar" ile responsive simülasyonu yeterli

---

## 14.5 Değerlendirme

> Bu bölüm test çalışması tamamlandıktan sonra ekip tarafından doldurulacaktır.

**Karşılaşılan Hatalar:**
_(Test sırasında bulunan ve düzeltilen / iz açılan hatalar listelenir)_

**Çözülen Hatalar:**
_(Hata düzeltme süreci)_

**İleri Sürüme Bırakılanlar:**
_(Test sonrası tespit edilen ama mevcut sürüme dahil edilmeyen iyileştirmeler)_

**Genel Değerlendirme:**
_(Sistemin kabul kriterlerini karşılama düzeyi, kullanılabilirlik notları)_
