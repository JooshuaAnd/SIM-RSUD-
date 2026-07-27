<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - SIM Diklat Pendidikan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/jpeg" href="<?= base_url('assets/img/logo_rs.jpg') ?>">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f7f6;
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
        <h5 class="text-muted">Lupa Password</h5>
        <p class="text-muted small mt-2">Masukkan alamat email Anda yang terdaftar dan kami akan mengirimkan instruksi untuk mengatur ulang password.</p>
    </div>

    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i> <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')) : ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i> <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <form action="<?= base_url('pendidikan/forgot-password/process') ?>" method="POST">
        <div class="mb-4">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" placeholder="Contoh: email@institusi.com" required>
        </div>

        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary">Kirim Instruksi</button>
        </div>
        <div class="mt-4 text-center">
            <a href="<?= base_url('pendidikan/login') ?>" class="text-decoration-none fw-bold" style="color: #c62828;">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Login
            </a>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
