<?php
include "../config/koneksi.php";

if (isset($_POST['create'])) {
    $bundle_id = $_POST['bundle_id'];
    $kode = $_POST['kode_unik'];

    $sql = "INSERT INTO vouchers(bundle_id, kode_unik) VALUES('$bundle_id', '$kode')";

    if ($conn->query($sql)) {
        echo "<script>alert('Voucher berhasil dibuat'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('Gagal: Kode sudah ada'); window.location='batas_waktu_voucher.php';</script>";
    }
}

if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $conn->query("DELETE FROM vouchers WHERE id=$id");
    header("Location: index.php");
}
?>