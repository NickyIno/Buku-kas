<?php
include '../aksi/koneksi.php';
if (!isset($_SESSION['username']) || !isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

$errors   = [];
$success  = false;
$old      = ['username' => '', 'role' => 'user'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    $username  = trim($_POST['username'] ?? '');
    $password  = $_POST['password'] ?? '';
    $confirm   = $_POST['confirm_password'] ?? '';
    $role      = $_POST['role'] ?? 'user';

    // Validasi
    if (empty($username)) {
        $errors['username'] = 'Username wajib diisi.';
    } elseif (strlen($username) < 3) {
        $errors['username'] = 'Username minimal 3 karakter.';
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $errors['username'] = 'Username hanya boleh huruf, angka, dan underscore.';
    } else {

        $stmt = mysqli_prepare($koneksi, "SELECT id FROM users WHERE username = ?");
        mysqli_stmt_bind_param($stmt, 's', $username);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $errors['username'] = 'Username sudah digunakan.';
        }
        mysqli_stmt_close($stmt);
    }

    if (empty($password)) {
        $errors['password'] = 'Password wajib diisi.';
    } elseif (strlen($password) < 6) {
        $errors['password'] = 'Password minimal 6 karakter.';
    }

    if (empty($confirm)) {
        $errors['confirm'] = 'Konfirmasi password wajib diisi.';
    } elseif ($password !== $confirm) {
        $errors['confirm'] = 'Password tidak cocok.';
    }

    if (!in_array($role, ['admin', 'user'])) {
        $errors['role'] = 'Role tidak valid.';
    }

    $old = ['username' => htmlspecialchars($username), 'role' => $role];

    if (empty($errors)) {
        $hashed = password_hash($password, PASSWORD_BCRYPT);
        $stmt   = mysqli_prepare($koneksi, "INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt, 'sss', $username, $hashed, $role);

        if (mysqli_stmt_execute($stmt)) {
            $success = true;
        } else {
            $errors['general'] = 'Gagal menyimpan akun. Silakan coba lagi.';
        }
        mysqli_stmt_close($stmt);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Akun</title>

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>
    <!-- CSS -->
    <link rel="stylesheet" href="../style/main.css">
    <link rel="stylesheet" href="../style/tambahAkun.css">
</head>
<body>
    <div class="main-wrapper" style="margin: auto;">
        <div class="page-content">

            <!-- Header -->
            <div class="page-header">
                <div class="page-header-left">
                    <a href="kelolaAkun.php" class="btn-back">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                    <h1><i class="fa-solid fa-user-plus"></i> Tambah Akun</h1>
                </div>
            </div>

            <!-- Form Card -->
            <div class="form-card">
                <div class="form-card-header">
                    <p class="form-card-desc">Isi data di bawah untuk membuat akun baru.</p>
                </div>

                <?php if (!empty($errors['general'])): ?>
                <div class="alert alert-danger">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <?php echo $errors['general']; ?>
                </div>
                <?php endif; ?>

                <form method="POST" action="" id="tambahAkunForm" novalidate>
                    <?php echo csrf_field(); ?>

                    <!-- Username -->
                    <div class="form-group <?php echo isset($errors['username']) ? 'has-error' : ''; ?>">
                        <label class="form-label" for="username">
                            <i class="fa-solid fa-at"></i> Username
                        </label>
                        <div class="input-wrapper">
                            <input
                                type="text"
                                id="username"
                                name="username"
                                class="form-input"
                                placeholder="Contoh: budi_admin"
                                value="<?php echo $old['username']; ?>"
                                autocomplete="off"
                                maxlength="50"
                            >
                            <span class="input-icon-right" id="usernameStatus"></span>
                        </div>
                        <?php if (isset($errors['username'])): ?>
                            <p class="field-error"><i class="fa-solid fa-triangle-exclamation"></i> <?php echo $errors['username']; ?></p>
                        <?php else: ?>
                            <p class="field-hint">Hanya huruf, angka, dan underscore. Minimal 3 karakter.</p>
                        <?php endif; ?>
                    </div>

                    <!-- Password -->
                    <div class="form-group <?php echo isset($errors['password']) ? 'has-error' : ''; ?>">
                        <label class="form-label" for="password">
                            <i class="fa-solid fa-lock"></i> Password
                        </label>
                        <div class="input-wrapper">
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="form-input"
                                placeholder="Minimal 6 karakter"
                                autocomplete="new-password"
                            >
                            <button type="button" class="toggle-password" data-target="password" title="Tampilkan password">
                                <i class="fa-solid fa-eye" style="color: black;"></i>
                            </button>
                        </div>
                        <!-- Password Strength -->
                        <div class="strength-bar" id="strengthBar">
                            <div class="strength-fill" id="strengthFill"></div>
                        </div>
                        <p class="strength-label" id="strengthLabel"></p>
                        <?php if (isset($errors['password'])): ?>
                            <p class="field-error"><i class="fa-solid fa-triangle-exclamation"></i> <?php echo $errors['password']; ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- Konfirmasi Password -->
                    <div class="form-group <?php echo isset($errors['confirm']) ? 'has-error' : ''; ?>">
                        <label class="form-label" for="confirm_password">
                            <i class="fa-solid fa-lock-open"></i> Konfirmasi Password
                        </label>
                        <div class="input-wrapper">
                            <input
                                type="password"
                                id="confirm_password"
                                name="confirm_password"
                                class="form-input"
                                placeholder="Ulangi password"
                                autocomplete="new-password"
                            >
                            <button type="button" class="toggle-password" data-target="confirm_password" title="Tampilkan password">
                                <i class="fa-solid fa-eye" style="color: black;"></i>
                            </button>
                        </div>
                        <?php if (isset($errors['confirm'])): ?>
                            <p class="field-error"><i class="fa-solid fa-triangle-exclamation"></i> <?php echo $errors['confirm']; ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- Role -->
                    <div class="form-group <?php echo isset($errors['role']) ? 'has-error' : ''; ?>">
                        <label class="form-label">
                            <i class="fa-solid fa-shield-halved"></i> Role
                        </label>
                        <div class="role-options">
                            <label class="role-card <?php echo ($old['role'] === 'user') ? 'selected' : ''; ?>">
                                <input type="radio" name="role" value="user" <?php echo ($old['role'] === 'user') ? 'checked' : ''; ?>>
                                <span class="role-card-icon user"><i class="fa-solid fa-user"></i></span>
                                <span class="role-card-body">
                                    <span class="role-card-title">User</span>
                                    <span class="role-card-desc">Akses standar, dapat mengelola data kas</span>
                                </span>
                                <span class="role-card-check"><i class="fa-solid fa-check"></i></span>
                            </label>
                            <label class="role-card <?php echo ($old['role'] === 'admin') ? 'selected' : ''; ?>">
                                <input type="radio" name="role" value="admin" <?php echo ($old['role'] === 'admin') ? 'checked' : ''; ?>>
                                <span class="role-card-icon admin"><i class="fa-solid fa-shield-halved"></i></span>
                                <span class="role-card-body">
                                    <span class="role-card-title">Admin</span>
                                    <span class="role-card-desc">Akses penuh termasuk manajemen akun</span>
                                </span>
                                <span class="role-card-check"><i class="fa-solid fa-check"></i></span>
                            </label>
                        </div>
                        <?php if (isset($errors['role'])): ?>
                            <p class="field-error"><i class="fa-solid fa-triangle-exclamation"></i> <?php echo $errors['role']; ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- Actions -->
                    <div class="form-actions">
                        <a href="kelolaAkun.php" class="btn-secondary">
                            <i class="fa-solid fa-xmark"></i> Batal
                        </a>
                        <button type="submit" class="btn-primary" id="submitBtn">
                            <i class="fa-solid fa-floppy-disk"></i> Simpan Akun
                        </button>
                        
                    </div>
                </form>
            </div><!-- /form-card -->

        </div>
    </div>

    <?php if ($success): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Akun baru berhasil ditambahkan.',
                confirmButtonColor: '#217346',
                confirmButtonText: 'Kembali ke Daftar'
            }).then(function () {
                window.location.href = 'kelolaAkun.php';
            });
        });
    </script>
    <?php endif; ?>

    <script>
        /* ── Toggle Tampilkan Password ── */
        document.querySelectorAll('.toggle-password').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const targetId = this.dataset.target;
                const input    = document.getElementById(targetId);
                const icon     = this.querySelector('i');
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.replace('fa-eye', 'fa-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.replace('fa-eye-slash', 'fa-eye');
                }
            });
        });

        /* ── Password Strength Meter ── */
        const pwInput      = document.getElementById('password');
        const strengthFill = document.getElementById('strengthFill');
        const strengthLabel = document.getElementById('strengthLabel');

        const levels = [
            { label: '',          color: 'transparent', width: '0%'   },
            { label: 'Sangat Lemah', color: '#C62828',  width: '20%'  },
            { label: 'Lemah',     color: '#E65100',     width: '40%'  },
            { label: 'Cukup',     color: '#F9A825',     width: '60%'  },
            { label: 'Kuat',      color: '#2E8B57',     width: '80%'  },
            { label: 'Sangat Kuat', color: '#217346',   width: '100%' }
        ];

        pwInput.addEventListener('input', function () {
            const val = this.value;
            let score = 0;
            if (val.length >= 6)  score++;
            if (val.length >= 10) score++;
            if (/[A-Z]/.test(val)) score++;
            if (/[0-9]/.test(val)) score++;
            if (/[^a-zA-Z0-9]/.test(val)) score++;

            const lvl = val.length === 0 ? 0 : Math.min(score + 1, 5);
            strengthFill.style.width           = levels[lvl].width;
            strengthFill.style.backgroundColor = levels[lvl].color;
            strengthLabel.textContent          = levels[lvl].label;
            strengthLabel.style.color          = levels[lvl].color;
        });

        /* ── Role Card visual toggle ── */
        document.querySelectorAll('.role-card input[type="radio"]').forEach(function (radio) {
            radio.addEventListener('change', function () {
                document.querySelectorAll('.role-card').forEach(c => c.classList.remove('selected'));
                this.closest('.role-card').classList.add('selected');
            });
        });
    </script>
</body>
</html>