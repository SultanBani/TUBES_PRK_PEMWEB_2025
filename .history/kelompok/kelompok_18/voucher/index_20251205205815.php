<?php
include "../layouts/header.php";
include "../config/koneksi.php";  // menggunakan $conn dari file koneksi

// Voucher mendekati expired (3 hari sebelum expired_at)
$queryWarning = "SELECT * FROM vouchers 
                 WHERE expired_at > CURDATE()
                 AND expired_at <= DATE_ADD(CURDATE(), INTERVAL 3 DAY)
                 ORDER BY expired_at ASC";

// Voucher sudah expired
$queryExpired = "SELECT * FROM vouchers 
                 WHERE expired_at < CURDATE()
                 ORDER BY expired_at ASC";

// Voucher masih aman (lebih dari 3 hari)
$queryNormal = "SELECT * FROM vouchers 
                WHERE expired_at > DATE_ADD(CURDATE(), INTERVAL 3 DAY)
                ORDER BY expired_at ASC";

$resWarning = mysqli_query($conn, $queryWarning);
$resExpired = mysqli_query($conn, $queryExpired);
$resNormal = mysqli_query($conn, $queryNormal);
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

<!-- Voucher Normal -->
<?php while ($row = mysqli_fetch_assoc($resNormal)) { ?>
<tr>
    <td><?= $row['id'] ?></td>
    <td><?= $row['kode_unik'] ?></td>
    <td><?= $row['expired_at'] ?></td>
    <td style="color:green;">Normal</td>
</tr>
<?php } ?>

<!-- Voucher Mendekati Expired -->
<?php while ($row = mysqli_fetch_assoc($resWarning)) { ?>
<tr style="background:orange; color:white;">
    <td><?= $row['id'] ?></td>
    <td><?= $row['kode_unik'] ?></td>
    <td><?= $row['expired_at'] ?></td>
    <td>Mendekati Expire</td>
</tr>
<?php } ?>

<!-- Voucher Expired -->
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
