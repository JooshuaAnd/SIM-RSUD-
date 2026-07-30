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

        <form action="<?= base_url('pendidikan/register/process') ?>" method="POST" enctype="multipart/form-data">
            <!-- Data Institusi -->
            <div class="section-title">
                <i class="fas fa-university"></i> Data Institusi
            </div>
            <div class="row mb-3">
                <div class="col-md-8">
                    <label class="form-label">Nama Institusi</label>
                    <input type="text" class="form-control" name="nama_institusi" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Jenis Institusi</label>
                    <select class="form-select" name="jenis_institusi">
                        <option value="Negeri">Negeri</option>
                        <option value="Swasta">Swasta</option>
                    </select>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Alamat Lengkap</label>
                <textarea class="form-control" name="alamat_institusi" rows="2" required></textarea>
            </div>
            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label">Email Institusi</label>
                    <input type="email" class="form-control" name="email_institusi" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nomor Telepon Kantor</label>
                    <input type="text" class="form-control" name="telp_institusi" required>
                </div>
            </div>

            <!-- Data Penanggung Jawab -->
            <div class="section-title mt-4">
                <i class="fas fa-user-tie"></i> Data Penanggung Jawab
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control" name="nama_pj" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Jabatan</label>
                    <input type="text" class="form-control" name="jabatan_pj" required>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label">Nomor HP / WhatsApp</label>
                    <input type="text" class="form-control" name="hp_pj" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email Pribadi / Kerja</label>
                    <input type="email" class="form-control" name="email_pj" required>
                </div>
            </div>

            <!-- Upload Dokumen -->
            <div class="section-title mt-4">
                <i class="fas fa-file-upload"></i> Dokumen Pendukung
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">MoU / PKS (PDF)</label>
                    <input type="file" class="form-control" name="file_mou">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Surat Permohonan Kerja Sama (PDF)</label>
                    <input type="file" class="form-control" name="file_permohonan">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Tanggal Mulai MoU</label>
                    <input type="date" class="form-control" name="tgl_mulai_mou">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tanggal Selesai MoU</label>
                    <input type="date" class="form-control" name="tgl_selesai_mou">
                </div>
            </div>
            <div class="mb-4">
                <label class="form-label">Dokumen Pendukung Lainnya</label>
                <input type="file" class="form-control" name="file_lainnya">
            </div>

            <!-- Akun Login -->
            <div class="section-title mt-4">
                <i class="fas fa-lock"></i> Pengaturan Akun
            </div>
            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label">Password</label>
                    <input type="password" class="form-control" name="password" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Konfirmasi Password</label>
                    <input type="password" class="form-control" name="confirm_password" required>
                </div>
            </div>

            <div class="form-check mb-4">
                <input class="form-check-input" type="checkbox" id="terms" required>
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

    function autofillTesting() {
        document.querySelector('input[name="nama_institusi"]').value = 'Universitas Testing Sim Diklat';
        document.querySelector('select[name="jenis_institusi"]').value = 'Swasta';
        document.querySelector('textarea[name="alamat_institusi"]').value = 'Jl. Percobaan Sistem No. 99, Jakarta';
        
        const randomNum = Math.floor(Math.random() * 10000);
        document.querySelector('input[name="email_institusi"]').value = 'institusi' + randomNum + '@testing.com';
        document.querySelector('input[name="telp_institusi"]').value = '021-1234567';
        
        document.querySelector('input[name="nama_pj"]').value = 'Dr. Anton Santoso';
        document.querySelector('input[name="jabatan_pj"]').value = 'Dekan Fakultas Ilmu Kesehatan';
        document.querySelector('input[name="hp_pj"]').value = '0812' + randomNum + '445';
        document.querySelector('input[name="email_pj"]').value = 'anton' + randomNum + '@testing.com';
        
        document.querySelector('input[name="tgl_mulai_mou"]').value = '2026-01-01';
        document.querySelector('input[name="tgl_selesai_mou"]').value = '2027-12-31';
        
        document.querySelector('input[name="password"]').value = '12345678';
        document.querySelector('input[name="confirm_password"]').value = '12345678';
        
        document.querySelector('#terms').checked = true;
        
        fetch('<?= base_url('testing_dummy.png') ?>')
            .then(res => res.blob())
            .then(blob => {
                const file = new File([blob], 'dummy_testing.png', { type: 'image/png' });
                
                const fileInputs = document.querySelectorAll('input[type="file"]');
                fileInputs.forEach(input => {
                    const dt = new DataTransfer();
                    dt.items.add(file);
                    input.files = dt.files;
                });
                alert('Data teks & dokumen registrasi berhasil diisi secara otomatis menggunakan gambar testing!');
            })
            .catch(err => {
                console.error(err);
                alert('Data teks berhasil diisi! Tetapi gagal memuat gambar testing_dummy.png');
            });
    }
</script>
</body>
</html>
