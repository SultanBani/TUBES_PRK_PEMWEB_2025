<?php
include '../config/koneksi.php';
include '../layouts/header.php';

// 1. Cek Login
$my_id = $_SESSION['id'] ?? $_SESSION['user_id'] ?? null;
if (!$my_id) {
    echo "<script>alert('Sesi habis. Silakan login ulang.'); window.location='../auth/login.php';</script>";
    exit;
}

// 2. Query Data Kolaborasi Aktif (Status = 'active')
$query = "SELECT b.*, 
          CASE 
            WHEN b.pembuat_id = '$my_id' THEN u_mitra.nama_toko 
            ELSE u_buat.nama_toko 
          END as nama_partner,
          CASE 
            WHEN b.pembuat_id = '$my_id' THEN u_mitra.foto_profil 
            ELSE u_buat.foto_profil 
          END as foto_partner,
          CASE 
            WHEN b.pembuat_id = '$my_id' THEN u_mitra.kategori_bisnis 
            ELSE u_buat.kategori_bisnis 
          END as kategori_partner,
          CASE 
            WHEN b.pembuat_id = '$my_id' THEN u_mitra.alamat_toko 
            ELSE u_buat.alamat_toko 
          END as alamat_partner
          FROM bundles b
          JOIN users u_buat ON b.pembuat_id = u_buat.id
          JOIN users u_mitra ON b.mitra_id = u_mitra.id
          WHERE (b.pembuat_id = '$my_id' OR b.mitra_id = '$my_id') 
          AND b.status = 'active'
          ORDER BY b.created_at DESC";

$result = mysqli_query($koneksi, $query);

if (!$result) {
    die("Error Database: " . mysqli_error($koneksi));
}
?>

<link rel="stylesheet" href="../assets/css/style_partner.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<div class="partner-header">
    <div class="container">
        <h2 class="mb-0"><i class="fa fa-handshake me-2"></i> Kolaborasi Aktif</h2>
        <p class="mt-2 mb-0">Kelola dan kembangkan kemitraan bisnis Anda dengan mitra terpercaya.</p>
    </div>
</div>

<div class="container mb-5" style="max-width: 1200px;">

    <div class="menu-nav">
        <a href="index.php" class="btn-menu">
            <i class="fa fa-store"></i> Jelajahi Mitra
        </a>
        <a href="request.php" class="btn-menu">
            <i class="fa fa-envelope"></i> Inbox Request
            <?php 
            $cek = mysqli_query($koneksi, "SELECT id FROM bundles WHERE mitra_id='$my_id' AND status='pending'");
            if(mysqli_num_rows($cek) > 0) echo "<span class='badge bg-danger rounded-pill ms-1'>".mysqli_num_rows($cek)."</span>";
            ?>
        </a>
        <a href="my_bundles.php" class="btn-menu active">
            <i class="fa fa-handshake"></i> Kolaborasi Aktif
        </a>
        <a href="history.php" class="btn-menu">
            <i class="fa fa-history"></i> Riwayat
        </a>
    </div>

    <div class="row g-4">
        <?php if(mysqli_num_rows($result) > 0): ?>
            <?php while($row = mysqli_fetch_assoc($result)): ?>
            <div class="col-md-6 col-xl-4">
                <div class="bundle-card h-100 shadow-sm">
                        <div class="bundle-img-wrapper">
                        <img src="<?= !empty($row['foto_partner']) && file_exists('../assets/uploads/'.$row['foto_partner']) 
                                    ? '../assets/uploads/'.$row['foto_partner'] 
                                    : 'https://ui-avatars.com/api/?name='.urlencode($row['nama_partner']).'&background=random&size=300&bold=true' ?>" 
                             alt="<?= htmlspecialchars($row['nama_partner']) ?>">
                             
                        <div class="card-category bg-success shadow-sm">
                            <i class="fa fa-check-circle me-1"></i> Aktif
                        </div>
                    </div>
                    
                    <div class="card-body-custom">
                        <h5 class="fw-bold mb-2 text-dark">
                            <?= htmlspecialchars($row['nama_partner']) ?>
                        </h5>
                        
                        <div class="mb-3">
                            <span class="badge bg-light text-dark border me-1">
                                <i class="fa fa-tag me-1"></i>
                                <?= htmlspecialchars($row['kategori_partner'] ?? 'Umum') ?>
                            </span>
                        </div>
                        
                        <p class="small text-muted mb-3">
                            <i class="fa fa-map-marker-alt text-danger me-1"></i> 
                            <?= htmlspecialchars($row['alamat_partner'] ?? 'Lokasi tidak tersedia') ?>
                        </p>
                        
                        <div class="alert alert-light border small mb-3 py-2">
                            <i class="fa fa-calendar-alt me-2 text-primary"></i> 
                            <strong>Kolaborasi sejak:</strong><br>
                            <span class="text-muted"><?= date('d F Y', strtotime($row['created_at'])) ?></span>
                        </div>

                        <div class="collaboration-cta">
                            <i class="fas fa-comments"></i>
                            <h6 class="mb-1">Mari Berkolaborasi!</h6>
                            <p class="small mb-3">Diskusikan strategi dan tingkatkan penjualan bersama mitra Anda</p>
                        </div>

                        <div class="d-grid gap-2">
                            <a href="chat_room.php?bundle_id=<?= $row['id'] ?>" class="btn btn-cari rounded-pill">
                                <i class="fa fa-comments me-2"></i> Buka Ruang Diskusi
                            </a>
                            <a href="detail.php?bundle_id=<?= $row['id'] ?>" class="btn btn-outline-secondary rounded-pill">
                                <i class="fa fa-info-circle me-2"></i> Detail Kolaborasi
                            </a>
                            <form action="proses_partner.php" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan kolaborasi ini? Tindakan ini tidak dapat dibatalkan.');">
                                <input type="hidden" name="action" value="cancel_bundle">
                                <input type="hidden" name="bundle_id" value="<?= $row['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger w-100 rounded-pill mt-2">
                                    <i class="fa fa-times-circle me-2"></i> Batalkan Kolaborasi
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="empty-state-wrapper bg-white rounded-4 border shadow-sm">
                    <img src="https://cdni.iconscout.com/illustration/premium/thumb/business-partnership-4439199-3687396.png" 
                         alt="Belum Ada Kolaborasi" 
                         class="img-fluid">
                    <h4 class="fw-bold text-dark mb-3">Belum Ada Kolaborasi Aktif</h4>
                    <p class="text-muted mb-4 mx-auto" style="max-width: 500px;">
                        Mulai perjalanan bisnis Anda dengan menemukan mitra yang tepat. 
                        Terima request yang masuk atau jelajahi katalog mitra untuk kolaborasi baru!
                    </p>
                    <div class="d-flex gap-3 justify-content-center flex-wrap">
                        <a href="index.php" class="btn btn-cari px-4 py-2 rounded-pill">
                            <i class="fa fa-search me-2"></i> Cari Mitra Sekarang
                        </a>
                        <a href="request.php" class="btn btn-outline-warning text-dark px-4 py-2 rounded-pill">
                            <i class="fa fa-envelope me-2"></i> Cek Request Masuk
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../layouts/footer.php'; ?>