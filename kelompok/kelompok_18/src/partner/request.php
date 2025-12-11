<?php
include '../config/koneksi.php';
include '../layouts/header.php';
$my_id = $_SESSION['id'] ?? $_SESSION['user_id'] ?? null;
if (!$my_id) echo "<script>window.location='../auth/login.php';</script>";

$query_in = "SELECT b.*, u.nama_toko, u.foto_profil, c.message as pesan_awal
             FROM bundles b 
             JOIN users u ON b.pembuat_id = u.id 
             LEFT JOIN chats c ON c.bundle_id = b.id AND c.id = (SELECT MIN(id) FROM chats WHERE bundle_id = b.id)
             WHERE b.mitra_id = '$my_id' AND b.status = 'pending'";
$res_in = mysqli_query($koneksi, $query_in);

$query_out = "SELECT b.*, u.nama_toko, u.foto_profil 
              FROM bundles b 
              JOIN users u ON b.mitra_id = u.id 
              WHERE b.pembuat_id = '$my_id' AND b.status = 'pending'";
$res_out = mysqli_query($koneksi, $query_out);
?>

<link rel="stylesheet" href="../assets/css/style_partner.css">

<style>
    .menu-nav {
        display: flex;
        flex-wrap: wrap; 
        justify-content: center;
        gap: 10px;
    }

    @media (max-width: 576px) {
        .request-box {
            flex-direction: column; 
            align-items: flex-start !important;
            text-align: left;
        }
        
        .request-box img {
            margin-bottom: 15px; 
        }
        
        .request-box .d-flex {
            width: 100%;
            margin-top: 15px;
            justify-content: stretch;
        }
        
        .request-box .btn {
            flex: 1; 
        }
        
        .btn-menu {
            font-size: 0.85rem;
            padding: 8px 15px;
        }
    }
</style>

<div class="partner-header">
    <div class="container">
        <h2 class="fw-bold mb-0">Inbox Request</h2>
    </div>
</div>

<div class="container mb-5" style="max-width: 900px;">

    <div class="menu-nav">
        <a href="index.php" class="btn-menu">Jelajahi Mitra</a>
        <a href="request.php" class="btn-menu active">Inbox Request</a>
        <a href="my_bundles.php" class="btn-menu">Kolaborasi Aktif</a>
        <a href="agreements.php" class="btn-menu">Produk Deal</a>
        <a href="history.php" class="btn-menu">
            <i class="fa fa-history"></i> Riwayat
        </a>
    </div>

    <ul class="nav nav-tabs mb-4 justify-content-center border-0">
        <li class="nav-item">
            <a class="nav-link active fw-bold text-dark border-0 border-bottom border-warning border-3" data-bs-toggle="tab" href="#masuk">Masuk (<?= mysqli_num_rows($res_in) ?>)</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-muted border-0" data-bs-toggle="tab" href="#keluar">Terkirim</a>
        </li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane fade show active" id="masuk">
            <?php if(mysqli_num_rows($res_in) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($res_in)): ?>
                <div class="request-box">
                    <img src="<?= !empty($row['foto_profil']) ? '../assets/uploads/'.$row['foto_profil'] : 'https://ui-avatars.com/api/?name='.urlencode($row['nama_toko']) ?>" class="rounded-circle" width="60" height="60">
                    
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between">
                            <h5 class="fw-bold mb-1"><?= htmlspecialchars($row['nama_toko']) ?></h5>
                            <small class="text-muted"><?= date('d M', strtotime($row['created_at'])) ?></small>
                        </div>
                        <p class="text-muted small mb-0">"<?= htmlspecialchars(substr($row['pesan_awal'], 0, 80)) ?>..."</p>
                    </div>

                    <div class="d-flex gap-2">
                        <form action="proses_partner.php" method="POST">
                            <input type="hidden" name="bundle_id" value="<?= $row['id'] ?>">
                            <button type="submit" name="action" value="accept" class="btn btn-success btn-sm px-3">Terima</button>
                            <button type="submit" name="action" value="reject" class="btn btn-outline-danger btn-sm px-3">Tolak</button>
                        </form>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="text-center py-5 text-muted">Belum ada permintaan masuk.</div>
            <?php endif; ?>
        </div>

        <div class="tab-pane fade" id="keluar">
            <?php if(mysqli_num_rows($res_out) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($res_out)): ?>
                <div class="request-box" style="opacity: 0.8;">
                    <img src="<?= !empty($row['foto_profil']) ? '../assets/uploads/'.$row['foto_profil'] : 'https://ui-avatars.com/api/?name='.urlencode($row['nama_toko']) ?>" class="rounded-circle grayscale" width="50" height="50">
                    <div class="flex-grow-1">
                        <h6 class="fw-bold mb-0">Menunggu respon <?= htmlspecialchars($row['nama_toko']) ?></h6>
                        <small class="text-warning">Status: Pending</small>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="text-center py-5 text-muted">Belum ada request terkirim.</div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php include '../layouts/footer.php'; ?>