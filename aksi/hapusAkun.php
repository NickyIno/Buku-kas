<?php
include 'koneksi.php';

if (!isset($_SESSION['username']) || !isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

if (isset($_POST['hapus']) && isset($_POST['id'])) {
    csrf_check();

    $id = (int)$_POST['id'];

    if ($id <= 0) {
        header("Location: ../user/kelolaAkun.php?pesan=hapus_gagal");
        exit;
    }

    if ($id === (int)$_SESSION['user_id']) {
        header("Location: ../user/kelolaAkun.php?pesan=tidak_boleh_hapus_self");
        exit;
    }

    $stmt = mysqli_prepare($koneksi, "DELETE FROM users WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    if (mysqli_stmt_execute($stmt)) {
        header("Location: ../user/kelolaAkun.php?pesan=hapus_berhasil");
    } else {
        error_log('hapusAkun gagal: ' . mysqli_error($koneksi));
        header("Location: ../user/kelolaAkun.php?pesan=hapus_gagal");
    }
    mysqli_stmt_close($stmt);
    exit;
}

header("Location: ../user/kelolaAkun.php");
exit;
