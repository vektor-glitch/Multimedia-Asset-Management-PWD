<?php 
$host = "127.0.0.1";
$user = "root";
$pass = "";
$database = "latres_web_si-d";
$port = 8111;

$conn = new mysqli($host, $user, $pass, $database, $port);
if (!$conn) {
    die("Koneksi gagal tersambung!" . $conn->connect_error);
}
?>