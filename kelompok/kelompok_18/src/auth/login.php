<?php 
// Panggil konfigurasi & tampilan header
include '../config/koneksi.php'; 
include '../layouts/header.php'; 

// Kalau user sudah login, tendang ke dashboard (gak boleh akses halaman login lagi)
if (isset($_SESSION['status']) && $_SESSION['status'] == 'login') {
    echo "<script>location.href='../produk/index.php';</script>";
    exit;
}
?>

<div class="row justify-content-center align-items-center" style="min-height: 60vh;">
    <div class="col-md-5">
        <div class="card shadow border-0">
            <div class="card-body p-5">
                <div class="text-center mb-4">
                    <h2 class="fw-bold text-primary"><i class="fa-solid fa-right-to-bracket"></i> Masuk</h2>
                    <p class="text-muted">Kelola kolaborasi bisnis Anda sekarang.</p>
                </div>
                
                <?php if(isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fa-solid fa-triangle-exclamation"></i> 
                        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if(isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fa-solid fa-check-circle"></i>
                        <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <form action="proses_auth.php?aksi=login" method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Email</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fa-solid fa-envelope"></i></span>
                            <input type="email" name="email" class="form-control" required placeholder="toko@email.com">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fa-solid fa-lock"></i></span>
                            <input type="password" name="password" class="form-control" required placeholder="******">
                        </div>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary py-2 fw-bold">Masuk Sekarang</button>
                    </div>
                </form>
                
                <hr class="my-4">
                
                <div class="text-center">
                    <span class="text-muted">Belum punya akun?</span>
                    <a href="<?php echo $base_url; ?>/auth/register.php" class="text-decoration-none fw-bold">Daftar UMKM Baru</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../layouts/footer.php'; ?>