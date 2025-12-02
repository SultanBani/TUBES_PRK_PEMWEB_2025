<?php
include '../config/koneksi.php';
include '../layouts/header.php';
$my_id = $_SESSION['id'] ?? $_SESSION['user_id'] ?? null;

// REQUEST MASUK (Orang lain ngajak kita)
$query_in = "SELECT b.*, u.nama_toko, u.foto_profil 
             FROM bundles b 
             JOIN users u ON b.pembuat_id = u.id 
             WHERE b.mitra_id = '$my_id' AND b.status = 'pending'";
$res_in = mysqli_query($koneksi, $query_in);

// REQUEST KELUAR (Kita ngajak orang)
$query_out = "SELECT b.*, u.nama_toko, u.foto_profil 
              FROM bundles b 
              JOIN users u ON b.mitra_id = u.id 
              WHERE b.pembuat_id = '$my_id' AND b.status = 'pending'";
$res_out = mysqli_query($koneksi, $query_out);
?>
<link rel="stylesheet" href="../assets/css/style_partner.css">

<div class="container py-5">
    <h3 class="fw-bold mb-4">🔔 Request Kolaborasi</h3>
    
    <ul class="nav nav-tabs mb-4">
        <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#masuk">Permintaan Masuk</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#keluar">Terkirim</a></li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane fade show active" id="masuk">
            <div class="row g-3">
                <?php if(mysqli_num_rows($res_in) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($res_in)): ?>
                    <div class="col-md-6">
                        <div class="card p-3 request-card shadow-sm">
                            <div class="d-flex align-items-center gap-3">
                                <img src="<?= !empty($row['foto_profil']) ? '../assets/uploads/'.$row['foto_profil'] : 'https://ui-avatars.com/api/?name='.$row['nama_toko'] ?>" class="rounded-circle" width="60" height="60">
                                <div class="flex-grow-1">
                                    <h5 class="mb-0 fw-bold"><?= $row['nama_toko'] ?></h5>
                                    <small class="text-muted">Mengajak kolaborasi • <?= date('d M Y', strtotime($row['created_at'])) ?></small>
                                </div>
                                <div>
                                    <form action="proses_partner.php" method="POST" class="d-flex gap-2">
                                        <input type="hidden" name="bundle_id" value="<?= $row['id'] ?>">
                                        <button type="submit" name="action" value="accept" class="btn btn-success btn-sm rounded-pill"><i class="fa fa-check"></i> Terima</button>
                                        <button type="submit" name="action" value="reject" class="btn btn-outline-danger btn-sm rounded-pill"><i class="fa fa-times"></i></button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="text-muted text-center py-4">Belum ada permintaan masuk.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="tab-pane fade" id="keluar">
            <div class="row g-3">
                <?php while($row = mysqli_fetch_assoc($res_out)): ?>
                <div class="col-md-6">
                    <div class="card p-3 border shadow-sm">
                        <div class="d-flex align-items-center gap-3">
                            <div class="flex-grow-1">
                                <h6 class="mb-0">Menunggu respon <strong><?= $row['nama_toko'] ?></strong></h6>
                                <span class="badge bg-warning text-dark">Pending</span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</div>
<?php include '../layouts/footer.php'; ?>