<?php
include 'koneksi.php';

if (!isset($_SESSION['username']) || !isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

if (isset($_POST['hapus']) && isset($_POST['id_transaksi']) && isset($_POST['back'])) {
    csrf_check();

    $id_trans  = (int)$_POST['id_transaksi'];
    $id_master = (int)$_POST['back'];

    if ($id_trans <= 0 || $id_master <= 0) {
        header("Location: ../user/bukuKas.php?id=" . $id_master . "&pesan=hapus_gagal");
        exit;
    }

    $stmt = mysqli_prepare($koneksi, "DELETE FROM transaksi_kas WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id_trans);
    if (mysqli_stmt_execute($stmt)) {
        header("Location: ../user/bukuKas.php?id=" . $id_master);
    } else {
        error_log('hapusTransaksi gagal: ' . mysqli_error($koneksi));
        header("Location: ../user/bukuKas.php?id=" . $id_master . "&pesan=hapus_gagal");
    }
    mysqli_stmt_close($stmt);
    exit;
}

header("Location: ../user/dashboard.php");
exit;
