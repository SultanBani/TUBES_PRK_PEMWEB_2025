<?php
include __DIR__ . '/../config/koneksi.php';
include __DIR__ . '/../layouts/header.php';

// Cek Login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}
?>

<link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/style_voucher.css">

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-header bg-terracotta text-white text-center py-3">
                    <h4 class="mb-0 fw-bold"><i class="fa-solid fa-cash-register me-2"></i> Validasi Voucher</h4>
                </div>
                <div class="card-body p-5">
                    
                    <div class="text-center mb-4">
                        <img src="https://cdni.iconscout.com/illustration/premium/thumb/voucher-code-8043657-6437198.png" width="150" alt="Voucher">
                        <p class="text-muted mt-3">Masukkan kode voucher dari pembeli untuk mengecek validitas dan kuota.</p>
                    </div>

                    <div class="form-group mb-4">
                        <label class="fw-bold text-brown mb-2">Kode Voucher</label>
                        <input type="text" id="kode" class="form-control form-control-lg text-center text-uppercase fw-bold" placeholder="CONTOH: HEMAT50" style="letter-spacing: 2px;">
                    </div>

                    <button onclick="checkVoucher()" class="btn btn-add w-100 py-3 fw-bold shadow-sm">
                        <i class="fa-solid fa-magnifying-glass me-2"></i> CEK KODE
                    </button>

                    <!-- Tempat Hasil Cek Muncul -->
                    <div id="hasil-cek" class="mt-4 text-center" style="display: none;"></div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script Khusus Halaman Ini -->
<script src="<?php echo $base_url; ?>/assets/js/script_voucher.js"></script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>