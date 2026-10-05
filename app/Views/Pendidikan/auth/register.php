<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Institusi - SIM Diklat Pendidikan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/jpeg" href="<?= base_url('assets/img/logo_rs.jpg') ?>">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f7f6;
            padding: 40px 0;
        }
        .register-container {
            max-width: 800px;
            margin: auto;
        }
        .register-card {
            padding: 40px;
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            background: white;
        }
        .section-title {
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 20px;
            color: #c62828;
            font-weight: 600;
            display: flex;
            align-items: center;
        }
        .section-title i { margin-right: 10px; }
        .btn-primary {
            background-color: #c62828;
            border-color: #c62828;
            padding: 12px 30px;
            font-weight: 600;
        }
        .btn-primary:hover {
            background-color: #b71c1c;
            border-color: #b71c1c;
        }
        .form-label { font-weight: 500; color: #555; }
        .brand-header { text-align: center; margin-bottom: 40px; }
        .brand-header h2 { color: #c62828; font-weight: 700; }
    </style>
</head>
<body>

<div class="container register-container">
    <div class="brand-header">
        <h2>SIM DIKLAT RSUD</h2>
        <h5 class="text-muted">Registrasi Institusi Pendidikan Baru</h5>
    </div>

    <div class="register-card">

        <form id="registrationForm" action="<?= base_url('pendidikan/register/process') ?>" method="POST" enctype="multipart/form-data">
            <!-- Data Institusi -->
            <div class="section-title">
                <i class="fas fa-university"></i> Data Institusi
            </div>
            <div class="row mb-3">
                <div class="col-md-8">
                    <label class="form-label">Nama Institusi</label>
                    <input type="text" class="form-control" name="nama_institusi" value="<?= esc(old('nama_institusi')) ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Jenis Institusi</label>
                    <select class="form-select" name="jenis_institusi">
                        <option value="Negeri" <?= (old('jenis_institusi') ?: 'Negeri') === 'Negeri' ? 'selected' : '' ?>>Negeri</option>
                        <option value="Swasta" <?= old('jenis_institusi') === 'Swasta' ? 'selected' : '' ?>>Swasta</option>
                    </select>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Alamat Lengkap</label>
                <textarea class="form-control" name="alamat_institusi" rows="2" required><?= esc(old('alamat_institusi')) ?></textarea>
            </div>
            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label">Email Institusi</label>
                    <input type="email" class="form-control" name="email_institusi" value="<?= esc(old('email_institusi')) ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nomor Telepon Kantor</label>
                    <input type="text" class="form-control" name="telp_institusi" value="<?= esc(old('telp_institusi')) ?>" inputmode="numeric" pattern="[0-9]+" title="Nomor telepon hanya boleh berisi angka." data-numeric-phone required>
                </div>
            </div>

            <!-- Data Penanggung Jawab -->
            <div class="section-title mt-4">
                <i class="fas fa-user-tie"></i> Data Penanggung Jawab
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control" name="nama_pj" value="<?= esc(old('nama_pj')) ?>" pattern="[A-Za-zÀ-ÖØ-öø-ÿ .,'-]+" title="Nama hanya boleh berisi huruf, spasi, titik, koma, apostrof, atau tanda hubung." data-name-pj required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Jabatan</label>
                    <input type="text" class="form-control" name="jabatan_pj" value="<?= esc(old('jabatan_pj')) ?>" required>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label">Nomor HP / WhatsApp</label>
                    <input type="text" class="form-control" name="hp_pj" value="<?= esc(old('hp_pj')) ?>" inputmode="numeric" pattern="[0-9]+" title="Nomor HP/WhatsApp hanya boleh berisi angka." data-numeric-phone required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email Pribadi / Kerja</label>
                    <input type="email" class="form-control" name="email_pj" value="<?= esc(old('email_pj')) ?>" required>
                </div>
            </div>

            <!-- Upload Dokumen -->
            <div class="section-title mt-4">
                <i class="fas fa-file-upload"></i> Dokumen Pendukung
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">MoU / PKS (PDF)</label>
                    <input type="file" class="form-control" name="file_mou" accept=".pdf,application/pdf" data-pdf-file required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Surat Permohonan Kerja Sama (PDF)</label>
                    <input type="file" class="form-control" name="file_permohonan" accept=".pdf,application/pdf" data-pdf-file required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Tanggal Mulai MoU</label>
                    <input type="date" class="form-control" name="tgl_mulai_mou" id="tglMulaiMou" value="<?= esc(old('tgl_mulai_mou')) ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tanggal Selesai MoU</label>
                    <input type="date" class="form-control" name="tgl_selesai_mou" id="tglSelesaiMou" value="<?= esc(old('tgl_selesai_mou')) ?>" required>
                </div>
            </div>
            <div class="mb-4">
                <label class="form-label">Dokumen Pendukung Lainnya</label>
                <input type="file" class="form-control" name="file_lainnya" accept=".pdf,application/pdf" data-pdf-file>
            </div>

            <!-- Akun Login -->
            <div class="section-title mt-4">
                <i class="fas fa-lock"></i> Pengaturan Akun
            </div>
            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label">Password</label>
                    <input type="password" class="form-control" name="password" id="password" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Konfirmasi Password</label>
                    <input type="password" class="form-control" name="confirm_password" id="confirmPassword" required>
                </div>
            </div>

            <div class="form-check mb-4">
                <input class="form-check-input" type="checkbox" id="terms" name="terms" <?= old('terms') ? 'checked' : '' ?> required>
                <label class="form-check-label text-muted small" for="terms">
                    Saya menyatakan bahwa semua data yang diisi adalah benar dan dapat dipertanggungjawabkan.
                </label>
            </div>

            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <a href="<?= base_url('pendidikan/login') ?>" class="text-decoration-none text-muted me-3"><i class="fas fa-arrow-left"></i> Login</a>
                    <a href="<?= base_url('/') ?>" class="text-decoration-none text-muted small"><i class="fas fa-home"></i> Beranda</a>
                </div>
                <div>
                    <button type="submit" class="btn btn-primary">DAFTAR SEKARANG</button>
                </div>
            </div>
        </form>
    </div>
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

    const registrationForm = document.getElementById('registrationForm');
    const tanggalMulaiMou = document.getElementById('tglMulaiMou');
    const tanggalSelesaiMou = document.getElementById('tglSelesaiMou');
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirmPassword');

    function tampilkanErrorValidasi(pesan) {
        Swal.fire({
            icon: 'error',
            title: 'Data tidak valid',
            text: pesan,
        });
    }

    function sinkronkanTanggalSelesai() {
        tanggalSelesaiMou.min = tanggalMulaiMou.value || '';

        if (tanggalMulaiMou.value && tanggalSelesaiMou.value && tanggalSelesaiMou.value < tanggalMulaiMou.value) {
            tanggalSelesaiMou.value = '';
            tampilkanErrorValidasi('Tanggal selesai MoU tidak boleh lebih awal dari tanggal mulai MoU.');
        }
    }

    document.querySelectorAll('[data-numeric-phone]').forEach((input) => {
        input.addEventListener('input', function() {
            this.value = this.value.replace(/\D/g, '');
        });
    });

    document.querySelector('[data-name-pj]').addEventListener('input', function() {
        this.value = this.value.replace(/[^\p{L}\s.,'-]/gu, '');
    });

    document.querySelectorAll('[data-pdf-file]').forEach((input) => {
        input.addEventListener('change', function() {
            if (this.files[0] && !this.files[0].name.toLowerCase().endsWith('.pdf')) {
                this.value = '';
                tampilkanErrorValidasi('Dokumen yang diunggah harus berupa file PDF.');
            }
        });
    });

    tanggalMulaiMou.addEventListener('change', sinkronkanTanggalSelesai);
    tanggalSelesaiMou.addEventListener('change', sinkronkanTanggalSelesai);
    sinkronkanTanggalSelesai();

    registrationForm.addEventListener('submit', function(event) {
        if (!tanggalMulaiMou.value || !tanggalSelesaiMou.value) {
            event.preventDefault();
            tampilkanErrorValidasi('Tanggal mulai dan tanggal selesai MoU wajib diisi.');
            return;
        }

        if (tanggalSelesaiMou.value < tanggalMulaiMou.value) {
            event.preventDefault();
            tampilkanErrorValidasi('Tanggal selesai MoU tidak boleh lebih awal dari tanggal mulai MoU.');
            return;
        }

        if (password.value !== confirmPassword.value) {
            event.preventDefault();
            password.value = '';
            confirmPassword.value = '';
            tampilkanErrorValidasi('Konfirmasi password tidak cocok. Silakan isi ulang password dan konfirmasi password.');
        }
    });
</script>
</body>
</html>
