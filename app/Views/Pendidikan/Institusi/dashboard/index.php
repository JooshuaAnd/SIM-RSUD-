<?= $this->include('Pendidikan/Institusi/layout/header') ?>
<?= $this->include('Pendidikan/Institusi/layout/sidebar') ?>

<div class="row">
    <div class="col-12 mb-4">
        <h4 class="fw-bold">Selamat Datang, <?= session()->get('name') ?></h4>
        <p class="text-muted">Pantau status pengajuan mahasiswa dan kelola data mahasiswa Anda.</p>
    </div>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="card text-white bg-dark">
            <div class="card-body py-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-uppercase small mb-2" style="opacity: 0.8;">Total Pengajuan</h6>
                        <h2 class="mb-0 fw-bold"><?= $stats['total_pengajuan'] ?></h2>
                    </div>
                    <i class="fas fa-file-alt fa-2x" style="opacity: 0.3;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-white" style="border-bottom: 4px solid #ff9800;">
            <div class="card-body py-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-uppercase small text-muted mb-2">Menunggu</h6>
                        <h2 class="mb-0 fw-bold text-dark"><?= $stats['menunggu'] ?></h2>
                    </div>
                    <i class="fas fa-clock fa-2x text-warning" style="opacity: 0.5;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-white" style="border-bottom: 4px solid #4caf50;">
            <div class="card-body py-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-uppercase small text-muted mb-2">Disetujui</h6>
                        <h2 class="mb-0 fw-bold text-dark"><?= $stats['disetujui'] ?></h2>
                    </div>
                    <i class="fas fa-check-circle fa-2x text-success" style="opacity: 0.5;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-white" style="border-bottom: 4px solid #f44336;">
            <div class="card-body py-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-uppercase small text-muted mb-2">Ditolak</h6>
                        <h2 class="mb-0 fw-bold text-dark"><?= $stats['ditolak'] ?></h2>
                    </div>
                    <i class="fas fa-times-circle fa-2x text-danger" style="opacity: 0.5;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-bold">Panduan Pengajuan Mahasiswa</h6>
            </div>
            <div class="card-body">
                <div class="d-flex mb-4">
                    <div class="me-3">
                        <span class="badge rounded-pill bg-danger py-2 px-3">1</span>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1">Lengkapi Profil Institusi</h6>
                        <p class="text-muted small mb-0">Pastikan MoU dan dokumen pendukung masih berlaku.</p>
                    </div>
                </div>
                <div class="d-flex mb-4">
                    <div class="me-3">
                        <span class="badge rounded-pill bg-danger py-2 px-3">2</span>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1">Isi Form Pengajuan</h6>
                        <p class="text-muted small mb-0">Masukkan data periode, prodi, dan daftar mahasiswa lengkap.</p>
                    </div>
                </div>
                <div class="d-flex">
                    <div class="me-3">
                        <span class="badge rounded-pill bg-danger py-2 px-3">3</span>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1">Pantau Status</h6>
                        <p class="text-muted small mb-0">Tunggu verifikasi dari pihak Diklat RSUD melalui menu Status Pengajuan.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-danger text-white">
            <div class="card-body py-5 text-center">
                <i class="fas fa-user-graduate fa-3x mb-3"></i>
                <h5>Siap Mengajukan?</h5>
                <p class="small mb-4">Mulai pengajuan mahasiswa baru sekarang.</p>
                <a href="<?= base_url('pendidikan/institusi/pengajuan/create') ?>" class="btn btn-light fw-bold text-danger">AJUKAN SEKARANG</a>
            </div>
        </div>
    </div>
</div>



<?= $this->include('Pendidikan/Institusi/layout/footer') ?>
