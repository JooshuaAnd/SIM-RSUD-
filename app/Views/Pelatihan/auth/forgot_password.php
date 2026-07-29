<?php helper('pelatihan'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Lupa Password' ?> - SIM-DIKLAT RSUD KOTA YOGYAKARTA</title>
    <link rel="shortcut icon" type="image/x-icon" href="<?= get_system_favicon() ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-red: #c62828;
            --primary-dark: #1e293b;
            --primary-yellow: #ffc107;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f8fafc;
            height: 100vh;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            overflow: hidden;
        }

        .login-container {
            background: white;
            overflow: hidden;
            display: flex;
            width: 100vw;
            height: 100vh;
            max-width: 100%;
            margin: 0;
            border: none;
        }

        .login-side-info {
            background: linear-gradient(135deg, var(--primary-dark) 0%, #334155 100%);
            width: 400px;
            padding: 3rem;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            height: 100%;
        }

        .login-side-info::after {
            content: '';
            position: absolute;
            bottom: 0;
            right: 0;
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, var(--primary-red) 0%, transparent 70%);
            opacity: 0.1;
            filter: blur(40px);
        }

        .login-form-side {
            flex: 1;
            padding: 4rem;
            background: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            height: 100%;
            position: relative;
        }

        .logo-img {
            height: 80px;
            width: auto;
            filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.1));
        }

        .brand-title {
            font-weight: 800;
            font-size: 1.8rem;
            line-height: 1.2;
            margin-top: 2rem;
            letter-spacing: -0.5px;
        }

        .tagline {
            font-size: 0.85rem;
            opacity: 0.8;
            border-left: 3px solid var(--primary-yellow);
            padding-left: 1rem;
            margin-top: 1rem;
        }

        .form-control {
            border-radius: 16px;
            padding: 14px 20px;
            border: 2px solid #f1f5f9;
            background-color: #ffffff;
            font-weight: 600;
            transition: all 0.3s;
            color: var(--primary-dark);
        }

        .form-control:focus {
            background-color: #fff;
            border-color: var(--primary-red);
            box-shadow: 0 0 0 4px rgba(206, 33, 39, 0.1);
            color: var(--primary-dark);
        }

        .btn-login-submit {
            background: var(--primary-red);
            border: none;
            border-radius: 16px;
            padding: 14px 30px;
            font-weight: 800;
            color: white;
            transition: all 0.3s;
            box-shadow: 0 8px 16px rgba(206, 33, 39, 0.2);
        }

        .btn-login-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 24px rgba(206, 33, 39, 0.3);
            background: #a51a1f;
        }

        .back-home-floating {
            position: absolute;
            top: 2rem;
            right: 4rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #64748b;
            text-decoration: none;
            font-weight: 700;
            font-size: 0.85rem;
            padding: 8px 16px;
            border-radius: 12px;
            border: 2px solid #f1f5f9;
            background: white;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .back-home-floating:hover {
            color: var(--primary-red);
            border-color: var(--primary-red);
            background: #fff5f5;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(206, 33, 39, 0.1);
        }

        @media (max-width: 992px) {
            .login-container {
                flex-direction: column;
                width: 100vw;
                height: 100vh;
            }

            .login-side-info {
                width: 100%;
                padding: 1.5rem;
                flex: 0 0 auto;
                height: auto;
            }

            .login-form-side {
                padding: 2rem;
                flex: 1 1 auto;
                height: auto;
                display: flex;
                flex-direction: column;
                justify-content: center;
            }

            .back-home-floating {
                top: 1rem;
                right: 1.5rem;
                padding: 6px 12px;
                font-size: 0.75rem;
            }
        }

        /* Figure-Ground Custom Modal Styles */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(8px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            opacity: 1;
            transition: opacity 0.3s ease;
        }

        .modal-card {
            background: white;
            border-radius: 24px;
            padding: 2.5rem;
            width: 480px;
            max-width: 90%;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            text-align: center;
            transform: scale(0.9);
            animation: modalIn 0.3s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        @keyframes modalIn {
            to {
                transform: scale(1);
            }
        }

        .animate-bounce {
            animation: bounce 1s infinite;
        }

        @keyframes bounce {
            0%, 100% {
                transform: translateY(-5%);
                animation-timing-function: cubic-bezier(0.8, 0, 1, 1);
            }
            50% {
                transform: none;
                animation-timing-function: cubic-bezier(0, 0, 0.2, 1);
            }
        }
    </style>
</head>

<body>

    <div class="login-container">
        <div class="login-side-info">
            <div>
                <img src="<?= get_system_logo() ?>" alt="Logo RSUD" class="logo-img" style="border-radius: 12px; height: 60px; width: auto; object-fit: contain;">
                <h1 class="brand-title">SIM DIKLAT<br><span style="color: var(--primary-yellow)">RSUD KOTA YOGYAKARTA</span></h1>
                <p class="tagline">Sistem Informasi Manajemen Pelatihan dan Pengembangan Kompetensi Pegawai.</p>
            </div>
            <div class="small opacity-50">
                &copy; 2026 RSUD Kota Yogyakarta.
            </div>
        </div>

        <div class="login-form-side">
            <a href="<?= base_url('pelatihan/login') ?>" class="back-home-floating">
                <i class="fas fa-arrow-left"></i>
                <span>Kembali ke Login</span>
            </a>

            <!-- Figure-Ground Modal Error Alert -->
            <?php if (session()->getFlashdata('error')) : ?>
                <div id="errorModalOverlay" class="modal-overlay">
                    <div class="modal-card">
                        <div class="mb-3">
                            <i class="fas fa-exclamation-triangle fa-3x text-danger animate-bounce"></i>
                        </div>
                        <h5 class="fw-extrabold text-dark mb-2">Gagal</h5>
                        <p class="text-muted small mb-4"><?= session()->getFlashdata('error') ?></p>
                        <button type="button" class="btn btn-login-submit w-100" onclick="closeErrorModal()">
                            Mengerti
                        </button>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('success')) : ?>
                <div class="alert alert-success alert-dismissible fade show mb-4 rounded-4 shadow-sm" role="alert">
                    <i class="fas fa-check-circle me-2"></i> <?= session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div id="loginHeader" class="mb-5">
                <h3 class="fw-bold text-dark">Lupa Password</h3>
                <p class="text-muted small" id="loginSub">Masukkan alamat email aktif yang Anda gunakan saat mendaftar. Kami akan mengirimkan password baru ke email Anda.</p>
            </div>

            <form action="<?= base_url('pelatihan/lupa-password/submit') ?>" method="POST" id="loginForm">
                <div id="credentialsForm">
                    <div class="mb-4">
                        <label class="form-label small fw-bold">ALAMAT EMAIL</label>
                        <input type="email" name="email" class="form-control" placeholder="Masukkan alamat email aktif" required value="<?= old('email') ?>">
                    </div>

                    <button type="submit" class="btn btn-login-submit w-100 mt-2">
                        Kirim Password Baru <i class="fas fa-paper-plane ms-2"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const emailInput = document.getElementsByName('email')[0];
            if (emailInput) {
                emailInput.focus();
            }
        });

        function closeErrorModal() {
            const overlay = document.getElementById('errorModalOverlay');
            if (overlay) {
                overlay.style.opacity = '0';
                setTimeout(() => overlay.remove(), 300);
            }
        }
    </script>
</body>
</html>
