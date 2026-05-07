<?php
session_start();

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit;
}

include "connection.php";

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header("Location: index.php");
    exit;
}

// Ambil data asset yang akan diedit
$stmt = $conn->prepare("SELECT id_asset, serial_number, nama_alat, merk, status, jumlah, url_gambar FROM assets WHERE id_asset = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: index.php");
    exit;
}

$asset = $result->fetch_assoc();
$stmt->close();

$error = "";
$success = "";

// Proses update data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_alat = trim($_POST['nama_alat'] ?? '');
    $merk      = trim($_POST['merk'] ?? '');
    $status    = $_POST['status'] ?? '';
    $jumlah    = (int)($_POST['jumlah'] ?? 0);
    $url_gambar  = trim($_POST['url_gambar'] ?? '');

    if (empty($nama_alat) || empty($status)) {
        $error = "Nama Alat dan Status wajib diisi!";
    } elseif ($jumlah < 0) {
        $error = "Jumlah unit tidak boleh negatif!";
    } else {
        $stmtupdate = $conn->prepare("UPDATE assets SET nama_alat=?, merk=?, status=?, jumlah=?, url_gambar=? WHERE id_asset=?");
        $stmtupdate->bind_param("sssisi", $nama_alat, $merk, $status, $jumlah, $url_gambar, $id);
        if ($stmtupdate->execute()) {
            echo "<script>
                    alert('Data berhasil diperbarui!');
                    window.location.href = 'detail.php?id=" . $id . "';
                  </script>";
            exit;
        } else {
            $error = "Gagal memperbarui data: " . $conn->error;
        }
        $stmtupdate->close();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Asset - <?= htmlspecialchars($asset['nama_alat']) ?> | MAM</title>
    <link rel="stylesheet" href="./src/output.css?v=<?= time(); ?>">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindplus/elements@1" type="module"></script>
    <style>
        body {
            background-color: #f1f5f9;
        }
        .navbar-sticky {
            position: sticky; top: 0; z-index: 50;
        }
    </style>
</head>
<body>

<?php if ($error): ?>
    <script>alert("<?= addslashes($error) ?>");</script>
<?php endif; ?>

<header class="bg-[#1e1e2d] shadow-md border-b border-white/5 navbar-sticky">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
        <div class="flex items-center">
            <a href="#" class="font-sans text-2xl font-bold text-white tracking-wide">MAM.</a>
        </div>
    </div>
</header>

<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

    <a href="detail.php?id=<?= $asset['id_asset'] ?>" class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-gray-800 transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-4">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
        </svg>
        Batal &amp; Kembali
    </a>

    <h1 class="mt-4 font-sans text-3xl font-bold text-gray-900">Perbarui Informasi Asset</h1>
    <p class="mt-2 text-base text-gray-500">Lakukan perubahan pada detail perangkat untuk memastikan data inventaris tetap akurat.</p>

    <div class="mt-6">
        <div class="rounded-xl bg-white shadow-sm border border-gray-200">
            <div class="px-6 py-8 sm:p-10">

                <form method="POST" action="">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

                        <!-- Kolom Kiri: Form -->
                        <div class="lg:col-span-7 space-y-5">

                            <!-- Serial Number (readonly) -->
                            <div>
                                <label for="serial_number" class="block text-sm font-semibold text-gray-700 mb-1">Serial Number</label>
                                <input id="serial_number" type="text" name="serial_number"
                                    value="<?= htmlspecialchars($asset['serial_number']) ?>"
                                    readonly
                                    class="w-full border border-gray-200 rounded-lg p-2.5 bg-gray-50 text-gray-500 text-sm cursor-not-allowed outline-none">
                                <p class="mt-1 text-xs text-rose-500">Serial Number tidak dapat diubah untuk menjaga integritas data.</p>
                            </div>

                            <!-- Nama Alat -->
                            <div>
                                <label for="nama_alat" class="block text-sm font-semibold text-gray-700 mb-1">Nama Alat</label>
                                <input id="nama_alat" type="text" name="nama_alat"
                                    value="<?= htmlspecialchars($asset['nama_alat']) ?>"
                                    class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition text-sm"
                                    placeholder="Contoh: Sony Alpha a7 III Mirrorless">
                            </div>

                            <!-- Merk -->
                            <div>
                                <label for="merk" class="block text-sm font-semibold text-gray-700 mb-1">Merk</label>
                                <input id="merk" type="text" name="merk"
                                    value="<?= htmlspecialchars($asset['merk'] ?? '') ?>"
                                    class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition text-sm"
                                    placeholder="Contoh: Sony">
                            </div>

                            <!-- Status & Jumlah -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="status" class="block text-sm font-semibold text-gray-700 mb-1">Status Saat Ini</label>
                                    <select id="status" name="status"
                                        class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition bg-white text-gray-700 text-sm">
                                        <option value="Tersedia"   <?= $asset['status'] === 'Tersedia'    ? 'selected' : '' ?>>Tersedia</option>
                                        <option value="Dipinjam"   <?= $asset['status'] === 'Dipinjam'    ? 'selected' : '' ?>>Dipinjam</option>
                                        <option value="Maintenance"<?= $asset['status'] === 'Maintenance' ? 'selected' : '' ?>>Maintenance</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="jumlah" class="block text-sm font-semibold text-gray-700 mb-1">Jumlah Unit</label>
                                    <input id="jumlah" type="number" name="jumlah"
                                        value="<?= htmlspecialchars($asset['jumlah']) ?>"
                                        min="0"
                                        class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition text-sm">
                                </div>
                            </div>

                            <!-- URL Foto -->
                            <div>
                                <label for="foto_url" class="block text-sm font-semibold text-gray-700 mb-1">
                                    Update URL Foto <span class="font-normal text-gray-400">(Opsional)</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244" />
                                        </svg>
                                    </div>
                                    <input id="url_gambar" type="url" name="url_gambar"
                                        value="<?= htmlspecialchars($asset['url_gambar'] ?? '') ?>"
                                        class="w-full pl-9 border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition text-sm"
                                        placeholder="https://example.com/camera.jpg">
                                </div>
                            </div>

                            <!-- Tombol Simpan -->
                            <div class="pt-2">
                                <button type="submit"
                                    class="w-full flex items-center justify-center gap-2 bg-[#1e1e2d] hover:bg-gray-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors focus:outline-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                                    </svg>
                                    Simpan Perubahan Data
                                </button>
                            </div>

                        </div>

                        <!-- Kolom Kanan: Panel Info Mode Penyuntingan -->
                        <div class="lg:col-span-5">
                            <div class="bg-slate-50 rounded-xl p-6 h-full flex flex-col gap-5">

                                <div>
                                    <h3 class="text-base font-bold text-slate-800 flex items-center gap-2 mb-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-slate-600">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                                        </svg>
                                        Mode Penyuntingan
                                    </h3>
                                    <p class="text-sm text-slate-500">Anda sedang mengubah data asset. Pastikan untuk:</p>
                                </div>

                                <ul class="space-y-3">
                                    <li class="flex items-start gap-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5">
                                            <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm13.36-1.814a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z" clip-rule="evenodd" />
                                        </svg>
                                        <p class="text-sm text-slate-600">Memverifikasi <span class="font-semibold text-slate-800">status terbaru</span> (apakah alat baru saja kembali atau masuk servis).</p>
                                    </li>
                                    <li class="flex items-start gap-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5">
                                            <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm13.36-1.814a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z" clip-rule="evenodd" />
                                        </svg>
                                        <p class="text-sm text-slate-600">Memastikan <span class="font-semibold text-slate-800">jumlah unit</span> sudah sesuai dengan stok fisik di lemari penyimpanan.</p>
                                    </li>
                                    <li class="flex items-start gap-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5">
                                            <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm13.36-1.814a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z" clip-rule="evenodd" />
                                        </svg>
                                        <p class="text-sm text-slate-600">Memperbarui <span class="font-semibold text-slate-800">URL foto</span> jika terdapat kerusakan fisik yang perlu didokumentasikan.</p>
                                    </li>
                                </ul>

                                <!-- Warning Box -->
                                <div class="mt-auto bg-amber-50 border border-amber-200 rounded-lg p-4 flex items-start gap-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5">
                                        <path fill-rule="evenodd" d="M9.401 3.003c1.155-2 4.043-2 5.197 0l7.355 12.748c1.154 2-.29 4.5-2.599 4.5H4.645c-2.309 0-3.752-2.5-2.598-4.5L9.4 3.003ZM12 8.25a.75.75 0 0 1 .75.75v3.75a.75.75 0 0 1-1.5 0V9a.75.75 0 0 1 .75-.75Zm0 8.25a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" clip-rule="evenodd" />
                                    </svg>
                                    <p class="text-sm text-amber-800">Perubahan ini akan langsung berdampak pada laporan ketersediaan alat di Dashboard.</p>
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
