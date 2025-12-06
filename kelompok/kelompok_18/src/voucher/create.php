<?php
// Gunakan __DIR__ biar path aman
include __DIR__ . '/../config/koneksi.php';
include __DIR__ . '/../layouts/header.php';

// Cek Login
if (!isset($_SESSION['user_id'])) {
    echo "<script>location.href='../auth/login.php';</script>";
    exit;
}

$id_user = $_SESSION['user_id'];

// AMBIL DATA BUNDLE AKTIF (Milik User Ini)
// Biar user tinggal pilih, gak perlu hafal ID angka
$query_bundle = mysqli_query($koneksi, "SELECT * FROM bundles WHERE status='active' AND (pembuat_id='$id_user' OR mitra_id='$id_user')");
?>

<link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/style_voucher.css">

<div class="container mt-4 mb-5">
    <div class="form-container mx-auto">
        <h2 class="title-section text-center mb-4">Buat Voucher Baru</h2>

        <form action="proses_voucher.php" method="POST" class="form-voucher">

            <!-- 1. PILIH BUNDLE (Gabungan logic create.php lama) -->
            <div class="form-group mb-3">
                <label class="fw-bold">Pilih Paket Kolaborasi:</label>
                <select name="bundle_id" class="form-select" required>
                    <option value="" disabled selected>-- Pilih Bundle Aktif --</option>
                    <?php if(mysqli_num_rows($query_bundle) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($query_bundle)): ?>
                            <option value="<?= $row['id'] ?>">
                                <?= $row['nama_bundle'] ?> (Harga: Rp <?= number_format($row['harga_bundle']) ?>)
                            </option>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <option value="" disabled>Belum ada kolaborasi yang aktif/deal.</option>
                    <?php endif; ?>
                </select>
                <div class="form-text text-muted">Voucher hanya bisa dibuat untuk bundle yang statusnya 'Active'.</div>
            </div>

            <!-- 2. KODE UNIK -->
            <div class="form-group mb-3">
                <label class="fw-bold">Kode Voucher:</label>
                <input type="text" name="kode_unik" class="form-control" placeholder="Contoh: RAMADHAN2025" style="text-transform:uppercase" required>
            </div>

            <!-- 3. DISKON & KUOTA -->
            <div class="row">
                <div class="col-md-6 form-group mb-3">
                    <label class="fw-bold">Potongan Harga (Rp):</label>
                    <input type="number" name="potongan" class="form-control" placeholder="5000" required>
                </div>
                <div class="col-md-6 form-group mb-3">
                    <label class="fw-bold">Kuota Maksimal:</label>
                    <input type="number" name="kuota" class="form-control" value="100" required>
                </div>
            </div>

            <!-- 4. BATAS WAKTU (Gabungan logic batas_waktu.php) -->
            <div class="form-group mb-4">
                <label class="fw-bold">Berlaku Sampai Tanggal:</label>
                <input type="date" name="expired_at" class="form-control" required>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-add w-100 py-2 fw-bold text-white" style="background: linear-gradient(135deg, #ED7D31, #6C5F5B);">
                    <i class="fa-solid fa-save me-2"></i> Simpan Voucher
                </button>
            </div>
            
            <div class="text-center mt-3">
                <a href="index.php" class="text-muted text-decoration-none">Kembali ke Daftar</a>
            </div>

        </form>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>