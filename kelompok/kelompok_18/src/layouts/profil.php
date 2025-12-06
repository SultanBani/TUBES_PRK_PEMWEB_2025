<?php
include '../config/koneksi.php';
include '../layouts/header.php';

// Cek Login
if (!isset($_SESSION['user_id'])) {
    echo "<script>location.href='login.php';</script>";
    exit;
}

// Ambil data user terbaru
$id_user = $_SESSION['user_id'];
$query   = mysqli_query($koneksi, "SELECT * FROM users WHERE id='$id_user'");
$data    = mysqli_fetch_assoc($query);
?>

<div class="row justify-content-center mt-4 mb-5">
    <div class="col-md-8">
        <div class="card shadow border-0" style="border-radius: 15px;">
            <div class="card-header bg-white py-3 border-bottom-0">
                <h4 class="mb-0 fw-bold" style="color: #ED7D31;">
                    <i class="fa-solid fa-store me-2"></i> Edit Profil Toko
                </h4>
            </div>
            <div class="card-body p-4">
                
                <?php if(isset($_SESSION['success'])): ?>
                    <div class="alert alert-success border-0 shadow-sm" style="background-color: #d4edda; color: #155724;">
                        <i class="fa-solid fa-check-circle me-2"></i> <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                    </div>
                <?php endif; ?>

                <form action="proses_auth.php?aksi=update_profil" method="POST">
                    
                    <!-- Nama Toko -->
                    <div class="mb-3">
                        <label class="form-label fw-bold" style="color: #4F4A45;">Nama Toko</label>
                        <!-- FIX: Tambahkan ?? '' agar tidak error jika NULL -->
                        <input type="text" name="nama_toko" class="form-control" value="<?php echo htmlspecialchars($data['nama_toko'] ?? ''); ?>" required>
                    </div>

                    <!-- Kategori Bisnis -->
                    <div class="mb-3">
                        <label class="form-label fw-bold" style="color: #4F4A45;">Kategori Bisnis</label>
                        <select name="kategori_bisnis" class="form-select">
                            <?php 
                            $kategori = ['Kuliner (FnB)', 'Fashion', 'Agribisnis', 'Manufaktur/Kerajinan', 'Jasa', 'Retail/Grosir', 'Teknologi', 'Lainnya'];
                            // FIX: Cek isset sebelum membandingkan
                            $kategori_user = $data['kategori_bisnis'] ?? '';
                            
                            foreach($kategori as $kat) {
                                $selected = ($kategori_user == $kat) ? 'selected' : '';
                                echo "<option value='$kat' $selected>$kat</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <!-- Nomor HP (BARU) -->
                    <div class="mb-3">
                        <label class="form-label fw-bold" style="color: #4F4A45;">Nomor WhatsApp / HP</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted"><i class="fa-brands fa-whatsapp"></i></span>
                            <!-- FIX: Tambahkan ?? '' -->
                            <input type="text" name="no_hp" class="form-control" value="<?php echo htmlspecialchars($data['no_hp'] ?? ''); ?>" placeholder="Contoh: 08123456789">
                        </div>
                        <div class="form-text">Nomor ini akan dilihat calon partner untuk menghubungi Anda.</div>
                    </div>

                    <!-- Alamat -->
                    <div class="mb-3">
                        <label class="form-label fw-bold" style="color: #4F4A45;">Alamat Lengkap</label>
                        <!-- FIX: Tambahkan ?? '' -->
                        <textarea name="alamat_toko" class="form-control" rows="2"><?php echo htmlspecialchars($data['alamat_toko'] ?? ''); ?></textarea>
                    </div>

                    <!-- Deskripsi (BARU) -->
                    <div class="mb-4">
                        <label class="form-label fw-bold" style="color: #4F4A45;">Deskripsi Toko</label>
                        <!-- FIX: Tambahkan ?? '' -->
                        <textarea name="deskripsi_toko" class="form-control" rows="4" placeholder="Ceritakan keunggulan toko Anda agar partner tertarik..."><?php echo htmlspecialchars($data['deskripsi_toko'] ?? ''); ?></textarea>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="../produk/index.php" class="btn btn-outline-secondary me-md-2 px-4">Batal</a>
                        <button type="submit" class="btn text-white px-4" style="background-color: #ED7D31;">Simpan Perubahan</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../layouts/footer.php'; ?>