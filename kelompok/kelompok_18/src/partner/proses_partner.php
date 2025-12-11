<?php
include '../config/koneksi.php';
session_start();

$my_id = $_SESSION['id'] ?? $_SESSION['user_id'] ?? null;
if (!$my_id) {
    echo "<script>alert('Sesi habis. Silakan login ulang.'); window.location='../auth/login.php';</script>";
    exit;
}

$action = $_POST['action'] ?? '';

function getMasterBundleId($koneksi, $user1, $user2) {
    $q = mysqli_query($koneksi, "SELECT id FROM bundles 
         WHERE ((pembuat_id='$user1' AND mitra_id='$user2') OR (pembuat_id='$user2' AND mitra_id='$user1'))
         ORDER BY id ASC LIMIT 1");
    $d = mysqli_fetch_assoc($q);
    return $d['id'] ?? null;
}

// 1. KIRIM REQUEST / AJAK KOLABORASI
if ($action == 'create_request') {
    $mitra_id    = mysqli_real_escape_string($koneksi, $_POST['mitra_id']);
    $nama_bundle = mysqli_real_escape_string($koneksi, $_POST['nama_bundle'] ?? 'Kolaborasi Baru');
    $pesan_awal  = mysqli_real_escape_string($koneksi, $_POST['pesan_awal']);

    $cek = mysqli_query($koneksi, "SELECT id FROM bundles WHERE pembuat_id='$my_id' AND mitra_id='$mitra_id' AND status='pending'");
    if (mysqli_num_rows($cek) > 0) {
        echo "<script>alert('Request masih pending.'); window.location='index.php';</script>";
        exit;
    }

    $q_bundle = "INSERT INTO bundles (pembuat_id, mitra_id, nama_bundle, status, created_at) 
                 VALUES ('$my_id', '$mitra_id', '$nama_bundle', 'pending', NOW())";
    
    if (mysqli_query($koneksi, $q_bundle)) {
        $new_bundle_id = mysqli_insert_id($koneksi);
        $q_chat = "INSERT INTO chats (bundle_id, sender_id, message, created_at) 
                   VALUES ('$new_bundle_id', '$my_id', '$pesan_awal', NOW())";
        mysqli_query($koneksi, $q_chat);
        echo "<script>alert('Ajakan Terkirim!'); window.location='chat_room.php?bundle_id=$new_bundle_id';</script>";
    } else {
        echo "<script>alert('Gagal: " . mysqli_error($koneksi) . "'); window.location='index.php';</script>";
    }
}

// 2. KIRIM PESAN CHAT (+ FILE UPLOAD)
if ($action == 'send_message') {
    $bundle_id = mysqli_real_escape_string($koneksi, $_POST['bundle_id']);
    $message   = mysqli_real_escape_string($koneksi, $_POST['message']);
    
    $attachment = null;
    $attachment_type = null;

    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == 0) {
        $target_dir = "../assets/uploads/chat/";
        if (!file_exists($target_dir)) { mkdir($target_dir, 0777, true); }

        $file_name = time() . '_' . basename($_FILES["attachment"]["name"]);
        $target_file = $target_dir . $file_name;
        $file_ext = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $img_exts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $doc_exts = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'zip', 'rar'];

        if (in_array($file_ext, $img_exts)) { $attachment_type = 'image'; } 
        elseif (in_array($file_ext, $doc_exts)) { $attachment_type = 'file'; } 
        else {
            echo "<script>alert('Format file tidak didukung!'); window.history.back();</script>"; exit;
        }

        if (move_uploaded_file($_FILES["attachment"]["tmp_name"], $target_file)) {
            $attachment = $file_name;
        }
    }

    if (!empty($bundle_id) && (!empty($message) || !empty($attachment))) {
        $q_b = mysqli_query($koneksi, "SELECT pembuat_id, mitra_id FROM bundles WHERE id='$bundle_id'");
        $d_b = mysqli_fetch_assoc($q_b);
        $partner_id = ($d_b['pembuat_id'] == $my_id) ? $d_b['mitra_id'] : $d_b['pembuat_id'];
        $master_chat_id = getMasterBundleId($koneksi, $my_id, $partner_id);
        if(!$master_chat_id) $master_chat_id = $bundle_id;

        // PERBAIKAN: Handle NULL value untuk attachment agar tidak error 'Data truncated'
        $sql_attachment = $attachment ? "'$attachment'" : "NULL";
        $sql_type       = $attachment_type ? "'$attachment_type'" : "NULL";

        $q_chat = "INSERT INTO chats (bundle_id, sender_id, message, attachment, attachment_type, created_at) 
                   VALUES ('$master_chat_id', '$my_id', '$message', $sql_attachment, $sql_type, NOW())";
        
        if (mysqli_query($koneksi, $q_chat)) {
            header("Location: chat_room.php?bundle_id=$master_chat_id"); exit;
        } else {
            echo "Error: " . mysqli_error($koneksi);
        }
    } else {
        header("Location: chat_room.php?bundle_id=$bundle_id");
    }
}

