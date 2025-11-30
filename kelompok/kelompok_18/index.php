<?php
include 'config/koneksi.php';

include 'layouts/header.php';
?>

<div class="row align-items-center py-5">
    <div class="col-md-6">
        <h1 class="display-4 fw-bold text-primary">Kolaborasi UMKM <br>Jadi Lebih Mudah.</h1>
        <p class="lead text-muted">Temukan partner bisnis, buat bundling produk, dan tingkatkan penjualan Anda bersama X-Bundle.</p>
        <a href="/auth/register.php" class="btn btn-primary btn-lg mt-3">Gabung Sekarang <i class="fa-solid fa-arrow-right"></i></a>
    </div>
    <div class="col-md-6 text-center">
        <img src="https://via.placeholder.com/500x300?text=Ilustrasi+UMKM" alt="Hero Image" class="img-fluid rounded shadow">
    </div>
</div>

<div class="alert alert-info mt-5">
    <h5><i class="fa-solid fa-check-circle"></i> Status Sistem:</h5>
    <p class="mb-0">
        <?php 
        if ($koneksi) {
            echo "✅ Database Terhubung! Siap digunakan.";
        } else {
            echo "❌ Database Gagal Terhubung.";
        }
        ?>
    </p>
</div>

<?php
include 'layouts/footer.php';
?>