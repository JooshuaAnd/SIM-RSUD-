<?php
/**
 * @var array $data
 */
?>
<?= $this->extend('Riset/peneliti/layout/template') ?>

<?= $this->section('content') ?>

<!-- Custom Timeline CSS -->
<style>
    .timeline-steps {
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: relative;
        padding: 40px 0;
    }

    .timeline-steps::before {
        content: "";
        position: absolute;
        top: 50%;
        left: 0;
        width: 100%;
        height: 4px;
        background: #eee;
        z-index: 1;
        transform: translateY(-50%);
    }

    .step-item {
        position: relative;
        z-index: 2;
        text-align: center;
        width: 33.33%;
    }

    .step-icon {
        width: 40px;
        height: 40px;
        background: #fff;
        border: 4px solid #eee;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 10px;
        transition: 0.3s;
        color: #ccc;
    }

    .step-item.active .step-icon {
        border-color: #e53935;
        color: #e53935;
        box-shadow: 0 0 10px rgba(229, 57, 53, 0.2);
    }

    .step-item.completed .step-icon {
        background: #e53935;
        border-color: #e53935;
        color: #fff;
    }

    .step-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        margin-bottom: 2px;
        display: block;
    }

    .step-date {
        font-size: 10px;
        color: #999;
    }

    .timeline-progress {
        position: absolute;
        top: 50%;
        left: 0;
        height: 4px;
        background: #e53935;
        z-index: 1;
        transform: translateY(-50%);
        transition: 0.5s;
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center">
        <a href="<?= base_url('riset/peneliti/status') ?>" class="btn btn-light rounded-circle shadow-sm me-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
            <i class="fas fa-arrow-left text-dark"></i>
        </a>
        <div>
            <h4 class="fw-bold text-dark mb-1">Status Pengajuan</h4>
            <p class="text-muted small mb-0">Lacak progres permohonan riset Anda secara real-time.</p>
        </div>
    </div>
</div>

<!-- Timeline Card -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body px-5">
        <h6 class="fw-bold text-dark mb-4" style="font-size: 14px;">Timeline Status Pengajuan</h6>
        
        <div class="timeline-steps">
            <!-- Progress Line Background -->
            <?php 
                $progress_width = '0%';
                if ($data['status'] == 'upload') $progress_width = '16.66%';
                if ($data['status'] == 'review') $progress_width = '50%';
                if ($data['status'] == 'selesai') $progress_width = '100%';
            ?>
            <div class="timeline-progress" style="width: <?= $progress_width ?>;"></div>

            <!-- Stage 1: Upload -->
            <div class="step-item <?= ($data['status'] != 'upload') ? 'completed' : 'active' ?>">
                <div class="step-icon">
                    <i class="fas <?= ($data['status'] != 'upload') ? 'fa-check' : 'fa-upload' ?>"></i>
                </div>
                <span class="step-label">Upload Berkas</span>
                <span class="step-date">23 Apr 2026</span>
            </div>

            <!-- Stage 2: Review -->
            <div class="step-item <?= ($data['status'] == 'review') ? 'active' : (($data['status'] == 'selesai') ? 'completed' : '') ?>">
                <div class="step-icon">
                    <i class="fas <?= ($data['status'] == 'selesai') ? 'fa-check' : 'fa-hourglass-half' ?>"></i>
                </div>
                <span class="step-label">Review Admin</span>
                <span class="step-date"><?= ($data['status'] == 'review') ? 'Sedang Berlangsung' : '-' ?></span>
            </div>

            <!-- Stage 3: Selesai -->
            <div class="step-item <?= ($data['status'] == 'selesai') ? 'completed' : '' ?>">
                <div class="step-icon">
                    <i class="fas fa-flag-checkered"></i>
                </div>
                <span class="step-label">Selesai</span>
                <span class="step-date">-</span>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Detail Section -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white py-4 px-4 border-0">
                <h6 class="fw-bold mb-0 text-danger" style="font-size: 15px;">Detail Pengajuan <?= esc(format_pengajuan_id($data['id'], $data['jenis_pengajuan'] ?? 'publikasi')) ?></h6>
            </div>
            <hr class="m-0" style="color: #eee;">
            <div class="card-body p-4">
                <?php if ($data['status'] == 'direvisi' && !empty($data['catatan_revisi'])): ?>
                    <div class="alert alert-warning border-0 rounded-4 p-4 mb-4" style="background: rgba(255, 193, 7, 0.1);">
                        <div class="d-flex align-items-start">
                            <div class="bg-warning text-dark rounded-circle p-2 me-3" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-edit"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="fw-bold text-dark mb-1">Catatan Revisi Admin:</h6>
                                <p class="text-dark small mb-3"><?= $data['catatan_revisi'] ?></p>
                                <a href="<?= base_url('riset/peneliti/pengajuan/stupen/baru?id=' . $data['id']) ?>" class="btn btn-warning btn-sm rounded-pill px-4 fw-bold shadow-sm" style="font-size: 11px;">
                                    <i class="fas fa-sync-alt me-2"></i> Mulai Revisi Sekarang
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($data['status'] == 'ditolak' && !empty($data['catatan_revisi'])): ?>
                    <div class="alert alert-danger border-0 rounded-4 p-4 mb-4" style="background: rgba(229, 57, 53, 0.1);">
                        <div class="d-flex align-items-start">
                            <div class="bg-danger text-white rounded-circle p-2 me-3" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-times-circle"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="fw-bold text-danger mb-1">Alasan Penolakan:</h6>
                                <p class="text-dark small mb-0"><?= $data['catatan_revisi'] ?></p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="row mb-4">
                    <div class="col-md-4">
                        <label class="text-uppercase fw-bold text-muted" style="font-size: 10px; letter-spacing: 0.5px;">Nama Peneliti</label>
                    </div>
                    <div class="col-md-8">
                        <p class="fw-bold text-dark" style="font-size: 13px;"><?= $data['nama'] ?></p>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-4">
                        <label class="text-uppercase fw-bold text-muted" style="font-size: 10px; letter-spacing: 0.5px;">Institusi</label>
                    </div>
                    <div class="col-md-8">
                        <p class="text-dark" style="font-size: 13px;"><?= $data['institusi'] ?></p>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-4">
                        <label class="text-uppercase fw-bold text-muted" style="font-size: 10px; letter-spacing: 0.5px;">Judul Riset</label>
                    </div>
                    <div class="col-md-8">
                        <p class="fw-bold text-dark" style="font-size: 13px; line-height: 1.6;"><?= $data['judul'] ?></p>
                    </div>
                </div>



                <div class="row mb-4">
                    <div class="col-md-4">
                        <label class="text-uppercase fw-bold text-muted" style="font-size: 10px; letter-spacing: 0.5px;">Tanggal Pengajuan</label>
                    </div>
                    <div class="col-md-8">
                        <p class="text-dark" style="font-size: 13px;"><?= $data['tanggal'] ?></p>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-4">
                        <label class="text-uppercase fw-bold text-muted" style="font-size: 10px; letter-spacing: 0.5px;">Lampiran Dokumen</label>
                    </div>
                    <div class="col-md-8">
                        <?php foreach ($data['dokumen_upload'] as $doc): ?>
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-file-pdf text-danger me-2" style="font-size: 14px;"></i>
                                <span style="font-size: 12px; color: #555;"><?= $doc ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="mt-4 pt-4 border-top">
                    <?php if ($data['status'] == 'selesai'): ?>
                        <div class="d-grid gap-2">
                            <a href="<?= base_url('riset/peneliti/pengajuan/stupen/print/' . $data['id']) ?>" class="btn btn-success py-3 rounded-3 fw-bold shadow-sm d-flex align-items-center justify-content-center">
                                <i class="fas fa-file-pdf me-3" style="font-size: 18px;"></i> Cetak Surat Izin Meneliti
                            </a>
                            <p class="text-center text-muted mt-2" style="font-size: 11px;">Surat ini telah ditandatangani secara elektronik oleh Direktur RSUD.</p>
                        </div>
                    <?php else: ?>
                        <div class="p-3 bg-light rounded-3 text-center">
                            <p class="text-muted mb-0" style="font-size: 11px;">Seluruh dokumen terkait pengajuan ini dapat diunduh pada daftar lampiran di atas.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Log Activity -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white py-4 px-4 border-0">
                <h6 class="fw-bold mb-0 text-dark" style="font-size: 15px;">Log Aktivitas</h6>
            </div>
            <hr class="m-0" style="color: #eee;">
            <div class="card-body p-0 pt-3">
                <?php foreach ($data['logs'] as $log): ?>
                    <div class="px-4 py-3 border-bottom border-light">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <span class="fw-bold text-dark" style="font-size: 12px;"><?= $log['label'] ?></span>
                            <span class="text-muted" style="font-size: 10px;"><?= $log['time'] ?></span>
                        </div>
                        <p class="text-muted mb-0" style="font-size: 11px; line-height: 1.5;"><?= $log['desc'] ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

