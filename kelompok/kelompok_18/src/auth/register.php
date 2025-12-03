<?php 
include '../config/koneksi.php'; 
include '../layouts/header.php'; 
?>

<div class="row justify-content-center mt-4">
    <div class="col-md-6">
        <div class="card shadow border-0">
            <div class="card-body p-5">
                <h3 class="text-center mb-4 text-primary fw-bold">Daftar Akun UMKM</h3>
                
                <?php if(isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger">
                        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>

                <form action="proses_auth.php?aksi=register" method="POST">
                    
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap Pemilik</label>
                        <input type="text" name="nama_lengkap" class="form-control" required placeholder="Budi Santoso">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Toko</label>
                            <input type="text" name="nama_toko" class="form-control" required placeholder="Kopi Senja">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email (Untuk Login)</label>
                            <input type="email" name="email" class="form-control" required placeholder="email@contoh.com">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Alamat Toko</label>
                        <textarea name="alamat" class="form-control" rows="3" placeholder="Jl. Mawar No. 12..."></textarea>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">Daftar Sekarang</button>
                    </div>
                </form>
                
                <div class="text-center mt-3">
                    <small>Sudah punya akun? <a href="<?php echo $base_url; ?>/auth/login.php">Login di sini</a></small>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../layouts/footer.php'; ?>