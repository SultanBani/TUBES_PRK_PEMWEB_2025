<?php
$hostname = "localhost";
$username = "root";
$password = ""; 
$database = "db_xbundle"; 

$koneksi = mysqli_connect($hostname, $username, $password, $database);

if (!$koneksi) {
    die("Koneksi Database Gagal: " . mysqli_connect_error());
}
$base_url = "http://x-bundle.test/TUBES_PRK_PEMWEB_2025/kelompok/kelompok_18/";
?>