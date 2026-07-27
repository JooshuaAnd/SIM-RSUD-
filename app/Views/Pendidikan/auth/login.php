<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIM Diklat Pendidikan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/jpeg" href="<?= base_url('assets/img/logo_rs.jpg') ?>">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('https://img-kd.kalbe.co.id/wGqDuQeO_pgL96fkCEHD5AyfMnw=/fit-in/615x480/filters:quality(90)/oneonco-prd/faskes/34711003_RSUD_Kota_Yogyakarta.webp') no-repeat center center fixed;
            background-size: cover;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }
        .login-card {
            width: 100%;
            max-width: 450px;
            padding: 40px;
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            background: white;
        }
        .btn-primary {
            background-color: #c62828;
            border-color: #c62828;
            padding: 12px;
            font-weight: 600;
        }
        .btn-primary:hover {
            background-color: #b71c1c;
            border-color: #b71c1c;
        }
        .form-label {
            font-weight: 500;
            color: #444;
        }
        .brand-text {
            color: #c62828;
            font-weight: 700;
            letter-spacing: -1px;
        }
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .form-control:focus {
            border-color: #c62828;
            box-shadow: 0 0 0 0.25rem rgba(198, 40, 40, 0.1);
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="login-header">
        <img src="<?= base_url('assets/img/logo_rs.jpg') ?>" alt="Logo" width="80" class="mb-3">
        <h2 class="brand-text">SIM DIKLAT</h2>
        <h5 class="text-muted">Modul Pendidikan</h5>
    </div>



    <form action="<?= base_url('pendidikan/login/process') ?>" method="POST">
        <div class="mb-3">
            <label for="email" class="form-label">Email / Username</label>
            <input type="text" class="form-control" id="email" name="email" placeholder="Contoh: institusi / pengabdian" required>
        </div>
        <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <label for="password" class="form-label mb-0">Password</label>
                <a href="<?= base_url('pendidikan/forgot-password') ?>" class="text-decoration-none small" style="color: #c62828;">Lupa Password?</a>
            </div>
            <div class="input-group">
                <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                <button class="btn btn-outline-secondary" type="button" id="togglePassword" style="border-color: #ced4da;">
                    <i class="fas fa-eye text-muted"></i>
                </button>
            </div>
        </div>

        <div class="d-grid gap-2 mt-4">
            <button type="submit" class="btn btn-primary">MASUK</button>
        </div>
        <div class="mt-4 text-center">
            <p class="mb-0 text-muted">Belum punya akun institusi?</p>
            <a href="<?= base_url('pendidikan/register') ?>" class="text-decoration-none fw-bold" style="color: #c62828;">Daftar Institusi Baru</a>
        </div>
        <div class="mt-4 border-top pt-3 text-center">
            <a href="<?= base_url('/') ?>" class="text-decoration-none text-muted small">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Beranda
            </a>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    <?php if (session()->getFlashdata('success')) : ?>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: '<?= session()->getFlashdata('success') ?>',
        });
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')) : ?>
        Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: '<?= session()->getFlashdata('error') ?>',
        });
    <?php endif; ?>

    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#password');
    const icon = togglePassword.querySelector('i');

    togglePassword.addEventListener('click', function (e) {
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        
        if (type === 'text') {
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });
</script>
</body>
</html>
