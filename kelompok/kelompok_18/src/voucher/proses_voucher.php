<?php
session_start();
// Gunakan __DIR__ agar path include selalu aman (Best Practice)
include __DIR__ . '/../config/koneksi.php';

// Cek Login (Security)
if (!isset($_SESSION['user_id'])) {
    echo "<script>location.href='../auth/login.php';</script>";
    exit;
}

// 1. TANGKAP SEMUA DATA DARI FORM CREATE.PHP
$bundle_id   = $_POST['bundle_id'];
// Ubah kode jadi huruf besar semua biar rapi (contoh: hemat50 -> HEMAT50)
$kode_unik   = strtoupper(mysqli_real_escape_string($koneksi, $_POST['kode_unik']));
$potongan    = $_POST['potongan'];
$kuota       = $_POST['kuota'];
$expired_at  = $_POST['expired_at'];

// 2. CEK KODE KEMBAR (Validasi)
$cek = mysqli_query($koneksi, "SELECT * FROM vouchers WHERE kode_voucher='$kode_unik'");
if (mysqli_num_rows($cek) > 0) {
    echo "<script>alert('Gagal! Kode voucher $kode_unik sudah pernah digunakan. Coba kode lain.'); window.location='create.php';</script>";
    exit;
}

// 3. INSERT KE DATABASE (Lengkap dengan Kuota & Potongan)
// Perhatikan nama kolom database harus sesuai dengan yang di SQL V4.2
$query = "INSERT INTO vouchers (bundle_id, kode_voucher, potongan_harga, kuota_maksimal, kuota_terpakai, expired_at, status)
          VALUES ('$bundle_id', '$kode_unik', '$potongan', '$kuota', 0, '$expired_at', 'available')";

if (mysqli_query($koneksi, $query)) {
    echo "<script>alert('Voucher berhasil dibuat!'); window.location='index.php';</script>";
} else {
    // Tampilkan error SQL jika gagal (untuk debugging)
    echo "Error Database: " . mysqli_error($koneksi);
}
?>