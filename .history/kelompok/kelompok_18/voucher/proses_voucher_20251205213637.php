<?php
include "../config/koneksi.php";

if (isset($_POST['create'])) {
    $bundle_id = $_POST['bundle_id'];
    $kode = $_POST['kode_unik'];

    $sql = "INSERT INTO vouchers (bundle_id, kode_unik) VALUES ('$bundle_id', '$kode')";

    if (mysqli_query($conn, $sql)) {
        header("Location: batas_waktu_voucher.php?success=1");
    } else {
        header("Location: batas_waktu_voucher.php?error=1");
    }
    exit;
}
?>
