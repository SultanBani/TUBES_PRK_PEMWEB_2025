<?php
session_start();
require_once '../config/koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$nama_toko = $_SESSION['nama_toko'] ?? 'Toko Saya';

$query = "SELECT * FROM produk WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

include '../layouts/header.php';
?>

<div class="produk-container">
    <div class="produk-header">
        <div>
            <h1>Kelola Produk</h1>
            <p class="subtitle">Toko: <?php echo htmlspecialchars($nama_toko); ?></p>
        </div>
        <a href="tambah.php" class="btn-primary">+ Tambah Produk</a>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?php 
            echo $_SESSION['success']; 
            unset($_SESSION['success']);
            ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error">
            <?php 
            echo $_SESSION['error']; 
            unset($_SESSION['error']);
            ?>
        </div>
    <?php endif; ?>

    <div class="produk-grid">
        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="produk-card">
                    <div class="produk-image">
                        <?php if ($row['gambar']): ?>
                            <img src="../assets/img/<?php echo htmlspecialchars($row['gambar']); ?>" alt="<?php echo htmlspecialchars($row['nama_produk']); ?>">
                        <?php else: ?>
                            <div class="no-image">Tidak ada gambar</div>
                        <?php endif; ?>
                    </div>
                    <div class="produk-content">
                        <span class="produk-kategori"><?php echo htmlspecialchars($row['kategori']); ?></span>
                        <h3><?php echo htmlspecialchars($row['nama_produk']); ?></h3>
                        <p class="produk-deskripsi"><?php echo htmlspecialchars(substr($row['deskripsi'], 0, 80)); ?><?php echo strlen($row['deskripsi']) > 80 ? '...' : ''; ?></p>
                        <div class="produk-info">
                            <span class="produk-harga">Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></span>
                            <span class="produk-satuan">/<?php echo htmlspecialchars($row['satuan']); ?></span>
                        </div>
                        <div class="produk-actions">
                            <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn-edit">Edit</a>
                            <a href="hapus.php?id=<?php echo $row['id']; ?>" class="btn-delete" onclick="return confirm('Yakin ingin menghapus produk ini?')">Hapus</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="empty-state">
                <p>Belum ada produk. Yuk tambahkan produk pertamamu!</p>
                <a href="tambah.php" class="btn-primary">Tambah Produk Sekarang</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$stmt->close();
$conn->close();
include '../layouts/footer.php';
?>