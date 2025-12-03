<?php
// partner/proses_partner.php
include '../config/koneksi.php';
session_start();

$my_id = $_SESSION['id'] ?? $_SESSION['user_id'] ?? $_SESSION['id_user'] ?? null;
if (!$my_id) {
    header("Location: ../auth/login.php");
    exit;
}

$action = $_POST['action'] ?? '';

// 1. KIRIM REQUEST
if ($action == 'create_request') {
    $mitra_id = $_POST['mitra_id'];
    $pesan = mysqli_real_escape_string($koneksi, $_POST['pesan_awal']);

    // Cek Duplikat
    $cek = mysqli_query($koneksi, "SELECT id FROM bundles WHERE pembuat_id='$my_id' AND mitra_id='$mitra_id' AND status='pending'");
    if (mysqli_num_rows($cek) > 0) {
        echo "<script>alert('Request sudah ada!'); window.location='index.php';</script>";
        exit;
    }

    // Insert Bundle
    $q1 = "INSERT INTO bundles (pembuat_id, mitra_id, status) VALUES ('$my_id', '$mitra_id', 'pending')";
    if (mysqli_query($koneksi, $q1)) {
        $bundle_id = mysqli_insert_id($koneksi);
        // Insert Chat Pembuka
        mysqli_query($koneksi, "INSERT INTO chats (bundle_id, sender_id, message) VALUES ('$bundle_id', '$my_id', '$pesan')");
        echo "<script>alert('Ajakan Terkirim!'); window.location='index.php';</script>";
    }
}

// 2. TERIMA REQUEST
if ($action == 'accept') {
    $bundle_id = $_POST['bundle_id'];
    // Update jadi active
    mysqli_query($koneksi, "UPDATE bundles SET status='active' WHERE id='$bundle_id' AND mitra_id='$my_id'");
    // Redirect ke Chat Room (nanti) atau List Aktif
    echo "<script>alert('Kolaborasi Diterima!'); window.location='my_bundles.php';</script>";
}

// 3. TOLAK REQUEST
if ($action == 'reject') {
    $bundle_id = $_POST['bundle_id'];
    mysqli_query($koneksi, "UPDATE bundles SET status='rejected' WHERE id='$bundle_id' AND mitra_id='$my_id'");
    header("Location: request.php");
}
?>