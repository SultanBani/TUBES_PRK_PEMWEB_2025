<?php
$hostname = "localhost";
$username = "root";
$password = ""; 
$database = "db_xbundle"; 
$port = 3307;

$conn = mysqli_connect($hostname, $username, $password, $database, $port);

if (!$conn) {
    die("Koneksi Database Gagal: " . mysqli_connect_error());
}
<<<<<<< HEAD:kelompok/kelompok_18/src/config/koneksi.php
$base_url = "http://localhost/TUBES_PRK_PEMWEB_2025/kelompok/kelompok_18/src/";
?>
=======
?>
>>>>>>> master:kelompok/kelompok_18/config/koneksi.php
