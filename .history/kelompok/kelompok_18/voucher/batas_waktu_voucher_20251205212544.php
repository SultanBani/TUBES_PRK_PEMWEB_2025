<?php
$pageTitle = "Generate Voucher Baru";  // judul halaman
include_once "../config/koneksi.php";   // koneksi database
include_once "../layouts/header.php";   // header global

if (!isset($conn)) {
    die("Koneksi database gagal. Pastikan koneksi.php menggunakan variabel \$conn");
}
?>

<link rel="stylesheet" href="../assets/css/style_voucher.css">

<div class="container">
    <h2>Membuat Voucher Baru</h2>

    <?php if (isset($_GET['success'])) { ?>
        <div class="alert-success">✔ Voucher berhasil dibuat!</div>
    <?php } ?>

    <?php if (isset($_GET['error'])) { ?>
        <div class="alert-error">✖ Gagal membuat voucher! Kode mungkin sudah digunakan.</div>
    <?php } ?>

    <form action="proses_voucher.php" method="POST" class="form-voucher">

        <label>Bundle ID:</label>
        <input type="number" name="bundle_id" placeholder="Masukkan ID Bundle" required>

        <label>Kode Unik Voucher:</label>
        <input type="text" name="kode_unik" placeholder="Contoh: XB-KopiRoti-001" required>

        <button type="submit" name="create" class="btn btn-add">Buat Voucher</button>
    </form>
</div>

<?php include_once "../layouts/footer.php"; ?>
