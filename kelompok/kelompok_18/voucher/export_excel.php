<?php
include "../config/koneksi.php";

// Header file Excel
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Laporan-Voucher.xls");
header("Pragma: no-cache");
header("Expires: 0");

$sql = "SELECT vouchers.*, bundles.nama_bundle
        FROM vouchers
        JOIN bundles ON vouchers.bundle_id = bundles.id
        WHERE vouchers.status = 'used'
        ORDER BY vouchers.created_at DESC";

$q = $conn->query($sql);

echo "<table border='1'>
        <tr>
            <th>Kode Voucher</th>
            <th>Bundle</th>
            <th>Status</th>
            <th>Tanggal Digunakan</th>
        </tr>";

if ($q && $q->num_rows > 0) {
    while ($row = $q->fetch_assoc()) {
        $tanggal = date('d-m-Y H:i', strtotime($row['created_at']));
        echo "
        <tr>
            <td>{$row['kode_unik']}</td>
            <td>{$row['nama_bundle']}</td>
            <td>{$row['status']}</td>
            <td>{$tanggal}</td>
        </tr>";
    }
}

echo "</table>";
