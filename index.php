<?php 
session_start();

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit;
}

include "connection.php";

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Index</title>
    <link rel="stylesheet" href="./src/output.css?v=<?= time(); ?>">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindplus/elements@1" type="module"></script>
    <style>
        body { 
            background-color: #f1f5f9;
        }
    </style>
</head>
<body class="overflow-hidden">
<header class="bg-[#1e1e2d] shadow-md border-b border-white/5">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
        <div class="flex items-center">
            <a href="#" class="font-sans text-2xl font-bold text-white tracking-wide">MAM.</a>
        </div>

        <div class="flex items-center gap-6">
            <span class="text-sm font-medium text-gray-400 hidden sm:block">Admin Multimedia</span>
            <a href="logout.php" class="flex items-center gap-2 rounded-md border border-gray-600 px-4 py-2 text-sm font-medium text-gray-300 transition-colors hover:bg-gray-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-gray-500">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                </svg>
                Logout
            </a>
        </div>
    </div>
</header>
<br>
<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="font-sans text-3xl font-bold text-black dark:text-white">Inventaris Alat Multimedia</h1>
            <p class="mt-2 text-lg text-gray-500 dark:text-gray-400">Kelola stok kamera, lensa, dan aksesoris studio</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <button type="button" class="inline-flex items-center rounded-md bg-black px-4 py-2.5 text-sm font-semibold text-white shadow-md hover:bg-gray-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-900">
                <a href="tambah.php">+ Tambah Alat</a>
            </button>
        </div>
    </div>
</div>
<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 mt-6">
  <div class="mt-4 flow-root">
    <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
      <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
        <div class="overflow-hidden shadow-sm outline-1 outline-black/5 sm:rounded-lg dark:shadow-none dark:-outline-offset-1 dark:outline-white/10">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50/50">
              <tr>
                <th scope="col" class="py-3.5 px-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">NO</th>
                <th scope="col" class="px-3 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">SERIAL NUMBER</th>
                <th scope="col" class="px-3 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">NAMA ALAT</th>
                <th scope="col" class="px-3 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">MERK</th>
                <th scope="col" class="px-3 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">STATUS</th>
                <th scope="col" class="px-3 py-3.5 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">JUMLAH</th>
                <th scope="col" class="px-3 py-3.5 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">AKSI</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
    <?php
    $queryasset = "SELECT id_asset, serial_number, nama_alat, merk, status, jumlah FROM assets";
    $resultasset = $conn->query($queryasset);

    if ($resultasset && mysqli_num_rows($resultasset) > 0) {
        $no = 1;
        while ($row = mysqli_fetch_assoc($resultasset)) {
            $assid = htmlspecialchars($row['id_asset']);
            $assnum = htmlspecialchars($row['serial_number']);
            $assname = htmlspecialchars($row['nama_alat']);
            $assmerk = htmlspecialchars($row['merk']);
            $status = htmlspecialchars($row['status']);
            $jumlah = htmlspecialchars($row['jumlah']);

            $rowBg = "bg-white"; // Default (Tersedia)
            $badgeBg = "bg-emerald-600 text-white"; 
            
            $statusLower = strtolower($status);
            if ($statusLower === 'dipinjam') {
                $rowBg = "bg-cyan-100/50";
                $badgeBg = "bg-cyan-400 text-white";
            } elseif ($statusLower === 'maintenance') {
                $rowBg = "bg-orange-100/60";
                $badgeBg = "bg-yellow-400 text-black"; 
            }
            ?>

            <tr class="<?= $rowBg ?>">
                <td class="whitespace-nowrap px-3 py-4 text-sm font-medium text-gray-900 text-center"><?= $no++; ?></td>
                <td class="whitespace-nowrap px-3 py-4 text-sm font-bold text-gray-700"><?= $assnum; ?></td>
                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-700"><?= $assname; ?></td>
                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-700"><?= $assmerk; ?></td>
                
                <td class="whitespace-nowrap px-3 py-4 text-sm">
                    <span class="inline-flex items-center rounded-md px-2.5 py-1 text-xs font-semibold <?= $badgeBg ?>">
                        <?= $status; ?>
                    </span>
                </td>
                
                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-700 text-center"><?= $jumlah; ?></td>
                
                <td class="whitespace-nowrap px-3 py-4 text-sm font-medium">
                    <div class="flex items-center justify-center gap-2">
                        <a href="detail.php?id=<?= $assid ?>" class="rounded bg-indigo-100 p-1.5 text-indigo-600 hover:bg-indigo-200">
                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                        </a>
                        <a href="edit.php?id=<?= $assid ?>" class="rounded bg-orange-100 p-1.5 text-orange-600 hover:bg-orange-200">
                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" /></svg>
                        </a>
                        <a href="delete.php?id=<?= $assid ?>" class="rounded bg-red-100 p-1.5 text-red-600 hover:bg-red-200">
                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>
                        </a>
                    </div>
                </td>
            </tr>
            <?php
        }
    } else {
        echo '<tr><td colspan="7" class="py-6 text-center text-sm font-medium text-gray-500">Belum ada alat multimedia.</td></tr>';
    }
    mysqli_close($conn);
    ?>
</tbody>

          </table>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>