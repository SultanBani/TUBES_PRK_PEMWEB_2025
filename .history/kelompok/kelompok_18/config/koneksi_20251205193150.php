<?php
$hostname = "localhost";
$username = "root";
$password = ""; 
$database = "db_xbundle"; 
$port = 3307; // karena MySQL berjalan di port 3307

$koneksi = mysqli_connect($hostname, $username, $password, $database, $port);

if (!$koneksi) {
    die("Koneksi Database Gagal: " . mysqli_connect_error());
} else {
    // echo "Koneksi Berhasil"; // aktifkan jika ingin cek
}
?>
