<?= $this->extend('Pelatihan/layout/peserta_layout') ?>

<?= $this->section('content') ?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card border-0 shadow-lg rounded-lg overflow-hidden text-center">
            <?php if ($status == 'Lulus'): ?>
                <div class="bg-success p-5 text-white">
                    <div class="bg-white rounded-circle d-inline-flex align-items-center justify-content-center mb-4 shadow" style="width: 100px; height: 100px;">
                        <i class="fas fa-trophy text-success fa-3x"></i>
                    </div>
                    <h2 class="fw-bold mb-1">SELAMAT!</h2>
                    <p class="opacity-75 mb-0">Anda telah menyelesaikan kuis dengan hasil memuaskan.</p>
                </div>
            <?php else: ?>
                <div class="bg-danger p-5 text-white">
                    <div class="bg-white rounded-circle d-inline-flex align-items-center justify-content-center mb-4 shadow" style="width: 100px; height: 100px;">
                        <i class="fas fa-times-circle text-danger fa-3x"></i>
                    </div>
                    <h2 class="fw-bold mb-1">MOHON MAAF</h2>
                    <p class="opacity-75 mb-0">Nilai Anda belum mencapai batas kelulusan.</p>
                </div>
            <?php endif; ?>

            <div class="card-body p-5">
                <div class="row g-4 mb-4">
                    <div class="col-4 border-end">
                        <div class="small text-muted fw-bold mb-1">NILAI ANDA</div>
                        <h1 class="fw-bold <?= $status == 'Lulus' ? 'text-success' : 'text-danger' ?> mb-0"><?= $nilai ?></h1>
                    </div>
                    <div class="col-4 border-end">
                        <div class="small text-muted fw-bold mb-1">STATUS</div>
                        <h3 class="fw-bold mt-2 <?= $status == 'Lulus' ? 'text-success' : 'text-danger' ?> mb-0"><?= $status ?></h3>
                    </div>
                    <div class="col-4">
                        <div class="small text-muted fw-bold mb-1">KESEMPATAN</div>
                        <h3 class="fw-bold mt-2 text-dark mb-0">Tanpa Batas</h3>
                    </div>
                </div>

                <?php if ($status == 'Lulus'): ?>
                    <div class="alert alert-success border-0 small rounded-lg p-3 mb-5">
                        <i class="fas fa-check-circle me-2"></i> Nilai Anda berada di atas ambang batas kelulusan (70).
                    </div>
                    <div class="d-grid gap-3">
                        <a href="<?= base_url('pelatihan/peserta/evaluasi/'.$id) ?>" class="btn btn-primary-custom btn-lg rounded-pill fw-bold py-3 shadow">ISI EVALUASI <i class="fas fa-arrow-right ms-2"></i></a>
                        <a href="<?= base_url('pelatihan/peserta/belajar/'.$id) ?>" class="btn btn-outline-dark btn-lg rounded-pill fw-bold py-3">KEMBALI KE MATERI</a>
                    </div>
                    <p class="small text-muted mt-4 mb-0">Langkah terakhir: Isi evaluasi untuk mendapatkan sertifikat.</p>
                <?php else: ?>
                    <div class="alert alert-danger border-0 small rounded-lg p-3 mb-5">
                        <i class="fas fa-exclamation-circle me-2"></i> Ambang batas kelulusan adalah 70.
                    </div>
                    <div class="d-grid gap-3">
                        <a href="<?= base_url('pelatihan/peserta/kuis/'.$id) ?>" class="btn btn-warning btn-lg rounded-pill fw-bold py-3 shadow text-dark"><i class="fas fa-redo me-2"></i> ULANGI KUIS</a>
                        <a href="<?= base_url('pelatihan/peserta/belajar/'.$id) ?>" class="btn btn-outline-dark btn-lg rounded-pill fw-bold py-3">KEMBALI KE MATERI</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
