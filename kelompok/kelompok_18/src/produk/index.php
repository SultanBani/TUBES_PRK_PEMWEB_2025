<?php
// Gunakan __DIR__ agar path include aman (Anti Error Path)
include __DIR__ . '/../config/koneksi.php';
include __DIR__ . '/../layouts/header.php';

// Cek Login (Wajib)
if (!isset($_SESSION['user_id'])) {
    echo "<script>location.href='../auth/login.php';</script>";
    exit;
}

$user_id = $_SESSION['user_id'];
$nama_toko = $_SESSION['toko'] ?? 'Toko Saya'; // Ambil nama toko dari session

// QUERY AMBIL DATA PRODUK
// Tabel: 'products' (sesuai database V4.2)
// Filter: Hanya produk milik user yang sedang login
$query = "SELECT * FROM products WHERE user_id = '$user_id' ORDER BY created_at DESC";
$result = mysqli_query($koneksi, $query);
?>

<!-- Panggil CSS Produk -->
<link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/style_produk.css">

<div class="produk-container container mb-5">
    
    <!-- Header Halaman -->
    <div class="produk-header d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
        <div>
            <h2 class="fw-bold" style="color: var(--dark-brown);">Kelola Produk</h2>
            <p class="subtitle text-muted mb-0">Toko: <strong><?php echo htmlspecialchars($nama_toko); ?></strong></p>
        </div>
        <a href="tambah.php" class="btn btn-primary-custom text-white text-decoration-none">
            <i class="fa-solid fa-plus me-2"></i> Tambah Produk
        </a>
    </div>

    <!-- Alert Notifikasi -->
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

    <!-- Grid Produk -->
    <div class="produk-grid">
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while($row = mysqli_fetch_assoc($result)): ?>
                
                <div class="produk-card">
                    <!-- Gambar Produk -->
                    <div class="produk-image">
                        <?php if ($row['gambar'] != 'no-image.jpg' && file_exists('../assets/img/'.$row['gambar'])): ?>
                            <img src="../assets/img/<?php echo $row['gambar']; ?>" alt="<?php echo htmlspecialchars($row['nama_produk']); ?>">
                        <?php else: ?>
                            <!-- Placeholder jika tidak ada gambar -->
                            <div class="d-flex align-items-center justify-content-center h-100 text-muted bg-light">
                                <i class="fa-solid fa-image fa-3x opacity-50"></i>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Badge Stok -->
                        <span class="position-absolute top-0 end-0 m-2 badge bg-dark opacity-75">
                            Stok: <?php echo $row['stok']; ?>
                        </span>
                    </div>
                    
                    <div class="produk-content">
                        <!-- Kategori -->
                        <span class="produk-kategori"><?php echo ucfirst($row['kategori']); ?></span>
                        
                        <!-- Nama Produk -->
                        <h3 class="text-truncate" title="<?php echo htmlspecialchars($row['nama_produk']); ?>">
                            <?php echo htmlspecialchars($row['nama_produk']); ?>
                        </h3>
                        
                        <!-- Info Harga & Satuan -->
                        <div class="mb-3">
                            <span class="produk-harga">Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></span>
                            <span class="produk-satuan text-muted">/ <?php echo htmlspecialchars($row['satuan']); ?></span>
                        </div>
                        
                        <!-- Tombol Aksi -->
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
            
            <!-- Tampilan Kosong (Empty State) -->
            <div class="text-center py-5 w-100" style="grid-column: 1 / -1;">
                <div class="opacity-50 mb-3">
                    <i class="fa-solid fa-box-open fa-4x text-muted"></i>
                </div>
                <h5 class="text-muted">Belum ada produk yang dijual.</h5>
                <p class="text-muted small">Mulai tambahkan produk pertamamu untuk berkolaborasi.</p>
                <a href="tambah.php" class="btn btn-outline-secondary mt-2">Upload Produk Pertama</a>
            </div>

        <?php endif; ?>
    </div>
</div>

<!-- Load Script JS Produk (Untuk Auto Hide Alert, dll) -->
<script src="<?php echo $base_url; ?>/assets/js/script_produk.js"></script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>