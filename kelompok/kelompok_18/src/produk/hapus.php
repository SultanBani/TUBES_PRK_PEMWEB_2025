<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['user_id'])) { header('Location: ../auth/login.php'); exit; }
if (!isset($_GET['id'])) { header('Location: index.php'); exit; }

$id_produk = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Ambil info gambar dulu
$query = mysqli_query($koneksi, "SELECT gambar FROM products WHERE id='$id_produk' AND user_id='$user_id'");
if (mysqli_num_rows($query) > 0) {
    $data = mysqli_fetch_assoc($query);
    $gambar = $data['gambar'];

    // Hapus Data dari DB
    mysqli_query($koneksi, "DELETE FROM products WHERE id='$id_produk'");

    // Hapus File Fisik
    if ($gambar != 'no-image.jpg' && file_exists("../assets/img/$gambar")) {
        unlink("../assets/img/$gambar");
    }
    
    $_SESSION['success'] = "Produk berhasil dihapus.";
} else {
    $_SESSION['error'] = "Gagal hapus. Produk tidak ditemukan.";
}

header("Location: index.php");
?>