<?php
include __DIR__ . '/../config/koneksi.php';
include __DIR__ . '/../layouts/header.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>location.href='../auth/login.php';</script>";
    exit;
}

if (!isset($_GET['id'])) {
    echo "<script>location.href='index.php';</script>";
    exit;
}

$id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Ambil data produk milik user ini
$query = mysqli_query($koneksi, "SELECT * FROM products WHERE id='$id' AND user_id='$user_id'");
if (mysqli_num_rows($query) == 0) {
    echo "<script>alert('Produk tidak ditemukan!'); location.href='index.php';</script>"; 
    exit;
}
$data = mysqli_fetch_assoc($query);
?>

<link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/style_produk.css">

<div class="form-container">
    <div class="produk-header border-bottom mb-4 pb-2">
        <h2 class="fw-bold" style="color: var(--dark-brown);">Edit Produk</h2>
    </div>

    <form action="proses_produk.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="action" value="edit">
        <input type="hidden" name="id" value="<?php echo $data['id']; ?>">
        <input type="hidden" name="gambar_lama" value="<?php echo $data['gambar']; ?>">

        <div class="mb-3">
            <label class="fw-bold mb-2">Nama Produk</label>
            <input type="text" name="nama_produk" class="form-control" value="<?php echo htmlspecialchars($data['nama_produk']); ?>" required>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="fw-bold mb-2">Kategori</label>
                <select name="kategori" class="form-select" required>
                    <?php 
                    $kats = ['makanan', 'minuman', 'fashion', 'kerajinan', 'jasa', 'lainnya'];
                    foreach($kats as $k) {
                        $selected = ($data['kategori'] == $k) ? 'selected' : '';
                        echo "<option value='$k' $selected>" . ucfirst($k) . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label class="fw-bold mb-2">Satuan</label>
                <input type="text" name="satuan" class="form-control" value="<?php echo htmlspecialchars($data['satuan']); ?>" required>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="fw-bold mb-2">Harga</label>
                <input type="number" name="harga" class="form-control" value="<?php echo $data['harga']; ?>" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="fw-bold mb-2">Stok</label>
                <input type="number" name="stok" class="form-control" value="<?php echo $data['stok']; ?>" required>
            </div>
        </div>

        <div class="mb-3">
            <label class="fw-bold mb-2">Deskripsi</label>
            <textarea name="deskripsi" class="form-control" rows="4"><?php echo htmlspecialchars($data['deskripsi']); ?></textarea>
        </div>

        <div class="mb-4">
            <label class="fw-bold mb-2">Gambar Produk</label>
            <?php if($data['gambar'] != 'no-image.jpg'): ?>
                <div class="mb-2">
                    <img src="../assets/img/<?php echo $data['gambar']; ?>" width="100" class="rounded border">
                    <small class="d-block text-muted">Gambar saat ini</small>
                </div>
            <?php endif; ?>
            <input type="file" name="gambar" class="form-control" accept="image/*" onchange="previewImage(event)">
            <div id="preview-container" class="mt-3" style="display:none;">
                <img id="preview-image" src="" alt="Preview" style="max-width: 200px; border-radius: 10px;">
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 border-top pt-3">
            <a href="index.php" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary-custom text-white">Update Produk</button>
        </div>
    </form>
</div>

<script src="<?php echo $base_url; ?>/assets/js/script_produk.js"></script>
<?php include __DIR__ . '/../layouts/footer.php'; ?>