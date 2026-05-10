\# 🦾 Robot Kol — Kahve Bardağı Taşıyan Web Kontrollü Robot Kolu



\[!\[Status](https://img.shields.io/badge/Status-Geli%C5%9Ftirme%20A%C5%9Famas%C4%B1-yellow)]()

\[!\[Language](https://img.shields.io/badge/Backend-PHP%208.4-blue)]()

\[!\[Language](https://img.shields.io/badge/Pi-Python%203-green)]()

\[!\[Database](https://img.shields.io/badge/DB-MySQL%2FMariaDB-orange)]()

\[!\[License](https://img.shields.io/badge/Akademik-IEU%20BP-lightgrey)]()



> \*\*İzmir Ekonomi Üniversitesi — Bilgisayar Programcılığı\*\*

> Sistem Analizi ve Tasarımı dersi grup ödevi.

> Onyıl Bilişim ortaklığında geliştirilen, 6 servolu robot kolun web arayüzünden kontrol edildiği uçtan uca bir SDLC projesidir.



\---



\## 📌 Proje Özeti



Robot Kol; \*\*Raspberry Pi 4\*\* üzerinde çalışan ve web arayüzü üzerinden uzaktan kontrol edilen, \*\*6 servolu\*\* (2× MG995 + 4× SG90) bir robot kol sistemidir. Yetkili kullanıcılar (yönetici / operatör) tarayıcıdan kola komut göndererek bir \*\*kahve bardağını mevcut konumundan başka bir konuma taşıyabilir\*\*.



\*\*Çözülen problem:\*\* Robot kol kontrolünü fiziksel müdahale gerektirmeksizin, web tabanlı bir arayüz aracılığıyla gerçekleştirmek.



\*\*Mimari:\*\* 4 katmanlı — Sunum (Web) ↔ Uygulama+Veri (PHP / MySQL) ↔ Kontrol (Pi / Python polling) ↔ Donanım (PCA9685 / 6 servo).



\---



\## 👥 Proje Ekibi



| Öğrenci No | Ad Soyad | Birincil Sorumluluk |

|---|---|---|

| \*\*20242425034\*\* | \*\*Ali Eren Onyıl\*\* | Donanım, Pi yazılımı, sistem entegrasyonu, proje yönetimi |

| \*\*20242425020\*\* | \*\*Murat Ege Ertürk\*\* | Web arayüzü, slider kontrolleri, use case analizi |

| \*\*20242425039\*\* | \*\*Arda Savaş\*\* | 3D baskı üretim, veritabanı şeması, ER diyagramı, log entegrasyonu |

| \*\*20242425003\*\* | \*\*Muhammed Burak Arıkan\*\* | Backend API, paydaş \& gereksinim analizi, login \& rol sistemi |



\---



\## 🧭 Sistem Mimarisi



```

┌──────────────────────────────────────────────────────────────────┐

│                         SUNUM KATMANI                             │

│  index.php (tanıtım) → login.php → panel.php (kontrol arayüzü)    │

│  HTML / CSS / Vanilla JS — responsive, mobil uyumlu               │

└──────────────────────────────┬───────────────────────────────────┘

&#x20;                              │ HTTPS / POST / GET (JSON)

┌──────────────────────────────▼───────────────────────────────────┐

│                  UYGULAMA + VERİ KATMANI                          │

│   api.php   → komut kuyruğu, durum sorgu, Pi cek/tamamla         │

│   auth.php  → session + bcrypt + rol yönetimi (admin/operator)    │

│   db.php    → PDO bağlantı (utf8mb4)                              │

│   ├─ jarvis.php  (kapsam dışı – AI sohbet katmanı)                │

│   └─ tts.php     (kapsam dışı – ses sentezi)                      │

│   MySQL / MariaDB — 6 tablo                                       │

└──────────────────────────────┬───────────────────────────────────┘

&#x20;                              │ HTTP polling (1 sn)

┌──────────────────────────────▼───────────────────────────────────┐

│                      KONTROL KATMANI (Pi)                         │

│   dinleyici.py  → bekleyen komutu çek, servoları çalıştır,        │

│                   sonucu geri bildir                              │

└──────────────────────────────┬───────────────────────────────────┘

&#x20;                              │ I²C (SCL/SDA, 50 Hz PWM)

┌──────────────────────────────▼───────────────────────────────────┐

│                       DONANIM KATMANI                             │

│   PCA9685 servo sürücü ─ 6 PWM kanalı                             │

│   ch0 Taban (MG995)   ch1 Omuz (MG995)                            │

│   ch2 Dirsek (SG90)   ch3 Bilek (SG90)                            │

│   ch4 Ekstra1 (SG90)  ch5 Ekstra2 / Gripper (SG90)                │

└──────────────────────────────────────────────────────────────────┘

```



\---



\## 🔧 Donanım Listesi



| Bileşen | Model / Adet | Açıklama |

|---|---|---|

| Tek kart bilgisayar | Raspberry Pi 4 (4 GB) | Ana kontrol birimi |

| Servo sürücü | PCA9685 (16 kanal) | I²C, 50 Hz PWM |

| Büyük servo | MG995 × 2 | Taban (ch0) + Omuz (ch1) |

| Küçük servo | SG90 × 4 | Dirsek, Bilek, Ekstra1, Ekstra2 |

| Güç kaynağı | 5V harici (servolar için ayrı hat) | PCA9685 V+ beslemesi |

| Mekanik | 3D baskı (HowToMechatronics tasarımı) | Bambu Lab H2S, PLA |

| Hosting | Onyıl Bilişim — `robocoffee.onyapp.com.tr` | MySQL veritabanı |



\### Servo Konfigürasyonu (hardcoded — `dinleyici.py`)



| Kanal | Eklem | Servo | min\_pulse | max\_pulse | Aralık | Başlangıç |

|---|---|---|---|---|---|---|

| ch0 | Taban | MG995 | 500 | 2500 | 0–180° | 90° |

| ch1 | Omuz | MG995 | 250 | 2500 | 0–130° (270° actuation) | 30° (dik) |

| ch2 | Dirsek | SG90 | 500 | 2400 | 0–180° | 30° |

| ch3 | Bilek | SG90 | 500 | 2400 | 0–180° | 90° |

| ch4 | Ekstra1 | SG90 | 500 | 2400 | \*\*80–100°\*\* (güvenli dar aralık) | 90° |

| ch5 | Ekstra2 | SG90 | 500 | 2400 | \*\*80–100°\*\* (güvenli dar aralık) | 90° |



> ⚠️ \*\*Not:\*\* ch4 ve ch5 fiziksel kalibrasyon tamamlanana kadar dar güvenli aralıkta tutulmaktadır. Kalibrasyon sonrası `dinleyici.py`'deki `CH4\_MIN/MAX` ve `CH5\_MIN/MAX` sabitleri güncellenecektir.



\---



\## 🗂 Veritabanı Şeması (6 tablo)



| Tablo | Amaç | Boyut |

|---|---|---|

| `users` | Kimlik doğrulama (admin/operator), bcrypt şifre | 8 alan |

| `komutlar` | Komut kuyruğu (Pi polling kaynağı) | 14 alan |

| `pi\_durum` | Pi canlılık ve IP kaydı (tek satır) | 3 alan |

| `servo\_pozisyon` | 6 servonun anlık pozisyon önbelleği (tek satır) | 8 alan |

| `ayarlar` | Anahtar-değer yapılandırma (kapsam dışı modüller için) | 2 alan |

| `jarvis\_konusmalar` | AI sohbet geçmişi \*(kapsam dışı – ileriki sürüm)\* | 6 alan |



> Kurulum SQL dosyaları `/sql/` klasöründedir. Detaylı şema için bkz. `RobotKol\_SDLC\_v6.docx` Bölüm 12.



\---



\## 🚀 Kurulum



\### 1) Web Sunucu (Hosting / Linux)



```bash

\# Web kök dizinine kopyala

git clone https://github.com/alierenonyil/robotkolsistemanalizi.git

cd robotkolsistemanalizi/web

\# → public\_html/ veya benzeri kök dizine yükle



\# db.php içindeki kimlik bilgilerini düzenle

nano db.php

\# DB\_NAME, DB\_USER, DB\_PASS değerlerini hosting'e göre güncelle

```



\### 2) Veritabanı (phpMyAdmin / MySQL)



1\. `robocoffee` adında bir veritabanı oluştur (utf8mb4\_unicode\_ci)

2\. `sql/schema.sql` dosyasını içe aktar (mevcut 5 tablo)

3\. `sql/users\_table.sql` dosyasını içe aktar (yeni `users` tablosu + default kullanıcılar)

4\. \*\*İlk girişten sonra default şifreleri değiştir!\*\*



\### 3) Raspberry Pi (Python tarafı)



```bash

\# Bağımlılıklar

sudo apt update

sudo apt install python3-pip i2c-tools

sudo pip3 install adafruit-circuitpython-pca9685 adafruit-circuitpython-motor requests



\# I²C'yi etkinleştir

sudo raspi-config   # → Interface Options → I2C → Enable



\# Servoları bağla, dinleyiciyi başlat

cd pi/

python3 dinleyici.py

```



\### 4) systemd Servisi (opsiyonel, otomatik başlatma)



```bash

sudo tee /etc/systemd/system/robotkol.service > /dev/null <<EOF

\[Unit]

Description=Robot Kol Pi Dinleyici

After=network.target



\[Service]

Type=simple

User=pi

WorkingDirectory=/home/pi/robotkol

ExecStart=/usr/bin/python3 /home/pi/robotkol/dinleyici.py

Restart=always



\[Install]

WantedBy=multi-user.target

EOF



sudo systemctl enable --now robotkol

```



\---



\## 🔑 Default Kullanıcılar



| Kullanıcı | Şifre | Rol |

|---|---|---|

| `admin` | `Admin2026!` | Yönetici (tam yetki) |

| `operator` | `Operator2026!` | Operatör (komut + izleme) |



> ⚠️ İlk girişten sonra panel veya phpMyAdmin üzerinden şifreleri \*\*mutlaka\*\* değiştir.



\---



\## 🌊 Kullanıcı Akışı



```

index.php (tanıtım)

&#x20;  │

&#x20;  ▼

login.php (kullanıcı adı + şifre)

&#x20;  │  ✓ doğrulama

&#x20;  ▼

panel.php (kontrol paneli)

&#x20;  ├─ Manuel slider kontrolü (6 servo, 0–180°)

&#x20;  ├─ Bardak Taşıma test butonları (FR-04, 7 adım)

&#x20;  ├─ Pi durum izleme (canlı/offline + kuyruk)

&#x20;  └─ \[Kapsam dışı] Jarvis AI sesli kontrol

```



\### FR-04 — Bardak Taşıma Test Adımları



Panel'de tek tıkla gönderilebilen 7 hardcoded pozisyon:



1\. 🤲 Bardağa Uzan

2\. ✊ Bardağı Kavra

3\. ⬆️ Bardağı Kaldır

4\. ↪️ Hedefe Taşı

5\. ⬇️ Bardağı İndir

6\. 🖐 Bardağı Bırak

7\. 🏠 Başlangıç Pozisyonuna Dön



\---



\## 📁 Klasör Yapısı



```

robotkolsistemanalizi/

├── web/                          # Web sunucu dosyaları

│   ├── index.php                 # Tanıtım sayfası

│   ├── login.php                 # Giriş ekranı

│   ├── panel.php                 # Kontrol paneli (auth korumalı)

│   ├── auth.php                  # Session + login/logout helpers

│   ├── api.php                   # REST API (hareket, durum, pi\_cek, pi\_tamamla)

│   ├── db.php                    # PDO bağlantı

│   ├── jarvis.php                # AI sohbet (kapsam dışı)

│   └── tts.php                   # ElevenLabs TTS (kapsam dışı)

│

├── pi/                           # Raspberry Pi tarafı

│   └── dinleyici.py              # Polling dinleyicisi + servo kontrolü

│

├── sql/                          # Veritabanı şeması

│   ├── schema.sql                # Mevcut 5 tablo

│   └── users\_table.sql           # Yeni users tablosu + default kullanıcılar

│

├── docs/                         # SDLC dokümantasyonu

│   ├── RobotKol\_SDLC\_v6.docx     # Ana doküman (Planlama + Analiz)

│   └── er\_diagram.png            # Chen notasyonu ER diyagramı

│

├── 3d\_models/                    # 3D baskı dosyaları (STL)

│   └── (HowToMechatronics tasarımı + modifikasyonlar)

│

└── README.md                     # Bu dosya

```



\---



\## 📅 SDLC Aşamaları



| Sprint | Hafta | Aşama | Durum |

|---|---|---|---|

| \*\*Sprint 1\*\* | 1–3 | Donanım \& Planlama | ✅ Tamamlandı |

| \*\*Sprint 2\*\* | 4–6 | Analiz Aşaması (gereksinim, use case, ER) | ✅ Tamamlandı |

| \*\*Sprint 3\*\* | 7–9 | Geliştirme \& Test | 🚧 IN PROCCESS |



Detaylı görev tablosu için bkz. `docs/RobotKol\_SDLC\_v6.docx` Bölüm 13.



\---



\## 🎯 Detaylı Görev Dağılımı



\### 👨‍💻 Ali Eren Onyıl (20242425034) — Donanım \& Pi Yazılımı \& Proje Yönetimi



\*\*Sprint 1 (Donanım \& Planlama):\*\*

\- Raspberry Pi 4 kurulumu ve Raspbian OS konfigürasyonu (Hafta 1)

\- 3D baskı robot kol üretimi — Bambu Lab H2S üzerinde HowToMechatronics tasarımının basımı (Hafta 1–2, Arda ile birlikte)

\- PCA9685 servo sürücüsünün Pi'ye I²C üzerinden bağlanması (Hafta 2, Murat ile birlikte)

\- Güç kaynağı bağlantıları ve servo kalibrasyonu (Hafta 2–3, Arda ile birlikte)

\- Python servo kontrol kütüphanesi `dinleyici.py` ilk sürüm yazımı (Hafta 2–3, Murat ile birlikte)

\- Jira backlog ve Sprint 1 açılışı (Hafta 3)



\*\*Sprint 2 (Analiz):\*\*

\- Paydaş \& Gereksinim Analizi MoSCoW önceliklendirmesi (Hafta 4, Burak ile birlikte)

\- Use Case senaryoları ve UML diyagramı tasarımı (Hafta 4–5, Murat ile birlikte)



\*\*Sprint 3 (Geliştirme):\*\*

\- Login + Rol Sistemi (admin/operatör) entegrasyonu (Hafta 7, Burak ile birlikte)

\- Pi Polling Dinleyici optimizasyonu — 1 sn polling, çoklu komut sıraya dizme, hata recovery (Hafta 8, Murat ile birlikte)

\- Eklem konfigürasyonunun veritabanından okunması (kapsam dışı bırakıldı, hardcoded olarak kaldı) (Hafta 8)

\- Uçtan uca entegrasyon testi (Hafta 8–9, tüm ekip)

\- Kullanıcı kabul testleri (Hafta 9, tüm ekip)



\*\*Ek roller:\*\*

\- Onyıl Bilişim ile ticari koordinasyon ve hosting yönetimi

\- 3D model OpenSCAD modifikasyonları (Arm\_02\_v3.STL üzerinde duvar kaldırma, servo yuvası genişletme)

\- AI/sohbet katmanı (Jarvis) altyapı kurulumu — kapsam dışı tutuldu



\---



\### 👨‍💻 Murat Ege Ertürk (20242425020) — Web Arayüzü \& UML



\*\*Sprint 1:\*\*

\- PCA9685 ve servo bağlantılarının kontrolü (Hafta 2, Ali Eren ile birlikte)

\- Python servo kontrol kütüphanesi `dinleyici.py` katkısı — komut işleme akışı (Hafta 2–3, Ali Eren ile birlikte)

\- Web arayüzü ilk sürüm — slider kontrolleri ve durum göstergesi (Hafta 3–4)

\- Pi polling dinleyicisinin web tarafıyla entegrasyonu (Hafta 5, Ali Eren ile birlikte)



\*\*Sprint 2:\*\*

\- Use Case senaryoları ve UML Use Case diyagramı (Hafta 4–5, Ali Eren ile birlikte)

\- 6 use case'in detay senaryolarının yazımı



\*\*Sprint 3:\*\*

\- Web arayüzü slider kontrol panelinin son hâli — 6 servo için ayrı slider, anlık değer gösterimi (Hafta 7–8, Arda ile birlikte)

\- Pozisyon Preset tablosu ve UI (kapsam dışı bırakıldı; hardcoded bardak test butonları olarak yeniden tasarlandı) (Hafta 7–8, Burak ile birlikte)

\- Pi Polling Dinleyici optimizasyonu (Hafta 8, Ali Eren ile birlikte)



\*\*Ek roller:\*\*

\- Tarayıcı uyumluluk testleri (Chrome, Firefox, Edge, Safari)

\- Mobil responsive tasarım kontrolü

\- Glassmorphism arayüz tasarımı (radial gradient + backdrop blur)



\---



\### 👨‍💻 Arda Savaş (20242425039) — 3D Üretim \& Veritabanı \& ER



\*\*Sprint 1:\*\*

\- 3D baskı robot kol üretimi (Hafta 1–2, Ali Eren ile birlikte)

\- Güç kaynağı bağlantıları ve servo kalibrasyonu (Hafta 2–3, Ali Eren ile birlikte)

\- MySQL veritabanı şema tasarımı — 6 tablo (Hafta 2, Burak ile birlikte)

\- Miro Backlog ve Risk Tablosu — 8 risk maddesi ROAM çerçevesinde sınıflandırılması (Hafta 3)



\*\*Sprint 2:\*\*

\- ER Diyagramı detay tasarımı — 6 tablolu Chen notasyonu (Hafta 5, Burak ile birlikte)

\- Tablo ilişkilerinin (kardinalite) belirlenmesi

\- Veritabanı normalizasyon kontrolü (3NF)



\*\*Sprint 3:\*\*

\- Web arayüzü slider kontrol paneli (Hafta 7–8, Murat ile birlikte)

\- Hareket Log ve Hata Log entegrasyonu — `komutlar` tablosundaki `durum` ve `hata\_mesaji` alanlarının kullanımı (Hafta 8, Burak ile birlikte)



\*\*Ek roller:\*\*

\- 3D yazıcı kalibrasyonu ve baskı sonrası işlem (zımparalama, vida deliği temizleme)

\- STL dosyalarının versiyon yönetimi

\- Risk yönetimi takibi



\---



\### 👨‍💻 Muhammed Burak Arıkan (20242425003) — Backend API \& Analiz \& Auth



\*\*Sprint 1:\*\*

\- MySQL veritabanı şema tasarımı (Hafta 2, Arda ile birlikte)

\- Backend API ilk sürümü — `api.php` endpoint'leri (`hareket`, `durum`, `pi\_cek`, `pi\_tamamla`) (Hafta 6)



\*\*Sprint 2:\*\*

\- Paydaş \& Gereksinim Analizi MoSCoW önceliklendirmesi — 7 paydaş, 10 işlevsel (FR-01 → FR-10) ve 7 işlevsel olmayan (NFR-01 → NFR-07) gereksinim (Hafta 4, Ali Eren ile birlikte)

\- ER Diyagramı (Hafta 5, Arda ile birlikte)



\*\*Sprint 3:\*\*

\- Backend API tamamlama — PDO prepared statement'lar, hata yönetimi, JSON yanıt formatı (Hafta 7)

\- Login + Rol Sistemi (admin/operatör) — bcrypt şifre hash'leme, PHP session yönetimi, `auth.php` (Hafta 7, Ali Eren ile birlikte)

\- Pozisyon Preset Tablosu ve UI (Hafta 7–8, Murat ile birlikte) → kapsam revizyonu sonrası hardcoded test butonlarına dönüştürüldü

\- Hareket Log ve Hata Log entegrasyonu (Hafta 8, Arda ile birlikte)



\*\*Ek roller:\*\*

\- SQL injection koruması (PDO prepared statement zorunluluğu)

\- API endpoint dokümantasyonu

\- Rol bazlı erişim kontrolü mantığı



\---



\## 📋 Tüm Ekip — Ortak Görevler



\- \*\*Sprint 2 Retrospektifi \& Kapanışı\*\* — Hafta 6

\- \*\*Uçtan Uca Entegrasyon Testi (E2E)\*\* — Hafta 8–9

\- \*\*Kullanıcı Kabul Testleri (UAT)\*\* — Hafta 9

\- \*\*Hata Düzeltme ve Optimizasyon\*\* — Hafta 8–9

\- Sprint planlama toplantıları (haftalık)

\- Code review



\---



\## 🚫 Kapsam Dışı (İleri Sürüm Notları)



Bu sürüm için kapsam dışı bırakılmış olan modüller:



| Modül | Durum | Sebep |

|---|---|---|

| Sipariş alma / Müşteri yönetimi / Ödeme | Kapsam dışı | Akademik proje hedefi: yalnızca robot kontrolü |

| Robotun kahve hazırlaması / makine entegrasyonu | Kapsam dışı | Mekanik karmaşıklık + zaman kısıtı |

| Sesli AI asistan (Jarvis) | Altyapısı mevcut, devre dışı | İleriki sürümde aktif edilecek |

| ElevenLabs TTS | Altyapısı mevcut, devre dışı | İleriki sürümde aktif edilecek |

| Pozisyon Preset tablo + UI | Hardcoded butonlara dönüştürüldü | FR-04 testleri yeterli |

| Eklem konfigürasyonu DB'den okuma | Hardcoded kaldı | Güvenlik önceliği — Pi limit'leri kod içinde |

| Joystick ile anlık yön kontrolü | Slider yeterli | Mevcut sürümde slider kullanılır |

| Çoklu robot kol yönetim paneli | İleriki sürüm | Şu an tek cihaz |

| Yük hücresi / sıcaklık / akım sensörleri | İleriki sürüm | Donanım eklemesi gerektirir |



\---



\## 🔗 İlgili Bağlantılar



\- \*\*GitHub Deposu:\*\* https://github.com/alierenonyil/robotkolsistemanalizi

\- \*\*Canlı Sistem:\*\* https://robocoffee.onyapp.com.tr

\- \*\*Jira Projesi:\*\* alierenonyil.atlassian.net (proje anahtarı `DEV`)

\- \*\*Miro Boardu:\*\* Backlog + Use Case + ER diyagramı

\- \*\*3D Tasarım Kaynağı:\*\* \[HowToMechatronics — Robot Arm](https://howtomechatronics.com/) (Cults3D)



\---



\## 🛡 Güvenlik Notları



\- Şifreler bcrypt ile hash'lenir (cost=10), düz metin olarak saklanmaz

\- Tüm SQL sorguları PDO \*\*prepared statement\*\* ile yapılır → SQL injection koruması

\- API endpoint'leri (`api.php`) ileri sürümde session kontrolü ile koruma altına alınacak

\- `db.php` içindeki kimlik bilgileri \*\*versiyon kontrolüne girmemelidir\*\* (production'da `.env` veya benzeri çözüm önerilir)

\- ch4/ch5 servoları yazılım katmanında \*\*80–100°\*\* ile sınırlandırılmıştır (mekanik koruma)



\---



\## 📜 Lisans / Akademik Bilgi



Bu proje \*\*İzmir Ekonomi Üniversitesi Bilgisayar Programcılığı\*\* programı kapsamında \*\*Sistem Analizi ve Tasarımı\*\* dersi için grup ödevi olarak geliştirilmiştir.



Akademik kullanım dışında kopyalama, dağıtım veya ticari kullanım için \*\*Onyıl Bilişim\*\* ile iletişime geçiniz.



\---



\## 📞 İletişim



Sorularınız ve katkılarınız için:



\- \*\*Proje sahibi:\*\* Ali Eren Onyıl — `alierenonyil@...`

\- \*\*Ticari ortak:\*\* Onyıl Bilişim — `info@onyil.com.tr`

\- \*\*GitHub Issues:\*\* https://github.com/alierenonyil/robotkolsistemanalizi/issues



\---



<p align="center">

&#x20; <i>"Web arayüzünden bir tıkla, bardak senin elinde."</i><br>

&#x20; <b>🤖 Robot Kol — 2026</b>

</p>

