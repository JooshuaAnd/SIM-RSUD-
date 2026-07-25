<?php
/**
 * @var array $stats
 * @var array $riwayat
 * @var array $arsip_izin
 * @var array $arsip_publikasi
 */

$bulanIndo = [
    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
    '04' => 'April', '05' => 'Mei', '06' => 'Juni',
    '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
    '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
];
?>
<?= $this->extend('riset/peneliti/layout/template') ?>

<?= $this->section('content') ?>

<div class="mb-4 d-flex justify-content-between align-items-end">
    <div>
        <h3 class="fw-bold text-dark mb-1" style="letter-spacing: -0.5px;">Dashboard Peneliti</h3>
        <p class="text-muted small mb-0">Pantau seluruh progres penelitian Anda di satu tempat terpadu.</p>
    </div>
    <div>
        <span class="badge bg-light text-muted border px-3 py-2 rounded-pill shadow-sm" style="font-size: 11px; font-weight: 500;">
            <i class="fas fa-calendar-alt me-1 text-danger"></i> <?= date('d') . ' ' . $bulanIndo[date('m')] . ' ' . date('Y') ?>
        </span>
    </div>
</div>

<div class="row g-4">
    <!-- Left Column: History & Stats -->
    <div class="col-lg-8">
        
        <!-- Clean Quick Stats -->
        <div class="row g-3 mb-4">
            <div class="col-md-4 col-12">
                <div class="card border border-light shadow-sm rounded-4 h-100 p-3 bg-white transition-hover d-flex flex-row align-items-center">
                    <div class="bg-danger bg-opacity-10 text-danger rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px; flex-shrink: 0;">
                        <i class="fas fa-microscope" style="font-size: 18px;"></i>
                    </div>
                    <div>
                        <div class="text-muted fw-semibold text-uppercase mb-1" style="font-size: 10px; letter-spacing: 0.5px;">Total Pengajuan</div>
                        <h4 class="fw-bold text-dark mb-0"><?= esc($stats['total_pengajuan']) ?> <span class="text-muted fw-normal" style="font-size: 11px;">Judul</span></h4>
                        <p class="text-muted mb-0 d-none d-md-block mt-1" style="font-size: 11px;">Keseluruhan riset diajukan</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-12">
                <div class="card border border-light shadow-sm rounded-4 h-100 p-3 bg-white transition-hover d-flex flex-row align-items-center">
                    <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px; flex-shrink: 0;">
                        <i class="fas fa-hourglass-half" style="font-size: 18px;"></i>
                    </div>
                    <div>
                        <div class="text-muted fw-semibold text-uppercase mb-1" style="font-size: 10px; letter-spacing: 0.5px;">Dalam Proses</div>
                        <h4 class="fw-bold text-dark mb-0"><?= esc($stats['dalam_proses']) ?> <span class="text-muted fw-normal" style="font-size: 11px;">Dokumen</span></h4>
                        <p class="text-muted mb-0 d-none d-md-block mt-1" style="font-size: 11px;">Sedang diproses sistem</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-12">
                <div class="card border border-light shadow-sm rounded-4 h-100 p-3 bg-white transition-hover d-flex flex-row align-items-center">
                    <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px; flex-shrink: 0;">
                        <i class="fas fa-check-double" style="font-size: 18px;"></i>
                    </div>
                    <div>
                        <div class="text-muted fw-semibold text-uppercase mb-1" style="font-size: 10px; letter-spacing: 0.5px;">Riset Selesai</div>
                        <h4 class="fw-bold text-dark mb-0"><?= esc($stats['selesai']) ?> <span class="text-muted fw-normal" style="font-size: 11px;">Judul</span></h4>
                        <p class="text-muted mb-0 d-none d-md-block mt-1" style="font-size: 11px;">Telah dapat izin final</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Applications Table (Clean Version) -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark" style="font-size: 13px;">Riwayat Pengajuan Terbaru</h6>
                <a href="<?= base_url('riset/peneliti/status') ?>" class="text-primary fw-semibold text-decoration-none" style="font-size: 11px;">Lihat Selengkapnya <i class="fas fa-angle-right ms-1"></i></a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 border-white">
                        <thead class="bg-light bg-opacity-50">
                            <tr style="font-size: 10px; letter-spacing: 0.5px;" class="text-muted text-uppercase fw-semibold">
                                <th class="ps-4 py-3 border-0">Informasi Riset</th>
                                <th class="border-0">Tanggal</th>
                                <th class="text-center border-0">Status</th>
                                <th class="pe-4 text-center border-0">Detail</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($riwayat)): ?>
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted" style="font-size: 12px;">
                                    Belum ada pengajuan riset.
                                </td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($riwayat as $row): ?>
                                <tr>
                                    <td class="ps-4 py-3 border-light">
                                        <div style="font-size: 12px; color: #333; font-weight: 600; line-height: 1.4;"><?= esc($row['judul']) ?></div>
                                        <div class="text-muted" style="font-size: 10px; margin-top: 2px;">Jenis: <?= str_replace('_', ' ', ucwords(esc($row['jenis_pengajuan']))) ?></div>
                                    </td>
                                    <td style="font-size: 11px; color: #666;" class="border-light"><?= date('d', strtotime($row['created_at'])) . ' ' . $bulanIndo[date('m', strtotime($row['created_at']))] . ' ' . date('Y', strtotime($row['created_at'])) ?></td>
                                    <td class="text-center border-light">
                                        <?php if ($row['status'] == 'selesai'): ?>
                                            <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill fw-semibold" style="font-size: 10px;"><i class="fas fa-check-circle me-1"></i> Selesai</span>
                                        <?php elseif ($row['status'] == 'ditolak'): ?>
                                            <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill fw-semibold" style="font-size: 10px;"><i class="fas fa-times-circle me-1"></i> Ditolak</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2 rounded-pill fw-semibold" style="font-size: 10px;"><i class="fas fa-clock me-1"></i> Diproses</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="pe-4 text-center border-light">
                                        <?php 
                                            if ($row['jenis_pengajuan'] == 'publikasi') {
                                                $url = base_url("riset/peneliti/publikasi/detail?id={$row['id']}");
                                            } else {
                                                $jenis = $row['jenis_pengajuan'] == 'studi_pendahuluan' ? 'stupen' : 'izin';
                                                $url = base_url("riset/peneliti/pengajuan/{$jenis}/detail/{$row['id']}");
                                            }
                                        ?>
                                        <a href="<?= $url ?>" class="btn btn-sm text-primary p-0">
                                            <i class="fas fa-chevron-right" style="font-size: 12px;"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Shortcuts & Documents -->
    <div class="col-lg-4">
        
        <!-- Shortcut Mendaftar -->
        <div class="card border border-danger border-opacity-10 shadow-sm rounded-4 mb-4" style="background: linear-gradient(145deg, #ffffff, #fffafa);">
            <div class="card-body p-4 text-center">
                <div class="bg-danger bg-opacity-10 text-danger rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 56px; height: 56px;">
                    <i class="fas fa-plus" style="font-size: 20px;"></i>
                </div>
                <h6 class="fw-bold text-dark mb-2">Punya Topik Baru?</h6>
                <p class="text-muted mb-4" style="font-size: 11px; line-height: 1.5;">Ajukan studi pendahuluan untuk mengawali prosedur perizinan riset secara resmi.</p>
                <a href="<?= base_url('riset/peneliti/pengajuan/stupen/baru') ?>" class="btn btn-danger w-100 rounded-pill fw-semibold py-2 shadow-sm d-flex align-items-center justify-content-center" style="font-size: 12px;">
                    Ajukan Riset Sekarang <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
        </div>

        <!-- Dokumen Terbit -->
        <div class="card border border-light shadow-sm rounded-4 mb-4 bg-white">
            <div class="card-header bg-transparent py-3 px-4 border-bottom border-light">
                <h6 class="fw-bold mb-0 text-dark" style="font-size: 13px;">
                    Arsip Dokumen Penting
                </h6>
            </div>
            <div class="card-body p-4">
                <?php if(empty($arsip_izin) && empty($arsip_publikasi)): ?>
                    <div class="text-center py-4 text-muted" style="font-size: 12px;">
                        Belum ada dokumen yang diterbitkan.
                    </div>
                <?php else: ?>
                    <?php foreach ($arsip_publikasi as $pub): ?>
                    <div class="d-flex justify-content-between align-items-center p-3 rounded-4 mb-3 border border-light transition-hover">
                        <div class="d-flex align-items-center">
                            <div class="bg-danger bg-opacity-10 text-danger rounded-3 d-flex align-items-center justify-content-center me-3" style="width: 38px; height: 38px;">
                                <i class="fas fa-file-pdf" style="font-size: 18px;"></i>
                            </div>
                            <div>
                                <div class="fw-semibold text-dark" style="font-size: 12px;">Surat Izin Publikasi</div>
                                <div class="text-muted mt-1" style="font-size: 10px;">No: <?= esc($pub['no_surat_izin'] ?? '-') ?></div>
                            </div>
                        </div>
                        <a href="<?= base_url('riset/peneliti/publikasi/print_izin/' . $pub['id']) ?>" target="_blank" class="btn btn-sm btn-light border-0 text-primary bg-primary bg-opacity-10 rounded-circle d-flex justify-content-center align-items-center" style="width: 32px; height: 32px;" title="Cetak Surat">
                            <i class="fas fa-download" style="font-size: 12px;"></i>
                        </a>
                    </div>
                    <?php endforeach; ?>

                    <?php foreach ($arsip_izin as $izin): ?>
                    <div class="d-flex justify-content-between align-items-center p-3 rounded-4 mb-3 border border-light transition-hover">
                        <div class="d-flex align-items-center">
                            <div class="bg-success bg-opacity-10 text-success rounded-3 d-flex align-items-center justify-content-center me-3" style="width: 38px; height: 38px;">
                                <i class="fas fa-file-signature" style="font-size: 18px;"></i>
                            </div>
                            <div>
                                <div class="fw-semibold text-dark" style="font-size: 12px;">Surat Izin Penelitian</div>
                                <div class="text-muted mt-1" style="font-size: 10px;">No: <?= esc(!empty($izin['nomor_surat']) ? $izin['nomor_surat'] : format_pengajuan_id($izin['id'], 'penelitian')) ?></div>
                            </div>
                        </div>
                        <a href="<?= base_url('riset/peneliti/pengajuan/izin/print/' . $izin['id']) ?>" target="_blank" class="btn btn-sm btn-light border-0 text-primary bg-primary bg-opacity-10 rounded-circle d-flex justify-content-center align-items-center" style="width: 32px; height: 32px;" title="Cetak Surat">
                            <i class="fas fa-download" style="font-size: 12px;"></i>
                        </a>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

<style>
    .transition-hover {
        transition: all 0.2s ease-in-out;
    }
    .transition-hover:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.05) !important;
    }
</style>

<?= $this->endSection() ?>

