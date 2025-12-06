<?php
include "../layouts/header.php";
include "../config/koneksi.php";

$query = "SELECT * FROM vouchers ORDER BY id DESC";
$result = mysqli_query($conn, $query);
?>

<link rel="stylesheet" href="../assets/css/style_voucher.css">

<div class="page-wrapper">
    <h2 class="title-section">Daftar Voucher</h2>

    <div class="card-table">

        <div class="top-bar">
            <a href="create.php"><button class="btn-add">+ Buat Voucher Baru</button></a>
        </div>

        <table class="table-voucher">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Kode Voucher</th>
                    <th>Tanggal Expired</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            <?php if(mysqli_num_rows($result) > 0) { 
                while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= $row['kode_unik'] ?></td>
                    <td><?= $row['expired_at'] ?: '-' ?></td>
                    <td>
                        <span class="badge-status 
                            <?= ($row['status'] == 'available') ? 'status-available' : 'status-used' ?>">
                            <?= ucfirst($row['status']) ?>
                        </span>
                    </td>
                </tr>
            <?php }} else { ?>
                <tr><td colspan="4" class="empty-data">Tidak ada data voucher</td></tr>
            <?php } ?>
            </tbody>
        </table>

    </div>
</div>

<?php include "../layouts/footer.php"; ?>
