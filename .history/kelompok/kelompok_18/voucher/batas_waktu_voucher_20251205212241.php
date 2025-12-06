<?php include "../config/koneksi.php"; ?>
<link rel="stylesheet" href="style_voucher.css">

<div class="container">
<h2>Generate Voucher Baru</h2>

<form action="proses_voucher.php" method="POST">
<label>Bundle ID:</label><br>
<input type="number" name="bundle_id" required><br><br>

<label>Kode Unik Voucher:</label><br>
<input type="text" name="kode_unik" required><br><br>

<button type="submit" name="create" class="btn btn-add">Buat Voucher</button>
</form>
</div>