// 3. TERIMA REQUEST (ACCEPT)
if ($action == 'accept') {
    $bundle_id = $_POST['bundle_id'];
    $update = mysqli_query($koneksi, "UPDATE bundles SET status='active' WHERE id='$bundle_id' AND mitra_id='$my_id'");
    
    if ($update) {
        $sys_msg = "[SISTEM] Kolaborasi disetujui! Silakan mulai diskusi.";
        mysqli_query($koneksi, "INSERT INTO chats (bundle_id, sender_id, message) VALUES ('$bundle_id', '$my_id', '$sys_msg')");
        echo "<script>alert('Kolaborasi Diterima!'); window.location='chat_room.php?bundle_id=$bundle_id';</script>";
    } else {
        echo "<script>alert('Gagal menerima request.'); window.location='request.php';</script>";
    }
}

// 4. TOLAK REQUEST (REJECT)
if ($action == 'reject') {
    $bundle_id = $_POST['bundle_id'];
    mysqli_query($koneksi, "UPDATE bundles SET status='rejected' WHERE id='$bundle_id' AND mitra_id='$my_id'");
    echo "<script>alert('Kolaborasi Ditolak.'); window.location='request.php';</script>";
}

// 5. AJUKAN KESEPAKATAN / DEAL (PROPOSAL)
if ($action == 'propose_deal') {
    $bundle_id = mysqli_real_escape_string($koneksi, $_POST['bundle_id']);
    
    $q_b = mysqli_query($koneksi, "SELECT pembuat_id, mitra_id FROM bundles WHERE id='$bundle_id'");
    $d_b = mysqli_fetch_assoc($q_b);
    $partner_id = ($d_b['pembuat_id'] == $my_id) ? $d_b['mitra_id'] : $d_b['pembuat_id'];
    $master_chat_id = getMasterBundleId($koneksi, $my_id, $partner_id);

    $data_json = json_encode([
        'target_bundle_id' => $bundle_id,
        'nama_bundle' => $_POST['nama_bundle'],
        'harga' => $_POST['harga_bundle'],
        'prod_A' => $_POST['produk_saya'],
        'prod_B' => $_POST['produk_partner'],
        'catatan' => $_POST['pesan_proposal']
    ]);
    $msg = "[DEAL_PROPOSAL]" . $data_json;
    
    mysqli_query($koneksi, "INSERT INTO chats (bundle_id, sender_id, message, created_at) VALUES ('$master_chat_id', '$my_id', '$msg', NOW())");
    echo "<script>window.location='chat_room.php?bundle_id=$master_chat_id';</script>";
}


