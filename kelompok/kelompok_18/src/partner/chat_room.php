<?php
include '../config/koneksi.php';
include '../layouts/header.php';

// 1. Cek Sesi
$my_id = $_SESSION['id'] ?? $_SESSION['user_id'] ?? null;
if (!$my_id) {
    echo "<script>alert('Sesi habis.'); window.location='../auth/login.php';</script>";
    exit;
}

// ==========================================
// LOGIKA TOMBOL KEMBALI (SMART BACK BUTTON)
// ==========================================
// Simpan URL asal ke session agar tidak hilang saat reload/kirim pesan
if (!isset($_SESSION['chat_back_url'])) {
    $_SESSION['chat_back_url'] = 'my_bundles.php'; // Default fallback
}

// Deteksi jika user baru masuk dari halaman lain (bukan reload dari chat_room atau proses_partner)
if (isset($_SERVER['HTTP_REFERER'])) {
    $ref = $_SERVER['HTTP_REFERER'];
    // Cek apakah referer BUKAN dari chat_room sendiri atau proses_partner
    if (strpos($ref, 'chat_room.php') === false && strpos($ref, 'proses_partner.php') === false) {
        if (strpos($ref, 'index.php') !== false) {
            $_SESSION['chat_back_url'] = 'index.php';
        } elseif (strpos($ref, 'request.php') !== false) {
            $_SESSION['chat_back_url'] = 'request.php';
        } elseif (strpos($ref, 'history.php') !== false) {
            $_SESSION['chat_back_url'] = 'history.php';
        } elseif (strpos($ref, 'my_bundles.php') !== false) {
            $_SESSION['chat_back_url'] = 'my_bundles.php';
        }
    }
}
$back_url = $_SESSION['chat_back_url'];
// ==========================================


// 2. Inisialisasi Data
$partner_id = null;
$bundle_id  = null;
$bundle_data = null;

// Ambil Bundle ID / Partner ID dari URL
if (isset($_GET['bundle_id'])) {
    $bid = mysqli_real_escape_string($koneksi, $_GET['bundle_id']);
    $q_cek = mysqli_query($koneksi, "SELECT * FROM bundles WHERE id='$bid'");
    $bundle_data = mysqli_fetch_assoc($q_cek);
    if ($bundle_data) {
        $bundle_id = $bid;
        $partner_id = ($bundle_data['pembuat_id'] == $my_id) ? $bundle_data['mitra_id'] : $bundle_data['pembuat_id'];
    }
} elseif (isset($_GET['partner_id'])) {
    $partner_id = mysqli_real_escape_string($koneksi, $_GET['partner_id']);
    // Cek bundle terakhir untuk melanjutkan chat
    $q_last = mysqli_query($koneksi, "SELECT * FROM bundles WHERE (pembuat_id='$my_id' AND mitra_id='$partner_id') OR (pembuat_id='$partner_id' AND mitra_id='$my_id') ORDER BY created_at DESC LIMIT 1");
    $bundle_data = mysqli_fetch_assoc($q_last);
    if ($bundle_data) $bundle_id = $bundle_data['id'];
}

// Validasi Partner
if (!$partner_id) {
    echo "<script>window.location='index.php';</script>";
    exit;
}

// Ambil Profil Partner
$partner = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM users WHERE id='$partner_id'"));

// Ambil Chat
$chats = [];
if ($bundle_id) {
    $q_chat = mysqli_query($koneksi, "SELECT * FROM chats WHERE bundle_id='$bundle_id' ORDER BY created_at ASC");
}
?>

<link rel="stylesheet" href="../assets/css/style_partner.css?v=<?= time(); ?>">

