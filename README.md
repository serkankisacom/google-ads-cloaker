# Google Ads Cloaker

Google Ads reklam politika ihlallerini ortadan kaldırmak amacıyla geliştirilmiş, PHP 7.0 ve üzeri sürümlerle uyumlu web sitesi projesi.

---

# Gereksinimler

- PHP 7.0 veya üzeri
- cURL aktif olmalı
- Apache / Nginx sunucu

---

# Dosya Yapısı

Aşağıdaki klasör yapısını oluşturun:

/site-klasoru  
├── index.php  
├── themes-nakliye  
│ └── index.html  
└── log  

---

# Kurulum

## Dosyaları Sunucunuza Yükleyin

Script dosyasını aşağıdaki konuma yükleyin:


# Script Ayarları

Kodun üst kısmında bulunan alanları düzenleyin:

$hedef_url   = 'Yonlendirelecek URL';
$tema_dizini = 'themes-nakliye';
$log_dizini  = 'log';
$hedef_ulke  = 'TR';

---

# Ayar Açıklamaları

## hedef_url

Ziyaretçilerin yönlendirileceği adres

$hedef_url = 'https://site.com/landing';

---

## tema_dizini

Tema klasörünün adı

$tema_dizini = 'themes-nakliye';

---

## log_dizini

Log klasörü

$log_dizini = 'log';

---

## hedef_ulke

Yönlendirilecek ülke kodu

$hedef_ulke = 'TR';

---

# Oluşturulan Log Dosyaları

Script aşağıdaki dosyaları oluşturur:

log/google_botlar.txt
log/yonlendirilenler.txt
log/yonlendirilmeyenler.txt
log/api_hatalar.txt

---

# Çalışma Mantığı

Script aşağıdaki sırayla çalışır:

1. Ziyaretçi IP adresi alınır
2. Ülke tespiti yapılır
3. Google bot kontrolü yapılır
4. Koşullara göre işlem yapılır

---

# Yönlendirme Kuralları

| Durum | Sonuç |
|------|------|
| Google Bot | Tema gösterilir |
| Türkiye Kullanıcı | Yönlendirilir |
| Diğer Ülke | Tema gösterilir |
| API Hatası | Tema gösterilir |

---

# Google Ads Parametreleri

Script aşağıdaki parametreleri otomatik taşır:

gclid
gbraid
wbraid
utm_source
utm_medium
utm_campaign
utm_term
utm_content

---

# Kurulum Testi

Kurulum sonrası test edin:

1. Siteye giriş yapın
2. Log klasörünü kontrol edin
3. TR IP ile test edin
4. VPN ile farklı ülke test edin
5. Google Bot tespiti için pagespeed insights kullanın.
   
---

- Cloudflare uyumludur
- Google bot dostudur
- SEO güvenlidir
- gclid parametreleri korunur
