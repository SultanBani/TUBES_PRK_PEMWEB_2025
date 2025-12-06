<?php
$pageTitle = "Generate Voucher Baru";  // judul halaman
include "../config/koneksi.php";       // koneksi database

if (!$conn) {
    die("Koneksi database gagal!");
}

include "../layouts/header.php";       // header template
?>

<link rel="stylesheet" href="../assets/css/style_voucher.css">

<div class="page-wrapper">
    <h2 class="title-section">Membuat Voucher Baru</h2>

    <?php if (isset($_GET['success'])) { ?>
        <div class="alert-success">✔ Voucher berhasil dibuat!</div>
    <?php } ?>

    <?php if (isset($_GET['error'])) { ?>
        <div class="alert-error">✖ Gagal membuat voucher! Kode unik sudah ada atau kesalahan lain.</div>
    <?php } ?>

    <div class="card-table">
        <form action="proses_voucher.php" method="POST" class="form-voucher">

            <label>Bundle ID:</label>
            <input type="number" name="bundle_id" placeholder="Masukkan ID Bundle" required>

            <label>Kode Unik Voucher:</label>
            <input type="text" name="kode_unik" placeholder="Contoh: XB-KopiRoti-001" required>

            <div class="btn-center">
                <button type="submit" class="btn-add">Buat Voucher</button>
            </div>

        </form>
    </div>
</div>

<?php include "../layouts/footer.php"; ?>
