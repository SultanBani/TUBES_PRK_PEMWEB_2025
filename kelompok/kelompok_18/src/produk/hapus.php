<?php
session_start();
require_once '../config/koneksi.php';

// Cek login
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Ambil ID produk
if (!isset($_GET['id'])) {
    $_SESSION['error'] = "ID produk tidak valid.";
    header('Location: index.php');
    exit;
}

$produk_id = (int)$_GET['id'];

// Ambil data produk
$query = "SELECT gambar FROM produk WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $produk_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    $_SESSION['error'] = "Produk tidak ditemukan atau bukan milik Anda.";
    header('Location: index.php');
    exit;
}

$produk = $result->fetch_assoc();
$stmt->close();

// Hapus gambar jika ada
if ($produk['gambar'] && file_exists("../assets/img/" . $produk['gambar'])) {
    unlink("../assets/img/" . $produk['gambar']);
}

// Hapus dari database
$delete_query = "DELETE FROM produk WHERE id = ? AND user_id = ?";
$delete_stmt = $conn->prepare($delete_query);
$delete_stmt->bind_param("ii", $produk_id, $user_id);

if ($delete_stmt->execute()) {
    $_SESSION['success'] = "Produk berhasil dihapus!";
} else {
    $_SESSION['error'] = "Gagal menghapus produk. Silakan coba lagi.";
}

$delete_stmt->close();
$conn->close();

header('Location: index.php');
exit;
?>