<?= $this->include('Pendidikan/mahasiswa/layout/header') ?>
<?= $this->include('Pendidikan/mahasiswa/layout/sidebar') ?>

<div class="row">
    <div class="col-12 mb-4">
        <div class="p-4 bg-white rounded-3 shadow-sm d-flex align-items-center justify-content-between" style="border-left: 5px solid var(--primary-red);">
            <div>
                <h4 class="fw-bold mb-1">Selamat Datang, <?= $mahasiswa['nama'] ?>! 👋</h4>
                <p class="text-muted mb-0">Portal Akademik Mahasiswa Diklat RSUD.</p>
            </div>
            <div class="text-end d-none d-md-block">
                <span class="badge badge-active px-3 py-2"><i class="fas fa-circle-check me-1"></i> Status: <?= $mahasiswa['status'] ?></span>
                <?php if (($mahasiswa['payment_status'] ?? '') == 'Lunas') : ?>
                    <span class="badge bg-success px-3 py-2 ms-2"><i class="fas fa-credit-card me-1"></i> Pembayaran: Lunas</span>
                <?php else : ?>
                    <span class="badge bg-danger px-3 py-2 ms-2"><i class="fas fa-exclamation-triangle me-1"></i> Pembayaran: Belum Lunas</span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php if (session()->getFlashdata('success')) : ?>
    <div class="row">
        <div class="col-12 mb-3">
            <div class="alert alert-success border-0 shadow-sm py-3 px-4 d-flex align-items-center gap-3">
                <i class="fas fa-check-circle fa-lg text-success"></i>
                <div>
                    <span class="fw-bold d-block text-success">Berhasil</span>
                    <span class="small text-muted"><?= session()->getFlashdata('success') ?></span>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')) : ?>
    <div class="row">
        <div class="col-12 mb-3">
            <div class="alert alert-danger border-0 shadow-sm py-3 px-4 d-flex align-items-center gap-3">
                <i class="fas fa-exclamation-circle fa-lg text-danger"></i>
                <div>
                    <span class="fw-bold d-block text-danger">Akses Terkunci!</span>
                    <span class="small text-muted"><?= session()->getFlashdata('error') ?></span>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>



<?php if (($mahasiswa['payment_status'] ?? '') !== 'Lunas') : ?>
    <div class="row">
        <div class="col-12 mb-4">
            <div class="p-4 bg-white rounded-3 shadow-sm border-0 d-flex align-items-center gap-4" style="border-left: 5px solid #dc3545 !important;">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 55px; height: 55px; flex-shrink: 0; background: rgba(220, 53, 69, 0.1); color: #dc3545 !important;">
                    <i class="fas fa-lock fa-lg"></i>
                </div>
                <div>
                    <h6 class="fw-bold text-danger mb-1"><i class="fas fa-info-circle me-1"></i> Fitur Akademik Terbatas (Administrasi Belum Lunas)</h6>
                    <p class="text-muted small mb-0">Halaman <strong>Penilaian Stase</strong> saat ini terkunci. Silakan hubungi Institusi asal Anda untuk menyelesaikan pembayaran biaya praktek agar Anda dapat melihat lembar penilaian dan mengunduh sertifikat.</p>
                </div>
            </div>
        </div>
    </div>
<?php else : ?>
    <div class="row">
        <div class="col-12 mb-4">
            <div class="p-4 bg-white rounded-3 shadow-sm border-0 d-flex align-items-center gap-4" style="border-left: 5px solid #28a745 !important;">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 55px; height: 55px; flex-shrink: 0; background: rgba(40, 167, 69, 0.1); color: #28a745 !important;">
                    <i class="fas fa-unlock fa-lg"></i>
                </div>
                <div>
                    <h6 class="fw-bold text-success mb-1"><i class="fas fa-check-circle me-1"></i> Pembayaran Terverifikasi (Akses Penuh)</h6>
                    <p class="text-muted small mb-0">Terima kasih, administrasi stase Anda telah lunas. Semua fitur Portal Akademik seperti melihat <strong>Penilaian Stase</strong> dan mengunduh sertifikat telah diaktifkan.</p>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-12">
        <div class="card p-3 border-0 shadow-sm mb-4">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-light-danger p-3 rounded-3" style="background: rgba(198, 40, 40, 0.1); color: var(--primary-red);">
                    <i class="fas fa-layer-group fa-2x"></i>
                </div>
                <div>
                    <p class="text-muted small mb-0 fw-bold text-uppercase">Progress Stase</p>
                    <h4 class="fw-bold mb-0">Stase <?= $activeStase ?> / <?= $totalStase ?></h4>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Informasi Mahasiswa -->
    <div class="col-md-8 mx-auto">
        <div class="card mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-id-card me-2 text-danger"></i> Profil Akademik</h6>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <?php if (!empty($mahasiswa['file_foto'])) : ?>
                        <img src="<?= base_url('uploads/dokumen_mahasiswa/' . $mahasiswa['file_foto']) ?>" class="rounded-circle shadow-sm border p-1" width="100" height="100" style="object-fit: cover;">
                    <?php else : ?>
                        <img src="https://ui-avatars.com/api/?name=<?= urlencode($mahasiswa['nama']) ?>&size=120&background=c62828&color=fff" class="rounded-circle shadow-sm border p-1" width="100" height="100">
                    <?php endif; ?>
                    <h5 class="fw-bold mt-3 mb-0"><?= $mahasiswa['nama'] ?></h5>
                    <p class="text-muted small"><?= $mahasiswa['nim'] ?></p>
                </div>
                <hr>
                <div class="row mb-3">
                    <div class="col-5 text-muted small">Institusi</div>
                    <div class="col-7 fw-semibold small"><?= $mahasiswa['institusi'] ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-5 text-muted small">Program Studi</div>
                    <div class="col-7 fw-semibold small"><?= $mahasiswa['prodi'] ?></div>
                </div>
                <div class="row mb-0">
                    <div class="col-5 text-muted small">Periode</div>
                    <div class="col-7 fw-semibold small"><?= $mahasiswa['periode'] ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->include('Pendidikan/mahasiswa/layout/footer') ?>
