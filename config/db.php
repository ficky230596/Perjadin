<?php
$host = 'localhost';
$db = 'db_perjadin';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}

// Fonnte Token - Ganti dengan token Anda
$fonnte_token = 'cUYc8QvuE29aQSHUad3R';  // Contoh: @123abc

function sendWaNotification($phone, $message, $token) {
    if (!$phone) return;  // Skip jika no HP kosong
    $url = 'https://api.fonnte.com/send';
    $data = [
        'target' => $phone,
        'message' => $message,
        'countryCode' => '62'  // Indonesia
    ];
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => http_build_query($data),
        CURLOPT_HTTPHEADER => ['Authorization: ' . $token],
    ]);
    $response = curl_exec($curl);
    curl_close($curl);
    // Optional: error_log($response);
}

/**
 * Ambil antrian pengajuan berdasarkan status, urutkan sesuai prioritas
 */
function getScheduledQueue(PDO $pdo, string $status): array {
    $stmt = $pdo->prepare("SELECT * FROM pengajuan WHERE status = ? ORDER BY prioritas_skor DESC, id ASC");
    $stmt->execute([$status]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
