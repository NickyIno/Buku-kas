<?php
include 'koneksi.php';

if (!isset($_SESSION['username']) || !isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

if (isset($_POST['hapus']) && isset($_POST['id'])) {
    csrf_check();

    $id = (int)$_POST['id'];

    if ($id <= 0) {
        header("Location: ../user/dashboard.php?pesan=hapus_gagal");
        exit;
    }

    $stmt = mysqli_prepare($koneksi, "DELETE FROM master_kas WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    if (mysqli_stmt_execute($stmt)) {
        header("Location: ../user/dashboard.php?pesan=hapus_berhasil");
    } else {
        error_log('hapusMaster gagal: ' . mysqli_error($koneksi));
        header("Location: ../user/dashboard.php?pesan=hapus_gagal");
    }
    mysqli_stmt_close($stmt);
    exit;
}

header("Location: ../user/dashboard.php");
exit;
