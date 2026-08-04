<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Login' ?> - SIM DIKLAT RSUD Yogyakarta</title>
    <!-- Favicon -->
    <link rel="icon" type="image/jpeg" href="<?= base_url('assets/img/logo_rs.jpg') ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* Hide native password reveal icon in Edge */
        input[type="password"]::-ms-reveal,
        input[type="password"]::-ms-clear {
            display: none;
        }

        :root {
            --primary-red: #e53935;
            --jakarta: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            font-family: var(--jakarta);
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('https://img-kd.kalbe.co.id/wGqDuQeO_pgL96fkCEHD5AyfMnw=/fit-in/615x480/filters:quality(90)/oneonco-prd/faskes/34711003_RSUD_Kota_Yogyakarta.webp') no-repeat center center fixed;
            background-size: cover;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            overflow: hidden;
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            background: #fff;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.06);
            border: 1px solid rgba(0, 0, 0, 0.05);
            position: relative;
            z-index: 2;
        }

        .card-header-riset {
            background: #fff;
            padding: 45px 30px 20px;
            text-align: center;
            position: relative;
        }

        .card-header-riset h4 {
            color: #1a1a1a;
            font-weight: 800;
            margin-bottom: 5px;
            letter-spacing: -0.5px;
            font-size: 22px;
        }

        .card-header-riset h4 span {
            color: var(--primary-red);
        }

        .card-header-riset p {
            color: #999;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 0;
        }

        .card-body-riset {
            padding: 30px 40px 45px;
        }

        .form-label-riset {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            color: #666;
            margin-bottom: 8px;
            display: block;
            letter-spacing: 1px;
        }

        .form-control-riset {
            background: #fdfdfd;
            border: 1.5px solid #eee;
            padding: 12px 18px;
            border-radius: 12px;
            font-size: 14px;
            color: #333;
            transition: 0.3s;
        }

        .form-control-riset:focus {
            background: #fff;
            border-color: var(--primary-red);
            box-shadow: 0 0 0 4px rgba(229, 57, 53, 0.05);
        }

        .btn-login-riset {
            background: var(--primary-red);
            color: white;
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            font-size: 12px;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-top: 15px;
            box-shadow: 0 8px 20px rgba(229, 57, 53, 0.2);
            transition: 0.3s;
        }

        .btn-login-riset:hover {
            background: #c62828;
            transform: translateY(-2px);
            color: #fff;
        }

        .register-link {
            text-align: center;
            margin-top: 25px;
            font-size: 13px;
            color: #666;
        }

        .register-link a {
            color: var(--primary-red);
            text-decoration: none;
            font-weight: 700;
        }

        .footer-link {
            color: #aaa;
            font-size: 11px;
            text-align: center;
            margin-top: 20px;
            display: block;
            text-decoration: none;
            transition: 0.3s;
        }

        .footer-link:hover {
            color: #666;
        }

        /* Decorative Elements */
        .bg-circle {
            position: fixed;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(229, 57, 53, 0.04) 0%, rgba(229, 57, 53, 0) 70%);
            z-index: 1;
            border-radius: 50%;
        }

        .circle-top {
            top: -200px;
            right: -200px;
        }

        .circle-bottom {
            bottom: -200px;
            left: -200px;
        }
    </style>
</head>

<body>

    <div class="bg-circle circle-top"></div>
    <div class="bg-circle circle-bottom"></div>

    <div class="login-card mx-3 mx-sm-0">
        <div class="card-header-riset">
            <i class="fas fa-microscope text-danger mb-3" style="font-size: 32px;"></i>
            <h4>SIM <span>DIKLAT</span> RISET</h4>
            <p>Portal Riset & Publikasi Ilmiah</p>
        </div>
        <div class="card-body-riset">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success border-0 small rounded-4 mb-4 py-3" style="background: rgba(40, 167, 69, 0.05); color: #28a745;">
                    <i class="fas fa-check-circle me-2"></i> <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger border-0 small rounded-4 mb-4 py-3" style="background: rgba(229, 57, 53, 0.05); color: var(--primary-red);">
                    <i class="fas fa-exclamation-circle me-2"></i> <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('riset/authenticate') ?>" method="post">
                <div class="mb-3">
                    <label class="form-label-riset">Email Address</label>
                    <input type="email" name="email" class="form-control form-control-riset" placeholder="nama@email.com" value="<?= old('email') ?>" required>
                </div>

                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="form-label-riset mb-0">Password</label>
                        <a href="<?= base_url('riset/lupa-password') ?>" class="text-decoration-none fw-bold" style="font-size: 10px; color: var(--primary-red);">Lupa Password?</a>
                    </div>
                    <div class="position-relative">
                        <input type="password" id="password" name="password" class="form-control form-control-riset pe-5" placeholder="••••••••" required>
                        <span class="position-absolute top-50 end-0 translate-middle-y pe-3" style="cursor: pointer; z-index: 10;" onclick="togglePassword('password', 'toggleIcon')">
                            <i class="fas fa-eye text-muted" id="toggleIcon"></i>
                        </span>
                    </div>
                </div>

                <button type="submit" class="btn btn-login-riset">Masuk Sekarang</button>

                <div class="register-link">
                    Belum memiliki akun? <a href="<?= base_url('riset/register') ?>">Daftar di sini</a>
                </div>

                <div class="text-center mt-3">
                    <a href="<?= base_url('repository/catalog') ?>" class="text-decoration-none small fw-bold" style="color: #666; font-size: 11px;">
                        <i class="fas fa-long-arrow-alt-left me-2"></i> Kembali ke Repository
                    </a>
                </div>

            </form>
        </div>
    </div>

    <div class="position-fixed bottom-0 w-100 text-center pb-4 text-muted small opacity-50" style="z-index: 0;">
        &copy; 2026 RSUD Kota Yogyakarta. All rights reserved.
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePassword(inputId, iconId) {
            var x = document.getElementById(inputId);
            var icon = document.getElementById(iconId);
            if (x.type === "password") {
                x.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            } else {
                x.type = "password";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('input[required]');
            inputs.forEach(input => {
                input.addEventListener('invalid', function(e) {
                    if (e.target.validity.valueMissing) {
                        e.target.setCustomValidity('Kolom ini wajib diisi.');
                    } else if (e.target.validity.typeMismatch && e.target.type === 'email') {
                        e.target.setCustomValidity('Mohon masukkan alamat email yang valid.');
                    } else {
                        e.target.setCustomValidity('');
                    }
                });
                input.addEventListener('input', function(e) {
                    e.target.setCustomValidity('');
                });
            });
        });
    </script>
</body>

</html>