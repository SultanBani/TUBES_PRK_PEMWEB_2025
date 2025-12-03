<?php
include '../config/koneksi.php';
include '../layouts/header.php';

// Cek Login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Ambil data user terbaru dari database
$id_user = $_SESSION['user_id'];
$query   = mysqli_query($koneksi, "SELECT * FROM users WHERE id='$id_user'");
$data    = mysqli_fetch_assoc($query);
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow border-0">
            <div class="card-header bg-white py-3">
                <h4 class="mb-0 fw-bold text-primary"><i class="fa-solid fa-store"></i> Edit Profil Toko</h4>
            </div>
            <div class="card-body p-4">
                
                <?php if(isset($_SESSION['success'])): ?>
                    <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
                <?php endif; ?>

                <form action="proses_auth.php?aksi=update_profil" method="POST">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Toko</label>
                        <input type="text" name="nama_toko" class="form-control" value="<?php echo $data['nama_toko']; ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Nomor WhatsApp / HP</label>
                        <input type="text" name="no_hp" class="form-control" value="<?php echo $data['no_hp']; ?>" placeholder="Contoh: 08123456789">
                        <div class="form-text">Nomor ini akan digunakan calon partner untuk menghubungi Anda.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Alamat Lengkap</label>
                        <textarea name="alamat_toko" class="form-control" rows="2"><?php echo $data['alamat_toko']; ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Deskripsi Toko</label>
                        <textarea name="deskripsi_toko" class="form-control" rows="4" placeholder="Ceritakan tentang produk unggulan Anda..."><?php echo $data['deskripsi_toko']; ?></textarea>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="../produk/index.php" class="btn btn-secondary me-md-2">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../layouts/footer.php'; ?>