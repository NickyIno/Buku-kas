<?php
include 'aksi/koneksi.php';

if (isset($_SESSION['username']) && isset($_SESSION['user_id'])) {
    header("Location: user/dashboard.php");
    exit();
}

$error = false;
$error_msg = 'Username atau password salah!';

if (isset($_POST['login'])) {
    csrf_check();

    if (isset($_SESSION['login_lock_until']) && time() < $_SESSION['login_lock_until']) {
        $error = true;
        $sisa = ceil(($_SESSION['login_lock_until'] - time()) / 60);
        $error_msg = 'Terlalu banyak percobaan login. Coba lagi dalam ' . $sisa . ' menit.';
    } else {
        $username = trim((string)($_POST['username'] ?? ''));
        $password = (string)($_POST['password'] ?? '');

        if ($username === '' || $password === '' || strlen($username) > 255) {
            $error = true;
        } else {
            $stmt = mysqli_prepare($koneksi, "SELECT id, role, password FROM users WHERE username = ?");
            mysqli_stmt_bind_param($stmt, "s", $username);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if ($row = mysqli_fetch_assoc($result)) {
                if (password_verify($password, $row['password'])) {
                    session_regenerate_id(true);
                    unset($_SESSION['login_attempts'], $_SESSION['login_lock_until']);
                    $_SESSION['username'] = $username;
                    $_SESSION['user_id'] = $row['id'];
                    $_SESSION['role'] = $row['role'];
                    header("Location: user/dashboard.php");
                    exit();
                }
            }
            mysqli_stmt_close($stmt);

            $_SESSION['login_attempts'] = (int)($_SESSION['login_attempts'] ?? 0) + 1;
            if ($_SESSION['login_attempts'] >= 5) {
                $_SESSION['login_lock_until'] = time() + 900;
                $_SESSION['login_attempts'] = 0;
            }
            $error = true;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Buku Kas</title>

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>
    <!-- CSS -->
    <link rel="stylesheet" href="style/main.css">
    <link rel="stylesheet" href="style/components.css">
</head>
<body class="full-center-layout">

    <div class="auth-card">
        <div class="auth-title">
            <i class="fas fa-table" style="color: var(--excel-green); font-size: 32px; margin-bottom: 8px;"></i>
            <h2>Buku Kas</h2>
        </div>

        <form method="POST" action="">
            <?php echo csrf_field(); ?>
            <div class="form-group">
                <label class="form-label" for="username">Username</label>
                <input class="form-control" type="text" id="username" name="username" placeholder="Masukkan username..." autocomplete="username" maxlength="255" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <input class="form-control" type="password" id="password" name="password" placeholder="Masukkan password..." autocomplete="current-password" maxlength="255" required>
            </div>

            <button type="submit" name="login" class="btn btn-primary btn-block-mobile" style="width: 100%; margin-top: var(--space-sm);">
                <i class="fas fa-arrow-right-to-bracket"></i> Login
            </button>
        </form>
    </div>

    <script src="style/app.js"></script>
    <?php if ($error): ?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                icon: "error",
                title: "Akses Ditolak",
                text: <?php echo json_encode($error_msg, JSON_UNESCAPED_UNICODE); ?>,
                confirmButtonColor: '#C62828'
            });
        });
    </script>
    <?php endif; ?>
</body>
</html>
