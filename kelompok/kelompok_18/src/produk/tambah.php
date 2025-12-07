<?php
include __DIR__ . '/../config/koneksi.php';
include __DIR__ . '/../layouts/header.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>location.href='../auth/login.php';</script>";
    exit;
}
?>

<link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/style_produk.css">

<div class="form-container">
    <div class="produk-header border-bottom mb-4 pb-2">
        <h2 class="fw-bold" style="color: var(--dark-brown);">Tambah Produk Baru</h2>
    </div>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <form action="proses_produk.php" method="POST" enctype="multipart/form-data" class="form-produk">
        <input type="hidden" name="action" value="tambah">

        <div class="mb-3">
            <label class="fw-bold mb-2">Nama Produk <span class="text-danger">*</span></label>
            <input type="text" name="nama_produk" class="form-control" required placeholder="Contoh: Kopi Arabica Premium">
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="fw-bold mb-2">Kategori <span class="text-danger">*</span></label>
                <select name="kategori" class="form-select" required>
                    <option value="" disabled selected>Pilih Kategori</option>
                    <option value="makanan">Makanan</option>
                    <option value="minuman">Minuman</option>
                    <option value="fashion">Fashion</option>
                    <option value="kerajinan">Kerajinan</option>
                    <option value="jasa">Jasa</option>
                    <option value="lainnya">Lainnya</option>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label class="fw-bold mb-2">Satuan <span class="text-danger">*</span></label>
                <input type="text" name="satuan" class="form-control" required placeholder="Contoh: Pcs, Box, Kg">
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="fw-bold mb-2">Harga (Rp) <span class="text-danger">*</span></label>
                <input type="number" name="harga" class="form-control" required min="0" placeholder="0">
            </div>
            <div class="col-md-6 mb-3">
                <label class="fw-bold mb-2">Stok Awal <span class="text-danger">*</span></label>
                <input type="number" name="stok" class="form-control" required min="0" value="10">
            </div>
        </div>

        <div class="mb-3">
            <label class="fw-bold mb-2">Deskripsi</label>
            <textarea name="deskripsi" class="form-control" rows="4" placeholder="Jelaskan keunggulan produk Anda..."></textarea>
        </div>

        <div class="mb-4">
            <label class="fw-bold mb-2">Gambar Produk</label>
            <input type="file" name="gambar" class="form-control" accept="image/*" onchange="previewImage(event)">
            <small class="text-muted">Format: JPG/PNG, Maks 2MB.</small>
            <div id="preview-container" class="mt-3" style="display: none;">
                <img id="preview-image" src="" alt="Preview" style="max-width: 200px; border-radius: 10px; border: 2px solid #ddd;">
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 border-top pt-3">
            <a href="index.php" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary-custom text-white">Simpan Produk</button>
        </div>
    </form>
</div>

<script src="<?php echo $base_url; ?>/assets/js/script_produk.js"></script>
<?php include __DIR__ . '/../layouts/footer.php'; ?>