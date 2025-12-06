<?php
include "../layouts/header.php";
include "../config/koneksi.php";

// Voucher mau expired = 3 hari sebelum expired_at
$queryWarning = "SELECT * FROM vouchers 
                 WHERE expired_at > CURDATE()
                 AND expired_at <= DATE_ADD(CURDATE(), INTERVAL 3 DAY)";

$queryExpired = "SELECT * FROM vouchers 
                 WHERE expired_at < CURDATE()";

$queryNormal = "SELECT * FROM vouchers 
                WHERE expired_at > DATE_ADD(CURDATE(), INTERVAL 3 DAY)";

$resWarning = mysqli_query($koneksi, $queryWarning);
$resExpired = mysqli_query($koneksi, $queryExpired);
$resNormal = mysqli_query($koneksi, $queryNormal);
?>
<link rel="stylesheet" href="../assets/css/style_voucher.css">

<h2>Daftar Voucher</h2>

<table border="1" cellpadding="6">
<tr>
    <th>ID</th>
    <th>Kode Voucher</th>
    <th>Expired</th>
    <th>Status</th>
</tr>

<?php while ($row = mysqli_fetch_assoc($resNormal)) { ?>
<tr>
    <td><?= $row['id'] ?></td>
    <td><?= $row['kode_unik'] ?></td>
    <td><?= $row['expired_at'] ?></td>
    <td style="color:green;">Normal</td>
</tr>
<?php } ?>

<?php while ($row = mysqli_fetch_assoc($resWarning)) { ?>
<tr style="background:orange; color:white;">
    <td><?= $row['id'] ?></td>
    <td><?= $row['kode_unik'] ?></td>
    <td><?= $row['expired_at'] ?></td>
    <td>Mendekati Expire</td>
</tr>
<?php } ?>

<?php while ($row = mysqli_fetch_assoc($resExpired)) { ?>
<tr style="background:red; color:white;">
    <td><?= $row['id'] ?></td>
    <td><?= $row['kode_unik'] ?></td>
    <td><?= $row['expired_at'] ?></td>
    <td>Expired</td>
</tr>
<?php } ?>
</table>

<?php include "../layouts/footer.php"; ?>