<?php
// partner/proses_partner.php
include '../config/koneksi.php';
session_start();

// 1. Cek Login
$my_id = $_SESSION['id'] ?? $_SESSION['user_id'] ?? null;
if (!$my_id) {
    echo "<script>alert('Sesi habis. Silakan login ulang.'); window.location='../auth/login.php';</script>";
    exit;
}

$action = $_POST['action'] ?? '';

// ==========================================
// 1. KIRIM REQUEST / BUAT BUNDLE BARU
// ==========================================
if ($action == 'create_request') {
    $mitra_id    = mysqli_real_escape_string($koneksi, $_POST['mitra_id']);
    $nama_bundle = mysqli_real_escape_string($koneksi, $_POST['nama_bundle'] ?? 'Kolaborasi Baru');
    $pesan_awal  = mysqli_real_escape_string($koneksi, $_POST['pesan_awal']);

    // Cek apakah sudah ada request pending dengan mitra ini?
    $cek = mysqli_query($koneksi, "SELECT id FROM bundles WHERE pembuat_id='$my_id' AND mitra_id='$mitra_id' AND status='pending'");
    
    if (mysqli_num_rows($cek) > 0) {
        echo "<script>alert('Request kolaborasi masih menunggu konfirmasi (Pending).'); window.location='chat_room.php?partner_id=$mitra_id';</script>";
        exit;
    }

    // Insert ke tabel bundles
    $q_bundle = "INSERT INTO bundles (pembuat_id, mitra_id, nama_bundle, status, created_at) 
                 VALUES ('$my_id', '$mitra_id', '$nama_bundle', 'pending', NOW())";
    
    if (mysqli_query($koneksi, $q_bundle)) {
        $new_bundle_id = mysqli_insert_id($koneksi);

        // Insert pesan pembuka ke tabel chats
        $q_chat = "INSERT INTO chats (bundle_id, sender_id, message, created_at) 
                   VALUES ('$new_bundle_id', '$my_id', '$pesan_awal', NOW())";
        mysqli_query($koneksi, $q_chat);

        echo "<script>alert('Ajakan Kolaborasi Terkirim!'); window.location='chat_room.php?bundle_id=$new_bundle_id';</script>";
    } else {
        echo "<script>alert('Gagal mengirim request: " . mysqli_error($koneksi) . "'); window.location='index.php';</script>";
    }
}

// ==========================================
// 2. KIRIM PESAN CHAT (BACKEND CHAT ROOM)
// ==========================================
if ($action == 'send_message') {
    $bundle_id = mysqli_real_escape_string($koneksi, $_POST['bundle_id']);
    $message   = mysqli_real_escape_string($koneksi, $_POST['message']);

    if (!empty($bundle_id) && !empty($message)) {
        $q_chat = "INSERT INTO chats (bundle_id, sender_id, message, created_at) 
                   VALUES ('$bundle_id', '$my_id', '$message', NOW())";
        
        if (mysqli_query($koneksi, $q_chat)) {
            // Berhasil, kembali ke chat room
            header("Location: chat_room.php?bundle_id=$bundle_id");
            exit;
        } else {
            echo "Error: " . mysqli_error($koneksi);
        }
    } else {
        echo "<script>alert('Pesan tidak boleh kosong!'); window.history.back();</script>";
    }
}

// ==========================================
// 3. TERIMA REQUEST (ACCEPT)
// ==========================================
if ($action == 'accept') {
    $bundle_id = $_POST['bundle_id'];
    
    // Update status jadi active
    $update = mysqli_query($koneksi, "UPDATE bundles SET status='active' WHERE id='$bundle_id' AND mitra_id='$my_id'");
    
    if ($update) {
        // Kirim notifikasi sistem di chat
        $sys_msg = "[SISTEM] Kolaborasi telah disetujui! Silakan mulai diskusi.";
        mysqli_query($koneksi, "INSERT INTO chats (bundle_id, sender_id, message) VALUES ('$bundle_id', '$my_id', '$sys_msg')");
        
        // Tetap di chat room, jangan redirect ke my_bundles
        echo "<script>alert('Kolaborasi Diterima!'); window.location='chat_room.php?bundle_id=$bundle_id';</script>";
    } else {
        echo "<script>alert('Gagal menerima request.'); window.location='request.php';</script>";
    }
}

// ==========================================
// 4. TOLAK REQUEST (REJECT)
// ==========================================
if ($action == 'reject') {
    $bundle_id = $_POST['bundle_id'];
    
    // Update status jadi rejected
    mysqli_query($koneksi, "UPDATE bundles SET status='rejected' WHERE id='$bundle_id' AND mitra_id='$my_id'");
    
    echo "<script>alert('Kolaborasi Ditolak.'); window.location='request.php';</script>";
}

// ==========================================
// 5. BATALKAN KOLABORASI (CANCEL) - BARU
// ==========================================
if ($action == 'cancel_bundle') {
    $bundle_id = mysqli_real_escape_string($koneksi, $_POST['bundle_id']);
    
    // Validasi: Pastikan bundle milik user ini (baik sebagai pembuat atau mitra)
    $cek = mysqli_query($koneksi, "SELECT id FROM bundles WHERE id='$bundle_id' AND (pembuat_id='$my_id' OR mitra_id='$my_id')");
    
    if (mysqli_num_rows($cek) > 0) {
        // Update status jadi cancelled
        $update = mysqli_query($koneksi, "UPDATE bundles SET status='cancelled' WHERE id='$bundle_id'");
        
        if ($update) {
            // Kirim pesan sistem agar tercatat di chat
            $sys_msg = "[SISTEM] Kolaborasi telah dibatalkan.";
            mysqli_query($koneksi, "INSERT INTO chats (bundle_id, sender_id, message) VALUES ('$bundle_id', '$my_id', '$sys_msg')");
            
            // Redirect ke history karena statusnya sudah batal
            echo "<script>alert('Kolaborasi berhasil dibatalkan.'); window.location='history.php';</script>";
        } else {
            echo "<script>alert('Gagal membatalkan kolaborasi.'); window.location='my_bundles.php';</script>";
        }
    } else {
        echo "<script>alert('Akses ditolak.'); window.location='my_bundles.php';</script>";
    }
}
?>