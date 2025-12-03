<?php
include 'config/koneksi.php';
include 'layouts/header.php';
?>

<section class="py-5 align-items-center d-flex" style="min-height: 80vh;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6 order-2 order-md-1">
                <span class="badge bg-light text-warning mb-3 px-3 py-2 rounded-pill border fw-bold text-uppercase" style="color: #ED7D31 !important;">
                     Solusi Digital UMKM
                </span>
                <h1 class="display-4 fw-bold mb-4" style="color: #4F4A45;">
                    Tingkatkan Bisnis UMKM,<br> 
                    <span style="color: #ED7D31;">Kolaborasi Tanpa Batas.</span>
                </h1>
                <p class="lead mb-5" style="color: #6C5F5B;">
                    X-Bundle membantu Anda menemukan mitra bisnis, membuat bundling produk eksklusif, dan menjangkau pasar yang lebih luas.
                </p>
                
                <div class="d-flex gap-3">
                    <a href="<?php echo $base_url; ?>/auth/register.php" class="btn btn-primary btn-lg shadow-lg">
                        Mulai Sekarang <i class="fa-solid fa-arrow-right ms-2"></i>
                    </a>
                    <a href="#fitur" class="btn btn-outline-primary btn-lg">
                        Pelajari Dulu
                    </a>
                </div>
            </div>
            
            <div class="col-md-6 order-1 order-md-2 text-center mb-5 mb-md-0">
                <img src="https://cdni.iconscout.com/illustration/premium/thumb/business-partnership-2975816-2476892.png" alt="Ilustrasi Kolaborasi" class="img-fluid floating-animate">
            </div>
        </div>
    </div>
</section>

<section id="fitur" class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h6 class="text-uppercase fw-bold" style="color: #ED7D31;">Kenapa X-Bundle?</h6>
            <h2 class="fw-bold">Fitur Andalan Kami</h2>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 p-4 text-center">
                    <div class="mb-3">
                        <i class="fa-solid fa-handshake fa-3x" style="color: #ED7D31;"></i>
                    </div>
                    <h4>Cari Partner</h4>
                    <p class="text-muted">Temukan UMKM lain yang cocok untuk berkolaborasi dengan produk Anda.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 p-4 text-center">
                    <div class="mb-3">
                        <i class="fa-solid fa-box-open fa-3x" style="color: #6C5F5B;"></i>
                    </div>
                    <h4>Bundle Produk</h4>
                    <p class="text-muted">Gabungkan dua produk menjadi satu paket hemat yang menarik pembeli.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 p-4 text-center">
                    <div class="mb-3">
                        <i class="fa-solid fa-chart-line fa-3x" style="color: #4F4A45;"></i>
                    </div>
                    <h4>Laporan Transaksi</h4>
                    <p class="text-muted">Pantau penggunaan voucher dan hasil penjualan dari kolaborasi Anda.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
@keyframes float {
    0% { transform: translateY(0px); }
    50% { transform: translateY(-20px); }
    100% { transform: translateY(0px); }
}
.floating-animate {
    animation: float 6s ease-in-out infinite;
}
</style>

<?php
include 'layouts/footer.php';
?>