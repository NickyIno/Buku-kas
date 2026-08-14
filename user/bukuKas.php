<?php
include '../aksi/koneksi.php';

if (!isset($_SESSION['username']) || !isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$id_kas  = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$user_id = (int)$_SESSION['user_id'];

if ($id_kas <= 0) {
    header("Location: dashboard.php");
    exit();
}

$stmt = mysqli_prepare($koneksi, "SELECT * FROM master_kas WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $id_kas);
mysqli_stmt_execute($stmt);
$res_master = mysqli_stmt_get_result($stmt);
$master = mysqli_fetch_assoc($res_master);
mysqli_stmt_close($stmt);

if (!$master) {
    header("Location: dashboard.php");
    exit();
}

if (isset($_POST['add_row'])) {
    csrf_check();

    $tgl    = trim((string)($_POST['tgl'] ?? ''));
    $ket    = trim((string)($_POST['ket'] ?? ''));
    $masuk  = (int)($_POST['masuk'] ?? 0);
    $keluar = (int)($_POST['keluar'] ?? 0);

    if ($tgl === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $tgl) || !strtotime($tgl)) {
        header("Location: bukuKas.php?id=$id_kas&pesan=tanggal_tidak_valid");
        exit();
    }
    $tgl_db = date('Y-m-d H:i:s', strtotime($tgl));

    if ($masuk < 0 || $keluar < 0) {
        header("Location: bukuKas.php?id=$id_kas&pesan=jumlah_tidak_valid");
        exit();
    }
    if (mb_strlen($ket) > 255) {
        $ket = mb_substr($ket, 0, 255);
    }

    if ($masuk == 0 && $keluar == 0) {
        header("Location: bukuKas.php?id=$id_kas");
        exit();
    }

    $type = ($masuk > 0) ? 'masuk' : 'keluar';
    $jumlah_uang = ($type === 'masuk') ? $masuk : $keluar;

    mysqli_begin_transaction($koneksi);

    $stmt1 = mysqli_prepare($koneksi, "INSERT INTO transaksi_kas (id_master, tanggal, keterangan, jumlah_uang, type) VALUES (?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt1, 'issds', $id_kas, $tgl_db, $ket, $jumlah_uang, $type);
    $ok1 = mysqli_stmt_execute($stmt1);
    mysqli_stmt_close($stmt1);

    if ($type === 'masuk') {
        $stmt2 = mysqli_prepare($koneksi, "UPDATE master_kas SET total_masuk = total_masuk + ?, user_id = ? WHERE id = ?");
    } else {
        $stmt2 = mysqli_prepare($koneksi, "UPDATE master_kas SET total_keluar = total_keluar + ?, user_id = ? WHERE id = ?");
    }
    mysqli_stmt_bind_param($stmt2, 'dii', $jumlah_uang, $user_id, $id_kas);
    $ok2 = mysqli_stmt_execute($stmt2);
    mysqli_stmt_close($stmt2);

    if ($ok1 && $ok2) {
        mysqli_commit($koneksi);
        header("Location: bukuKas.php?id=$id_kas");
    } else {
        mysqli_rollback($koneksi);
        error_log('Tambah transaksi gagal: ' . mysqli_error($koneksi));
        header("Location: bukuKas.php?id=$id_kas&pesan=simpan_gagal");
    }
    exit();
}

$stmt = mysqli_prepare($koneksi, "SELECT * FROM transaksi_kas WHERE id_master = ? ORDER BY tanggal ASC");
mysqli_stmt_bind_param($stmt, 'i', $id_kas);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$transaksi_rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_stmt_close($stmt);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku Kas — <?php echo e($master['nama_kas']); ?></title>
    <link rel="stylesheet" href="../style/bukuKas.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../style/app.js"></script>
</head>
<body>

    <!-- ===== HEADER BAR ===== -->
    <header class="summary-bar">
        <strong><i class="fa-solid fa-book"></i> <?php echo e($master['nama_kas']); ?></strong>
        <a href="dashboard.php" class="back-link">Kembali</a>
    </header>

    <!-- ===== TABEL BUKU KAS ===== -->
    <main>
    <div class="table-wrapper">
        <form method="POST">
            <?php echo csrf_field(); ?>
            <table class="excel-table">
                <thead>
                    <tr>
                        <th class="no-col hide-mobile">No</th>
                        <th style="width:130px">Tanggal</th>
                        <th>Keterangan Transaksi</th>
                        <th style="width:130px">Masuk (Rp)</th>
                        <th style="width:130px">Keluar (Rp)</th>
                        <th style="width:70px">Opsi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1; $total_m = 0; $total_k = 0;
                    foreach ($transaksi_rows as $row):
                        if ($row['type'] == "masuk") {
                            $total_m += (float)$row['jumlah_uang'];
                        } else {
                            $total_k += (float)$row['jumlah_uang'];
                        }
                    ?>
                    <tr>
                        <td class="no-col hide-mobile"><?php echo $no++; ?></td>
                        <td><?php echo e($row['tanggal']); ?></td>
                        <td><?php echo e($row['keterangan']); ?></td>
                        <td class="nominal"><?php if ($row['type'] == "masuk") echo number_format((float)$row['jumlah_uang'], 0, ',', '.'); ?></td>
                        <td class="nominal"><?php if ($row['type'] == "keluar") echo number_format((float)$row['jumlah_uang'], 0, ',', '.'); ?></td>
                        <td align="center">
                            <form method="POST" action="../aksi/hapusTransaksi.php" class="hapus-form" onsubmit="return konfirmasiHapusTransaksi(event, this)">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="id_transaksi" value="<?php echo (int)$row['id']; ?>">
                                <input type="hidden" name="back" value="<?php echo $id_kas; ?>">
                                <button type="submit" name="hapus" value="1" class="link-hapus">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>

                    <?php if (empty($transaksi_rows)): ?>
                    <tr>
                        <td colspan="6" align="center" style="padding: 24px; color: var(--teks-muted);">
                            Belum ada transaksi. Silakan isi baris di bawah untuk mencatat.
                        </td>
                    </tr>
                    <?php endif; ?>

                    <!-- Baris Input Baru -->
                    <tr class="input-row">
                        <td class="no-col hide-mobile" align="center" style="color:#888">*</td>
                        <td><input type="date" name="tgl" value="<?php echo date('Y-m-d'); ?>" required></td>
                        <td><input type="text" name="ket" placeholder="Ketik keterangan di sini..." maxlength="255" required></td>
                        <td><input type="number" name="masuk" placeholder="0" min="0" step="any"></td>
                        <td><input type="number" name="keluar" placeholder="0" min="0" step="any"></td>
                        <td align="center"><button type="submit" name="add_row" class="btn-save" id="btnAddRow">ADD</button></td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2" class="hide-mobile"></td>
                        <td style="text-align: right" class="label-total">SALDO AKHIR :</td>
                        <td class="nominal">Rp <?php echo number_format($total_m, 0, ',', '.'); ?></td>
                        <td class="nominal">Rp <?php echo number_format($total_k, 0, ',', '.'); ?></td>
                        <td class="saldo-cell">Rp <?php echo number_format($total_m - $total_k, 0, ',', '.'); ?></td>
                    </tr>
                </tfoot>
            </table>
        </form>
    </div>
    </main>

    <script>
function konfirmasiHapusTransaksi(event, form) {
    event.preventDefault();

    Swal.fire({
        title: "Apakah Anda yakin?",
        text: "Data transaksi ini akan dihapus permanen!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Ya, hapus!",
        cancelButtonText: "Batal"
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });

    return false;
}

document.addEventListener("DOMContentLoaded", function () {
    const btn = document.getElementById("btnAddRow");
    if (btn) {
        btn.closest("form").addEventListener("submit", function () {
            btn.disabled = true;
            btn.textContent = "...";
        });
    }
});
    </script>
</body>
</html>
