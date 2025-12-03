<?php
// Perhatikan path include-nya (Karena file ini ada di luar/root, jadi langsung panggil foldernya)
include 'config/koneksi.php';
include 'layouts/header.php';

// --- LOGIC PENCARIAN ---
$where = "";
if (isset($_GET['cari'])) {
    $keyword = mysqli_real_escape_string($koneksi, $_GET['cari']); // Sanitize input biar aman
    $where = "AND (products.nama_produk LIKE '%$keyword%' OR products.deskripsi LIKE '%$keyword%')";
}

// --- QUERY UTAMA ---
// Mengambil data produk + nama toko pemiliknya
$query = "SELECT products.*, users.nama_toko, users.nama_lengkap 
          FROM products 
          JOIN users ON products.user_id = users.id 
          WHERE 1=1 $where 
          ORDER BY products.id DESC";

$result = mysqli_query($koneksi, $query);
?>

<!-- === HEADER HERO SECTION === -->
<div class="bg-light py-5 mb-4 border-bottom">
    <div class="container text-center">
        <h1 class="fw-bold display-5" style="color: var(--dark-text);">Katalog Produk UMKM</h1>
        <p class="lead text-muted mb-4">Jelajahi ratusan produk lokal dan temukan peluang kolaborasi.</p>
        
        <!-- Form Pencarian -->
        <div class="row justify-content-center">
            <div class="col-md-6">
                <form action="" method="GET">
                    <div class="input-group input-group-lg shadow-sm">
                        <input type="text" name="cari" class="form-control border-0" placeholder="Cari kopi, keripik, jasa..." value="<?php echo isset($_GET['cari']) ? htmlspecialchars($_GET['cari']) : ''; ?>">
                        <button class="btn btn-primary px-4" type="submit">
                            <i class="fa-solid fa-search"></i> Cari
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- === GRID PRODUK === -->
<div class="container mb-5">
    
    <!-- Info Hasil Pencarian -->
    <?php if(isset($_GET['cari'])): ?>
        <div class="alert alert-info mb-4">
            Menampilkan hasil pencarian untuk: <strong>"<?php echo htmlspecialchars($_GET['cari']); ?>"</strong>
            <a href="katalog.php" class="float-end text-decoration-none fw-bold">Reset</a>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        
        <?php if(mysqli_num_rows($result) > 0): ?>
            <?php while($row = mysqli_fetch_assoc($result)): ?>
                
                <!-- START CARD ITEM -->
                <div class="col-md-3 col-sm-6">
                    <div class="card h-100 shadow-sm border-0 product-card hover-effect">
                        
                        <!-- Gambar Produk -->
                        <div style="height: 200px; overflow: hidden; background: #f8f9fa;" class="d-flex align-items-center justify-content-center position-relative">
                            <?php if($row['gambar'] != 'no-image.jpg' && file_exists('assets/img/'.$row['gambar'])): ?>
                                <img src="assets/img/<?php echo $row['gambar']; ?>" class="w-100 h-100" style="object-fit: cover;" alt="<?php echo $row['nama_produk']; ?>">
                            <?php else: ?>
                                <div class="text-center text-muted">
                                    <i class="fa-solid fa-image fa-3x mb-2"></i><br>
                                    <small>No Image</small>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Badge Stok -->
                            <span class="position-absolute top-0 end-0 badge bg-dark m-2 opacity-75">
                                Stok: <?php echo $row['stok']; ?>
                            </span>
                        </div>

                        <div class="card-body">
                            <!-- Nama Toko -->
                            <small class="text-uppercase fw-bold text-muted d-block mb-1" style="font-size: 0.75rem;">
                                <i class="fa-solid fa-store text-warning"></i> <?php echo htmlspecialchars($row['nama_toko']); ?>
                            </small>
                            
                            <!-- Nama Produk -->
                            <h5 class="card-title fw-bold text-dark text-truncate" title="<?php echo htmlspecialchars($row['nama_produk']); ?>">
                                <?php echo htmlspecialchars($row['nama_produk']); ?>
                            </h5>
                            
                            <!-- Harga -->
                            <p class="card-text text-primary fw-bold fs-5 mb-2">
                                Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?>
                            </p>
                            
                            <!-- Deskripsi Singkat -->
                            <p class="card-text text-muted small text-truncate">
                                <?php echo htmlspecialchars($row['deskripsi']); ?>
                            </p>
                        </div>

                        <!-- Footer Card: Tombol Aksi -->
                        <div class="card-footer bg-white border-0 pb-3 pt-0">
                            <div class="d-grid gap-2">
                                
                                <?php if(!isset($_SESSION['status'])): ?>
                                    <!-- LOGIKA 1: PENGUNJUNG UMUM (Gak Login) -->
                                    <!-- Fitur Ambil Voucher Promo -->
                                    <button type="button" class="btn btn-warning text-white fw-bold btn-sm" data-bs-toggle="modal" data-bs-target="#modalVoucher<?php echo $row['id']; ?>">
                                        <i class="fa-solid fa-ticket"></i> Cek Promo
                                    </button>
                                    
                                <?php elseif($_SESSION['user_id'] == $row['user_id']): ?>
                                    <!-- LOGIKA 2: PEMILIK TOKO SENDIRI -->
                                    <a href="produk/index.php" class="btn btn-outline-secondary btn-sm">
                                        <i class="fa-solid fa-gear"></i> Kelola Produk
                                    </a>

                                <?php else: ?>
                                    <!-- LOGIKA 3: UMKM LAIN (Member) -->
                                    <a href="partner/detail.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm">
                                        <i class="fa-solid fa-handshake"></i> Ajak Kolaborasi
                                    </a>
                                <?php endif; ?>

                            </div>
                        </div>
                    </div>
                </div>
                <!-- END CARD ITEM -->

                <!-- MODAL VOUCHER (Untuk Pengunjung Umum) -->
                <div class="modal fade" id="modalVoucher<?php echo $row['id']; ?>" tabindex="-1" aria-hidden="true">
                  <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content text-center p-4 border-0 shadow">
                        <div class="mb-3">
                            <i class="fa-solid fa-gift fa-3x text-warning"></i>
                        </div>
                        <h4 class="fw-bold text-dark">Promo Spesial!</h4>
                        <p class="text-muted">Gunakan kode voucher di bawah ini saat bertransaksi di toko <strong><?php echo $row['nama_toko']; ?></strong>.</p>
                        
                        <!-- Placeholder Kode Voucher (Nanti Person 4 bikin dinamis ambil dari DB) -->
                        <div class="bg-light p-3 rounded-3 border border-dashed mb-3 position-relative">
                            <h2 class="mb-0 fw-bold text-primary ls-2">HEMAT50</h2>
                            <small class="text-muted fst-italic mt-2 d-block">*Contoh Kode Promo</small>
                        </div>
                        
                        <button type="button" class="btn btn-outline-secondary w-100" data-bs-dismiss="modal">Tutup</button>
                    </div>
                  </div>
                </div>

            <?php endwhile; ?>
        <?php else: ?>
            
            <!-- Tampilan Jika Produk Kosong -->
            <div class="col-12 text-center py-5">
                <div class="opacity-50 mb-3">
                    <i class="fa-regular fa-folder-open fa-5x"></i>
                </div>
                <h3 class="fw-bold text-muted">Belum ada produk ditemukan.</h3>
                <p class="text-muted">Coba kata kunci lain atau jadilah yang pertama mengupload produk!</p>
                <?php if(isset($_SESSION['status'])): ?>
                    <a href="produk/tambah.php" class="btn btn-primary mt-2">Upload Produk Sekarang</a>
                <?php else: ?>
                    <a href="auth/login.php" class="btn btn-primary mt-2">Login untuk Upload</a>
                <?php endif; ?>
            </div>

        <?php endif; ?>

    </div>
</div>

<!-- Tambahan CSS Khusus Halaman Ini -->
<style>
    .product-card {
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }
    .ls-2 { letter-spacing: 2px; }
</style>

<?php include 'layouts/footer.php'; ?>