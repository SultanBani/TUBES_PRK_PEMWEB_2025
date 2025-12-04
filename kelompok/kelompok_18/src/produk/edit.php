<?php
session_start();
require_once '../config/koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$nama_toko = $_SESSION['nama_toko'] ?? 'Toko Saya';

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$produk_id = $_GET['id'];

$query = "SELECT * FROM produk WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $produk_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    $_SESSION['error'] = "Produk tidak ditemukan atau bukan milik Anda.";
    header('Location: index.php');
    exit;
}

$produk = $result->fetch_assoc();
$stmt->close();

include '../layouts/header.php';
?>

<div class="form-container">
    <div class="form-header">
        <h1>Edit Produk</h1>
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
        <input type="hidden" name="action" value="edit">
        <input type="hidden" name="id" value="<?php echo $produk['id']; ?>">
        <input type="hidden" name="gambar_lama" value="<?php echo $produk['gambar']; ?>">

        <div class="form-group">
            <label for="nama_produk">Nama Produk <span class="required">*</span></label>
            <input type="text" id="nama_produk" name="nama_produk" required value="<?php echo htmlspecialchars($produk['nama_produk']); ?>">
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="kategori">Kategori <span class="required">*</span></label>
                <select id="kategori" name="kategori" required>
                    <option value="">Pilih Kategori</option>
                    <option value="Makanan" <?php echo $produk['kategori'] == 'Makanan' ? 'selected' : ''; ?>>Makanan</option>
                    <option value="Minuman" <?php echo $produk['kategori'] == 'Minuman' ? 'selected' : ''; ?>>Minuman</option>
                    <option value="Snack" <?php echo $produk['kategori'] == 'Snack' ? 'selected' : ''; ?>>Snack</option>
                    <option value="Kue & Roti" <?php echo $produk['kategori'] == 'Kue & Roti' ? 'selected' : ''; ?>>Kue & Roti</option>
                    <option value="Bunga" <?php echo $produk['kategori'] == 'Bunga' ? 'selected' : ''; ?>>Bunga</option>
                    <option value="Gift & Hampers" <?php echo $produk['kategori'] == 'Gift & Hampers' ? 'selected' : ''; ?>>Gift & Hampers</option>
                    <option value="Aksesoris" <?php echo $produk['kategori'] == 'Aksesoris' ? 'selected' : ''; ?>>Aksesoris</option>
                    <option value="Lainnya" <?php echo $produk['kategori'] == 'Lainnya' ? 'selected' : ''; ?>>Lainnya</option>
                </select>
            </div>

            <div class="form-group">
                <label for="satuan">Satuan <span class="required">*</span></label>
                <input type="text" id="satuan" name="satuan" required value="<?php echo htmlspecialchars($produk['satuan']); ?>">
            </div>
        </div>

        <div class="form-group">
            <label for="harga">Harga (Rp) <span class="required">*</span></label>
            <input type="number" id="harga" name="harga" required min="0" step="1000" value="<?php echo $produk['harga']; ?>">
        </div>

        <div class="form-group">
            <label for="deskripsi">Deskripsi Produk</label>
            <textarea id="deskripsi" name="deskripsi" rows="5"><?php echo htmlspecialchars($produk['deskripsi']); ?></textarea>
        </div>

        <div class="form-group">
            <label for="gambar">Gambar Produk</label>
            <?php if ($produk['gambar']): ?>
                <div class="current-image">
                    <p>Gambar saat ini:</p>
                    <img src="../assets/img/<?php echo htmlspecialchars($produk['gambar']); ?>" alt="Current" style="max-width: 200px; margin: 10px 0;">
                </div>
            <?php endif; ?>
            <input type="file" id="gambar" name="gambar" accept="image/*" onchange="previewImage(event)">
            <small class="form-text">Kosongkan jika tidak ingin mengubah gambar. Format: JPG, PNG, maksimal 2MB</small>
            <div id="preview-container" style="display: none;">
                <p>Preview gambar baru:</p>
                <img id="preview-image" src="" alt="Preview">
            </div>
        </div>

        <div class="form-actions">
            <a href="index.php" class="btn-secondary">Batal</a>
            <button type="submit" class="btn-primary">Update Produk</button>
        </div>
    </form>
</div>

<script src="../assets/js/script_produk.js"></script>

<?php
$conn->close();
include '../layouts/footer.php';
?>