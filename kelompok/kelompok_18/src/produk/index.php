<?php
// Gunakan __DIR__ agar path include aman dan konsisten
include __DIR__ . '/../config/koneksi.php';
include __DIR__ . '/../layouts/header.php';

// Cek Login (Wajib)
if (!isset($_SESSION['user_id'])) {
    echo "<script>location.href='../auth/login.php';</script>";
    exit;
}

$user_id = $_SESSION['user_id'];

// Ambil Nama Toko (Cek session dulu, kalau tidak ada ambil dari DB)
if (isset($_SESSION['nama_toko'])) {
    $nama_toko = $_SESSION['nama_toko'];
} else {
    $q_user = mysqli_query($koneksi, "SELECT nama_toko FROM users WHERE id='$user_id'");
    $d_user = mysqli_fetch_assoc($q_user);
    $nama_toko = $d_user['nama_toko'] ?? 'Toko Saya';
}

// QUERY AMBIL DATA PRODUK (Tabel: products)
$query = "SELECT * FROM products WHERE user_id = '$user_id' ORDER BY created_at DESC";
$result = mysqli_query($koneksi, $query);
?>

<link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/style_produk.css">

<div class="produk-container container mb-5">
    
    <div class="produk-header d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
        <div>
            <h2 class="fw-bold" style="color: var(--dark-brown);">Kelola Produk</h2>
            <p class="subtitle text-muted mb-0">Toko: <strong><?php echo htmlspecialchars($nama_toko); ?></strong></p>
        </div>
        <a href="tambah.php" class="btn btn-primary-custom text-white text-decoration-none">
            <i class="fa-solid fa-plus me-2"></i> Tambah Produk
        </a>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success border-0 shadow-sm rounded-3">
            <i class="fa-solid fa-check-circle me-2"></i> <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger border-0 shadow-sm rounded-3">
            <i class="fa-solid fa-circle-exclamation me-2"></i> <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <div class="produk-grid">
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while($row = mysqli_fetch_assoc($result)): ?>
                
                <div class="produk-card">
                    <div class="produk-image">
                        <?php if ($row['gambar'] != 'no-image.jpg' && file_exists('../assets/img/'.$row['gambar'])): ?>
                            <img src="../assets/img/<?php echo $row['gambar']; ?>" alt="<?php echo htmlspecialchars($row['nama_produk']); ?>">
                        <?php else: ?>
                            <div class="d-flex align-items-center justify-content-center h-100 text-muted bg-light">
                                <i class="fa-solid fa-image fa-3x opacity-50"></i>
                            </div>
                        <?php endif; ?>
                        
                        <span class="position-absolute top-0 end-0 m-2 badge bg-dark opacity-75">
                            Stok: <?php echo $row['stok']; ?>
                        </span>
                    </div>
                    
                    <div class="produk-content">
                        <span class="produk-kategori"><?php echo ucfirst($row['kategori']); ?></span>
                        
                        <h3 class="text-truncate" title="<?php echo htmlspecialchars($row['nama_produk']); ?>">
                            <?php echo htmlspecialchars($row['nama_produk']); ?>
                        </h3>
                        
                        <div class="mb-3">
                            <span class="produk-harga">Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></span>
                            <span class="produk-satuan text-muted">/ <?php echo htmlspecialchars($row['satuan']); ?></span>
                        </div>
                        
                        <div class="produk-actions">
                            <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn-edit">
                                <i class="fa-solid fa-pen-to-square"></i> Edit
                            </a>
                            <a href="hapus.php?id=<?php echo $row['id']; ?>" class="btn-delete" onclick="return confirm('Yakin ingin menghapus produk ini? (Data bundle terkait juga akan terhapus)')">
                                <i class="fa-solid fa-trash"></i> Hapus
                            </a>
                        </div>
                    </div>
                </div>

            <?php endwhile; ?>
        <?php else: ?>
            
            <div class="text-center py-5 w-100" style="grid-column: 1 / -1;">
                <div class="opacity-50 mb-3">
                    <i class="fa-solid fa-box-open fa-4x text-muted"></i>
                </div>
                <h5 class="text-muted">Belum ada produk.</h5>
                <p class="text-muted small">Mulai tambahkan produk untuk berkolaborasi dengan UMKM lain.</p>
                <a href="tambah.php" class="btn btn-outline-secondary mt-2">Upload Produk Pertama</a>
            </div>

        <?php endif; ?>
    </div>
</div>

<script src="<?php echo $base_url; ?>/assets/js/script_produk.js"></script>
<?php include __DIR__ . '/../layouts/footer.php'; ?>