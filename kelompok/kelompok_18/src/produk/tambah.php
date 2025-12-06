<?php
session_start();
require_once '../config/koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

$nama_toko = $_SESSION['nama_toko'] ?? 'Toko Saya';

include '../layouts/header.php';
?>

<div class="form-container">
    <div class="form-header">
        <h1>Tambah Produk Baru</h1>
        <p class="subtitle">Toko: <?php echo htmlspecialchars($nama_toko); ?></p>
    </div>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error">
            <?php 
            echo $_SESSION['error']; 
            unset($_SESSION['error']);
            ?>
        </div>
    <?php endif; ?>

    <form action="proses_produk.php" method="POST" enctype="multipart/form-data" class="form-produk">
        <input type="hidden" name="action" value="tambah">

        <div class="form-group">
            <label for="nama_produk">Nama Produk <span class="required">*</span></label>
            <input type="text" id="nama_produk" name="nama_produk" required placeholder="Contoh: Kopi Arabica Premium">
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="kategori">Kategori <span class="required">*</span></label>
                <select id="kategori" name="kategori" required>
                    <option value="">Pilih Kategori</option>
                    <option value="Makanan">Makanan</option>
                    <option value="Minuman">Minuman</option>
                    <option value="Snack">Snack</option>
                    <option value="Kue & Roti">Kue & Roti</option>
                    <option value="Bunga">Bunga</option>
                    <option value="Gift & Hampers">Gift & Hampers</option>
                    <option value="Aksesoris">Aksesoris</option>
                    <option value="Lainnya">Lainnya</option>
                </select>
            </div>

            <div class="form-group">
                <label for="satuan">Satuan <span class="required">*</span></label>
                <input type="text" id="satuan" name="satuan" required placeholder="Contoh: Pcs, Kg, Liter, Box">
            </div>
        </div>

        <div class="form-group">
            <label for="harga">Harga (Rp) <span class="required">*</span></label>
            <input type="number" id="harga" name="harga" required min="0" step="1000" placeholder="Contoh: 25000">
        </div>

        <div class="form-group">
            <label for="deskripsi">Deskripsi Produk</label>
            <textarea id="deskripsi" name="deskripsi" rows="5" placeholder="Jelaskan detail produk Anda..."></textarea>
        </div>

        <div class="form-group">
            <label for="gambar">Gambar Produk</label>
            <input type="file" id="gambar" name="gambar" accept="image/*" onchange="previewImage(event)">
            <small class="form-text">Format: JPG, PNG, maksimal 2MB</small>
            <div id="preview-container" style="display: none;">
                <img id="preview-image" src="" alt="Preview">
            </div>
        </div>

        <div class="form-actions">
            <a href="index.php" class="btn-secondary">Batal</a>
            <button type="submit" class="btn-primary">Simpan Produk</button>
        </div>
    </form>
</div>

<script src="../assets/js/script_produk.js"></script>

<?php
include '../layouts/footer.php';
?>