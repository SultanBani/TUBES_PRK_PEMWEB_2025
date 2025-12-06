<?php
include "../config/koneksi.php";

$bundle_id   = $_POST['bundle_id'];
$kode_unik   = $_POST['kode_unik'];
$expired_at  = $_POST['expired_at'];

$cek = mysqli_query($conn, "SELECT * FROM vouchers WHERE kode_unik='$kode_unik'");
if (mysqli_num_rows($cek) > 0) {
    echo "<script>alert('Kode voucher sudah ada!'); window.location='index.php';</script>";
    exit;
}

$query = "INSERT INTO vouchers (bundle_id, kode_unik, status, created_at, expired_at)
          VALUES ('$bundle_id', '$kode_unik', 'available', NOW(), '$expired_at')";
mysqli_query($conn, $query);

echo "<script>alert('Voucher berhasil dibuat!'); window.location='index.php';</script>";
?>
