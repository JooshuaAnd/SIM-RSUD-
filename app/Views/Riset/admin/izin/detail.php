<?php

/** @var array $data */ /** @var string $default_nomor_surat */ ?>
<?= $this->extend('Riset/admin/layout/template') ?>

<?= $this->section('content') ?>

<div class="mb-4">
    <a href="<?= base_url('riset/admin/izin') ?>" class="text-decoration-none text-muted fw-bold d-inline-flex align-items-center" style="font-size: 13px; transition: color 0.2s;" onmouseover="this.style.color='#c62828'" onmouseout="this.style.color='#6c757d'">
        <i class="fas fa-arrow-left me-2"></i> Kembali ke Daftar Izin Penelitian
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white py-3 px-4 border-0 rounded-top-4">
                <h5 class="fw-bold mb-0" style="font-size: 16px;">Informasi Submisi <?= esc(format_pengajuan_id($data['id'], 'penelitian')) ?></h5>
            </div>
            <hr class="m-0" style="color: #eee;">
            <div class="card-body px-4 pt-3 pb-4">
                <!-- Researcher Info Row -->
                <div class="row mb-4">
                    <div class="col-md-7">
                        <label class="text-uppercase fw-bold text-muted mb-2" style="font-size: 10px; letter-spacing: 1px;">Data Peneliti</label>
                        <div class="fw-bold mb-1" style="font-size: 15px;"><?= esc($data['nama']) ?></div>
                        <div class="text-muted mb-1" style="font-size: 12px;"><?= esc($data['institusi']) ?></div>
                        <div class="text-muted" style="font-size: 12px;">Identitas: <?= esc($data['identitas']) ?></div>
                    </div>
                    <div class="col-md-5">
                        <hr class="d-md-none mt-3 mb-3" style="color: #eee;">
                        <div class="border-start-md border-light h-100 ps-md-3">
                            <label class="text-uppercase fw-bold text-muted mb-2" style="font-size: 10px; letter-spacing: 1px;">Kontak</label>
                            <div class="d-flex align-items-center mb-1" style="font-size: 12px;">
                                <i class="fas fa-envelope text-danger me-2" style="font-size: 10px;"></i> <?= esc($data['email'] ?? '-') ?>
                            </div>
                            <div class="d-flex align-items-center" style="font-size: 12px;">
                                <i class="fas fa-phone text-danger me-2" style="font-size: 10px;"></i> <?= esc($data['no_telp'] ?? '-') ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Research Title -->
                <div class="mb-4">
                    <label class="text-uppercase fw-bold text-muted mb-1" style="font-size: 10px; letter-spacing: 1px;">Judul Penelitian</label>
                    <h5 class="fw-bold" style="color: #e53935; line-height: 1.4; font-size: 18px;">
                        <?= esc($data['judul']) ?>
                    </h5>
                </div>

                <!-- Preliminary Study Details -->
                <?php
                $bulanIndo = [
                    '01' => 'Januari',
                    '02' => 'Februari',
                    '03' => 'Maret',
                    '04' => 'April',
                    '05' => 'Mei',
                    '06' => 'Juni',
                    '07' => 'Juli',
                    '08' => 'Agustus',
                    '09' => 'September',
                    '10' => 'Oktober',
                    '11' => 'November',
                    '12' => 'Desember'
                ];
                $formatMulai = (!empty($data['waktu_mulai']) && $data['waktu_mulai'] != '0000-00-00') ? date('d', strtotime($data['waktu_mulai'])) . ' ' . $bulanIndo[date('m', strtotime($data['waktu_mulai']))] . ' ' . date('Y', strtotime($data['waktu_mulai'])) : '-';
                $formatSelesai = (!empty($data['waktu_selesai']) && $data['waktu_selesai'] != '0000-00-00') ? date('d', strtotime($data['waktu_selesai'])) . ' ' . $bulanIndo[date('m', strtotime($data['waktu_selesai']))] . ' ' . date('Y', strtotime($data['waktu_selesai'])) : '-';
                ?>
                <div class="bg-light rounded-4 p-3 mb-4" style="background-color: #f8f9fa !important;">
                    <div class="d-flex align-items-center mb-3 pb-2 border-bottom">
                        <i class="fas fa-calendar-alt text-muted me-2"></i>
                        <span class="text-uppercase fw-bold text-muted" style="font-size: 10px; letter-spacing: 1px;">Detail Pelaksanaan</span>
                    </div>

                    <div class="row mb-0">
                        <div class="col-md-4">
                            <p class="text-muted mb-1" style="font-size: 11px;">Program Studi</p>
                            <p class="fw-bold mb-0" style="font-size: 13px;"><?= esc($data['prodi']) ?></p>
                        </div>
                        <div class="col-md-4">
                            <p class="text-muted mb-1" style="font-size: 11px;">Waktu Mulai</p>
                            <p class="fw-bold mb-0" style="font-size: 13px;"><?= $formatMulai ?></p>
                        </div>
                        <div class="col-md-4">
                            <p class="text-muted mb-1" style="font-size: 11px;">Waktu Selesai</p>
                            <p class="fw-bold mb-0" style="font-size: 13px;"><?= $formatSelesai ?></p>
                        </div>
                    </div>
                </div>

                <!-- Documents Section -->
                <div>
                    <label class="text-uppercase fw-bold text-muted mb-2" style="font-size: 10px; letter-spacing: 1px;">Dokumen Pendukung</label>
                    <div class="list-group list-group-flush">
                        <?php
                        $surat_permohonan = array_filter($data['dokumen'] ?? [], fn($d) => $d['jenis_dokumen'] == 'Surat Permohonan');
                        $surat_permohonan = reset($surat_permohonan);

                        $proposal = array_filter($data['dokumen'] ?? [], fn($d) => $d['jenis_dokumen'] == 'Proposal');
                        $proposal = reset($proposal);

                        $cv = array_filter($data['dokumen'] ?? [], fn($d) => strtolower($d['jenis_dokumen']) == 'cv');
                        $cv = reset($cv);

                        $draft_wawancara = array_filter($data['dokumen'] ?? [], fn($d) => $d['jenis_dokumen'] == 'Draft Wawancara');
                        $draft_wawancara = reset($draft_wawancara);
                        ?>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-3 border-light">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-file-pdf text-danger me-3" style="font-size: 20px;"></i>
                                <span style="font-size: 13px; color: #444;">1. Surat Permohonan</span>
                            </div>
                            <?php if ($surat_permohonan): ?>
                                <a href="<?= base_url($surat_permohonan['file_path']) ?>" target="_blank" class="btn btn-outline-dark btn-sm rounded-3 fw-bold border" style="font-size: 10px; border-color: #ddd; padding: 5px 15px;">Cek Berkas</a>
                            <?php else: ?>
                                <button class="btn btn-outline-secondary btn-sm rounded-3 fw-bold border disabled" style="font-size: 10px; border-color: #ddd; padding: 5px 15px;">Belum Diunggah</button>
                            <?php endif; ?>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-3 border-light">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-file-pdf text-danger me-3" style="font-size: 20px;"></i>
                                <span style="font-size: 13px; color: #444;">2. Proposal Riset</span>
                            </div>
                            <?php if ($proposal): ?>
                                <a href="<?= base_url($proposal['file_path']) ?>" target="_blank" class="btn btn-outline-dark btn-sm rounded-3 fw-bold border" style="font-size: 10px; border-color: #ddd; padding: 5px 15px;">Cek Berkas</a>
                            <?php else: ?>
                                <button class="btn btn-outline-secondary btn-sm rounded-3 fw-bold border disabled" style="font-size: 10px; border-color: #ddd; padding: 5px 15px;">Belum Diunggah</button>
                            <?php endif; ?>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-3 border-light">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-file-pdf text-danger me-3" style="font-size: 20px;"></i>
                                <span style="font-size: 13px; color: #444;">3. Curriculum Vitae (CV)</span>
                            </div>
                            <?php if ($cv): ?>
                                <a href="<?= base_url($cv['file_path']) ?>" target="_blank" class="btn btn-outline-dark btn-sm rounded-3 fw-bold border" style="font-size: 10px; border-color: #ddd; padding: 5px 15px;">Cek Berkas</a>
                            <?php else: ?>
                                <button class="btn btn-outline-secondary btn-sm rounded-3 fw-bold border disabled" style="font-size: 10px; border-color: #ddd; padding: 5px 15px;">Belum Diunggah</button>
                            <?php endif; ?>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-3 border-light">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-file-pdf text-danger me-3" style="font-size: 20px;"></i>
                                <span style="font-size: 13px; color: #444;">4. Draft Wawancara / Kuesioner</span>
                            </div>
                            <?php if ($draft_wawancara): ?>
                                <a href="<?= base_url($draft_wawancara['file_path']) ?>" target="_blank" class="btn btn-outline-dark btn-sm rounded-3 fw-bold border" style="font-size: 10px; border-color: #ddd; padding: 5px 15px;">Cek Berkas</a>
                            <?php else: ?>
                                <button class="btn btn-outline-secondary btn-sm rounded-3 fw-bold border disabled" style="font-size: 10px; border-color: #ddd; padding: 5px 15px;">Belum Diunggah</button>
                            <?php endif; ?>
                        </div>

                        <?php if (in_array($data['status'], ['review_bayar', 'menunggu_verifikasi', 'selesai', 'izin_terbit'])): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-3 border-light">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-receipt text-success me-3" style="font-size: 20px;"></i>
                                    <div>
                                        <span style="font-size: 13px; color: #444; display:block;">5. Bukti Pembayaran Izin Penelitian</span>
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success mt-1" style="font-size: 9px;">Terverifikasi</span>
                                    </div>
                                </div>
                                <?php if (!empty($data['bukti_file'])): ?>
                                    <a href="<?= base_url($data['bukti_file']) ?>" target="_blank" class="btn btn-outline-dark btn-sm rounded-3 fw-bold border" style="font-size: 10px; border-color: #ddd; padding: 5px 15px;">Cek Berkas</a>
                                <?php else: ?>
                                    <button class="btn btn-outline-secondary btn-sm rounded-3 fw-bold border disabled" style="font-size: 10px; border-color: #ddd; padding: 5px 15px;">Belum Diunggah</button>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if (in_array($data['status'], ['review_bayar', 'menunggu_verifikasi', 'selesai', 'izin_terbit'])): ?>
                        <div class="mt-4 p-4 rounded-4 border bg-white shadow-sm" style="max-width: 450px;">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="fw-bold text-dark mb-0" style="font-size: 13px;"><i class="fas fa-receipt text-danger me-2"></i> Bukti Transfer Pembayaran</h6>
                                <?php $nominal = isset($data['nominal_bayar']) ? $data['nominal_bayar'] : 0; ?>
                                <?php if ($nominal > 0): ?>
                                    <span class="badge bg-success bg-opacity-10 text-success fw-bold px-2 py-1" style="font-size: 10px;">Rp <?= number_format($nominal, 0, ',', '.') ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="p-2 bg-light rounded-3 text-center border">
                                <?php
                                $ext = strtolower(pathinfo((string)$data['bukti_file'], PATHINFO_EXTENSION));
                                if (in_array($ext, ['jpg', 'jpeg', 'png'])): ?>
                                    <img src="<?= base_url($data['bukti_file']) ?>" alt="Bukti Pembayaran" class="img-fluid rounded" style="max-height: 400px; object-fit: contain;">
                                <?php else: ?>
                                    <div class="py-5 text-center">
                                        <i class="fas fa-file-pdf text-danger mb-3" style="font-size: 48px;"></i>
                                        <p class="mb-0 text-muted" style="font-size: 13px;">File bukti pembayaran berupa dokumen.</p>
                                        <a href="<?= base_url($data['bukti_file']) ?>" target="_blank" class="btn btn-outline-danger btn-sm mt-3 px-4 rounded-pill">Buka Dokumen</a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Admin Actions Sidebar -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
            <div class="card-header bg-white py-3 px-4 border-0 border-top border-danger border-4">
                <h6 class="fw-bold mb-0" style="font-size: 15px;">Tindakan Admin</h6>
            </div>
            <hr class="m-0" style="color: #eee;">
            <div class="card-body px-4 pt-3 pb-4">
                <p class="text-muted mb-4" style="font-size: 12px;">Tinjau semua dokumen sebelum memberikan keputusan final.</p>

                <?php if ($data['status'] != 'selesai'): ?>
                <form id="formTindakanAdmin" action="<?= base_url('riset/admin/izin/approve') ?>" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?= esc($data['id']) ?>">
                    <input type="hidden" name="role" value="riset">
                <?php endif; ?>

                    <?php if ($data['status'] == 'selesai'): ?>
                        <div class="text-center py-4">
                            <div class="mb-3">
                                <i class="fas fa-check-circle text-success" style="font-size: 48px;"></i>
                            </div>
                            <h5 class="fw-bold text-dark mb-2">Izin Telah Terbit</h5>
                            <p class="text-muted mb-4" style="font-size: 12px;">Surat Izin Penelitian dengan nomor <strong><?= esc($data['nomor_surat'] ?? '-') ?></strong> telah berhasil diterbitkan dan dikirimkan ke peneliti.</p>

                            <a href="<?= base_url('riset/admin/izin/print/' . $data['id']) ?>" target="_blank" class="btn btn-primary w-100 py-2 mb-3 fw-bold shadow-sm" style="font-size: 12px; border-radius: 8px;">
                                <i class="fas fa-print me-2" style="font-size: 14px;"></i> CETAK SURAT IZIN
                            </a>

                            <?php 
                                $suratIzinDoc = array_filter($data['dokumen'] ?? [], fn($d) => $d['jenis_dokumen'] == 'Surat Izin Resmi');
                                $suratIzinDoc = reset($suratIzinDoc);
                            ?>

                            <form action="<?= base_url('riset/admin/izin/uploadSuratIzin') ?>" method="post" enctype="multipart/form-data" class="mt-3 text-start border border-success p-3 rounded-3 bg-light">
                                <input type="hidden" name="id" value="<?= esc($data['id']) ?>">
                                <label class="fw-bold mb-2 text-dark" style="font-size: 11px; text-transform: uppercase;">Upload Surat Izin Resmi (Bertanda Tangan)</label>
                                <input type="file" name="surat_izin" class="form-control mb-2" required accept=".pdf" style="font-size: 12px;">
                                <button type="submit" class="btn btn-warning w-100 fw-bold shadow-sm text-dark" style="font-size: 12px;"><i class="fas fa-upload me-2"></i> UPLOAD SURAT IZIN</button>
                                <?php if ($suratIzinDoc): ?>
                                    <div class="mt-3 p-2 bg-white rounded border border-success text-center">
                                        <span class="badge bg-success bg-opacity-10 text-success p-2 w-100 mb-2" style="font-size: 10px;">
                                            <i class="fas fa-check-circle me-1"></i> Surat telah diupload ke Peneliti
                                        </span>
                                        <a href="<?= base_url($suratIzinDoc['file_path']) ?>" target="_blank" class="btn btn-sm btn-outline-success w-100" style="font-size: 11px;"><i class="fas fa-eye me-1"></i> Lihat Berkas Terupload</a>
                                    </div>
                                <?php endif; ?>
                            </form>
                        </div>
                    <?php elseif ($data['status'] == 'direvisi' || $data['status'] == 'revisi'): ?>
                        <div class="text-center py-4">
                            <div class="mb-3">
                                <i class="fas fa-undo text-secondary" style="font-size: 48px;"></i>
                            </div>
                            <h5 class="fw-bold text-dark mb-2">Sedang Direvisi</h5>
                            <p class="text-muted mb-4" style="font-size: 12px;">Pengajuan ini telah dikembalikan ke peneliti untuk diperbaiki sesuai dengan catatan yang diberikan.</p>

                            <div class="text-start bg-light p-3 rounded-3 mb-4 border">
                                <strong class="d-block mb-1 text-dark" style="font-size: 11px;">Catatan Revisi:</strong>
                                <span class="text-muted" style="font-size: 12px;"><?= esc($data['catatan_revisi'] ?? 'Mohon lengkapi dokumen.') ?></span>
                            </div>
                        </div>
                    <?php elseif ($data['status'] == 'ditolak'): ?>
                        <div class="text-center py-4">
                            <div class="mb-3">
                                <i class="fas fa-times-circle text-danger" style="font-size: 48px;"></i>
                            </div>
                            <h5 class="fw-bold text-dark mb-2">Pengajuan Ditolak</h5>
                            <p class="text-muted mb-4" style="font-size: 12px;">Pengajuan ini telah ditolak dan tidak dapat diproses lebih lanjut.</p>

                            <div class="text-start bg-light p-3 rounded-3 mb-4 border">
                                <strong class="d-block mb-1 text-dark" style="font-size: 11px;">Alasan Penolakan:</strong>
                                <span class="text-muted" style="font-size: 12px;"><?= esc($data['catatan_penolakan'] ?? 'Tidak ada alasan yang diberikan.') ?></span>
                            </div>
                        </div>
                    <?php else: ?>

                        <?php if (in_array($data['status'], ['review_bayar', 'menunggu_verifikasi'])): ?>
                            <!-- Step 2: Konfirmasi Bukti Pembayaran -->
                            <div class="d-grid gap-2 mb-3">
                                <input type="radio" class="btn-check" name="status_validasi" id="statusBayarSetuju" autocomplete="off" value="konfirmasi_bayar" required oninvalid="this.setCustomValidity('Silakan pilih salah satu keputusan validasi di atas.')" onchange="this.setCustomValidity('')">
                                <label class="btn btn-outline-primary text-start px-3 py-2 rounded-3 fw-bold d-flex flex-column" for="statusBayarSetuju" style="font-size: 13px; border-width: 1px;">
                                    <div class="d-flex align-items-center mb-1">
                                        <i class="fas fa-file-signature me-2"></i> Setujui & Keluarkan Izin
                                    </div>
                                    <small class="ms-4 ps-1 fw-normal opacity-75" style="font-size: 10px;">Bukti bayar valid. Terbitkan surat izin meneliti otomatis.</small>
                                </label>

                                <input type="radio" class="btn-check" name="status_validasi" id="statusBayarTolak" autocomplete="off" value="revisi_bayar">
                                <label class="btn btn-outline-warning text-start px-3 py-2 rounded-3 fw-bold d-flex flex-column text-dark" for="statusBayarTolak" style="font-size: 13px; border-width: 1px;">
                                    <div class="d-flex align-items-center mb-1">
                                        <i class="fas fa-undo me-2"></i> Kembalikan Bukti Bayar
                                    </div>
                                    <small class="ms-4 ps-1 fw-normal opacity-75" style="font-size: 10px;">Bukti bayar tidak valid. Minta peneliti mengunggah ulang.</small>
                                </label>
                            </div>

                            <div id="nomorSuratArea" class="d-none mb-4 animate__animated animate__fadeIn">
                                <div class="mb-3">
                                    <label class="fw-bold mb-2 text-dark" style="font-size: 11px; text-transform: uppercase; letter-spacing: 1px;">Nomor Surat Izin <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control rounded-3 bg-light" name="nomor_surat" id="inputNomorSurat" placeholder="Masukkan nomor surat..." value="<?= esc($default_nomor_surat) ?>" style="font-size: 13px; border-color: #ddd; padding: 10px 12px;">
                                    <small class="text-muted" style="font-size: 10px;">Nomor surat otomatis di-generate berdasarkan urutan pengajuan, namun dapat diedit manual.</small>
                                </div>
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <label class="fw-bold mb-2 text-dark" style="font-size: 11px; text-transform: uppercase; letter-spacing: 1px;">Waktu Mulai <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control rounded-3 bg-light input-waktu" name="waktu_mulai" value="<?= esc($data['waktu_mulai'] ?? '') ?>" style="font-size: 13px; border-color: #ddd; padding: 10px 12px;">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="fw-bold mb-2 text-dark" style="font-size: 11px; text-transform: uppercase; letter-spacing: 1px;">Waktu Selesai <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control rounded-3 bg-light input-waktu" name="waktu_selesai" value="<?= esc($data['waktu_selesai'] ?? '') ?>" style="font-size: 13px; border-color: #ddd; padding: 10px 12px;">
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <!-- Step 1: Konfirmasi Dokumen -->
                            <div class="d-grid gap-2 mb-3">
                                <input type="radio" class="btn-check" name="status_validasi" id="statusDocSetuju" autocomplete="off" value="konfirmasi_dokumen" required oninvalid="this.setCustomValidity('Silakan pilih salah satu keputusan validasi di atas.')" onchange="this.setCustomValidity('')">
                                <label class="btn btn-outline-primary text-start px-3 py-2 rounded-3 fw-bold d-flex flex-column" for="statusDocSetuju" style="font-size: 13px; border-width: 1px;">
                                    <div class="d-flex align-items-center mb-1">
                                        <i class="fas fa-check me-2"></i> Konfirmasi & Lanjut Bayar
                                    </div>
                                    <small class="ms-4 ps-1 fw-normal opacity-75" style="font-size: 10px;">Dokumen lengkap. Lanjut ke proses pembayaran.</small>
                                </label>

                                <input type="radio" class="btn-check" name="status_validasi" id="statusDocRevisi" autocomplete="off" value="revisi">
                                <label class="btn btn-outline-warning text-start px-3 py-2 rounded-3 fw-bold d-flex flex-column text-dark" for="statusDocRevisi" style="font-size: 13px; border-width: 1px;">
                                    <div class="d-flex align-items-center mb-1">
                                        <i class="fas fa-pen me-2"></i> Kembalikan untuk Revisi
                                    </div>
                                    <small class="ms-4 ps-1 fw-normal opacity-75" style="font-size: 10px;">Minta peneliti melengkapi kekurangan dokumen.</small>
                                </label>

                                <input type="radio" class="btn-check" name="status_validasi" id="statusDocTolak" autocomplete="off" value="tolak">
                                <label class="btn btn-outline-danger text-start px-3 py-2 rounded-3 fw-bold d-flex flex-column" for="statusDocTolak" style="font-size: 13px; border-width: 1px;">
                                    <div class="d-flex align-items-center mb-1">
                                        <i class="fas fa-times me-2"></i> Tolak Pengajuan
                                    </div>
                                    <small class="ms-4 ps-1 fw-normal opacity-75" style="font-size: 10px;">Tolak pengajuan izin penelitian ini sepenuhnya.</small>
                                </label>
                            </div>
                        <?php endif; ?>

                        <div id="revisiNotesArea" class="d-none mb-4 animate__animated animate__fadeIn">
                            <label class="fw-bold mb-2 text-dark" style="font-size: 11px; text-transform: uppercase; letter-spacing: 1px;">Catatan Review / Revisi <span class="text-danger">*</span></label>
                            <textarea class="form-control rounded-3 bg-light" name="catatan" rows="4" placeholder="Tuliskan catatan perbaikan untuk peneliti..." style="font-size: 12px; border-color: #ddd; padding: 12px;"></textarea>
                        </div>

                        <div id="nominalBayarArea" class="d-none mb-4 animate__animated animate__fadeIn">
                            <label class="fw-bold mb-2 text-dark" style="font-size: 11px; text-transform: uppercase; letter-spacing: 1px;">Nominal Tagihan <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted fw-bold" style="border-color: #ddd; font-size: 13px;">Rp</span>
                                <input type="text" id="inputNominalFormatted" class="form-control rounded-end-3 bg-white" placeholder="0" style="font-size: 13px; border-color: #ddd; padding: 10px 12px;">
                                <input type="hidden" name="nominal_bayar" id="inputNominalActual">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-danger w-100 py-3 rounded-pill fw-bold shadow-sm d-flex align-items-center justify-content-center" style="background: #e53935; border: none; font-size: 13px; letter-spacing: 0.5px;">
                            KONFIRMASI TINDAKAN <i class="fas fa-paper-plane ms-2"></i>
                        </button>

                        <div class="alert alert-warning border-0 p-3 mb-0 mt-4" style="background-color: #fff9c4 !important; border-radius: 8px;">
                            <div class="d-flex">
                                <i class="fas fa-exclamation-triangle mt-1 me-2" style="font-size: 14px; color: #f9a825;"></i>
                                <p class="mb-0 fw-bold" style="font-size: 11px; color: #555; line-height: 1.4;">
                                    Peringatan! Keputusan yang telah dibuat tidak dapat dibatalkan melalui sistem otomatis.
                                </p>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php if ($data['status'] != 'selesai'): ?>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-body p-4 text-center">
                <div class="mb-3">
                    <i class="fas fa-exclamation-circle text-danger" style="font-size: 50px;"></i>
                </div>
                <h5 class="fw-bold mb-2">Konfirmasi Tindakan</h5>
                <p class="text-muted mb-4" style="font-size: 13px;">Apakah Anda yakin dengan keputusan validasi ini? Tindakan yang telah diproses <strong>tidak dapat dibatalkan</strong>.</p>
                <div class="d-flex gap-2 justify-content-center">
                    <button type="button" class="btn btn-light px-4 py-2 rounded-3 fw-bold" data-bs-dismiss="modal">Batal</button>
                    <button type="button" id="btnConfirmSubmit" class="btn btn-danger px-4 py-2 rounded-3 fw-bold shadow-sm" style="background-color: #e53935;">
                        Ya, Konfirmasi <i class="fas fa-check ms-1"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const radioDocRevisi = document.getElementById('statusDocRevisi');
        const radioDocTolak = document.getElementById('statusDocTolak');
        const radioBayarTolak = document.getElementById('statusBayarTolak');

        // For other options, hide the textarea
        const radios = document.querySelectorAll('input[name="status_validasi"]');
        const revisiArea = document.getElementById('revisiNotesArea');
        const nominalArea = document.getElementById('nominalBayarArea');
        const inputNominalFormatted = document.getElementById('inputNominalFormatted');
        const inputNominalActual = document.getElementById('inputNominalActual');

        if (inputNominalFormatted) {
            inputNominalFormatted.addEventListener('input', function(e) {
                this.setCustomValidity('');
                let value = this.value.replace(/[^0-9]/g, '');
                inputNominalActual.value = value;
                if (value) {
                    this.value = parseInt(value, 10).toLocaleString('id-ID');
                } else {
                    this.value = '';
                }
            });
        }

        if (revisiArea || nominalArea) {
            radios.forEach(r => {
                r.addEventListener('change', function() {
                    if (revisiArea) {
                        if (this.id === 'statusDocRevisi' || this.id === 'statusDocTolak' || this.id === 'statusBayarTolak') {
                            revisiArea.classList.remove('d-none');
                            const textarea = revisiArea.querySelector('textarea');
                            textarea.required = true;
                            textarea.oninvalid = function() {
                                this.setCustomValidity('Catatan atau alasan wajib diisi untuk tindakan ini.');
                            };
                            textarea.oninput = function() {
                                this.setCustomValidity('');
                            };

                            if (this.id === 'statusDocTolak') {
                                revisiArea.querySelector('label').innerHTML = 'Alasan Penolakan <span class="text-danger">*</span>';
                                revisiArea.querySelector('textarea').placeholder = 'Tuliskan alasan penolakan...';
                            } else {
                                revisiArea.querySelector('label').innerHTML = 'Catatan Review / Revisi <span class="text-danger">*</span>';
                                revisiArea.querySelector('textarea').placeholder = 'Tuliskan catatan perbaikan untuk peneliti...';
                            }
                        } else {
                            revisiArea.classList.add('d-none');
                            revisiArea.querySelector('textarea').required = false;
                        }
                    }

                    if (nominalArea) {
                        if (this.id === 'statusDocSetuju') {
                            nominalArea.classList.remove('d-none');
                            if (inputNominalFormatted) {
                                inputNominalFormatted.required = true;
                                inputNominalFormatted.oninvalid = function() {
                                    this.setCustomValidity('Nominal tagihan wajib diisi.');
                                };
                            }
                        } else {
                            nominalArea.classList.add('d-none');
                            if (inputNominalFormatted) {
                                inputNominalFormatted.required = false;
                                inputNominalFormatted.setCustomValidity('');
                            }
                        }
                    }

                    const nomorSuratArea = document.getElementById('nomorSuratArea');
                    if(nomorSuratArea) {
                        if(this.id === 'statusBayarSetuju') {
                            nomorSuratArea.classList.remove('d-none');
                            const inputSurat = document.getElementById('inputNomorSurat');
                            if (inputSurat) {
                                inputSurat.required = true;
                            }
                            document.querySelectorAll('.input-waktu').forEach(el => el.required = true);
                        } else {
                            nomorSuratArea.classList.add('d-none');
                            const inputSurat = document.getElementById('inputNomorSurat');
                            if (inputSurat) {
                                inputSurat.required = false;
                            }
                            document.querySelectorAll('.input-waktu').forEach(el => el.required = false);
                        }
                    }
                });
            });
        }

        // Intercept Form Submit to show Custom Modal
        const formAdmin = document.getElementById('formTindakanAdmin');
        if (formAdmin) {
            formAdmin.addEventListener('submit', function(e) {
                e.preventDefault(); // Prevent default submission
                var confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
                confirmModal.show();
            });

            document.getElementById('btnConfirmSubmit').addEventListener('click', function() {
                formAdmin.submit(); // Submit programmatically (bypasses the submit listener)
            });
        }
    });
</script>

<?= $this->endSection() ?>