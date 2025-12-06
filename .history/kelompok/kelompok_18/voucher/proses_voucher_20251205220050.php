<?php
include "../config/koneksi.php";

$bundle_id = $_POST['bundle_id'];
$kode_unik = $_POST['kode_unik'];

// cek apakah sudah ada
$cek = mysqli_query($koneksi, "SELECT * FROM vouchers WHERE kode_unik='$kode_unik'");
if(mysqli_num_rows($cek) > 0){
    echo "<script>alert('Voucher sudah ada dan siap digunakan'); window.location.href='index.php';</script>";
    exit;
}

// jika belum ada baru insert
$query = "INSERT INTO vouchers (bundle_id, kode_unik, status, created_at, expired_at)
VALUES ('$bundle_id', '$kode_unik', 'available', NOW(), DATE_ADD(NOW(), INTERVAL 7 DAY))";

mysqli_query($koneksi, $query);

echo "<script>alert('Voucher berhasil dibuat'); window.location.href='index.php';</script>";
?>