// 6. TERIMA KESEPAKATAN (ACCEPT DEAL)
if ($action == 'accept_deal_proposal') {
    $chat_id = $_POST['chat_id'];
    
    $q_chat = mysqli_query($koneksi, "SELECT * FROM chats WHERE id='$chat_id'");
    $chat   = mysqli_fetch_assoc($q_chat);
    
    $clean_json = str_replace("[DEAL_PROPOSAL]", "", $chat['message']);
    $data       = json_decode($clean_json, true);
    
    if (isset($data['status']) && $data['status'] == 'accepted') {
        echo "<script>alert('Kesepakatan ini SUDAH disetujui sebelumnya!'); window.location='chat_room.php?bundle_id={$chat['bundle_id']}';</script>";
        exit;
    }

    $target_bundle_id = $data['target_bundle_id'] ?? $chat['bundle_id'];
    $master_chat_id   = $chat['bundle_id'];

    $q_bundle = mysqli_query($koneksi, "SELECT * FROM bundles WHERE id='$target_bundle_id'");
    $bundle   = mysqli_fetch_assoc($q_bundle);

    if ($chat['sender_id'] == $bundle['pembuat_id']) {
        $prod_pembuat = $data['prod_A'];
        $prod_mitra   = $data['prod_B'];
    } else {
        $prod_mitra   = $data['prod_A']; 
        $prod_pembuat = $data['prod_B'];
    }

    $nama_bundle_baru = mysqli_real_escape_string($koneksi, $data['nama_bundle']);
    $harga_baru       = mysqli_real_escape_string($koneksi, $data['harga']);

    if (!empty($bundle['produk_pembuat_id']) || !empty($bundle['produk_mitra_id'])) {
        $pembuat_id = $bundle['pembuat_id'];
        $mitra_id   = $bundle['mitra_id'];
        $query_action = "INSERT INTO bundles (pembuat_id, mitra_id, produk_pembuat_id, produk_mitra_id, nama_bundle, harga_bundle, status, created_at) 
                         VALUES ('$pembuat_id', '$mitra_id', '$prod_pembuat', '$prod_mitra', '$nama_bundle_baru', '$harga_baru', 'active', NOW())";
        $msg_sukses = "Kesepakatan BARU disetujui! Bundle tambahan telah dibuat.";
    } else {
        $query_action = "UPDATE bundles SET 
                         nama_bundle = '$nama_bundle_baru',
                         harga_bundle = '$harga_baru',
                         produk_pembuat_id = '$prod_pembuat',
                         produk_mitra_id = '$prod_mitra',
                         status = 'active'
                         WHERE id = '$target_bundle_id'";
        $msg_sukses = "Kesepakatan PERTAMA disetujui! Bundle kini aktif.";
    }
    
    if (mysqli_query($koneksi, $query_action)) {
        $data['status'] = 'accepted';
        $new_json = json_encode($data);
        $new_message = "[DEAL_PROPOSAL]" . $new_json;
        $new_message_db = mysqli_real_escape_string($koneksi, $new_message);
        mysqli_query($koneksi, "UPDATE chats SET message='$new_message_db' WHERE id='$chat_id'");

        $pesan_mentah = "[SISTEM]  $msg_sukses Cek menu 'Detail Kolaborasi'.";
        $sys_msg = mysqli_real_escape_string($koneksi, $pesan_mentah); 
        mysqli_query($koneksi, "INSERT INTO chats (bundle_id, sender_id, message) VALUES ('$master_chat_id', '$my_id', '$sys_msg')");
        
        echo "<script>alert('$msg_sukses'); window.location='chat_room.php?bundle_id=$master_chat_id';</script>";
    } else {
        echo "<script>alert('Gagal memproses database.'); window.history.back();</script>";
    }
}

// 7. BATALKAN (CANCEL)
if ($action == 'cancel_bundle') {
    $bundle_id = mysqli_real_escape_string($koneksi, $_POST['bundle_id']);
    
    $q_b = mysqli_query($koneksi, "SELECT pembuat_id, mitra_id FROM bundles WHERE id='$bundle_id'");
    $d_b = mysqli_fetch_assoc($q_b);
    $partner_id = ($d_b['pembuat_id'] == $my_id) ? $d_b['mitra_id'] : $d_b['pembuat_id'];
    $master_chat_id = getMasterBundleId($koneksi, $my_id, $partner_id);
    if(!$master_chat_id) $master_chat_id = $bundle_id;

    $update = mysqli_query($koneksi, "UPDATE bundles SET status='cancelled' WHERE id='$bundle_id'");
    if ($update) {
        $sys_msg = "[SISTEM]  Paket kolaborasi dibatalkan.";
        mysqli_query($koneksi, "INSERT INTO chats (bundle_id, sender_id, message) VALUES ('$master_chat_id', '$my_id', '$sys_msg')");
        echo "<script>alert('Kolaborasi dibatalkan.'); window.location='history.php';</script>";
    } else {
        echo "<script>alert('Gagal membatalkan.'); window.location='my_bundles.php';</script>";
    }
}

