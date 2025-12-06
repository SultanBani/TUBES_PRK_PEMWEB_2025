<?php
session_start();
// Gunakan __DIR__ agar path include aman
include __DIR__ . '/../config/koneksi.php';

// 1. Cek Login (Keamanan)
if (!isset($_SESSION['user_id'])) {
    die("Akses Ditolak. Harap login terlebih dahulu.");
}

$id_user = $_SESSION['user_id'];

// 2. Header untuk memaksa browser mendownload sebagai Excel
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Laporan-Voucher-Saya.xls");
header("Pragma: no-cache");
header("Expires: 0");

// 3. Query Data (Hanya ambil voucher milik user yang login)
$query = "SELECT v.*, b.nama_bundle 
          FROM vouchers v
          JOIN bundles b ON v.bundle_id = b.id
          WHERE b.pembuat_id = '$id_user' OR b.mitra_id = '$id_user'
          ORDER BY v.created_at DESC";

$result = mysqli_query($koneksi, $query);

// 4. Buat Tabel HTML (Excel akan membacanya sebagai sel)
?>

<table border="1">
    <thead>
        <tr style="background-color: #ED7D31; color: white;">
            <th>No</th>
            <th>Kode Voucher</th>
            <th>Nama Bundle</th>
            <th>Nilai Diskon</th>
            <th>Kuota Terpakai</th>
            <th>Batas Kuota</th>
            <th>Tanggal Expired</th>
            <th>Status</th>
            <th>Tanggal Dibuat</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $no = 1;
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                // Format Status
                $status = 'Aktif';
                if ($row['status'] == 'expired') { $status = 'Kadaluarsa'; }
                elseif ($row['status'] == 'used') { $status = 'Habis'; } // Di DB baru enumnya 'available', 'used', 'expired' (used bisa kita anggap habis)
                
                // Format Tanggal
                $expired = $row['expired_at'] ? date('d-m-Y', strtotime($row['expired_at'])) : 'Seumur Hidup';
                $dibuat  = date('d-m-Y H:i', strtotime($row['created_at']));
        ?>
            <tr>
                <td><?= $no++; ?></td>
                <td style="font-weight: bold;"><?= $row['kode_voucher']; ?></td>
                <td><?= $row['nama_bundle']; ?></td>
                <td>Rp <?= number_format($row['potongan_harga'], 0, ',', '.'); ?></td>
                <td style="text-align: center;"><?= $row['kuota_terpakai']; ?></td>
                <td style="text-align: center;"><?= $row['kuota_maksimal']; ?></td>
                <td><?= $expired; ?></td>
                <td><?= $status; ?></td>
                <td><?= $dibuat; ?></td>
            </tr>
        <?php 
            }
        } else {
            echo "<tr><td colspan='9' style='text-align:center;'>Belum ada data voucher.</td></tr>";
        }
        ?>
    </tbody>
</table>