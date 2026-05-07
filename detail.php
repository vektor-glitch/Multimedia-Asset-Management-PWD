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

$stmtdet = $conn->prepare("SELECT id_asset, serial_number, nama_alat, merk, status, jumlah, url_gambar FROM assets WHERE id_asset = ?");;
$stmtdet->bind_param("i", $id);
$stmtdet->execute();
$result = $stmtdet->get_result();

if ($result->num_rows === 0) {
    header("Location: index.php");
    exit;
}

$asset = $result->fetch_assoc();
$stmtdet->close();

$statusLower = strtolower($asset['status']);
if ($statusLower === 'tersedia') {
    $badgeClass = "bg-green-600 text-white";
} elseif ($statusLower === 'dipinjam') {
    $badgeClass = "bg-cyan-400 text-white";
} else {
    $badgeClass = "bg-yellow-400 text-black";
}

$idFormatted = '#' . str_pad($asset['id_asset'], 5, '0', STR_PAD_LEFT);

$defaultFoto = 'https://picsum.photos/seed/' . $asset['id_asset'] . '/800/600';
$foto = !empty($asset['url_gambar'])
    ? htmlspecialchars($asset['url_gambar'])
    : $defaultFoto;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Alat - <?= htmlspecialchars($asset['nama_alat']) ?> | MAM</title>
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


<header class="bg-[#1e1e2d] shadow-md border-b border-white/5 navbar-sticky">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
        <div class="flex items-center">
            <a href="#" class="font-sans text-2xl font-bold text-white tracking-wide">MAM.</a>
        </div>
    </div>
</header>

<div class="mx-auto max-w-lg px-4 py-8 sm:px-6 lg:px-8">

    <a href="index.php" class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-gray-800 transition-colors mb-6">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-4">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
        </svg>
        Kembali ke Dashboard
    </a>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">

        <div class="w-full h-64 sm:h-72 overflow-hidden bg-gray-100">
            <img
                src="<?= $foto ?>"
                alt="Foto <?= htmlspecialchars($asset['nama_alat']) ?>"
                class="w-full h-full object-cover"
                onerror="this.onerror=null; this.src='https://picsum.photos/seed/<?= $asset['id_asset'] ?>/800/600';"
            >
        </div>

        <div class="p-6 space-y-4">

            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-0.5">Serial Number</p>
                    <p class="text-xl font-bold text-gray-900"><?= htmlspecialchars($asset['serial_number']) ?></p>
                </div>
                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold <?= $badgeClass ?> whitespace-nowrap mt-1">
                    <?= htmlspecialchars($asset['status']) ?>
                </span>
            </div>

            <hr class="border-gray-100">

            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-0.5">Nama Asset / Model</p>
                <p class="text-lg font-bold text-gray-900"><?= htmlspecialchars($asset['nama_alat']) ?></p>
                <p class="text-sm text-gray-500 mt-1">Merk: <span class="font-semibold text-gray-700"><?= htmlspecialchars($asset['merk']) ?: '-' ?></span></p>
            </div>

            <hr class="border-black-100">

            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-0.5">Ketersediaan Stok</p>
                    <p class="text-lg font-bold text-gray-900"><?= htmlspecialchars($asset['jumlah']) ?> Unit</p>
                </div>
                <a href="edit.php?id=<?= $asset['id_asset'] ?>" class="inline-flex items-center gap-2 rounded-lg bg-[#1e1e2d] px-4 py-2.5 text-sm font-semibold text-white hover:bg-gray-700 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                    </svg>
                    Edit Data
                </a>
            </div>

        </div>

        <div class="border-t border-gray-100 px-6 py-3 bg-gray-50 text-center">
            <p class="text-xs text-gray-400">
                ID Aset: <span class="font-semibold"><?= $idFormatted ?></span> &nbsp;|&nbsp; Terdaftar dalam sistem MAM.
            </p>
        </div>

    </div>

</div>

</body>
</html>
