<?php
$pageTitle = "Generate Voucher Baru";
include "../layouts/header.php";   // header template
include "../config/koneksi.php";   // koneksi database
?>
<link rel="stylesheet" href="../assets/css/style_voucher.css">
<h2>Generate Voucher Baru</h2>

<form action="proses_voucher.php" method="POST" class="form-voucher">

    <label>Bundle ID:</label>
    <input type="number" name="bundle_id" required>

    <label>Kode Unik Voucher:</label>
    <input type="text" name="kode_unik" required>

    <button type="submit" name="create" class="btn btn-add">Buat Voucher</button>
</form>

<?php include "../layouts/footer.php"; ?>
