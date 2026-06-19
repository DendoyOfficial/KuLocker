<?php
/* ============================================================
   KuLocker — resend-otp.php
   Endpoint AJAX untuk kirim ulang kode OTP via SMS (Twilio)
   ============================================================ */

session_start();
require_once 'vendor/autoload.php';

use Twilio\Rest\Client;

header('Content-Type: application/json');

// Hanya terima POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method tidak diizinkan.']);
    exit();
}

// Cek session masih ada
if (empty($_SESSION['reg_data']['no_hp'])) {
    echo json_encode(['success' => false, 'message' => 'Sesi tidak ditemukan. Silakan daftar ulang.']);
    exit();
}

// Batasi kirim ulang — maksimal 3x per sesi
$_SESSION['otp_resend_count'] = ($_SESSION['otp_resend_count'] ?? 0) + 1;

if ($_SESSION['otp_resend_count'] > 3) {
    echo json_encode(['success' => false, 'message' => 'Batas kirim ulang tercapai. Silakan daftar ulang.']);
    exit();
}

// Generate OTP baru
$otp = rand(100000, 999999);
$_SESSION['otp_hp']         = $otp;
$_SESSION['otp_expired_at'] = time() + (5 * 60); // 5 menit

$no_hp = $_SESSION['reg_data']['no_hp'];

// Format ke +62
$no_hp_intl = '+62' . ltrim($no_hp, '0');

try {
    $twilio = new Client(
        getenv('TWILIO_SID'),    // simpan di .env, bukan hardcode
        getenv('TWILIO_TOKEN')
    );

    $twilio->messages->create(
        $no_hp_intl,
        [
            'from' => getenv('TWILIO_FROM'),
            'body' => "Kode OTP KuLocker kamu: $otp\nBerlaku 5 menit. Jangan bagikan ke siapapun."
        ]
    );

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Gagal mengirim SMS. Coba beberapa saat lagi.'
    ]);
}