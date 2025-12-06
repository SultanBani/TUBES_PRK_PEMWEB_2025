<?php
include "../layouts/header.php";
include "../config/koneksi.php";

// Query semua voucher
$query = "SELECT * FROM vouchers ORDER BY expired_at ASC";
$result = mysqli_query($conn, $query);
?>

<link rel="stylesheet" href="../assets/css/style_voucher.css">

<div class="voucher-container">
    <h2>Daftar Voucher</h2>

    <table class="table-voucher">
        <tr>
            <th>ID</th>
            <th>Kode Voucher</th>
            <th>Tanggal Expired</th>
            <th>Status</th>
        </tr>

        <?php while ($row = mysqli_fetch_assoc($result)) {
            $statusColor = ($row['status'] == 'available') ? "status-available" : "status-used";
        ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= $row['kode_unik'] ?></td>
                <td><?= $row['expired_at'] ?></td>
                <td><span class="status <?= $statusColor ?>"><?= $row['status'] ?></span></td>
            </tr>
        <?php } ?>
    </table>
</div>

<?php include "../layouts/footer.php"; ?>
