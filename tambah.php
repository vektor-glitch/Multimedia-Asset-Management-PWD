<?php 
session_start();

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit;
}


include "connection.php";
$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $serial_number = trim($_POST['serial_number']);
    $nama_alat = trim($_POST['nama_alat']);
    $merk = trim($_POST['merk']);
    $status = $_POST['status'] ?? '';
    $jumlah = (int)$_POST['jumlah'];
    $url_gambar = trim($_POST['url_gambar']);

    if (empty($serial_number) || empty($nama_alat) || empty($status)) {
        $error = "Serial Number, Nama Alat, dan Status wajib diisi!";
    } else {
        $queryadd = "INSERT INTO assets (serial_number, nama_alat, merk, status, jumlah, url_gambar) VALUES (?, ?, ?, ?, ?, ?)";
        $stmtadd = $conn->prepare($queryadd);
        $stmtadd->bind_param("ssssis", $serial_number, $nama_alat, $merk, $status, $jumlah, $url_gambar);
        if ($stmtadd->execute()) {
            echo "<script>
                    alert('Data alat multimedia berhasil disimpan!');
                    window.location.href = 'index.php';
                  </script>";
            exit;
        } else {
            $error = "Gagal menyimpan data: " . $conn->error;
        }
        $stmtadd->close();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Alat Multimedia - MAM</title>
    <link rel="stylesheet" href="./src/output.css?v=<?= time(); ?>">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindplus/elements@1" type="module"></script>
    <style>
        body { 
            background-color: #f1f5f9;
        }
        .navbar-sticky {
            position: sticky; top: 0; z-index: 50;
        }
        .badge-black {
            background-color: #111827; color: #fff; font-size: 0.7rem; font-weight: 700; padding: 2px 7px; border-radius: 4px;
        }
        .tip-box {
            background-color: #dbeafe; border: 1px solid #bfdbfe; border-radius: 0.5rem; padding: 0.75rem; display: flex; gap: 0.75rem;
        }
        .tip-box svg {
            color: #3b82f6; flex-shrink: 0; margin-top: 2px;
        }
        .tip-box p {
            color: #1e3a5f; font-size: 0.875rem;
        }
    </style>
</head>
<body>

<?php if($error): ?>
    <script>
        alert("<?php echo $error; ?>");
    </script>
<?php endif; ?>

<header class="bg-[#1e1e2d] shadow-md border-b border-white/5 navbar-sticky">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
        <div class="flex items-center">
            <a href="#" class="font-sans text-2xl font-bold text-white tracking-wide">MAM.</a>
        </div>
    </div>
</header>

<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

    <a href="index.php" class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-gray-800 transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-4">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
        </svg>
        Kembali ke Dashboard
    </a>

    <h1 class="mt-4 font-sans text-3xl font-bold text-gray-900">Registrasi Alat Multimedia</h1>
    <p class="mt-2 text-base text-gray-500">Tambahkan unit kamera, lensa, atau aksesoris baru ke dalam inventaris.</p>

    <div class="mt-6">
        <div class="rounded-xl bg-white shadow-sm border border-gray-200">
            <div class="px-6 py-8 sm:p-10">

                <form method="POST" action="">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

                        <!-- Kolom Kiri: Form Input -->
                        <div class="lg:col-span-7 space-y-5">

                            <div>
                                <label for="serial_number" class="block text-sm font-semibold text-gray-700 mb-1">Serial Number</label>
                                <input id="serial_number" type="text" name="serial_number"
                                    class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition text-sm"
                                    placeholder="Contoh: CAM-SONY-001">
                            </div>

                            <div>
                                <label for="nama_alat" class="block text-sm font-semibold text-gray-700 mb-1">Nama Alat</label>
                                <input id="nama_alat" type="text" name="nama_alat"
                                    class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition text-sm"
                                    placeholder="Contoh: Sony Alpha a7 III Mirrorless">
                            </div>

                            <div>
                                <label for="merk" class="block text-sm font-semibold text-gray-700 mb-1">Merk</label>
                                <input id="merk" type="text" name="merk"
                                    class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition text-sm"
                                    placeholder="Contoh: Sony">
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="status" class="block text-sm font-semibold text-gray-700 mb-1">Status Awal</label>
                                    <select id="status" name="status"
                                        class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition bg-white text-gray-700 text-sm">
                                        <option value="" disabled selected>Pilih Status</option>
                                        <option value="Tersedia">Tersedia</option>
                                        <option value="Dipinjam">Dipinjam</option>
                                        <option value="Maintenance">Maintenance</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="jumlah" class="block text-sm font-semibold text-gray-700 mb-1">Jumlah Unit</label>
                                    <input id="jumlah" type="number" name="jumlah" value="1" min="1"
                                        class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition text-sm">
                                </div>
                            </div>

                            <div>
                                <label for="url_gambar" class="block text-sm font-semibold text-gray-700 mb-1">
                                    Link Foto Perangkat (URL)
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                        </svg>
                                    </div>
                                    <input id="url_gambar" type="url" name="url_gambar"
                                        class="w-full pl-10 border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition text-sm"
                                        placeholder="https://example.com/camera.jpg">
                                </div>
                                <p class="mt-1.5 text-xs text-gray-400">Gunakan URL gambar dari internet (Unsplash/Imgur).</p>
                            </div>

                            <div class="pt-2">
                                <button type="submit"
                                    class="w-full flex items-center justify-center gap-2 bg-[#1e1e2d] hover:bg-gray-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors focus:outline-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0 3 3m-3-3-3 3M6.75 19.5a4.5 4.5 0 0 1-1.41-8.775 5.25 5.25 0 0 1 10.233-2.33 3 3 0 0 1 3.758 3.848A3.752 3.752 0 0 1 18 19.5H6.75Z" />
                                    </svg>
                                    Simpan Alat Multimedia
                                </button>
                            </div>

                        </div>

                        <div class="lg:col-span-5">
                            <div class="bg-slate-50 rounded-xl p-6 h-full flex flex-col gap-5">

                                <div>
                                    <h3 class="text-base font-bold text-slate-800 flex items-center gap-2 mb-1">
                                        <svg class="w-5 h-5 text-slate-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                        </svg>
                                        Penomoran Asset
                                    </h3>
                                    <p class="text-sm text-slate-500">
                                        Format Serial Number (SN) untuk peralatan multimedia lab:
                                    </p>
                                </div>

                                <div class="space-y-4">
                                    <div>
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="badge-black">CAM</span>
                                            <span class="text-sm font-semibold text-slate-800">Kamera (Body/Kit)</span>
                                        </div>
                                        <p class="text-xs font-mono mb-0.5" style="color:#e11d48;">CAM-[MERK]-[NOMOR]</p>
                                        <p class="text-xs text-slate-400">Contoh: SN-CAM-SONY-01</p>
                                    </div>
                                    <!-- LNS -->
                                    <div>
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="badge-black">LNS</span>
                                            <span class="text-sm font-semibold text-slate-800">Lensa &amp; Optik</span>
                                        </div>
                                        <p class="text-xs font-mono mb-0.5" style="color:#e11d48;">LNS-[MERK]-[JARAK]</p>
                                        <p class="text-xs text-slate-400">Contoh: SN-LNS-CAN-50MM</p>
                                    </div>
                                    <!-- DRN -->
                                    <div>
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="badge-black">DRN</span>
                                            <span class="text-sm font-semibold text-slate-800">Drone &amp; Gimbal</span>
                                        </div>
                                        <p class="text-xs font-mono mb-0.5" style="color:#e11d48;">DRN-[MERK]-[NOMOR]</p>
                                        <p class="text-xs text-slate-400">Contoh: SN-DRN-DJI-05</p>
                                    </div>
                                </div>

                                <div class="tip-box">
                                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M12 2a7 7 0 0 1 5.292 11.584c-.84.988-1.292 2.088-1.292 3.166V17a1 1 0 0 1-1 1h-6a1 1 0 0 1-1-1v-.25c0-1.078-.452-2.178-1.292-3.166A7 7 0 0 1 12 2Z" />
                                        <path d="M9 19h6v1a1 1 0 0 1-1 1h-4a1 1 0 0 1-1-1v-1Z" />
                                    </svg>
                                    <p>Pastikan SN unik untuk setiap unit agar pelacakan peminjaman lebih akurat.</p>
                                </div>

                            </div>
                        </div>

                    </div>
                </form>

            </div>
        </div>
    </div>

</div>
</body>
</html>