// 8. AJUKAN VOUCHER (PROPOSAL)
if ($action == 'create_voucher') {
    $target_bundle_id = mysqli_real_escape_string($koneksi, $_POST['bundle_id']);
    $kode_voucher     = strtoupper(mysqli_real_escape_string($koneksi, $_POST['kode_voucher']));
    $cek = mysqli_query($koneksi, "SELECT id FROM vouchers WHERE kode_voucher='$kode_voucher'");
    if (mysqli_num_rows($cek) > 0) { echo "<script>alert('Kode Voucher sudah ada!'); window.history.back();</script>"; exit; }

    $q_b = mysqli_query($koneksi, "SELECT pembuat_id, mitra_id, nama_bundle FROM bundles WHERE id='$target_bundle_id'");
    $d_b = mysqli_fetch_assoc($q_b);
    $partner_id = ($d_b['pembuat_id'] == $my_id) ? $d_b['mitra_id'] : $d_b['pembuat_id'];
    $master_chat_id = getMasterBundleId($koneksi, $my_id, $partner_id);

    $json_voucher = json_encode([
        'target_bundle_id' => $target_bundle_id,
        'nama_bundle'      => $d_b['nama_bundle'],
        'kode'             => $kode_voucher,
        'potongan'         => $_POST['potongan_harga'],
        'kuota'            => $_POST['kuota_maksimal'],
        'expired'          => $_POST['expired_at']
    ]);
    $final_msg = "[VOUCHER_PROPOSAL]" . $json_voucher;
    mysqli_query($koneksi, "INSERT INTO chats (bundle_id, sender_id, message, created_at) VALUES ('$master_chat_id', '$my_id', '$final_msg', NOW())");
    echo "<script>window.location='chat_room.php?bundle_id=$master_chat_id';</script>";
}

// 9. TERIMA VOUCHER (ACCEPT)
if ($action == 'accept_voucher_proposal') {
    $chat_id = $_POST['chat_id'];
    
    $q_chat = mysqli_query($koneksi, "SELECT * FROM chats WHERE id='$chat_id'");
    $chat   = mysqli_fetch_assoc($q_chat);
    
    $clean_json = str_replace("[VOUCHER_PROPOSAL]", "", $chat['message']);
    $data       = json_decode($clean_json, true);

    if (isset($data['status']) && $data['status'] == 'accepted') {
        echo "<script>alert('Voucher ini SUDAH disetujui sebelumnya!'); window.location='chat_room.php?bundle_id={$chat['bundle_id']}';</script>";
        exit;
    }

    $target_bundle_id = $data['target_bundle_id'];
    $master_chat_id   = $chat['bundle_id'];

    $q_ins = "INSERT INTO vouchers (bundle_id, kode_voucher, potongan_harga, kuota_maksimal, expired_at, status) 
              VALUES ('$target_bundle_id', '{$data['kode']}', '{$data['potongan']}', '{$data['kuota']}', '{$data['expired']}', 'available')";

    if (mysqli_query($koneksi, $q_ins)) {
        $data['status'] = 'accepted';
        $new_json = json_encode($data);
        $new_message = "[VOUCHER_PROPOSAL]" . $new_json;
        $new_message_db = mysqli_real_escape_string($koneksi, $new_message);
        mysqli_query($koneksi, "UPDATE chats SET message='$new_message_db' WHERE id='$chat_id'");

        $sys_msg = "[SISTEM]  Voucher Aktif: {$data['kode']}";
        mysqli_query($koneksi, "INSERT INTO chats (bundle_id, sender_id, message) VALUES ('$master_chat_id', '$my_id', '$sys_msg')");
        
        echo "<script>alert('Voucher Disetujui & Aktif!'); window.location='chat_room.php?bundle_id=$master_chat_id';</script>";
    } else {
        echo "<script>alert('Gagal membuat voucher.'); window.history.back();</script>";
    }
}
?>