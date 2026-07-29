<?php
/**
 * @var array{
 *     id: string,
 *     judul: string,
 *     status: string,
 *     tanggal: string,
 *     tujuan_laporan: string,
 *     jenis_jurnal: string,
 *     kategori: string,
 *     issn: string,
 *     alamat_web: string,
 *     scope: string,
 *     bukti_file: string|null,
 *     dokumen: array<array{nama: string, tipe: string}>
 * } $data
 */
?>
<?= $this->extend('Riset/peneliti/layout/template') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center">
        <a href="<?= base_url('riset/peneliti/status') ?>" class="btn btn-light rounded-circle shadow-sm me-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
            <i class="fas fa-arrow-left text-dark"></i>
        </a>
        <div>
            <h4 class="fw-bold text-dark mb-1">Detail Izin Publikasi</h4>
            <p class="text-muted small mb-0">Rincian pengajuan izin publikasi dan laporan hasil penelitian.</p>
        </div>
    </div>
</div>

<?php if (session()->getFlashdata('success')): ?>
<div class="alert alert-success alert-dismissible fade show border-0 rounded-4 shadow-sm mb-4" style="font-size: 13px;">
    <i class="fas fa-check-circle me-2"></i> <?= session()->getFlashdata('success') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
<div class="alert alert-danger alert-dismissible fade show border-0 rounded-4 shadow-sm mb-4" style="font-size: 13px;">
    <i class="fas fa-exclamation-circle me-2"></i> <?= session()->getFlashdata('error') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
            <div class="card-header bg-white pt-4 pb-3 px-4 border-bottom border-light">
                <h5 class="fw-bold mb-0" style="color: #333; line-height: 1.4;"><?= (string) esc($data['judul']) ?></h5>
                <div class="mt-3 d-flex align-items-center gap-3">
                    <?php 
                        $status_lower = strtolower($data['status']);
                        if ($status_lower == 'selesai' || $status_lower == 'disetujui'): 
                    ?>
                        <span class="badge bg-success bg-opacity-10 text-success p-2 px-3 rounded-pill fw-bold" style="font-size: 10px; border: 1px solid rgba(46, 125, 50, 0.2);">
                            <i class="fas fa-check-circle me-1"></i> Publikasi Aktif
                        </span>
                    <?php elseif ($status_lower == 'menunggu_pembayaran' || $status_lower == 'menunggu pembayaran'): ?>
                        <span class="badge bg-warning bg-opacity-10 text-dark p-2 px-3 rounded-pill fw-bold" style="font-size: 10px; border: 1px solid rgba(255, 179, 0, 0.2);">
                            <i class="fas fa-wallet me-1 animate-pulse"></i> Menunggu Pembayaran
                        </span>
                    <?php elseif ($status_lower == 'menunggu_verifikasi'): ?>
                        <span class="badge bg-info bg-opacity-10 text-info p-2 px-3 rounded-pill fw-bold" style="font-size: 10px; border: 1px solid rgba(13, 202, 240, 0.2);">
                            <i class="fas fa-hourglass-half me-1"></i> Menunggu Verifikasi
                        </span>
                    <?php elseif ($status_lower == 'direvisi' || $status_lower == 'revisi'): ?>
                        <span class="badge bg-warning bg-opacity-10 text-warning p-2 px-3 rounded-pill fw-bold" style="font-size: 10px; border: 1px solid rgba(255, 193, 7, 0.2);">
                            <i class="fas fa-edit me-1"></i> Perlu Revisi
                        </span>
                    <?php elseif ($status_lower == 'ditolak'): ?>
                        <span class="badge bg-danger bg-opacity-10 text-danger p-2 px-3 rounded-pill fw-bold" style="font-size: 10px; border: 1px solid rgba(220, 53, 69, 0.2);">
                            <i class="fas fa-times-circle me-1"></i> Ditolak
                        </span>
                    <?php else: ?>
                        <span class="badge bg-warning bg-opacity-10 text-dark p-2 px-3 rounded-pill fw-bold" style="font-size: 10px;">
                            Dalam Review
                        </span>
                    <?php endif; ?>
                    <span class="text-muted" style="font-size: 12px;"><i class="fas fa-calendar-alt me-1"></i> Diunggah pada <?= (string) esc($data['tanggal']) ?></span>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="row g-4 mb-5">
                    <div class="col-md-12">
                        <div class="p-3 bg-light rounded-3 border-start border-danger border-4 d-flex justify-content-between align-items-center">
                            <div>
                                <label class="text-muted small mb-1 d-block" style="font-size: 10px; text-transform: uppercase; font-weight: 600;">Tujuan Laporan</label>
                                <div class="fw-bold text-dark" style="font-size: 14px;">
                                    <?= ($data['tujuan_laporan'] == 'upload') ? 'Hanya Upload Hasil Publikasi' : 'Izin Publikasi' ?>
                                </div>
                            </div>
                            <?php if ($data['tujuan_laporan'] == 'upload'): ?>
                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2 fw-bold" style="font-size: 10px; border: 1px solid rgba(46, 125, 50, 0.2);">
                                    <i class="fas fa-check-double me-1"></i> Gratis / Tanpa Tagihan
                                </span>
                            <?php else: ?>
                                <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-2 fw-bold" style="font-size: 10px; border: 1px solid rgba(220, 53, 69, 0.2);">
                                    <i class="fas fa-file-invoice-dollar me-1"></i> Layanan Berbayar
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="p-3 bg-light rounded-3 border-start border-danger border-4 h-100">
                            <label class="text-muted small mb-1">Data Peneliti</label>
                            <div class="fw-bold text-dark" style="font-size: 13px;"><?= esc($data['nama'] ?? session()->get('nama') ?? '-') ?></div>
                            <div class="text-muted mt-1" style="font-size: 11px;"><?= esc($data['institusi'] ?? session()->get('institusi') ?? '-') ?> <?= isset($data['prodi']) ? ' - ' . esc($data['prodi']) : '' ?></div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3 border-start border-danger border-4 h-100">
                            <label class="text-muted small mb-1">Nama Jurnal / Publikasi</label>
                            <div class="fw-bold text-dark" style="font-size: 13px;"><?= esc($data['nama_publikasi'] ?? '-') ?></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3 border-start border-danger border-4 h-100">
                            <label class="text-muted small mb-1">Jenis Jurnal / Publikasi</label>
                            <div class="fw-bold text-dark" style="font-size: 13px;"><?= esc($data['jenis_jurnal']) ?></div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3 border-start border-danger border-4 h-100">
                            <label class="text-muted small mb-1">Kategori</label>
                            <div class="fw-bold text-dark" style="font-size: 13px;"><?= esc($data['kategori_jurnal'] ?? '-') ?></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3 border-start border-danger border-4 h-100">
                            <label class="text-muted small mb-1">ISSN / E-ISSN</label>
                            <div class="fw-bold text-dark" style="font-size: 13px;"><?= esc($data['issn'] ?? '-') ?></div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="p-3 bg-light rounded-3 border-start border-danger border-4 h-100">
                            <label class="text-muted small mb-1">Alamat Web (URL)</label>
                            <div class="fw-bold text-primary" style="font-size: 13px;"><a href="<?= esc($data['alamat_web']) ?>" target="_blank" class="text-decoration-none text-danger"><?= esc($data['alamat_web']) ?></a></div>
                        </div>
                    </div>
                </div>

                <h6 class="fw-bold text-dark mb-3 border-bottom pb-2">Abstrak Penelitian</h6>
                <p class="text-muted mb-4" style="line-height: 1.8; font-size: 13px;">
                    <?= esc($data['abstrak'] ?? '-') ?>
                </p>

                <h6 class="fw-bold text-dark mb-3 border-bottom pb-2 mt-4">Scope / Bidang Penelitian</h6>
                <p class="text-muted mb-4" style="line-height: 1.8; font-size: 13px;">
                    <?= esc($data['scope'] ?? '-') ?>
                </p>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <!-- Dokumen Card -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <h6 class="fw-bold text-dark mb-3"><i class="fas fa-file-pdf text-danger me-2"></i> Dokumen Lampiran</h6>
                
                <div class="list-group list-group-flush">
                    <?php if (!empty($data['dokumen'])): ?>
                        <?php foreach ($data['dokumen'] as $idx => $doc): ?>
                            <?php if (strtolower($doc['jenis_dokumen']) == 'surat izin publikasi resmi' || strtolower($doc['jenis_dokumen']) == 'surat izin resmi') continue; ?>
                            <?php 
                                $docName = "Dokumen Lampiran";
                                if (isset($doc['jenis_dokumen'])) {
                                    if ($doc['jenis_dokumen'] == 'permohonan_izin') $docName = 'Surat Permohonan Izin Publikasi';
                                    elseif ($doc['jenis_dokumen'] == 'salinan_izin_penelitian') $docName = 'Salinan Surat Izin Penelitian';
                                    elseif ($doc['jenis_dokumen'] == 'draft_artikel') $docName = 'Draft Jurnal / Artikel';
                                    elseif ($doc['jenis_dokumen'] == 'pernyataan_anonimitas') $docName = 'Surat Pernyataan Anonimitas Data Pasien';
                                    else $docName = $doc['jenis_dokumen'];
                                }
                            ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-3 border-light bg-transparent">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-file-alt text-danger me-3" style="font-size: 20px;"></i>
                                    <span style="font-size: 13px; color: #444;"><?= $idx + 1 ?>. <?= esc($docName) ?></span>
                                </div>
                                <a href="<?= base_url($doc['file_path']) ?>" target="_blank" class="btn btn-outline-dark btn-sm rounded-3 fw-bold border" style="font-size: 10px; border-color: #ddd; padding: 5px 15px;">Lihat Berkas</a>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="list-group-item px-0 py-3 border-light text-muted text-center bg-transparent" style="font-size: 12px;">
                            Belum ada dokumen pendukung.
                        </div>
                    <?php endif; ?>
                </div>


            </div>
        </div>

        <!-- Status Publikasi Card -->
        <div class="card border-0 shadow-sm rounded-4 mb-4 text-center p-4">
            <label class="text-uppercase fw-bold text-muted mb-3 d-block" style="font-size: 10px; letter-spacing: 1px;">Status Saat Ini</label>
            <?php 
                $status_lower = strtolower($data['status']);
                if ($status_lower == 'disetujui' || $status_lower == 'selesai'): 
            ?>
                <div class="d-inline-block p-4 rounded-circle bg-success bg-opacity-10 text-success mb-3 mx-auto" style="width: 80px; height: 80px;">
                    <i class="fas fa-check-circle" style="font-size: 32px;"></i>
                </div>
                <h5 class="fw-bold text-success mb-1">Disetujui / Selesai</h5>
                <p class="text-muted small">Diajukan pada <?= $data['tanggal'] ?></p>
                <hr>
                <p class="text-muted mb-3" style="font-size: 11px;">Laporan ini telah disetujui untuk dipublikasikan pada Repository Umum dan dapat diakses oleh publik.</p>
                <div class="d-flex align-items-center justify-content-center p-2 rounded-3" style="background-color: #f0fdf4; border: 1px solid #bbf7d0;">
                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 24px; height: 24px; flex-shrink: 0;">
                        <i class="fas fa-check" style="font-size: 10px;"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-success" style="font-size: 11px;">Publikasi Aktif</div>
                    </div>
                </div>

                <?php if ($data['tujuan_laporan'] == 'izin'): ?>
                    <hr>
                    <?php 
                        $suratIzinDoc = array_filter($data['dokumen'] ?? [], fn($d) => strtolower($d['jenis_dokumen']) == 'surat izin publikasi resmi');
                        $suratIzinDoc = reset($suratIzinDoc);
                        if ($suratIzinDoc): 
                    ?>
                        <a href="<?= base_url($suratIzinDoc['file_path']) ?>" target="_blank" class="btn btn-success w-100 rounded-pill fw-bold shadow-sm" style="font-size: 12px; background: #2e7d32; border-color: #2e7d32;">
                            <i class="fas fa-download me-2"></i> Download Surat Izin
                        </a>
                    <?php else: ?>
                        <button class="btn btn-secondary w-100 rounded-pill fw-bold shadow-sm" style="font-size: 12px;" disabled>
                            <i class="fas fa-hourglass-half me-2"></i> Menunggu Surat Terbit
                        </button>
                        <p class="text-muted mt-2 mb-0" style="font-size: 10px;">Surat Izin Publikasi sedang diproses oleh Admin.</p>
                    <?php endif; ?>
                <?php else: ?>
                    <hr>
                    <div class="alert alert-success border-0 rounded-3 p-3 mb-0 text-center" style="font-size: 11px; background: #f0fdf4; border: 1px solid #bbf7d0 !important; color: #155724;">
                        <i class="fas fa-info-circle me-1 text-success"></i> Pengajuan Hanya Upload Dokumen (Tidak Menerbitkan Surat Izin)
                    </div>
                <?php endif; ?>
            <?php elseif ($status_lower == 'menunggu pembayaran' || $status_lower == 'menunggu_pembayaran'): ?>
                <div class="d-inline-block p-4 rounded-circle bg-warning bg-opacity-10 text-warning mb-3 mx-auto" style="width: 80px; height: 80px;">
                    <i class="fas fa-wallet" style="font-size: 32px;"></i>
                </div>
                <h5 class="fw-bold text-warning mb-1" style="color: #d39e00 !important;">Menunggu Pembayaran</h5>
                <p class="text-muted small">Disetujui pada <?= $data['tanggal'] ?></p>
                <hr>
                <?php if (!empty($data['nominal_bayar'])): ?>
                    <div class="bg-warning bg-opacity-10 rounded-3 p-3 mb-3 text-center border border-warning border-opacity-25">
                        <span class="d-block text-muted mb-1" style="font-size: 10px; font-weight: 600; text-transform: uppercase;">Total Tagihan</span>
                        <h4 class="fw-bold text-dark mb-0">Rp <?= number_format($data['nominal_bayar'], 0, ',', '.') ?></h4>
                    </div>
                <?php endif; ?>
                <?php if (!empty($data['catatan_revisi'])): ?>
                    <div class="alert alert-warning mb-3 p-2 text-start" style="font-size: 11px; border-radius: 8px;">
                        <strong>Catatan Admin:</strong><br><?= esc($data['catatan_revisi']) ?>
                    </div>
                    <p class="text-muted mb-3" style="font-size: 11px;">Silakan unggah ulang bukti pembayaran Anda yang valid.</p>
                <?php else: ?>
                    <p class="text-muted mb-3" style="font-size: 11px;">Pengajuan disetujui admin. Silakan unggah bukti bayar untuk menerbitkan surat izin publikasi.</p>
                <?php endif; ?>
                <button type="button" class="btn btn-warning text-dark btn-sm w-100 rounded-pill fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#paymentModalRiset">
                    <i class="fas fa-upload me-2"></i> Upload Bukti Bayar
                </button>
            <?php elseif ($status_lower == 'menunggu_verifikasi'): ?>
                <div class="d-inline-block p-4 rounded-circle bg-info bg-opacity-10 text-info mb-3 mx-auto" style="width: 80px; height: 80px;">
                    <i class="fas fa-hourglass-half" style="font-size: 32px;"></i>
                </div>
                <h5 class="fw-bold text-info mb-1">Menunggu Verifikasi</h5>
                <p class="text-muted small">Disubmit pada <?= $data['tanggal'] ?></p>
                <hr>
                <p class="text-muted mb-3" style="font-size: 11px;">Bukti pembayaran Anda sedang diperiksa oleh admin.</p>
                <?php if (!empty($data['bukti_file'])): ?>
                <div class="d-flex align-items-center p-3 rounded-3 text-start" style="background-color: #f8fafc; border: 1px solid #e2e8f0;">
                    <i class="fas fa-file-image text-primary me-3" style="font-size: 20px;"></i>
                    <div>
                        <a href="<?= base_url($data['bukti_file']) ?>" target="_blank" class="fw-bold text-primary text-decoration-none d-block text-truncate" style="font-size: 12px; max-width: 150px;"><?= esc(basename((string) $data['bukti_file'])) ?></a>
                        <div class="text-muted" style="font-size: 10px;">Bukti bayar terunggah</div>
                    </div>
                    <span class="ms-auto badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2" style="font-size: 9px;">
                        <i class="fas fa-check me-1"></i> Terunggah
                    </span>
                </div>
                <?php endif; ?>
            <?php elseif ($status_lower == 'dalam review' || $status_lower == 'dalam_review' || $status_lower == 'review'): ?>
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10 text-primary mb-3 mx-auto" style="width: 80px; height: 80px;">
                    <i class="fas fa-search" style="font-size: 32px;"></i>
                </div>
                <h5 class="fw-bold text-primary mb-1">Review Admin</h5>
                <p class="text-muted small">Diajukan pada <?= $data['tanggal'] ?></p>
                <hr>
                <p class="text-muted mb-0" style="font-size: 11px;">Dokumen laporan Anda sedang diverifikasi oleh staf Diklat Riset.</p>
            <?php elseif ($status_lower == 'direvisi' || $status_lower == 'revisi'): ?>
                <div class="d-inline-block p-4 rounded-circle bg-warning bg-opacity-10 text-warning mb-3 mx-auto" style="width: 80px; height: 80px;">
                    <i class="fas fa-edit" style="font-size: 32px;"></i>
                </div>
                <h5 class="fw-bold text-warning mb-1">Perlu Revisi</h5>
                <p class="text-muted small">Diajukan pada <?= $data['tanggal'] ?></p>
                <hr>
                <p class="text-muted mb-3" style="font-size: 11px;">Laporan hasil penelitian Anda perlu direvisi sesuai dengan masukan dari verifikator.</p>
                <div class="bg-warning bg-opacity-10 border border-warning border-opacity-25 rounded-3 p-3 text-start mx-auto mb-3">
                    <strong class="text-dark d-block mb-1" style="font-size: 11px;">Catatan:</strong>
                    <span class="text-dark" style="font-size: 11px;"><?= $data['catatan_revisi'] ?? 'Mohon perbaiki laporan Anda.' ?></span>
                </div>
                <a href="<?= base_url('riset/peneliti/publikasi/form?id=' . $data['id'] . '&revisi=1') ?>" class="btn btn-warning mt-2 btn-sm w-100 rounded-pill fw-bold shadow-sm" style="background-color: #fd7e14; border: none; color: white;">
                    <i class="fas fa-edit me-2"></i> Buka Formulir Revisi
                </a>
            <?php elseif ($status_lower == 'ditolak'): ?>
                <div class="d-inline-block p-4 rounded-circle bg-danger bg-opacity-10 text-danger mb-3 mx-auto" style="width: 80px; height: 80px;">
                    <i class="fas fa-times-circle" style="font-size: 32px;"></i>
                </div>
                <h5 class="fw-bold text-danger mb-1">Ditolak</h5>
                <p class="text-muted small">Diajukan pada <?= $data['tanggal'] ?></p>
                <hr>
                <p class="text-muted mb-2" style="font-size: 11px;">Pengajuan publikasi Anda ditolak oleh staf Diklat Riset.</p>
                <div class="bg-danger bg-opacity-10 border border-danger border-opacity-25 rounded-3 p-3 text-start mx-auto mt-2">
                    <strong class="text-danger d-block mb-1" style="font-size: 11px;">Alasan Penolakan:</strong>
                    <span class="text-dark" style="font-size: 12px;"><?= $data['catatan_revisi'] ?? 'Silakan hubungi admin.' ?></span>
                </div>
            <?php else: ?>
                <div class="d-inline-block p-4 rounded-circle bg-secondary bg-opacity-10 text-secondary mb-3 mx-auto" style="width: 80px; height: 80px;">
                    <i class="fas fa-question-circle" style="font-size: 32px;"></i>
                </div>
                <h5 class="fw-bold text-secondary mb-1"><?= ucfirst($data['status']) ?></h5>
                <p class="text-muted small">Diajukan pada <?= $data['tanggal'] ?></p>
            <?php endif; ?>
        </div>
        

    </div>
