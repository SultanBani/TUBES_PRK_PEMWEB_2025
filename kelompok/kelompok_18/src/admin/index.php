<?php
include 'config/koneksi.php';
include 'layouts/header.php';
?>

<link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/style_admin.css">

<!-- === HERO SECTION === -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            
            <!-- Teks Kiri -->
            <div class="col-md-6 order-2 order-md-1 reveal">
                <span class="hero-badge">
                    <i class="fa-solid fa-rocket me-2"></i>Solusi Digital UMKM
                </span>
                
                <h1 class="display-4 hero-title mb-4">
                    Tingkatkan Bisnis,<br> 
                    <span class="text-highlight">Kolaborasi Tanpa Batas.</span>
                </h1>
                
                <p class="hero-desc">
                    Platform X-Bundle membantu UMKM menemukan mitra strategis, membuat paket bundling eksklusif, dan menjangkau pasar yang lebih luas bersama-sama.
                </p>
                
                <div class="d-flex gap-3">
                    <?php if(!isset($_SESSION['status'])): ?>
                        <a href="auth/register.php" class="btn text-white px-4 py-2 fw-bold shadow" style="background-color: #ED7D31; border-radius: 50px;">
                            Mulai Sekarang <i class="fa-solid fa-arrow-right ms-2"></i>
                        </a>
                    <?php else: ?>
                        <a href="produk/index.php" class="btn text-white px-4 py-2 fw-bold shadow" style="background-color: #ED7D31; border-radius: 50px;">
                            Ke Dashboard <i class="fa-solid fa-gauge ms-2"></i>
                        </a>
                    <?php endif; ?>
                    
                    <a href="#fitur" class="btn btn-outline-secondary px-4 py-2 fw-bold" style="border-radius: 50px; border-color: #6C5F5B; color: #6C5F5B;">
                        Pelajari Dulu
                    </a>
                </div>
            </div>
            
            <!-- Gambar Kanan (Ilustrasi) -->
            <div class="col-md-6 order-1 order-md-2 text-center mb-5 mb-md-0 reveal">
                <!-- Gambar Ilustrasi (Pastikan konek internet untuk load gambar ini) -->
                <img src="https://cdni.iconscout.com/illustration/premium/thumb/business-partnership-2975816-2476892.png" 
                     alt="Ilustrasi Kolaborasi UMKM" 
                     class="img-fluid floating-animate">
            </div>

        </div>
    </div>
</section>

<!-- === FITUR SECTION === -->
<section id="fitur" class="feature-section">
    <div class="container">
        
        <div class="text-center mb-5 reveal">
            <h6 class="text-uppercase fw-bold" style="color: #ED7D31;">Kenapa X-Bundle?</h6>
            <h2 class="section-title">Fitur Andalan Kami</h2>
        </div>

        <div class="row g-4">
            <!-- Card 1: Cari Partner -->
            <div class="col-md-4 reveal">
                <div class="card h-100 feature-card text-center">
                    <div class="feature-icon-wrapper">
                        <i class="fa-solid fa-handshake fa-2x" style="color: #ED7D31;"></i>
                    </div>
                    <h4 class="fw-bold" style="color: #4F4A45;">Cari Partner</h4>
                    <p class="text-muted">Temukan UMKM lain yang memiliki visi sama untuk berkolaborasi dengan produk Anda.</p>
                </div>
            </div>

            <!-- Card 2: Bundle Produk -->
            <div class="col-md-4 reveal">
                <div class="card h-100 feature-card text-center">
                    <div class="feature-icon-wrapper">
                        <i class="fa-solid fa-box-open fa-2x" style="color: #ED7D31;"></i>
                    </div>
                    <h4 class="fw-bold" style="color: #4F4A45;">Bundle Produk</h4>
                    <p class="text-muted">Gabungkan dua produk dari toko berbeda menjadi satu paket hemat yang menarik pembeli.</p>
                </div>
            </div>

            <!-- Card 3: Laporan & Voucher -->
            <div class="col-md-4 reveal">
                <div class="card h-100 feature-card text-center">
                    <div class="feature-icon-wrapper">
                        <i class="fa-solid fa-chart-line fa-2x" style="color: #ED7D31;"></i>
                    </div>
                    <h4 class="fw-bold" style="color: #4F4A45;">Laporan & Voucher</h4>
                    <p class="text-muted">Pantau penggunaan kode voucher publik dan hasil penjualan dari kolaborasi Anda.</p>
                </div>
            </div>
        </div>

    </div>
</section>

<script src="<?php echo $base_url; ?>/assets/js/script_landing.js"></script>

<?php include 'layouts/footer.php'; ?>