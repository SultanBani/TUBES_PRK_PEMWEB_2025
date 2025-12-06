<?php
$pageTitle = "List Voucher";
include "../layouts/header.php";
include "../config/koneksi.php";
?>
<link rel="stylesheet" href="../assets/css/style_voucher.css">
<h2>Daftar Voucher</h2>
<button class="btn btn-add" onclick="window.location='batas_waktu_voucher.php'">Tambah Voucher</button>

<table class="table">
<thead>
<tr>
<th>ID</th>
<th>Kode Unik</th>
<th>Bundle ID</th>
<th>Status</th>
<th>Dibuat</th>
<th>Aksi</th>
</tr>
</thead>
<tbody>
<?php
$q = $conn->query("SELECT * FROM vouchers ORDER BY id DESC");
while ($row = $q->fetch_assoc()) {
    echo "<tr>
            <td>{$row['id']}</td>
            <td>{$row['kode_unik']}</td>
            <td>{$row['bundle_id']}</td>
            <td>{$row['status']}</td>
            <td>{$row['created_at']}</td>
            <td><button class='btn btn-delete' onclick='hapusVoucher({$row['id']})'>Hapus</button></td>
        </tr>";
}
?>
</tbody>
</table>

<?php include "../layouts/footer.php"; ?>