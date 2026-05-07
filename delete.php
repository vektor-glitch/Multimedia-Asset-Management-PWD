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

// Ambil nama asset untuk ditampilkan di konfirmasi
$stmt = $conn->prepare("SELECT nama_alat FROM assets WHERE id_asset = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: index.php");
    exit;
}

$asset = $result->fetch_assoc();
$stmt->close();

$namaAlat = addslashes($asset['nama_alat']);

// Jika user klik OK di konfirmasi, lakukan delete
if (isset($_GET['confirm']) && $_GET['confirm'] === 'yes') {
    $stmtdel = $conn->prepare("DELETE FROM assets WHERE id_asset = ?");
    $stmtdel->bind_param("i", $id);
    $stmtdel->execute();
    $stmtdel->close();
    $conn->close();

    echo "<script>
        alert('Asset \"$namaAlat\" berhasil dihapus.');
        window.location.href = 'index.php';
    </script>";
    exit;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Hapus Asset</title>
</head>
<body>
<script>
    var nama = "<?= $namaAlat ?>";
    var konfirmasi = confirm('Apakah Anda yakin ingin menghapus asset "' + nama + '"?\nTindakan ini tidak dapat dibatalkan.');

    if (konfirmasi) {
        window.location.href = 'delete.php?id=<?= $id ?>&confirm=yes';
    } else {
        window.location.href = 'index.php';
    }
</script>
</body>
</html>
