<?php
include '../aksi/koneksi.php';
session_start();

if (!isset($_SESSION['username']) || !isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$query = "SELECT master_kas.*, users.username FROM master_kas JOIN users ON master_kas.user_id = users.id ORDER BY id DESC;";
$result = mysqli_query($koneksi, $query);
$listKas = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Buku Kas</title>
    
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
            <button class="menu-toggle" id="mobile-menu-toggle">
                <i class="fas fa-bars"></i>
            </button>
            <span class="brand-title">Buku Kas</span>
        </div>
        <div class="top-bar-right">
            <div class="user-avatar">
                <?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?>
            </div>
        </div>
    </header>

    <!-- Sidebar (desktop) -->
    <aside class="sidebar" id="app-sidebar">
        <div class="sidebar-header">
            <i class="fas fa-table" style="color: var(--excel-green-light);"></i>
        </div>
        <div class="sidebar-menu">
            <a href="dashboard.php" class="sidebar-item active">
                <i class="fas fa-chart-line"></i> Dashboard
            </a>
            <a href="tambahKas.php" class="sidebar-item">
                <i class="fas fa-plus"></i> Tambah Kas
            </a>
            <a href="akun.php?id=<?php echo $_SESSION['user_id']; ?>" class="sidebar-item">
                <i class="fas fa-user"></i> Akun
            </a>
            <div style="margin-top: auto; padding: var(--space-md);">
                <a href="../aksi/logout.php" class="btn btn-danger" style="width: 100%; min-height: 38px;"
                onclick="konfirmasiLogout(event, this.href)">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </aside>

    <!-- Main - main  -->
    <div class="main-wrapper">
        <main class="page-content">
            
            <div class="mb-md" style="display:flex; justify-content:space-between; align-items:center;">
                <h2>Halo, <?php echo $_SESSION['username']; ?>!</h2>
                <a href="tambahKas.php" class="btn btn-primary" style="display: none;">
                    <i class="fas fa-plus"></i> Tambah Kas
                </a>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-book-open"></i></div>
                    <div>
                        <div class="stat-value"><?php echo count($listKas); ?></div>
                        <div class="stat-label">Total Buku Kas</div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <i class="fas fa-table"></i> Daftar Buku Kas
                </div>
                
                <div class="table-wrapper">
                    <table class="excel-table">
                            <tr>
                                <th>#</th>
                                <th>Nama Kas</th>
                                <th>Masuk</th>
                                <th>Keluar</th>
                                <th>Dibuat</th>
                                <th>Update</th>
                                <th>Opsi</th>
                                <th>Terakhir diubah oleh</th>
                                </tr>
                            <?php $no = 1;
                            foreach ($listKas as $kas): ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><strong><?php echo ($kas['nama_kas']); ?></strong></td>
                                <td style="color:var(--excel-green); font-weight: 500;">Rp <?php echo number_format($kas['total_masuk'], 0, ',', '.'); ?></td>
                                <td style="color:var(--color-danger); font-weight: 500;">Rp <?php echo number_format($kas['total_keluar'], 0, ',', '.'); ?></td>
                                <td><span class="badge badge-gray"><?php echo date('d/m/y', strtotime($kas['created_at'])); ?></span></td>
                                <td><span class="badge badge-gray"><?php echo date('d/m/y H:i', strtotime($kas['updated_at'])); ?></span></td>
                                <td>
                                    <div style="display: flex; gap: 4px;">
                                        <a href="bukuKas.php?id=<?php echo $kas['id']; ?>" class="btn btn-primary btn-sm">
                                            <i class="fas fa-folder-open"></i> Buka
                                        </a>
                                        <a href="../aksi/hapusMaster.php?id=<?php echo $kas['id']; ?>" class="btn btn-danger btn-sm" onclick="konfirmasiHapusMaster(event, this.href)">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                                <td><?php echo $kas['username']; ?></td>
                            </tr>
                            <?php endforeach; ?>

                            <?php if (empty($listKas)): ?>
                            <tr>
                                <td colspan="8" align="center" style="padding: var(--space-xl);">
                                    <div style="color: var(--gray-500);"><i class="fas fa-folder-open" style="font-size: 32px; margin-bottom: 8px;"></i><br>Belum ada data buku kas.</div>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    </div>

    <nav class="bottom-nav">
        <a href="dashboard.php" class="nav-item active">
            <i class="fas fa-chart-line"></i>
            <span>Dashboard</span>
        </a>
        <a href="tambahKas.php" class="nav-item">
            <i class="fas fa-plus"></i>
            <span>Tambah</span>
        </a>
        <a href="akun.php?id=<?php echo $_SESSION['user_id']; ?>" class="nav-item">
            <i class="fas fa-circle-user"></i>
            <span>Akun</span>
        </a>
    </nav>

    <!-- JS -->
    <script src="../style/app.js"></script>
    <script>
    function konfirmasiHapusMaster(event, url) {
        event.preventDefault();
        Swal.fire({
            title: "Hapus Buku?",
            text: "Data yang dihapus tidak dapat dikembalikan.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#C62828",
            cancelButtonColor: "#9E9E9E",
            confirmButtonText: '<i class="fas fa-trash"></i> Ya, Hapus',
            cancelButtonText: "Batal",
            borderRadius: '8px'
        }).then((result) => {
            if (result.isConfirmed) { window.location.href = url; }
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
