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
// Menggunakan CASE WHEN untuk menentukan data partner (lawan bicara)
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

// Cek error query (untuk debugging jika ada masalah SQL)
if (!$result) {
    die("Error Database: " . mysqli_error($koneksi));
}
?>

<link rel="stylesheet" href="../assets/css/style_partner.css">

<div class="partner-header">
    <div class="container">
        <h2 class="fw-bold mb-0">Kolaborasi Aktif</h2>
        <p class="text-muted mt-2">Daftar mitra yang sedang bekerjasama dengan Anda.</p>
    </div>
</div>

<div class="container mb-5" style="max-width: 1000px;">

    <div class="menu-nav">
        <a href="index.php" class="btn-menu">
            <i class="fa fa-store"></i> Jelajahi Mitra
        </a>
        <a href="request.php" class="btn-menu">
            <i class="fa fa-envelope"></i> Inbox Request
            <?php 
            // Cek notifikasi request pending
            $cek = mysqli_query($koneksi, "SELECT id FROM bundles WHERE mitra_id='$my_id' AND status='pending'");
            if(mysqli_num_rows($cek) > 0) echo "<span class='badge bg-danger rounded-pill ms-1'>!</span>";
            ?>
        </a>
        <a href="my_bundles.php" class="btn-menu active">
            <i class="fa fa-handshake"></i> Kolaborasi Aktif
        </a>
    </div>

    <div class="row g-4">
        <?php if(mysqli_num_rows($result) > 0): ?>
            <?php while($row = mysqli_fetch_assoc($result)): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card-partner h-100">
                    <div class="card-img-wrapper">
                        <img src="<?= !empty($row['foto_partner']) && file_exists('../assets/uploads/'.$row['foto_partner']) 
                                    ? '../assets/uploads/'.$row['foto_partner'] 
                                    : 'https://ui-avatars.com/api/?name='.urlencode($row['nama_partner']).'&background=random&size=200' ?>" 
                             alt="Foto Partner">
                             
                        <div class="card-category bg-success shadow-sm">
                            <i class="fa fa-check-circle me-1"></i> Aktif
                        </div>
                    </div>
                    
                    <div class="card-body-custom d-flex flex-column h-100">
                        <div class="flex-grow-1">
                            <h5 class="fw-bold mb-1 text-dark"><?= htmlspecialchars($row['nama_partner']) ?></h5>
                            
                            <p class="small text-muted mb-3">
                                <span class="badge bg-light text-dark border me-1">
                                    <?= htmlspecialchars($row['kategori_partner'] ?? 'Umum') ?>
                                </span>
                                <i class="fa fa-map-marker-alt text-danger ms-1"></i> <?= htmlspecialchars($row['alamat_partner'] ?? '-') ?>
                            </p>
                            
                            <div class="alert alert-light border small text-muted py-2 px-3 mb-3">
                                <i class="fa fa-calendar-alt me-1 text-primary"></i> 
                                Mulai: <?= date('d M Y', strtotime($row['created_at'])) ?>
                            </div>
                        </div>

                        <a href="chat_room.php?bundle_id=<?= $row['id'] ?>" class="btn btn-cari w-100 rounded-pill">
                            <i class="fa fa-comments me-2"></i> Ruang Diskusi
                        </a>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <div class="bg-white p-5 rounded-4 border shadow-sm d-inline-block">
                    <img src="https://cdni.iconscout.com/illustration/premium/thumb/business-partnership-4439199-3687396.png" width="200" style="opacity: 0.8;" alt="Kosong">
                    <h5 class="mt-4 fw-bold text-dark">Belum ada kolaborasi aktif.</h5>
                    <p class="text-muted">Terima request masuk atau ajak mitra baru di halaman pencarian.</p>
                    <a href="index.php" class="btn btn-outline-warning text-dark mt-2 px-4 rounded-pill fw-bold">Cari Mitra Sekarang</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../layouts/footer.php'; ?>