</div>



<style>
    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.5; }
        100% { opacity: 1; }
    }
    .animate-pulse {
        animation: pulse 1.5s infinite ease-in-out;
    }
</style>

<!-- Modal Pembayaran Riset Publikasi -->
<div class="modal fade" id="paymentModalRiset" tabindex="-1" aria-labelledby="paymentModalRisetLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <form action="<?= base_url('riset/peneliti/publikasi/bayar') ?>" method="post" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?= $data['id'] ?>">
                <div class="modal-header bg-warning text-dark border-0 py-3">
                    <h5 class="modal-title fw-bold" id="paymentModalRisetLabel" style="font-size: 15px;"><i class="fas fa-file-invoice-dollar me-2"></i> Informasi Pembayaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 bg-light text-start">
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-body p-4 text-center">
                            <div class="text-center mb-4">
                                <p class="text-muted small fw-bold text-uppercase mb-1" style="font-size: 10px; letter-spacing: 1px;">Total Tagihan Publikasi Riset</p>
                                <?php if (!empty($data['nominal_bayar'])): ?>
                                    <h3 class="fw-bold text-dark mb-0">Rp <?= number_format($data['nominal_bayar'], 0, ',', '.') ?></h3>
                                    <p class="text-muted small mt-1 mb-0" style="font-size: 11px;">Sesuai dengan tagihan dari admin.</p>
                                <?php else: ?>
                                    <h3 class="fw-bold text-dark mb-0">Sesuai Tarif Layanan</h3>
                                    <p class="text-muted small mt-1 mb-0" style="font-size: 11px;">Silakan konfirmasi ke admin untuk detail tagihan.</p>
                                <?php endif; ?>
                            </div>

                            <div class="bg-light p-3 rounded-3 border mb-0 text-start">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted small" style="font-size: 11px;">Bank Tujuan</span>
                                    <span class="fw-bold text-dark" style="font-size: 12px;"><?= isset($pengaturan['nama_bank']) && !empty($pengaturan['nama_bank']) ? esc($pengaturan['nama_bank']) : 'Bank BPD DIY' ?></span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted small" style="font-size: 11px;">Nomor Rekening</span>
                                    <span class="fw-bold text-primary" style="font-size: 14px; letter-spacing: 1px;"><?= isset($pengaturan['nomor_rekening']) && !empty($pengaturan['nomor_rekening']) ? esc($pengaturan['nomor_rekening']) : '1122334455' ?></span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted small" style="font-size: 11px;">Atas Nama</span>
                                    <span class="fw-bold text-dark" style="font-size: 12px;"><?= isset($pengaturan['nama_rekening']) && !empty($pengaturan['nama_rekening']) ? esc($pengaturan['nama_rekening']) : 'RSUD Kota Yogyakarta' ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="fw-bold text-dark mb-2" style="font-size: 12px;">Upload Bukti Pembayaran</label>
                        <input type="file" name="bukti_bayar" id="buktiBayarRiset" class="form-control" style="font-size: 12px; border-radius: 8px;" required>
                        <small class="text-muted mt-2 d-block" style="font-size: 10px;">Format PDF/JPG, maks 2MB. Pastikan nominal dan referensi terlihat jelas.</small>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-white py-3">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal" style="font-size: 12px; font-weight: 600;">Kembali</button>
                    <button type="submit" class="btn btn-warning text-dark rounded-pill px-4 fw-bold shadow-sm" style="font-size: 12px;">
                        <i class="fas fa-check-circle me-2"></i> Kirim Bukti Bayar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
