<?php
include '../aksi/koneksi.php';

// Proteksi halaman, hanya admin yang boleh akses
if (!isset($_SESSION['username']) || !isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Ambil ID dari URL
$id_akun = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_akun === 0) {
    header("Location: kelolaAkun.php");
    exit();
}

$errors   = [];
$success  = false;

// Ambil data akun yang mau diedit
$stmt = mysqli_prepare($koneksi, "SELECT username, role FROM users WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $id_akun);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 0) {
    header("Location: kelolaAkun.php");
    exit();
}
$akun = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

$old = ['username' => $akun['username'], 'role' => $akun['role']];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    $username  = trim($_POST['username'] ?? '');
    $password  = $_POST['password'] ?? '';
    $confirm   = $_POST['confirm_password'] ?? '';
    $role      = $_POST['role'] ?? 'user';

    // Validasi Username
    if (empty($username)) {
        $errors['username'] = 'Username wajib diisi.';
    } elseif (strlen($username) < 3) {
        $errors['username'] = 'Username minimal 3 karakter.';
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $errors['username'] = 'Username hanya boleh huruf, angka, dan underscore.';
    } else {
        // Cek apakah username sudah dipakai oleh ID LAIN
        $stmt_check = mysqli_prepare($koneksi, "SELECT id FROM users WHERE username = ? AND id != ?");
        mysqli_stmt_bind_param($stmt_check, 'si', $username, $id_akun);
        mysqli_stmt_execute($stmt_check);
        mysqli_stmt_store_result($stmt_check);
        if (mysqli_stmt_num_rows($stmt_check) > 0) {
            $errors['username'] = 'Username sudah digunakan oleh akun lain.';
        }
        mysqli_stmt_close($stmt_check);
    }

    // Validasi Password (Opsional)
    if (!empty($password)) {
        if (strlen($password) < 6) {
            $errors['password'] = 'Password minimal 6 karakter.';
        }
        if ($password !== $confirm) {
            $errors['confirm'] = 'Konfirmasi password tidak cocok.';
        }
    }

    // Validasi Role
    if (!in_array($role, ['admin', 'user'])) {
        $errors['role'] = 'Role tidak valid.';
    }
    // Cegah admin mengubah rolenya sendiri menjadi user agar tidak terkunci (lockout)
    if ($id_akun == $_SESSION['user_id'] && $role !== 'admin') {
        $errors['role'] = 'Anda tidak dapat mengubah hak akses (role) akun Anda sendiri.';
    }

    $old = ['username' => $username, 'role' => $role];

    // Proses Update ke Database
    if (empty($errors)) {
        if (!empty($password)) {
            // Jika password diisi, update semua
            $hashed = password_hash($password, PASSWORD_BCRYPT);
            $stmt_update = mysqli_prepare($koneksi, "UPDATE users SET username = ?, password = ?, role = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt_update, 'sssi', $username, $hashed, $role, $id_akun);
        } else {
            // Jika password kosong, jangan update kolom password
            $stmt_update = mysqli_prepare($koneksi, "UPDATE users SET username = ?, role = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt_update, 'ssi', $username, $role, $id_akun);
        }

        if (mysqli_stmt_execute($stmt_update)) {
            $success = true;
            // Update session jika yang diedit adalah akun yang sedang login
            if ($id_akun == $_SESSION['user_id']) {
                $_SESSION['username'] = $username;
            }
        } else {
            $errors['general'] = 'Gagal menyimpan perubahan. Silakan coba lagi.';
        }
        mysqli_stmt_close($stmt_update);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Akun</title>

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>
    <!-- CSS -->
    <link rel="stylesheet" href="../style/main.css">
    <link rel="stylesheet" href="../style/editAkun.css">
</head>
<body>
    <div class="main-wrapper" style="margin: auto;">
        <div class="page-content">

            <!-- Header -->
            <div class="page-header">
                <div class="page-header-left">
                    <!-- TOMBOL BACK HEADER -->
                    <a href="kelolaAkun.php" class="btn-back" title="Kembali">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                    <h1><i class="fa-solid fa-user-pen"></i> Edit Akun</h1>
                </div>
            </div>

            <!-- Form Card -->
            <div class="form-card">
                <div class="form-card-header">
                    <p class="form-card-desc">Ubah data akun pengguna di bawah ini. Kosongkan kolom password jika tidak ingin mengubahnya.</p>
                </div>

                <?php if (!empty($errors['general'])): ?>
                <div class="alert alert-danger">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <?php echo $errors['general']; ?>
                </div>
                <?php endif; ?>

                <form method="POST" action="" id="editAkunForm" novalidate>
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
                                value="<?php echo e($old['username']); ?>"
                                autocomplete="off"
                                maxlength="50"
                            >
                        </div>
                        <?php if (isset($errors['username'])): ?>
                            <p class="field-error"><i class="fa-solid fa-triangle-exclamation"></i> <?php echo $errors['username']; ?></p>
                        <?php else: ?>
                            <p class="field-hint">Hanya huruf, angka, dan underscore. Minimal 3 karakter.</p>
                        <?php endif; ?>
                    </div>

                    <!-- Password (Opsional) -->
                    <div class="form-group <?php echo isset($errors['password']) ? 'has-error' : ''; ?>">
                        <label class="form-label" for="password">
                            <i class="fa-solid fa-lock"></i> Password Baru <span style="color:var(--gray-500); font-weight:normal;">(Opsional)</span>
                        </label>
                        <div class="input-wrapper">
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="form-input"
                                placeholder="Biarkan kosong jika tidak diubah"
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
                            <i class="fa-solid fa-lock-open"></i> Konfirmasi Password Baru
                        </label>
                        <div class="input-wrapper">
                            <input
                                type="password"
                                id="confirm_password"
                                name="confirm_password"
                                class="form-input"
                                placeholder="Ulangi password baru"
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
                        <!-- TOMBOL BACK / KEMBALI FORM -->
                        <a href="kelolaAkun.php" class="btn-secondary">
                            <i class="fa-solid fa-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" class="btn-primary" id="submitBtn">
                            <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
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
                text: 'Data akun berhasil diperbarui.',
                confirmButtonColor: '#217346',
                confirmButtonText: 'Ke Daftar Akun'
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
        const pwInput       = document.getElementById('password');
        const strengthFill  = document.getElementById('strengthFill');
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