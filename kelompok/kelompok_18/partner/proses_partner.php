<?php
// partner/proses_partner.php
include '../config/koneksi.php';
session_start();

// Cek Session ID (Auto-detect)
$my_id = $_SESSION['id'] ?? $_SESSION['user_id'] ?? $_SESSION['id_user'] ?? null;
if (!$my_id) {
    header("Location: ../auth/login.php");
    exit;
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

// 1. KIRIM REQUEST KOLABORASI
if ($action == 'create_request') {
    $mitra_id = $_POST['mitra_id'];
    $pesan = mysqli_real_escape_string($koneksi, $_POST['pesan_awal']);

    // Cek duplikasi request
    $cek = mysqli_query($koneksi, "SELECT * FROM bundles WHERE pembuat_id='$my_id' AND mitra_id='$mitra_id' AND status='pending'");
    if (mysqli_num_rows($cek) > 0) {
        echo "<script>alert('Request sudah dikirim sebelumnya!'); window.location='index.php';</script>";
        exit;
    }

    // Insert Bundle (Status Pending)
    $query = "INSERT INTO bundles (pembuat_id, mitra_id, status) VALUES ('$my_id', '$mitra_id', 'pending')";
    if (mysqli_query($koneksi, $query)) {
        $bundle_id = mysqli_insert_id($koneksi);
        // Insert Chat Pertama
        mysqli_query($koneksi, "INSERT INTO chats (bundle_id, sender_id, message) VALUES ('$bundle_id', '$my_id', '$pesan')");
        echo "<script>alert('Ajakan terkirim!'); window.location='index.php';</script>";
    }
}

// 2. TERIMA REQUEST (ACCEPT)
if ($action == 'accept') {
    $bundle_id = $_POST['bundle_id'];
    // Update status jadi active
    $query = "UPDATE bundles SET status='active' WHERE id='$bundle_id' AND mitra_id='$my_id'";
    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Kolaborasi diterima! Silakan diskusi di chat.'); window.location='my_bundles.php';</script>";
    }
}

// 3. TOLAK REQUEST (REJECT)
if ($action == 'reject') {
    $bundle_id = $_POST['bundle_id'];
    $query = "UPDATE bundles SET status='rejected' WHERE id='$bundle_id' AND mitra_id='$my_id'";
    mysqli_query($koneksi, $query);
    header("Location: request.php");
}

// 4. KIRIM CHAT BARU
if ($action == 'send_chat') {
    $bundle_id = $_POST['bundle_id'];
    $message = mysqli_real_escape_string($koneksi, $_POST['message']);
    
    if (!empty($message)) {
        $query = "INSERT INTO chats (bundle_id, sender_id, message) VALUES ('$bundle_id', '$my_id', '$message')";
        mysqli_query($koneksi, $query);
    }
    // Kembali ke halaman chat
    header("Location: chat_room.php?bundle_id=" . $bundle_id);
}
?>