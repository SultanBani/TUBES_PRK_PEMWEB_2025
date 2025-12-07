<?php
session_start();
include __DIR__ . '/../config/koneksi.php';

if (!isset($_SESSION['user_id'])) { header('Location: ../auth/login.php'); exit; }
if (!isset($_GET['id'])) { header('Location: index.php'); exit; }

$id_produk = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Ambil info gambar dulu & Pastikan milik user yang login
$query = mysqli_query($koneksi, "SELECT gambar FROM products WHERE id='$id_produk' AND user_id='$user_id'");

if (mysqli_num_rows($query) > 0) {
    $data = mysqli_fetch_assoc($query);
    $gambar = $data['gambar'];

    // Hapus Data dari DB (Tabel Bundles & Chats akan aman karena ON DELETE CASCADE di DB)
    $del = mysqli_query($koneksi, "DELETE FROM products WHERE id='$id_produk'");

    if ($del) {
        // Hapus File Fisik Gambar jika bukan default
        if ($gambar != 'no-image.jpg' && file_exists("../assets/img/$gambar")) {
            unlink("../assets/img/$gambar");
        }
        $_SESSION['success'] = "Produk berhasil dihapus.";
    } else {
        $_SESSION['error'] = "Gagal hapus database.";
    }
} else {
    $_SESSION['error'] = "Gagal hapus. Produk tidak ditemukan atau bukan milik Anda.";
}

header("Location: index.php");
?>