<?php
include '../aksi/koneksi.php';
if (!isset($_SESSION['username']) || !isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

$query  = "SELECT * FROM users ORDER BY id ASC";
$result = mysqli_query($koneksi, $query);
$users  = mysqli_fetch_all($result, MYSQLI_ASSOC);

$current_user_id = (int)$_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Akun</title>

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>
    <!-- CSS -->
    <link rel="stylesheet" href="../style/main.css">
    <link rel="stylesheet" href="../style/kelolaAkun.css">
</head>
<body>
    <div class="main-wrapper" style="margin-left: 0px;">
        <div class="page-content">

            <!-- Header -->
            <div class="page-header">
                <h1><i class="fa-solid fa-users-gear"></i> Kelola Akun</h1>
                <a href="tambahAkun.php" class="btn-primary">
                    <i class="fa-solid fa-plus"></i> Tambah Akun
                </a>
                <a href="dashboard.php" class="btn-secondary">
                    <i class="fa-solid fa-arrow-left"></i> Kembali
                </a>
            </div>

            <!-- Card Tabel -->
            <div class="card">

                <!-- Toolbar: search + info -->
                <div class="card-toolbar">
                    <div class="search-wrapper">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input
                            type="text"
                            id="searchInput"
                            class="search-input"
                            placeholder="Cari username atau role…"
                            aria-label="Cari akun"
                        >
                    </div>

                    <span class="toolbar-meta" id="rowCount">
                        <?php echo count($users); ?> akun ditemukan
                    </span>

                </div>

                <!-- Tabel -->
                <div class="table-responsive">
                    <table class="akun-table" id="akunTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Role</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($users) > 0): ?>
                                <?php foreach ($users as $user):
                                    $isSelf = ((int)$user['id'] === $current_user_id);
                                ?>
                                <tr class="<?php echo $isSelf ? 'self-row' : ''; ?>">
                                    <td><?php echo e($user['id']); ?></td>
                                    <td>
                                        <?php echo e($user['username']); ?>
                                        <?php if ($isSelf): ?>
                                            <span class="self-tag">Anda</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge-role <?php echo e($user['role']); ?>">
                                            <?php if ($user['role'] === 'admin'): ?>
                                                <i class="fa-solid fa-shield-halved"></i>
                                            <?php else: ?>
                                                <i class="fa-solid fa-user"></i>
                                            <?php endif; ?>
                                            <?php echo e($user['role']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="aksi-group">
                                            <a href="editAkun.php?id=<?php echo (int)$user['id']; ?>" class="btn-edit">
                                                <i class="fa-solid fa-pen-to-square"></i> Edit
                                            </a>
                                            <form
                                                method="POST"
                                                action="../aksi/hapusAkun.php"
                                                class="aksi-group"
                                            >
                                                <?php echo csrf_field(); ?>
                                                <input type="hidden" name="id" value="<?php echo (int)$user['id']; ?>">
                                                <button
                                                    type="submit"
                                                    name="hapus"
                                                    value="1"
                                                    class="btn-hapus <?php echo $isSelf ? 'disabled' : ''; ?>"
                                                    <?php echo $isSelf ? 'disabled' : ''; ?>
                                                    data-name="<?php echo e($user['username']); ?>"
                                                >
                                                    <i class="fa-solid fa-trash-can"></i> Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4">
                                        <div class="empty-state">
                                            <i class="fa-solid fa-users-slash"></i>
                                            <p>Belum ada akun yang terdaftar.</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <script src="../style/app.js"></script>
    <script>
        document.querySelectorAll('.btn-hapus:not(.disabled)').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();

                const name = this.dataset.name;

                Swal.fire({
                    icon: 'question',
                    title: 'Hapus Akun?',
                    html: `Akun <strong>${name}</strong> akan dihapus secara permanen.`,
                    showCancelButton: true,
                    confirmButtonText: '<i class="fa-solid fa-trash-can"></i> Ya, Hapus',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#C62828',
                    cancelButtonColor: '#9E9E9E',
                    reverseButtons: true
                }).then(function (result) {
                    if (result.isConfirmed) {
                        this.closest('form').submit();
                    }
                }.bind(this));
            });
        });


        const searchInput = document.getElementById('searchInput');
        const tableRows   = document.querySelectorAll('#akunTable tbody tr');
        const rowCount    = document.getElementById('rowCount');

        searchInput.addEventListener('input', function () {
            const keyword = this.value.toLowerCase().trim();
            let visible   = 0;

            tableRows.forEach(function (row) {
                const text = row.innerText.toLowerCase();
                const show = text.includes(keyword);
                row.style.display = show ? '' : 'none';
                if (show) visible++;
            });

            rowCount.textContent = visible + ' akun ditemukan';
        });
    </script>
</body>
</html>
