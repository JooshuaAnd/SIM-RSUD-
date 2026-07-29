<?php
/**
 * @var array{
 *     id: string,
 *     judul: string,
 *     skema: string,
 *     status: string,
 *     catatan_revisi: string,
 *     tanggal: string,
 *     deskripsi: string,
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
            <h4 class="fw-bold text-dark mb-1">Detail Izin Penelitian</h4>
            <p class="text-muted small mb-0">Informasi lengkap mengenai proses pendaftaran izin penelitian Anda.</p>
        </div>
    </div>
</div>

<?php if ($data['status'] == 'direvisi'): ?>
    <div class="alert alert-warning border-0 rounded-4 p-4 mb-4" style="background: rgba(255, 193, 7, 0.1);">
        <div class="d-flex align-items-start">
            <div class="bg-warning text-dark rounded-circle p-2 me-3" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-edit"></i>
            </div>
            <div class="flex-grow-1">
                <h6 class="fw-bold text-dark mb-1">Status: Perlu Revisi</h6>
                <p class="text-dark small mb-0"><strong>Catatan Admin:</strong> <?= $data['catatan_revisi'] ?? 'Mohon lengkapi dan perbaiki berkas usulan pendaftaran Anda.' ?></p>
            </div>
        </div>
    </div>
<?php endif; ?>

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
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            <div class="card-header bg-white pt-4 pb-3 px-4 border-0">
                <h6 class="fw-bold mb-0 text-danger" style="font-size: 15px;">Informasi Pengajuan</h6>
            </div>
            <hr class="m-0" style="color: #eee;">
            <div class="card-body p-4">
                <div class="row mb-3 pb-3 border-bottom border-light">
                    <div class="col-sm-4">
                        <label class="text-uppercase fw-bold text-muted mb-1" style="font-size: 10px; letter-spacing: 0.5px;">Data Peneliti</label>
                    </div>
                    <div class="col-sm-8">
                        <p class="fw-bold text-dark mb-1" style="font-size: 13px;"><?= $data['nama'] ?? session()->get('nama') ?? '-' ?></p>
                        <p class="text-muted mb-0" style="font-size: 11px;"><?= $data['institusi'] ?? session()->get('institusi') ?? '-' ?> <?= isset($data['prodi']) ? ' - ' . $data['prodi'] : '' ?></p>
                    </div>
                </div>

                <div class="row mb-3 pb-3 border-bottom border-light">
                    <div class="col-sm-4">
                        <label class="text-uppercase fw-bold text-muted mb-1" style="font-size: 10px; letter-spacing: 0.5px;">Waktu Pelaksanaan</label>
                    </div>
                    <div class="col-sm-8">
                        <p class="text-dark mb-0" style="font-size: 12px;">
                            <?php
                                $bulan_indo = [
                                    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
                                    7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                                ];
                                $waktu_mulai = (!empty($data['waktu_mulai']) && $data['waktu_mulai'] != '0000-00-00') ? date('d', strtotime($data['waktu_mulai'])) . ' ' . $bulan_indo[(int)date('m', strtotime($data['waktu_mulai']))] . ' ' . date('Y', strtotime($data['waktu_mulai'])) : '-';
                                $waktu_selesai = (!empty($data['waktu_selesai']) && $data['waktu_selesai'] != '0000-00-00') ? date('d', strtotime($data['waktu_selesai'])) . ' ' . $bulan_indo[(int)date('m', strtotime($data['waktu_selesai']))] . ' ' . date('Y', strtotime($data['waktu_selesai'])) : '-';
                            ?>
                            <i class="far fa-calendar-alt text-danger me-1"></i> 
                            <?= $waktu_mulai ?> 
                            <span class="text-muted mx-1">s/d</span> 
                            <?= $waktu_selesai ?>
                        </p>
                    </div>
                </div>

                <div class="row mb-3 pb-3 border-bottom border-light">
                    <div class="col-sm-4">
                        <label class="text-uppercase fw-bold text-muted mb-1" style="font-size: 10px; letter-spacing: 0.5px;">Judul Penelitian</label>
                    </div>
                    <div class="col-sm-8">
                        <p class="fw-bold text-dark mb-0" style="font-size: 13px; line-height: 1.5;"><?= $data['judul'] ?></p>
                    </div>
                </div>

                <div class="row mb-0">
                    <div class="col-sm-4">
                        <label class="text-uppercase fw-bold text-muted mb-2" style="font-size: 10px; letter-spacing: 0.5px;">Lampiran Berkas</label>
                    </div>
                    <div class="col-sm-8">
                        <div class="row g-2">
                            <?php foreach ($data['dokumen'] as $doc): ?>
                                <?php if (strtolower($doc['jenis_dokumen']) == 'surat izin resmi') continue; ?>
                                <div class="col-12">
                                    <div class="p-2 border border-light rounded-3 d-flex align-items-center bg-light transition-hover">
                                        <div class="bg-danger bg-opacity-10 text-danger rounded p-2 me-3 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                            <i class="fas fa-file-pdf"></i>
                                        </div>
                                        <div class="flex-grow-1 overflow-hidden">
                                            <div class="fw-bold text-dark text-truncate" style="font-size: 12px;"><?= strtolower($doc['jenis_dokumen']) == 'cv' ? 'CV' : $doc['jenis_dokumen'] ?></div>
                                            <div class="text-muted" style="font-size: 10px;">Dokumen Berkas</div>
                                        </div>
                                        <div class="ms-2">
                                            <a href="<?= base_url($doc['file_path']) ?>" target="_blank" class="btn btn-sm btn-white border text-danger rounded-circle shadow-sm" style="width: 30px; height: 30px; padding: 0; display: inline-flex; align-items: center; justify-content: center;"><i class="fas fa-download" style="font-size: 10px;"></i></a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Status Card -->
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 text-center">
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
                <p class="text-muted mb-3" style="font-size: 11px;">Pendaftaran riset telah disetujui. Silakan cek menu Status & Download secara berkala untuk dokumen penerimaan riset.</p>
                <?php 
                    $suratIzinDoc = array_filter($data['dokumen'] ?? [], fn($d) => strtolower($d['jenis_dokumen']) == 'surat izin resmi');
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
                    <p class="text-muted mt-2 mb-0" style="font-size: 10px;">Surat Izin Resmi sedang diproses oleh Admin.</p>
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
                    <p class="text-muted mb-3" style="font-size: 11px;">Pengajuan disetujui admin. Silakan unggah bukti bayar untuk menerbitkan surat izin.</p>
                <?php endif; ?>
                <button type="button" class="btn btn-warning text-dark btn-sm w-100 rounded-pill fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#paymentModalRiset">
                    <i class="fas fa-upload me-2"></i> Upload Bukti Bayar
                </button>
            <?php elseif ($status_lower == 'menunggu_verifikasi'): ?>
                <div class="d-inline-block p-4 rounded-circle bg-info bg-opacity-10 text-info mb-3 mx-auto" style="width: 80px; height: 80px;">
                    <i class="fas fa-hourglass-half" style="font-size: 32px;"></i>
                </div>
                <h5 class="fw-bold text-info mb-1">Menunggu Verifikasi</h5>
                <p class="text-muted small">Bukti bayar diunggah pada <?= date('d') . ' ' . $bulan_indo[(int)date('m')] . ' ' . date('Y') ?></p>
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
                <p class="text-muted mb-0" style="font-size: 11px;">Berkas pendaftaran Anda sedang direview oleh admin. Harap tunggu hingga notifikasi selanjutnya untuk arahan berikutnya.</p>
            <?php elseif ($status_lower == 'direvisi' || $status_lower == 'revisi'): ?>
                <div class="d-inline-block p-4 rounded-circle bg-warning bg-opacity-10 text-warning mb-3 mx-auto" style="width: 80px; height: 80px;">
                    <i class="fas fa-edit" style="font-size: 32px;"></i>
                </div>
                <h5 class="fw-bold text-warning mb-1">Perlu Revisi</h5>
                <p class="text-muted small">Diajukan pada <?= $data['tanggal'] ?></p>
                <hr>
                <p class="text-muted mb-3" style="font-size: 11px;">Berkas pendaftaran Anda perlu direvisi. Silakan periksa catatan admin di bagian atas halaman.</p>
                <a href="<?= base_url('riset/peneliti/pengajuan/izin/baru?id=' . $data['id']) ?>" class="btn btn-warning mt-2 btn-sm w-100 rounded-pill fw-bold shadow-sm" style="background-color: #fd7e14; border: none; color: white;">
                    <i class="fas fa-edit me-2"></i> Buka Formulir Revisi
                </a>
            <?php elseif ($status_lower == 'ditolak'): ?>
                <div class="d-inline-block p-4 rounded-circle bg-danger bg-opacity-10 text-danger mb-3 mx-auto" style="width: 80px; height: 80px;">
                    <i class="fas fa-times-circle" style="font-size: 32px;"></i>
                </div>
                <h5 class="fw-bold text-danger mb-1">Ditolak</h5>
                <p class="text-muted small">Diajukan pada <?= $data['tanggal'] ?></p>
                <hr>
                <p class="text-muted mb-2" style="font-size: 11px;">Mohon maaf, pengajuan izin penelitian Anda ditolak oleh admin.</p>
                <div class="bg-danger bg-opacity-10 border border-danger border-opacity-25 rounded-3 p-3 text-start mx-auto mt-2">
                    <strong class="text-danger d-block mb-1" style="font-size: 11px;">Alasan Penolakan:</strong>
                    <span class="text-dark" style="font-size: 12px;"><?= $data['catatan_penolakan'] ?? 'Tidak ada catatan spesifik.' ?></span>
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

<!-- Modal Pembayaran Riset -->
<div class="modal fade" id="paymentModalRiset" tabindex="-1" aria-labelledby="paymentModalRisetLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <form action="<?= base_url('riset/peneliti/pengajuan/izin/bayar') ?>" method="post" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?= $data['id'] ?>">
                <div class="modal-header bg-warning text-dark border-0 py-3">
                    <h5 class="modal-title fw-bold" id="paymentModalRisetLabel" style="font-size: 15px;"><i class="fas fa-file-invoice-dollar me-2"></i> Informasi Pembayaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 bg-light">
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-body p-4">
                            <div class="text-center mb-4">
                                <p class="text-muted small fw-bold text-uppercase mb-1" style="font-size: 10px; letter-spacing: 1px;">Total Tagihan Pelaksanaan Riset</p>
                                <?php if (!empty($data['nominal_bayar'])): ?>
                                    <h3 class="fw-bold text-dark mb-0">Rp <?= number_format($data['nominal_bayar'], 0, ',', '.') ?></h3>
                                    <p class="text-muted small mt-1 mb-0" style="font-size: 11px;">Sesuai dengan tagihan dari admin.</p>
                                <?php else: ?>
                                    <h3 class="fw-bold text-dark mb-0">Sesuai Tarif Layanan</h3>
                                    <p class="text-muted small mt-1 mb-0" style="font-size: 11px;">Silakan konfirmasi ke admin untuk detail tagihan.</p>
                                <?php endif; ?>
                            </div>

                            <div class="bg-light p-3 rounded-3 border mb-0">
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
