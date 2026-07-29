<?= $this->extend('Pelatihan/layout/peserta_layout') ?>

<?= $this->section('content') ?>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Periode Pengumpulan JPL untuk Periode Tahunan</h4>
            <div class="d-flex gap-2 align-items-center mt-2">
                <span class="badge bg-dark px-3 py-2">04 APRIL 2024</span>
                <span class="badge bg-secondary px-2">s.d</span>
                <span class="badge bg-dark px-3 py-2">03 APRIL 2029</span>
            </div>
        </div>
        <a href="<?= base_url('pelatihan/peserta/upload_sertifikat') ?>" class="btn btn-primary-custom rounded-pill px-4 py-2 fw-bold">
            <i class="fas fa-plus me-2"></i> CATAT KEGIATAN BARU
        </a>
    </div>

    <!-- Stats Row -->
    <div class="card border-0 shadow-sm rounded-lg p-4 mb-4">
        <div class="row align-items-center">
            <div class="col-lg-9">
                <h6 class="fw-bold text-primary-custom mb-3">Perhitungan <span class="text-decoration-underline">Nilai Capaian JPL Disetujui</span> Berdasarkan Target Minimal Setiap Ranah</h6>
                <div class="row g-3">
                    <?php foreach ($target as $name => $t): ?>
                        <div class="col-md-6">
                            <div class="bg-light p-3 rounded-4 border">
                                <div class="d-flex justify-content-end mb-2">
                                    <span class="fw-bold small text-dark"><?= number_format($t['current'], 0) ?></span>
                                </div>
                                <div class="small text-muted fw-bold mb-2"><?= ($name == 'pembelajaran' ? 'Ranah A. Pembelajaran' : 'Ranah B. Pengabdian') ?> <span class="opacity-50 small">( <?= number_format($t['target_jpl'], 0) ?> JPL )</span></div>
                                <div class="progress" style="height: 8px; border-radius: 4px;">
                                    <div class="progress-bar bg-success" style="width: <?= min(100, ($t['current'] / $t['target_jpl']) * 100) ?>%"></div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="col-lg-3 border-start">
                <div class="p-3">
                    <div class="bg-light p-4 rounded-4 border text-center shadow-sm">
                        <h6 class="fw-bold text-primary-custom mb-3 small">Total Capaian JPL</h6>
                        <div class="h3 fw-bold text-dark mb-3"><?= number_format($total_capaian, 0) ?> / <?= number_format($total_target, 0) ?></div>
                        <span class="badge bg-success w-100 py-2 rounded-pill h5 mb-0">Cukup</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Riwayat Table -->
    <div class="card border-0 shadow-sm rounded-lg overflow-hidden mb-4">
        <div class="card-header bg-white border-0 py-3">
            <h6 class="fw-bold mb-0">Riwayat Pengajuan Verifikasi JPL</h6>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-center">
                <thead class="bg-light text-muted small fw-bold">
                    <tr>
                        <th class="py-3">Tanggal Pengajuan</th>
                        <th>Periode Tahunan</th>
                        <th>Ranah A. Pembelajaran</th>
                        <th>Ranah B. Pengabdian</th>
                        <!--<th>Ranah B. Pengabdian</th> -->
                        <th>Total Capaian</th>
                        <th>Hasil Verifikasi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="6" class="py-5 text-muted fw-bold">Belum ada pengajuan verifikasi JPL.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Daftar Kegiatan -->
    <div class="card border-0 shadow-sm rounded-lg overflow-hidden">
        <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0">Daftar Kegiatan</h6>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small fw-bold text-center">
                    <tr>
                        <th class="py-3" style="width: 50px;">No</th>
                        <th class="text-start">Kegiatan</th>
                        <th>Nilai JPL</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($certs)): ?>
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted fw-bold">Belum ada data kegiatan.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($certs as $index => $c): ?>
                            <tr>
                                <td class="text-center fw-bold text-muted"><?= $index + 1 ?></td>
                                <td class="py-4">
                                    <div class="fw-bold text-primary-custom mb-2"><?= $c['kategori_kegiatan'] ?? 'Kegiatan' ?></div>
                                    <table class="table table-sm table-borderless mb-0 small">
                                        <tr>
                                            <td class="ps-0 py-1 fw-bold" style="width: 150px;">Nama Acara</td>
                                            <td class="py-1">: <?= $c['judul'] ?></td>
                                        </tr>
                                        <tr>
                                            <td class="ps-0 py-1 fw-bold">Area Kegiatan</td>
                                            <td class="py-1">: <?= $c['area'] ?? '-' ?></td>
                                        </tr>
                                        <tr>
                                            <td class="ps-0 py-1 fw-bold">JPL</td>
                                            <td class="py-1">: <?= $c['jpl'] ?? 0 ?></td>
                                        </tr>
                                        <tr>
                                            <td class="ps-0 py-1 fw-bold">Tempat</td>
                                            <td class="py-1">: <?= $c['tempat'] ?? '-' ?></td>
                                        </tr>
                                        <tr>
                                            <td class="ps-0 py-1 fw-bold">Penyelenggara</td>
                                            <td class="py-1">: <?= $c['penerbit'] ?? '-' ?></td>
                                        </tr>
                                        <tr>
                                            <td class="ps-0 py-1 fw-bold">Dokumen Bukti</td>
                                            <td class="py-1">: <button class="btn btn-light btn-sm rounded-pill px-3 py-1 fw-bold small border">Lihat Dokumen Bukti</button></td>
                                        </tr>
                                    </table>
                                </td>
                                <td class="text-center fw-bold h5 text-dark"><?= number_format($c['jpl'] ?? 0, 0) ?></td>
                                <td class="text-center">
                                    <div class="small text-muted fw-bold mb-2">Tanggal Pelaporan :<br><?= tanggal_indo($c['tgl_upload'] ?? 'now') ?></div>
                                    <?php if (($c['verifikasi'] ?? 'pending') == 'pending'): ?>
                                        <div class="bg-warning text-dark px-3 py-2 rounded-3 small fw-bold mb-2">Menunggu Verifikasi</div>
                                    <?php else: ?>
                                        <div class="bg-success text-white px-3 py-2 rounded-3 small fw-bold mb-2">Disetujui<br><?= tanggal_indo($c['tgl_verifikasi'] ?? 'now') ?></div>
                                    <?php endif; ?>
                                    <button class="btn btn-dark btn-sm rounded-3 px-3 py-2 w-100"><i class="fas fa-lock me-2"></i> Terkunci</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Alert Verification -->
    <div class="alert alert-light border shadow-sm rounded-4 p-4 mt-5">
        <p class="small text-muted mb-0" style="line-height: 1.6;">
            Sesuai dengan KMK HK.01.07/MENKES/1561/2024, pemenuhan kecukupan JPL wajib terpenuhi :<br>
            Total JPL/ 5 tahun masing-masing profesi. Minimal proporsi ranah pemenuhan JPL yang terdiri dari :<br>
            <strong>Keadaan Umum :</strong> Pembelajaran (45%), Pengabdian (5 %)<br>
            <strong>Keadaan khusus (DTPK, dll) :</strong> Pembelajaran (25%), Pengabdian (5%)<br>
            JPL Pembelajaran/ tahun (20%)
        </p>
        <hr>
        <div class="d-flex justify-content-between align-items-center">
            <p class="small fw-bold text-danger mb-0">Tombol notifikasi "Kirim Permintaan Verifikasi Nilai JPL" akan muncul apabila masa berlaku SIP Saudara kurang dari atau sama dengan 1 (satu) tahun.</p>
            <button class="btn btn-secondary rounded-pill px-4 fw-bold opacity-50" disabled>KIRIM PERMINTAAN VERIFIKASI</button>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
