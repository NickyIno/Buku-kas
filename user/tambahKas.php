<?php
include '../aksi/koneksi.php';

if (!isset($_SESSION['username']) || !isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

if (isset($_POST['simpan'])) {
    csrf_check();

    $nama = trim((string)($_POST['nama_kas'] ?? ''));

    if ($nama === '') {
        $_SESSION['pesan'] = 'Nama kas tidak boleh kosong!';
        header("Location: tambahKas.php");
        exit();
    }

    if (mb_strlen($nama) > 100) {
        $_SESSION['pesan'] = 'Nama kas maksimal 100 karakter!';
        header("Location: tambahKas.php");
        exit();
    }

    $cek = mysqli_prepare($koneksi, "SELECT id FROM master_kas WHERE nama_kas = ?");
    mysqli_stmt_bind_param($cek, 's', $nama);
    mysqli_stmt_execute($cek);
    mysqli_stmt_store_result($cek);
    if (mysqli_stmt_num_rows($cek) > 0) {
        mysqli_stmt_close($cek);
        $_SESSION['pesan'] = 'Nama kas sudah ada!';
        header("Location: tambahKas.php");
        exit();
    }
    mysqli_stmt_close($cek);

    $user_id = (int)$_SESSION['user_id'];
    $stmt = mysqli_prepare($koneksi, "INSERT INTO master_kas (nama_kas, total_masuk, total_keluar, user_id) VALUES (?, 0, 0, ?)");
    mysqli_stmt_bind_param($stmt, "si", $nama, $user_id);

    if (mysqli_stmt_execute($stmt)) {
        $id_baru = mysqli_insert_id($koneksi);
        mysqli_stmt_close($stmt);
        header("Location: bukuKas.php?id=" . $id_baru);
        exit();
    } else {
        error_log('tambahKas gagal: ' . mysqli_error($koneksi));
        mysqli_stmt_close($stmt);
        $_SESSION['pesan'] = 'Gagal menambahkan kas, coba lagi.';
        header("Location: tambahKas.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Kas Baru - Buku Kas</title>

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>
    <!-- CSS -->
    <link rel="stylesheet" href="../style/main.css">
    <link rel="stylesheet" href="../style/components.css">
</head>
<body>

    <!-- Top bar (mobile/desktop) -->
    <header class="top-bar">
        <div class="top-bar-left">
            <button class="menu-toggle" id="mobile-menu-toggle" aria-label="Buka menu">
                <i class="fas fa-bars"></i>
            </button>
            <span class="brand-title">Buku Kas</span>
        </div>
        <div class="top-bar-right">
            <div class="user-avatar" aria-hidden="true">
                <?php echo e(strtoupper(substr($_SESSION['username'], 0, 1))); ?>
            </div>
        </div>
    </header>

    <!-- Sidebar (desktop) -->
    <aside class="sidebar" id="app-sidebar">
        <div class="sidebar-header">
            <i class="fas fa-table" style="color: var(--excel-green-light);"></i>
        </div>
        <nav class="sidebar-menu" aria-label="Menu utama">
            <a href="dashboard.php" class="sidebar-item">
                <i class="fas fa-chart-line"></i> Dashboard
            </a>
            <a href="tambahKas.php" class="sidebar-item active">
                <i class="fas fa-plus"></i> Tambah Kas
            </a>
            <a href="akun.php" class="sidebar-item">
                <i class="fas fa-user"></i> Akun
            </a>
            <div style="margin-top: auto; padding: var(--space-md);">
                <a href="../aksi/logout.php" class="btn btn-danger" style="width: 100%; min-height: 38px;"
                onclick="konfirmasiLogout(event, this.href)">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </nav>
    </aside>

    <!-- Main wrapper -->
    <div class="main-wrapper">
        <main class="page-content">

            <div class="mb-md">
                <h2><i class="fas fa-folder-plus" style="color: var(--excel-green); margin-right: 8px;"></i>Buat Buku Kas Baru</h2>
            </div>
            <div class="card card-form">
                <div class="card-header">
                    <i class="fas fa-pen-to-square"></i> Informasi Buku Kas
                </div>
                <form method="POST">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="simpan" value="1">

                    <div class="form-group">
                        <label class="form-label" for="nama_kas">Nama Kas / Periode</label>
                        <input class="form-control" type="text" id="nama_kas" name="nama_kas" placeholder="Contoh: Bulan April 2026..." maxlength="100" required>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block-mobile" onclick="konfirmasiSimpan(event)" style="width: 100%;">
                        <i class="fas fa-save"></i> Buat & Buka Catatan
                    </button>

                    <a href="dashboard.php" class="btn btn-outline" style="width: 100%; margin-top: 8px; justify-content: center;">
                        Batal
                    </a>

                    <?php if (isset($_SESSION['pesan'])): ?>
                        <div class="alert" style="margin-top: var(--space-md); padding: 10px 12px; border-radius: var(--radius-sm); background: #FFEBEE; color: var(--text-danger);">
                            <?php echo e($_SESSION['pesan']); ?>
                        </div>
                        <?php unset($_SESSION['pesan']); ?>
                    <?php endif; ?>
                </form>
            </div>

        </main>
    </div>

    <!-- Bottom nav (mobile) -->
    <nav class="bottom-nav" aria-label="Navigasi bawah">
        <a href="dashboard.php" class="nav-item">
            <i class="fas fa-chart-line"></i>
            <span>Dashboard</span>
        </a>
        <a href="tambahKas.php" class="nav-item active">
            <i class="fas fa-plus"></i>
            <span>Tambah</span>
        </a>
        <a href="akun.php" class="nav-item">
            <i class="fas fa-circle-user"></i>
            <span>Akun</span>
        </a>
    </nav>

    <!-- JS -->
    <script src="../style/app.js"></script>
    <script>
    function konfirmasiSimpan(event) {
        event.preventDefault();
        const form = event.target.form;

        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        Swal.fire({
            title: "Buat Buku Kas?",
            text: "Nama Kas ini akan langsung dibuatkan buku catatannya!",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#217346",
            cancelButtonColor: "#9E9E9E",
            confirmButtonText: '<i class="fas fa-check"></i> Ya, Buat Sekarang',
            cancelButtonText: "Batal",
            borderRadius: '8px'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    }

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
</body>
</html>
