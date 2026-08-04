<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Pendaftaran' ?> - SIM DIKLAT RISET</title>
    <!-- Favicon -->
    <link rel="icon" type="image/jpeg" href="<?= base_url('assets/img/logo_rs.jpg') ?>">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        /* Hide native password reveal icon in Edge */
        input[type="password"]::-ms-reveal,
        input[type="password"]::-ms-clear {
            display: none;
        }

        :root {
            --primary-red: #e53935;
            --soft-bg: #f8f9fc;
            --jakarta: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            font-family: var(--jakarta);
            background: linear-gradient(135deg, #f8f9fc 0%, #eef1f5 100%);
            color: #333;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
            padding: 20px;
        }

        .auth-container {
            width: 100%;
            max-width: 550px;
            position: relative;
            z-index: 2;
        }

        .auth-card {
            background: #fff;
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .brand-logo {
            font-size: 26px;
            font-weight: 800;
            letter-spacing: -1px;
            margin-bottom: 10px;
            text-align: center;
            color: #1a1a1a;
        }

        .form-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #666;
            margin-bottom: 8px;
        }

        .form-control {
            background: #fdfdfd;
            border: 1.5px solid #eee;
            border-radius: 12px;
            padding: 12px 18px;
            color: #333;
            font-size: 14px;
            transition: 0.3s;
        }

        .form-control:focus {
            background: #fff;
            border-color: var(--primary-red);
            box-shadow: 0 0 0 4px rgba(229, 57, 53, 0.05);
            color: #333;
        }

        .btn-register {
            background: var(--primary-red);
            border: none;
            border-radius: 12px;
            padding: 14px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            font-size: 12px;
            transition: 0.3s;
            margin-top: 10px;
            color: #fff;
        }

        .btn-register:hover {
            background: #c62828;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(229, 57, 53, 0.2);
            color: #fff;
        }

        .login-link {
            text-align: center;
            margin-top: 25px;
            font-size: 13px;
            color: #666;
        }

        .login-link a {
            color: var(--primary-red);
            text-decoration: none;
            font-weight: 700;
        }

        /* Decorative Elements */
        .decoration-circle {
            position: fixed;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(229, 57, 53, 0.03) 0%, rgba(229, 57, 53, 0) 70%);
            z-index: 1;
            border-radius: 50%;
        }

        .circle-1 {
            top: -100px;
            right: -100px;
        }

        .circle-2 {
            bottom: -100px;
            left: -100px;
        }
    </style>
</head>

<body>

    <div class="decoration-circle circle-1"></div>
    <div class="decoration-circle circle-2"></div>

    <div class="auth-container">
        <div class="brand-logo">
            <i></i>SIM DIKLAT <span class="text-danger">RSUD KOTA YOGYAKARTA</span>
        </div>

        <div class="auth-card">
            <div class="text-center mb-4">
                <h4 class="fw-bold text-dark mb-1">Daftar Akun Baru</h4>
                <p class="text-muted small">Silakan lengkapi biodata Anda untuk mulai meneliti.</p>
            </div>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger border-0 small rounded-4 mb-4 py-3" style="background: rgba(229, 57, 53, 0.05); color: var(--primary-red);">
                    <i class="fas fa-exclamation-circle me-2"></i> <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('riset/register/submit') ?>" method="POST">
                <div class="mb-3">
                    <label class="form-label">Nama Lengkap</label>
                    <div class="position-relative">
                        <input type="text" name="nama" class="form-control" placeholder="Contoh: Riski Kurniawan" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email Aktif</label>
                        <input type="email" name="email" class="form-control" placeholder="email@institusi.ac.id" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">NIM atau No. Identitas</label>
                        <input type="text" name="identitas" class="form-control" placeholder="NIM / No. Identitas" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Institusi / Universitas</label>
                    <input type="text" name="institusi" class="form-control" placeholder="Contoh: Universitas Kristen Duta Wacana" required>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Program Studi</label>
                        <input type="text" name="prodi" class="form-control" placeholder="Contoh: S1 Sistem Informasi" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nomor WhatsApp</label>
                        <input type="tel" name="no_telp" class="form-control" placeholder="081234567890" required maxlength="13" oninput="this.value = this.value.replace(/[^0-9+]/g, '')">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Alamat Lengkap</label>
                    <textarea name="alamat" class="form-control" rows="2" placeholder="Masukkan alamat lengkap sesuai KTP" required></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Password</label>
                        <div class="position-relative">
                            <input type="password" id="password" name="password" class="form-control pe-5" placeholder="Min. 8 karakter" required minlength="8">
                            <span class="position-absolute top-50 end-0 translate-middle-y pe-3" style="cursor: pointer; z-index: 10;" onclick="togglePassword('password', 'toggleIcon1')">
                                <i class="fas fa-eye text-muted" id="toggleIcon1"></i>
                            </span>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Konfirmasi Password</label>
                        <div class="position-relative">
                            <input type="password" id="confirm_password" name="confirm_password" class="form-control pe-5" placeholder="Ulangi password" required minlength="8">
                            <span class="position-absolute top-50 end-0 translate-middle-y pe-3" style="cursor: pointer; z-index: 10;" onclick="togglePassword('confirm_password', 'toggleIcon2')">
                                <i class="fas fa-eye text-muted" id="toggleIcon2"></i>
                            </span>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-danger btn-register w-100 shadow-sm mt-4">Buat Akun Sekarang</button>
            </form>

            <div class="login-link">
                Sudah memiliki akun? <a href="<?= base_url('riset/login') ?>">Masuk ke Portal</a>
            </div>
        </div>

        <div class="text-center mt-4 text-muted small opacity-50">
            &copy; 2026 RSUD Kota Yogyakarta. Diklat & Riset Division.
        </div>
    </div>

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
            const inputs = document.querySelectorAll('input[required], input[minlength]');
            inputs.forEach(input => {
                input.addEventListener('invalid', function(e) {
                    if (e.target.validity.valueMissing) {
                        e.target.setCustomValidity('Kolom ini wajib diisi.');
                    } else if (e.target.validity.tooShort) {
                        e.target.setCustomValidity('Teks ini terlalu pendek (minimal ' + e.target.minLength + ' karakter).');
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

            // Password confirmation validation
            const pass = document.querySelector('input[name="password"]');
            const conf = document.querySelector('input[name="confirm_password"]');
            if (pass && conf) {
                const validatePassword = () => {
                    if (pass.value !== conf.value) {
                        conf.setCustomValidity('Konfirmasi password tidak cocok dengan password.');
                    } else {
                        conf.setCustomValidity('');
                    }
                };
                pass.addEventListener('input', validatePassword);
                conf.addEventListener('input', validatePassword);
            }
        });
    </script>
</body>

</html>