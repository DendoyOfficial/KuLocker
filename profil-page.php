<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KuLocker - Profile Page</title>
    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#f8fafc] min-h-screen font-sans antialiased">

    <!-- 1. TOP NAVBAR -->
    <nav class="bg-white border-b border-slate-200 px-8 py-3 flex justify-between items-center sticky top-0 z-50">
        <h1 class="text-xl font-bold text-slate-800 tracking-tight">Profile</h1>
        <div class="flex items-center gap-5">
            <!-- Info User di Pojok Kanan -->
            <div class="flex items-center gap-2 cursor-pointer group">
                
                <span class="text-sm font-semibold text-slate-700 group-hover:text-slate-900">Budi Santoso</span>

                <img src="https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=80&h=80" class="w-8 h-8 rounded-full object-cover border border-slate-200" alt="Avatar">

                <span class="text-xs text-slate-400 group-hover:text-slate-600">▼</span>
            </div>
        </div>
    </nav>

    <!-- CONTAINER UTAMA -->
    <div class="max-w-7xl mx-auto p-4 md:p-6 space-y-6">
        
        <!-- 2. BLUE BANNER SECTION -->
        <div class="relative bg-gradient-to-r from-blue-600 to-blue-700 h-44 rounded-xl overflow-hidden shadow-sm flex items-end justify-end p-4">
            <!-- Pola Geometris Abstrak Dummy Backgroud -->
            <div class="absolute inset-0 opacity-15 bg-[linear-gradient(45deg,#fff_25%,transparent_25%,transparent_75%,#fff_75%,#fff),linear-gradient(45deg,#fff_25%,transparent_25%,transparent_75%,#fff_75%,#fff)] bg-[size:30px_30px] bg-[position:0_0,15px_15px]"></div>
            
            <button class="relative z-10 bg-white/10 hover:bg-white/20 backdrop-blur-md text-white text-xs font-semibold py-2 px-4 rounded-lg border border-white/20 flex items-center gap-2 transition duration-200">
                📷 Change Cover
            </button>
        </div>

        <!-- 3. DUA KOLOM UTAMA (GRID TATA LETAK) -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 -mt-14 relative z-20 px-2 md:px-4">
            
            <!-- KOLOM KIRI: KARTU RINGKASAN PROFIL -->
            <div class="lg:col-span-1 bg-white border border-slate-200 rounded-xl shadow-sm p-6 flex flex-col items-center text-center h-fit">
                <!-- Foto Profil dengan Icon Kamera -->
                <div class="relative w-28 h-28 mb-4">
                    <img src="https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=200&h=200" class="w-full h-full rounded-full object-cover border-4 border-white shadow-md">
                    <button class="absolute bottom-1 right-1 bg-blue-600 text-white p-1.5 rounded-full text-xs shadow-md border-2 border-white hover:bg-blue-700 transition" title="Ubah Foto">
                        📷
                    </button>
                </div>
                
                <h2 class="text-base font-bold text-slate-800">Budi Santoso</h2>
                <p class="text-xs text-slate-500 mb-6">Mahasiswa, Universitas Terbuka</p>

                <!-- Statistik Data Sampingan KuLocker -->
                <div class="w-full border-t border-b border-slate-100 py-3 space-y-3 text-left text-xs text-slate-600">
                    <div class="flex justify-between items-center">
                        <span class="flex items-center gap-2">🪙 Kredit Poin</span>
                        <span class="font-bold text-amber-500">120</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="flex items-center gap-2">📁 Berkas Tersimpan</span>
                        <span class="font-bold text-emerald-500">24</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="flex items-center gap-2">💾 Ruang Tersedia</span>
                        <span class="font-bold text-slate-700">1.5 GB</span>
                    </div>
                </div>

                <!-- Akses Publik Profil -->
                <button class="w-full mt-4 border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-semibold py-2 px-4 rounded-lg transition duration-200">
                    View Public Profile
                </button>
                <div class="w-full mt-3 flex items-center border border-slate-200 rounded-lg p-2 bg-slate-50">
                    <input type="text" readonly value="https://app.kulocker.com/budi..." class="bg-transparent text-[11px] text-slate-400 w-full focus:outline-none select-all font-mono">
                    <button class="text-slate-400 hover:text-slate-600 text-xs pl-1" title="Salin Tautan">📋</button>
                </div>
            </div>

            <!-- KOLOM KANAN: FORM SETTINGS UTAMA -->
            <div class="lg:col-span-3 bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden flex flex-col">
                <!-- Navigasi Menu Tab Atas -->
                <div class="flex border-b border-slate-200 px-6 overflow-x-auto bg-slate-50/50 scrollbar-none">
                    <button type="button" class="border-b-2 border-blue-600 text-blue-600 text-xs font-bold py-4 px-4 whitespace-nowrap">Profil Saya</button>
                    <button type="button" class="text-slate-500 hover:text-slate-700 text-xs font-medium py-4 px-4 whitespace-nowrap">Keamanan</button>
                    <button type="button" class="text-slate-500 hover:text-slate-700 text-xs font-medium py-4 px-4 whitespace-nowrap">Penyimpanan</button>
                    <button type="button" class="text-slate-500 hover:text-slate-700 text-xs font-medium py-4 px-4 whitespace-nowrap">Tagihan</button>
                    <button type="button" class="text-slate-500 hover:text-slate-700 text-xs font-medium py-4 px-4 whitespace-nowrap">Notifikasi</button>
                </div>

                <!-- Area Formulir Pengaturan -->
                <form class="p-6 space-y-5 flex-1">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        
                        <!-- 1. Nama Lengkap -->
                        <div class="flex flex-col gap-1.5">
                            <label for="nama" class="text-xs font-bold text-slate-700 uppercase tracking-wider">Nama Lengkap</label>
                            <input type="text" id="nama" value="Budi Santoso" required
                                class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm bg-white text-slate-800
                                       focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition duration-200">
                        </div>

                        <!-- 2. NIM -->
                        <div class="flex flex-col gap-1.5">
                            <label for="nim" class="text-xs font-bold text-slate-700 uppercase tracking-wider">NIM</label>
                            <input type="text" id="nim" value="12345678" required
                                class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm bg-white text-slate-800
                                       focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition duration-200">
                        </div>

                        <!-- 3. Email -->
                        <div class="flex flex-col gap-1.5">
                            <label for="email" class="text-xs font-bold text-slate-700 uppercase tracking-wider">Email</label>
                            <input type="email" id="email" value="budi.santoso@email.com" required
                                class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm bg-white text-slate-800
                                       focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition duration-200">
                        </div>

                        <!-- 4. No. Telepon -->
                        <div class="flex flex-col gap-1.5">
                            <label for="phone" class="text-xs font-bold text-slate-700 uppercase tracking-wider">No. Telepon</label>
                            <input type="tel" id="phone" value="+628120000000" required
                                class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm bg-white text-slate-800
                                       focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition duration-200">
                        </div>

                        <!-- 5. Alamat Lengkap (Full-Width Row) -->
                        <div class="flex flex-col gap-1.5 md:col-span-2">
                            <label for="alamat" class="text-xs font-bold text-slate-700 uppercase tracking-wider">Alamat Lengkap</label>
                            <textarea id="alamat" rows="4" required
                                class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm bg-white text-slate-800 resize-none
                                       focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition duration-200">Jl. Jenderal Sudirman No. 123, Blok A2, Jakarta Selatan, 12190</textarea>
                        </div>
                    </div>

                    <!-- Bagian Tombol Aksi Bawah -->
                    <div class="pt-4 border-t border-slate-100 flex justify-start">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm py-2 px-6 rounded-lg shadow-sm transition duration-200 active:scale-95 flex items-center gap-2">
                            <span>🔄</span> Perbarui
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>

</body>
</html>