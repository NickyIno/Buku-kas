<?php
include '../aksi/koneksi.php';

if (!isset($_SESSION['username']) || !isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$pesan = '';
$tipe  = '';
$user_id_sesi = (int)$_SESSION['user_id'];

// ===== UBAH AKUN =====
if (isset($_POST['ubah_akun'])) {
    csrf_check();

    $username_baru  = trim((string)($_POST['username_baru'] ?? ''));
    $password_baru  = (string)($_POST['password_baru'] ?? '');
    $password_ulang = (string)($_POST['password_ulang'] ?? '');
    $password_lama  = (string)($_POST['password_lama'] ?? '');

    // Ambil data user saat ini
    $stmt = mysqli_prepare($koneksi, "SELECT password FROM users WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $user_id_sesi);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if (!$user) {
        $pesan = 'Akun tidak ditemukan!';
        $tipe  = 'error';
    } elseif (!password_verify($password_lama, $user['password'])) {
        $pesan = 'Password lama salah!';
        $tipe  = 'error';
    } elseif ($username_baru === '') {
        $pesan = 'Username tidak boleh kosong!';
        $tipe  = 'error';
    } elseif (strlen($username_baru) < 3 || strlen($username_baru) > 255) {
        $pesan = 'Username minimal 3 dan maksimal 255 karakter!';
        $tipe  = 'error';
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username_baru)) {
        $pesan = 'Username hanya boleh huruf, angka, dan underscore.';
        $tipe  = 'error';
    } elseif (!empty($password_baru) && $password_baru !== $password_ulang) {
        $pesan = 'Konfirmasi password baru tidak cocok!';
        $tipe  = 'error';
    } elseif (!empty($password_baru) && strlen($password_baru) < 6) {
        $pesan = 'Password baru minimal 6 karakter!';
        $tipe  = 'error';
    } else {
        // Cek username baru sudah dipakai akun lain
        $stmt = mysqli_prepare($koneksi, "SELECT id FROM users WHERE username = ? AND id != ?");
        mysqli_stmt_bind_param($stmt, 'si', $username_baru, $user_id_sesi);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $pesan = 'Username sudah digunakan akun lain!';
            $tipe  = 'error';
        } else {
            mysqli_stmt_close($stmt);

            if (!empty($password_baru)) {
                $hash = password_hash($password_baru, PASSWORD_BCRYPT);
                $stmt = mysqli_prepare($koneksi, "UPDATE users SET username = ?, password = ? WHERE id = ?");
                mysqli_stmt_bind_param($stmt, 'ssi', $username_baru, $hash, $user_id_sesi);
            } else {
                $stmt = mysqli_prepare($koneksi, "UPDATE users SET username = ? WHERE id = ?");
                mysqli_stmt_bind_param($stmt, 'si', $username_baru, $user_id_sesi);
            }

            if (mysqli_stmt_execute($stmt)) {
                // Perbarui session username
                $_SESSION['username'] = $username_baru;
                $pesan = 'Akun berhasil diperbarui!';
                $tipe  = 'sukses';
            } else {
                error_log('ubah akun gagal: ' . mysqli_error($koneksi));
                $pesan = 'Gagal memperbarui akun, coba lagi.';
                $tipe  = 'error';
            }
            mysqli_stmt_close($stmt);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akun Saya - Buku Kas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../style/main.css">
    <link rel="stylesheet" href="../style/components.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

    <div class="overlay" id="sidebar-overlay"></div>

    <header class="top-bar">
        <div class="top-bar-left">
            <button class="menu-toggle" id="mobile-menu-toggle" aria-label="Buka menu">
                <i class="fas fa-bars"></i>
            </button>
            <span class="brand-title">Pengaturan Akun</span>
        </div>
        <div class="top-bar-right">
            <div class="user-avatar" aria-hidden="true">
                <?php echo e(strtoupper(substr($_SESSION['username'], 0, 1))); ?>
            </div>
        </div>
    </header>

    <aside class="sidebar" id="app-sidebar">
        <div class="sidebar-header">
            <i class="fas fa-table" style="color: var(--excel-green-light);"></i>
        </div>
        <nav class="sidebar-menu" aria-label="Menu utama">
            <a href="dashboard.php" class="sidebar-item">
                <i class="fas fa-chart-line"></i> Dashboard
            </a>
            <a href="tambahKas.php" class="sidebar-item">
                <i class="fas fa-plus"></i> Tambah Kas
            </a>
            <a href="akun.php" class="sidebar-item active">
                <i class="fas fa-user-gear"></i> Akun
            </a>

            <div style="margin-top: auto; padding: var(--space-md);">
                <a href="../aksi/logout.php" class="btn btn-danger" style="width: 100%; min-height: 38px;"
                onclick="konfirmasiLogout(event, this.href)">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </nav>
    </aside>

    <div class="main-wrapper">
        <main class="page-content">

            <div class="mb-md">
                <h2><i class="fas fa-circle-user" style="color: var(--excel-green); margin-right: 8px;"></i>Detail Profil</h2>
                <p style="color: var(--gray-500); font-size: 14px;">Lihat info profil saat ini.</p>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-user"></i></div>
                    <div>
                        <div class="stat-value" style="font-size: 18px;"><?php echo e($_SESSION['username']); ?></div>
                        <div class="stat-label">Username</div>
                    </div>
                </div>
            </div>

            <div class="card card-form" style="margin-top: var(--space-lg);">
                <div class="card-header">
                    <i class="fas fa-id-card"></i> Informasi Personal
                </div>

                <div class="form-group">
                    <label class="form-label" for="username-display">Username</label>
                    <input class="form-control" id="username-display" type="text" value="<?php echo e($_SESSION['username']); ?>" style="background: var(--gray-100);" readonly>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password-display">Password</label>
                    <input class="form-control" id="password-display" type="password" value="********" style="background: var(--gray-100);" readonly>
                    <small style="color: var(--gray-500); margin-top: 4px; display: block;">Hubungi admin untuk perubahan password.</small>
                </div>

                <hr style="border: none; border-top: var(--border-thin); margin: var(--space-lg) 0;">

                <a href="dashboard.php" class="btn btn-primary btn-block-mobile" style="width: 100%;">
                    <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
                </a>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <a href="kelolaAkun.php" class="btn btn-secondary btn-block-mobile" style="width: 100%; margin-top: var(--space-sm); border: 3px solid var(--excel-green);">
                        <i class="fas fa-user-gear"></i> Kelola Akun
                    </a>
                <?php endif; ?>
            </div>

        </main>
    </div>

    <nav class="bottom-nav" aria-label="Navigasi bawah">
        <a href="dashboard.php" class="nav-item">
            <i class="fas fa-chart-line"></i>
            <span>Dashboard</span>
        </a>
        <a href="tambahKas.php" class="nav-item">
            <i class="fas fa-plus"></i>
            <span>Tambah</span>
        </a>
        <a href="akun.php" class="nav-item active">
            <i class="fas fa-circle-user"></i>
            <span>Akun</span>
        </a>
    </nav>

    <script>
        const menuToggle = document.getElementById('mobile-menu-toggle');
        const sidebar = document.getElementById('app-sidebar');
        const overlay = document.getElementById('sidebar-overlay');

        function toggleSidebar() {
            sidebar.classList.toggle('mobile-open');
            overlay.classList.toggle('show');
        }

        menuToggle.addEventListener('click', toggleSidebar);
        overlay.addEventListener('click', toggleSidebar);

    function konfirmasiLogout(event, url) {
    event.preventDefault();
    Swal.fire({
        title: "Apakah Anda yakin?",
        text: "Anda akan keluar dari akun ini!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Ya, logout!",
        cancelButtonText: "Batal"
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = url;
        }
    });
}
    </script>

    <?php if ($pesan !== ''): ?>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            Swal.fire({
                icon: <?php echo $tipe === 'sukses' ? '"success"' : '"error"'; ?>,
                title: <?php echo $tipe === 'sukses' ? '"Berhasil"' : '"Gagal"'; ?>,
                text: <?php echo json_encode($pesan, JSON_UNESCAPED_UNICODE); ?>,
                confirmButtonColor: '#217346'
            });
        });
    </script>
    <?php endif; ?>
</body>
</html>