<div class="container-fluid px-0"> 
    <div class="chat-container full-screen-chat">
        
        <div class="chat-header">
            <div class="d-flex align-items-center gap-3">
                <a href="<?= $back_url ?>" class="text-secondary me-2"><i class="fa fa-arrow-left fa-lg"></i></a>
                
                <div class="position-relative">
                    <img src="<?= !empty($partner['foto_profil']) && file_exists('../assets/uploads/'.$partner['foto_profil']) ? '../assets/uploads/'.$partner['foto_profil'] : 'https://ui-avatars.com/api/?name='.urlencode($partner['nama_toko']).'&background=D7CCC8&color=6D4C41' ?>" 
                         class="rounded-circle border" width="45" height="45" style="object-fit: cover;">
                </div>
                <div>
                    <h6 class="mb-0 fw-bold"><?= htmlspecialchars($partner['nama_toko'] ?? '') ?></h6>
                    <div class="partner-status">
                        <span>Online</span>
                    </div>
                </div>
            </div>
            
            <div>
                <?php if (!$bundle_id): ?>
                    <button class="btn btn-primary btn-sm rounded-pill px-4 shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalAjak">
                        <i class="fa fa-plus me-1"></i> Kolaborasi
                    </button>
                <?php else: ?>
                    <span class="badge bg-light text-dark border px-3 py-2 rounded-pill">
                        <i class="fa fa-handshake text-primary me-1"></i> <?= htmlspecialchars($bundle_data['nama_bundle'] ?? '') ?>
                    </span>
                <?php endif; ?>
            </div>
        </div>

        <div class="chat-area" id="chatBox">
            <?php if ($bundle_id && mysqli_num_rows($q_chat) > 0): ?>
                <?php while($c = mysqli_fetch_assoc($q_chat)): ?>
                    <?php 
                        $isMe = ($c['sender_id'] == $my_id);
                        $time = date('H:i', strtotime($c['created_at']));
                        $pesan_raw = $c['message'] ?? ''; 
                        
                        $isSystem = strpos($pesan_raw, 'Halo, saya mengajukan') !== false || strpos($pesan_raw, '[SISTEM]') !== false;
                    ?>

                    <?php if($isSystem): ?>
                        <div class="system-message">
                            <span class="system-badge">
                                <?= htmlspecialchars($pesan_raw) ?>
                            </span>
                        </div>
                    <?php else: ?>
                        <div class="message-wrapper <?= $isMe ? 'me' : 'them' ?>">
                            <div class="message-bubble">
                                <?= nl2br(htmlspecialchars($pesan_raw)) ?>
                                <span class="msg-time">
                                    <?= $time ?> 
                                    <?= $isMe ? '<i class="fa fa-check-double ms-1"></i>' : '' ?>
                                </span>
                            </div>
                        </div>
                    <?php endif; ?>

                <?php endwhile; ?>
            <?php else: ?>
                <div class="d-flex flex-column align-items-center justify-content-center h-100 opacity-50">
                    <i class="fa fa-comments fa-4x text-muted mb-3"></i>
                    <p class="text-muted">Belum ada percakapan dengan <strong><?= htmlspecialchars($partner['nama_toko'] ?? '') ?></strong></p>
                </div>
            <?php endif; ?>
        </div>

        <div class="chat-input-area">
            <form action="proses_partner.php" method="POST" class="d-flex w-100 align-items-center gap-2 mb-0">
                <input type="hidden" name="action" value="send_message">
                <input type="hidden" name="bundle_id" value="<?= $bundle_id ?>">

                <?php if($bundle_id): ?>
                    <button type="button" class="btn btn-light rounded-circle text-muted border" style="width: 45px; height: 45px;">
                        <i class="fa fa-paperclip"></i>
                    </button>
                    <input type="text" name="message" class="input-msg" placeholder="Ketik pesan..." required autocomplete="off">
                    <button type="submit" class="btn-send">
                        <i class="fa fa-paper-plane"></i>
                    </button>
                <?php else: ?>
                    <input type="text" class="input-msg bg-light" placeholder="Buat bundle dulu untuk mulai chat..." disabled>
                    <button type="button" class="btn-send bg-secondary" disabled><i class="fa fa-lock"></i></button>
                <?php endif; ?>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalAjak" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg">
            <div class="modal-header text-white">
                <h5 class="modal-title fw-bold"><i class="fa fa-rocket me-2"></i>Ajak Kolaborasi</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="proses_partner.php" method="POST">
                <input type="hidden" name="action" value="create_request">
                <input type="hidden" name="mitra_id" value="<?= $partner_id ?>">
                <div class="modal-body p-4 bg-light">
                    <div class="mb-3">
                        <label class="fw-bold small text-secondary mb-1">Judul Bundle</label>
                        <input type="text" name="nama_bundle" class="form-control rounded-pill px-3" placeholder="Contoh: Paket Bundling Hemat" required>
                    </div>
                    <div class="mb-0">
                        <label class="fw-bold small text-secondary mb-1">Pesan Pembuka</label>
                        <textarea name="pesan_awal" class="form-control" rows="3" required>Halo, ayo kita buat bundling produk bareng!</textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-white">
                    <button type="button" class="btn btn-link text-muted" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Kirim <i class="fa fa-paper-plane ms-1"></i></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Auto Scroll ke bawah
    const chatBox = document.getElementById("chatBox");
    if(chatBox) chatBox.scrollTop = chatBox.scrollHeight;
</script>

<?php include '../layouts/footer.php'; ?>