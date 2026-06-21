<?php
// ==========================================
// BAGIAN PHP: MEMPROSES DATA FORM
// ==========================================
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Menangkap data dari form
    $fullName = htmlspecialchars($_POST['fullName']);
    $email    = htmlspecialchars($_POST['email']);
    $category = htmlspecialchars($_POST['category']);
    $details  = htmlspecialchars($_POST['details']);

    // Menerjemahkan nilai kategori
    $kategori_teks = "";
    switch($category) {
        case 'product': $kategori_teks = "Loker rusak"; break;
        case 'delivery': $kategori_teks = "Loker tidak bisa dibuka"; break;
        case 'service': $kategori_teks = "Pelayanan Tidak Memuaskan"; break;
        case 'website': $kategori_teks = "Kendala Website / Aplikasi"; break;
        case 'other': $kategori_teks = "Lainnya"; break;
        default: $kategori_teks = "Kategori tidak diketahui"; break;
    }

    // Generate nomor tiket acak
    $ticketNumber = "#TKT-" . rand(1000, 9999);

    // Menampilkan halaman sukses (menggantikan form)
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Laporan Terkirim</title>
        <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    </head>
    <body style="background-color: #fafafa; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; font-family: 'DM Sans', sans-serif;">
        <div style="width: 100%; max-width: 600px; margin: 20px; text-align: center; background: #fff; padding: 50px 40px; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); border-top: 6px solid #fbc531;">
            <div style="width: 80px; height: 80px; background: #fbc531; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px auto;">
                <svg width="40" height="40" fill="none" stroke="#fff" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <h1 style="color: #111; margin-bottom: 10px;">Berhasil Terkirim!</h1>
            <h3 style="color: #333; margin-bottom: 5px; font-weight: 500;">Terima kasih, <?= $fullName ?></h3>
            <p style="color: #666; line-height: 1.5;">Laporan Anda dengan kategori <strong><?= $kategori_teks ?></strong> telah kami terima dan akan segera diproses.</p>
            
            <div style="background: #f8f9fa; padding: 20px; margin: 30px 0; border-radius: 12px; border: 1px dashed #ccc;">
                <p style="margin: 0 0 5px 0; font-size: 14px; color: #555;">Nomor Tiket Laporan Anda:</p>
                <strong style="font-size: 28px; color: #111; letter-spacing: 1px;"><?= $ticketNumber ?></strong>
            </div>

            <p style="font-size: 13px; color: #888; margin-bottom: 5px;">Tembusan dan tindak lanjut akan dikirim ke email: <b><?= $email ?></b></p>
            <p style="font-size: 13px; color: #888; margin-bottom: 30px; font-style: italic;">"<?= $details ?>"</p>

            <a href="" style="display: inline-block; padding: 14px 30px; background: #fbc531; color: #111; text-decoration: none; border-radius: 30px; font-weight: bold; transition: 0.3s;">Kirim Laporan Baru</a>
        </div>
    </body>
    </html>
    <?php
    // Hentikan eksekusi script agar form di bawah tidak ikut dimuat
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Layanan Pengaduan Pelanggan</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&family=DM+Serif+Display&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- ========================================== -->
    <!-- BAGIAN CSS -->
    <!-- ========================================== -->
    <style>
        :root {
            --font-display: "DM Serif Display", Georgia, serif;
            --font-body: "DM Sans", system-ui, sans-serif;
            --radius-md: 14px;
            --shadow-focus: 0 0 0 3px rgba(225, 177, 44, 0.3);
            --shadow-btn: 0 8px 28px rgba(212, 175, 55, 0.35);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: var(--font-body);
        }

        body {
            background-color: #fafafa;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 0;
        }

        .back-btn {
            position: fixed;
            top: 1.5rem;
            left: 1.75rem;
            z-index: 10;
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.6);
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            color: #333;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            transition: background 0.2s, transform 0.2s, box-shadow 0.2s;
        }

        .back-btn:hover {
            background: rgba(255, 255, 255, 0.9);
            transform: translateX(-2px);
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.12);
        }

        .background-container {
            width: 100vw;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }

        .main-container {
            background-color: #ffffff;
            display: flex;
            width: 100%;
            height: 100%;
            border-radius: 0;
            overflow: hidden;
            box-shadow: none;
        }

        .card-left {
            background-color: #fbc531;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 80px;
            position: relative;
            color: #333;
        }

        .card-left h1 {
            font-family: var(--font-display);
            font-size: 48px;
            line-height: 1.2;
            margin-top: 20px;
        }

        .card-left p {
            margin-top: 15px;
            font-size: 16px;
            opacity: 0.9;
            max-width: 80%;
        }

        .overlay-text {
            position: absolute;
            bottom: 40px;
            left: 80px;
            font-size: 14px;
            font-weight: 500;
        }

        .card-right {
            background-color: #ffffff;
            flex: 1.2;
            padding: 50px 80px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-title {
            font-size: 32px;
            margin-bottom: 40px;
            font-family: var(--font-display);
            text-align: left;
            color: #1a1a1a;
        }

        .input-group {
            margin-bottom: 25px;
        }

        .input-group label {
            display: block;
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 8px;
            color: #555;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .input-group input, 
        .input-group select, 
        .input-group textarea {
            width: 100%;
            padding: 16px 20px;
            background-color: #f0f0f0;
            border: 2px solid transparent;
            border-radius: 12px;
            outline: none;
            font-size: 15px;
            color: #333;
            transition: all 0.3s ease;
            appearance: none;
        }

        .input-group textarea {
            border-radius: 12px;
            resize: vertical;
            min-height: 120px;
        }

        .input-group input:focus, 
        .input-group select:focus, 
        .input-group textarea:focus {
            background-color: #ffffff;
            border-color: #fbc531;
            box-shadow: var(--shadow-focus);
        }

        .select-wrapper {
            position: relative;
        }
        .select-wrapper::after {
            content: '▼';
            font-size: 10px;
            color: #777;
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
        }

        .btn-primary {
            width: 100%;
            padding: 18px;
            background-color: #fbc531;
            color: #111;
            border: none;
            border-radius: 12px;
            font-weight: bold;
            font-size: 16px;
            cursor: pointer;
            margin-top: 15px;
            transition: all 0.3s ease;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }

        .btn-primary:hover {
            background-color: #e1b12c;
            transform: translateY(-2px);
            box-shadow: var(--shadow-btn);
        }

        @media (max-width: 768px) {
            .main-container {
                flex-direction: column;
            }
            .card-left {
                padding: 40px;
                min-height: 300px;
                justify-content: flex-start;
            }
            .card-left p {
                max-width: 100%;
            }
            .overlay-text {
                bottom: 20px;
                left: 40px;
            }
            .card-right {
                padding: 40px 20px;
            }
            .form-title {
                font-size: 24px;
                text-align: center;
            }
        }
    </style>
</head>
<body>

    <a href="#" class="back-btn" title="Kembali">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M19 12H5M12 19l-7-7 7-7"/>
        </svg>
    </a>

    <div class="background-container">
        <div class="main-container">
            
            <!-- Kolom Kiri -->
            <div class="card-left">
                <div>
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
                    </svg>
                    <h1>Sampaikan<br>Suara Anda</h1>
                    <p>Kami sangat menghargai setiap masukan dan keluhan Anda. Beritahu kami agar kami bisa memberikan layanan yang lebih baik ke depannya.</p>
                </div>
                <div class="overlay-text">
                    Layanan Pelanggan 24/7
                </div>
            </div>

            <!-- Kolom Kanan (Form) -->
            <div class="card-right">
                <h2 class="form-title">Form Keluhan</h2>
                
                <!-- action dikosongkan agar mengirim data ke file ini sendiri -->
                <form action="" method="POST">
                    <div class="flex flex-col sm:flex-row gap-0 sm:gap-4">
                        <div class="input-group w-full">
                            <label for="fullName">Nama Lengkap</label>
                            <input type="text" id="fullName" name="fullName" required placeholder="Budi Santoso">
                        </div>
                        <div class="input-group w-full">
                            <label for="email">Alamat Email</label>
                            <input type="email" id="email" name="email" required placeholder="budi@email.com">
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="category">Kategori Keluhan</label>
                        <div class="select-wrapper">
                            <select id="category" name="category" required>
                                <option value="" disabled selected>Pilih Kategori...</option>
                                <option value="product">Loker rusak</option>
                                <option value="delivery">Loker tidak bisa dibuka</option>
                                <option value="service">Pelayanan Tidak Memuaskan</option>
                                <option value="website">Kendala Website / Aplikasi</option>
                                <option value="other">Lainnya</option>
                            </select>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="details">Detail Keluhan</label>
                        <textarea id="details" name="details" required placeholder="Jelaskan secara detail kendala yang Anda alami beserta alamat loker"></textarea>
                    </div>

                    <button type="submit" class="btn-primary">
                        Kirim Keluhan
                    </button>
                </form>
            </div>

        </div>
    </div>

</body>
</html>