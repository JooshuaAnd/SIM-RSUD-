<?php

/**
 * @var array|null $data
 */
?>
<?= $this->extend('Riset/peneliti/layout/template') ?>

<?= $this->section('content') ?>

<style>
    .form-control:focus,
    .form-select:focus,
    .form-check-input:focus {
        border-color: #e53935 !important;
        box-shadow: 0 0 0 4px rgba(229, 57, 53, 0.1) !important;
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1">Pengajuan Publikasi</h4>
        <p class="text-muted small mb-0">Silakan ajukan izin publikasi atau unggah berkas hasil penelitian Anda.</p>
    </div>
</div>

<?php if (isset($_GET['revisi']) && $_GET['revisi'] == '1'): ?>
    <div class="alert alert-warning border-0 rounded-4 shadow-sm mb-4 d-flex" style="background-color: #fff8e1;">
        <div class="me-3 mt-1">
            <i class="fas fa-exclamation-triangle text-warning" style="font-size: 24px;"></i>
        </div>
        <div>
            <h6 class="fw-bold text-dark mb-2" style="font-size: 14px;">Laporan Dikembalikan untuk Revisi</h6>
            <p class="text-dark mb-0" style="font-size: 13px; line-height: 1.5;">
                <strong>Catatan Admin:</strong><br>
                <?= esc($data['catatan_revisi'] ?? 'Tidak ada catatan revisi yang diberikan.') ?>
            </p>
        </div>
    </div>
<?php endif; ?>

<!-- Main Form Card -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
    <div class="card-header bg-white py-3 px-4 border-bottom border-light">
        <div class="d-flex align-items-center text-danger">
            <i class="fas fa-upload me-3" style="font-size: 18px;"></i>
            <h6 class="fw-bold mb-0 text-dark" style="font-size: 14px;">Formulir Pengajuan Publikasi</h6>
        </div>
    </div>
    <div class="card-body p-4">
        <form action="<?= base_url('riset/peneliti/publikasi/submit') ?>" method="post" enctype="multipart/form-data">

            <!-- Type Selection Section -->
            <div class="mb-4">
                <label class="fw-bold text-dark mb-3 d-block" style="font-size: 14px;">Pilih Tujuan Pengajuan</label>
                <div class="row g-3">
                    <!-- Option 1: Izin Publikasi -->
                    <?php if (!isset($data['id']) || (isset($data['tujuan_laporan']) && $data['tujuan_laporan'] == 'izin')): ?>
                    <div class="col-md-6">
                        <div class="card h-100 border-2 rounded-4 p-3 p-md-4 <?= !isset($data['id']) ? 'cursor-pointer' : '' ?> option-card active" id="cardIzin" <?= !isset($data['id']) ? 'onclick="selectOption(\'izin\')"' : '' ?>>
                            <div class="d-flex align-items-start">
                                <div class="icon-box rounded-circle bg-secondary bg-opacity-10 text-secondary p-2 p-md-3 me-2 me-md-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; flex-shrink: 0;">
                                    <i class="fas fa-file-signature fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1 text-dark" style="font-size: 14px;">Izin Publikasi</h6>
                                    <p class="text-muted mb-0" style="font-size: 11px; line-height: 1.4;">Memerlukan penerbitan Surat Izin resmi & verifikasi administrasi berbayar.</p>
                                </div>
                            </div>
                            <input class="form-check-input d-none" type="radio" name="tujuan_laporan" id="tujuanIzin" value="izin" <?= (!isset($data['tujuan_laporan']) || $data['tujuan_laporan'] == 'izin') ? 'checked' : '' ?>>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Option 2: Hanya Upload Dokumen -->
                    <?php if (!isset($data['id']) || (isset($data['tujuan_laporan']) && $data['tujuan_laporan'] == 'upload')): ?>
                    <div class="col-md-6">
                        <div class="card h-100 border-2 rounded-4 p-3 p-md-4 <?= !isset($data['id']) ? 'cursor-pointer' : '' ?> option-card" id="cardUpload" <?= !isset($data['id']) ? 'onclick="selectOption(\'upload\')"' : '' ?>>
                            <div class="d-flex align-items-start">
                                <div class="icon-box rounded-circle bg-secondary bg-opacity-10 text-secondary p-2 p-md-3 me-2 me-md-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; flex-shrink: 0;">
                                    <i class="fas fa-cloud-upload-alt fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1 text-dark" style="font-size: 14px;">Hanya Upload Hasil Publikasi</h6>
                                    <p class="text-muted mb-0" style="font-size: 11px; line-height: 1.4;">Hanya melaporkan hasil penelitian sebagai arsip mandiri (tidak berbayar).</p>
                                </div>
                            </div>
                            <input class="form-check-input d-none" type="radio" name="tujuan_laporan" id="tujuanUpload" value="upload" <?= (isset($data['tujuan_laporan']) && $data['tujuan_laporan'] == 'upload') ? 'checked' : '' ?>>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- General Research Title Section -->
            <div class="row g-3 mb-4">
                <div class="col-md-12">
                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3" style="font-size: 14px;"><i class="fas fa-book me-2 text-danger"></i> Data Peneliti & Penelitian</h6>
                </div>

                <div class="col-md-6">
                    <div class="mb-2">
                        <label class="fw-bold text-dark mb-1" style="font-size: 13px;">Nama Lengkap</label>
                        <input type="text" name="nama" value="<?= $data['nama'] ?? $user['nama'] ?? '' ?>" class="form-control rounded-3 py-2 bg-light" style="font-size: 13px; border-color: #eee; cursor: not-allowed;" placeholder="Otomatis terisi dari profil" readonly required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-2">
                        <label class="fw-bold text-dark mb-1" style="font-size: 13px;">NIM / NIDN</label>
                        <input type="text" name="identitas" value="<?= $data['identitas'] ?? $user['identitas'] ?? '' ?>" class="form-control rounded-3 py-2 bg-light" style="font-size: 13px; border-color: #eee; cursor: not-allowed;" placeholder="Otomatis terisi dari profil" readonly required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-2">
                        <label class="fw-bold text-dark mb-1" style="font-size: 13px;">Program Studi</label>
                        <input type="text" name="prodi" value="<?= $data['prodi'] ?? $user['prodi'] ?? '' ?>" class="form-control rounded-3 py-2 bg-light" style="font-size: 13px; border-color: #eee; cursor: not-allowed;" placeholder="Otomatis terisi dari profil" readonly required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-2">
                        <label class="fw-bold text-dark mb-1" style="font-size: 13px;">Institusi / Universitas</label>
                        <input type="text" name="institusi" value="<?= $data['institusi'] ?? $user['institusi'] ?? '' ?>" class="form-control rounded-3 py-2 bg-light" style="font-size: 13px; border-color: #eee; cursor: not-allowed;" placeholder="Otomatis terisi dari profil" readonly required>
                    </div>
                </div>

                <div class="col-md-12 mt-3" id="containerRegistrasiAsal">
                    <div class="mb-2">
                        <label class="fw-bold text-dark mb-1" style="font-size: 13px;">Registrasi Riset Asal</label>
                        <select name="riset_id" id="risetSelect" class="form-select rounded-3 py-2" style="font-size: 13px; border-color: #eee;" onchange="toggleJudulInput()">
                            <option value="">-- Pilih Registrasi Riset Anda --</option>
                            <?php if (!empty($riwayat_riset)): ?>
                                <?php foreach ($riwayat_riset as $riset): ?>
                                    <option value="<?= esc($riset['id']) ?>" 
                                            data-mulai="<?= esc($riset['waktu_mulai'] ?? '') ?>" 
                                            data-selesai="<?= esc($riset['waktu_selesai'] ?? '') ?>"
                                            <?= (isset($data['pengajuan_riset_id']) && $data['pengajuan_riset_id'] == $riset['id']) ? 'selected' : '' ?>>
                                        <?= esc($riset['judul']) ?> (<?= esc(format_pengajuan_id($riset['id'], 'penelitian')) ?>)
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            <option value="other" <?= (isset($data) && empty($data['pengajuan_riset_id'])) ? 'selected' : '' ?>>Lainnya (Ketik Manual / Unggah Baru)</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-12 mt-3 d-none" id="judulManualContainer">
                    <div class="mb-2">
                        <label class="fw-bold text-dark mb-1" id="labelJudulPenelitian" style="font-size: 13px;">Judul Penelitian / Riset <span class="text-danger req-star-judul">*</span></label>
                        <input type="text" name="judul_penelitian" id="judulPenelitianInput" value="<?= esc($data['judul'] ?? '') ?>" class="form-control rounded-3 py-2" style="font-size: 13px; border-color: #eee;" placeholder="Masukkan judul penelitian Anda secara lengkap">
                    </div>
                </div>

                <div class="col-md-6 mt-3" id="containerWaktuMulai">
                    <div class="mb-2">
                        <label class="fw-bold text-dark mb-1" style="font-size: 13px;">Waktu Mulai Penelitian</label>
                        <input type="date" name="waktu_mulai" id="waktuMulaiInput" value="<?= $data['waktu_mulai'] ?? '' ?>" class="form-control rounded-3 py-2" style="font-size: 13px; border-color: #eee;" required>
                    </div>
                </div>
                <div class="col-md-6 mt-3" id="containerWaktuSelesai">
                    <div class="mb-2">
                        <label class="fw-bold text-dark mb-1" style="font-size: 13px;">Waktu Selesai Penelitian</label>
                        <input type="date" name="waktu_selesai" id="waktuSelesaiInput" value="<?= $data['waktu_selesai'] ?? '' ?>" class="form-control rounded-3 py-2" style="font-size: 13px; border-color: #eee;" required>
                    </div>
                </div>
            </div>

            <!-- Section I: Jurnal / Publication Details -->
            <div id="sectionJurnal" style="transition: all 0.3s ease;">
                <div class="row g-3 mb-3">
                    <div class="col-md-12">
                        <h6 class="fw-bold text-dark border-bottom pb-2 mb-3" style="font-size: 14px;"><i class="fas fa-newspaper me-2 text-danger"></i> Detail Jurnal Target</h6>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-2">
                            <label class="fw-bold text-dark mb-1" style="font-size: 13px;">Jenis Jurnal <span class="text-danger req-star">*</span></label>
                            <input type="text" name="jenis_jurnal" id="jenisJurnalInput" value="<?= $data['jenis_jurnal'] ?? '' ?>" class="form-control rounded-3 py-2" style="font-size: 13px; border-color: #eee;" placeholder="Misal: Nasional / Internasional / Terakreditasi Sinta / Non-akreditasi">
                        </div>
                    </div>

                    <div class="col-md-6" id="containerKategori">
                        <div class="mb-2">
                            <label class="fw-bold text-dark mb-1" style="font-size: 13px;">Kategori / Bidang Jurnal <span class="text-danger req-star">*</span></label>
                            <input type="text" name="kategori_jurnal" id="kategoriInput" value="<?= $data['kategori_jurnal'] ?? '' ?>" class="form-control rounded-3 py-2" style="font-size: 13px; border-color: #eee;" placeholder="Misal: Ilmiah, Populer, Profesional, dsb.">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-2">
                            <label class="fw-bold text-dark mb-1" style="font-size: 13px;">Nama Publikasi / Jurnal <span class="text-danger req-star">*</span></label>
                            <input type="text" name="nama_publikasi" id="namaPublikasiInput" value="<?= $data['nama_publikasi'] ?? '' ?>" class="form-control rounded-3 py-2" style="font-size: 13px; border-color: #eee;" placeholder="Misal: Jurnal Teknologi Informasi dan Komunikasi">
                        </div>
                    </div>

                    <div class="col-md-6" id="containerIssn">
                        <div class="mb-2">
                            <label class="fw-bold text-dark mb-1" style="font-size: 13px;">ISSN / E-ISSN <span class="text-danger req-star">*</span></label>
                            <input type="text" name="issn" id="issnInput" value="<?= $data['issn'] ?? '' ?>" class="form-control rounded-3 py-2" style="font-size: 13px; border-color: #eee;" placeholder="Misal : 1234-5678">
                        </div>
                    </div>

                    <div class="col-md-6" id="containerScope">
                        <div class="mb-2">
                            <label class="fw-bold text-dark mb-1" style="font-size: 13px;">Scope/Bidang <span class="text-danger req-star">*</span></label>
                            <input type="text" name="scope" id="scopeInput" value="<?= $data['scope'] ?? '' ?>" class="form-control rounded-3 py-2" style="font-size: 13px; border-color: #eee;" placeholder="Misal: Teknik Informatika, Hukum, Ekonomi, Pendidikan, dsb.">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-2">
                            <label class="fw-bold text-dark mb-1" style="font-size: 13px;">Alamat Web (URL) <span class="text-danger req-star">*</span></label>
                            <input type="url" name="alamat_web" id="alamatWebInput" value="<?= $data['alamat_web'] ?? '' ?>" class="form-control rounded-3 py-2" style="font-size: 13px; border-color: #eee;" placeholder="Misal: https://jurnal.namauniversitas.ac.id">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section: Abstrak -->
            <div class="row g-3 mb-3">
                <div class="col-md-12">
                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3" style="font-size: 14px;"><i class="fas fa-align-left me-2 text-danger"></i> Abstrak Penelitian</h6>
                </div>
                <div class="col-md-12">
                    <div class="mb-2">
                        <label class="fw-bold text-dark mb-1" style="font-size: 13px;">Abstrak <span class="text-danger req-star">*</span></label>
                        <textarea name="abstrak" id="abstrakInput" rows="5" class="form-control rounded-3 py-2" style="font-size: 13px; border-color: #eee; line-height: 1.6;" placeholder="Tuliskan abstrak penelitian Anda secara ringkas (maksimal 250 kata). Jelaskan latar belakang, metode, hasil, dan kesimpulan penelitian."><?= esc($data['abstrak'] ?? '') ?></textarea>
                        <small class="text-muted" style="font-size: 10px;">Minimal 100 kata, maksimal 250 kata. (Opsional untuk Upload Arsip)</small>
                    </div>
                </div>
            </div>

            <!-- Section II: Documents / Supporting Files -->
            <div>
                <div class="row g-3 mb-4">
                    <div class="col-md-12">
                        <h6 class="fw-bold text-dark border-bottom pb-2 mb-3" style="font-size: 14px;"><i class="fas fa-file-alt me-2 text-danger"></i> Dokumen Lampiran Pendukung</h6>
                    </div>

                    <!-- File 1: Surat Permohonan (Only for Izin Publikasi) -->
                    <div class="col-md-6" id="containerPermohonan">
                        <div class="mb-2">
                            <label class="fw-bold text-dark mb-1" style="font-size: 13px;">Surat Permohonan Izin Publikasi <?= !isset($data['id']) ? '<span class="text-danger">*</span>' : '' ?></label>
                            <input type="file" name="permohonan_izin" id="permohonanIzinInput" class="form-control py-2" style="font-size: 12px; border-radius: 8px;">
                            <small class="text-muted d-block mt-1" style="font-size: 10px;">Format PDF max 2MB. <?= isset($data['id']) ? 'Biarkan kosong jika tidak ingin mengubah dokumen lama.' : '' ?></small>
                        </div>
                    </div>

                    <!-- File 2: Salinan Surat Izin Penelitian (Only for Izin Publikasi) -->
                    <div class="col-md-6" id="containerIzinPenelitian">
                        <div class="mb-2">
                            <label class="fw-bold text-dark mb-1" style="font-size: 13px;">Salinan Surat Izin Penelitian <?= !isset($data['id']) ? '<span class="text-danger">*</span>' : '' ?></label>
                            <input type="file" name="salinan_izin_penelitian" id="izinPenelitianInput" class="form-control py-2" style="font-size: 12px; border-radius: 8px;">
                            <small class="text-muted d-block mt-1" style="font-size: 10px;">Format PDF max 2MB. <?= isset($data['id']) ? 'Biarkan kosong jika tidak ingin mengubah dokumen lama.' : '' ?></small>
                        </div>
                    </div>

                    <!-- File 3: Draft Jurnal / Artikel (Required for both) -->
                    <div class="col-md-6">
                        <div class="mb-2">
                            <label class="fw-bold text-dark mb-1" id="labelDraftArtikel" style="font-size: 13px;">Draft Jurnal / Artikel <?= !isset($data['id']) ? '<span class="text-danger req-star-artikel">*</span>' : '' ?></label>
                            <input type="file" name="draft_artikel" id="draftArtikelInput" class="form-control py-2" style="font-size: 12px; border-radius: 8px;">
                            <small class="text-muted d-block mt-1" style="font-size: 10px;">Format PDF / DOCX max 5MB. <?= isset($data['id']) ? 'Biarkan kosong jika tidak ingin mengubah dokumen lama.' : '' ?></small>
                        </div>
                    </div>

                    <!-- File 4: Surat Pernyataan Anonimitas -->
                    <div class="col-md-6" id="containerAnonimitas">
                        <div class="mb-2">
                            <label class="fw-bold text-dark mb-1" style="font-size: 13px;">Surat Pernyataan Anonimitas Data Pasien <?= !isset($data['id']) ? '<span class="text-danger">*</span>' : '' ?></label>
                            <input type="file" name="pernyataan_anonimitas" id="anonimitasInput" class="form-control py-2" style="font-size: 12px; border-radius: 8px;">
                            <small class="text-muted d-block mt-1" style="font-size: 10px;">Format PDF max 2MB. <?= isset($data['id']) ? 'Biarkan kosong jika tidak ingin mengubah dokumen lama.' : '' ?></small>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end border-top pt-4 mt-4">
                    <?php if (isset($data['id'])): ?>
                        <input type="hidden" name="is_revisi" value="1">
                        <input type="hidden" name="id" value="<?= $data['id'] ?>">
                        <button type="submit" class="btn btn-warning px-5 py-3 rounded-pill shadow fw-bold d-flex align-items-center text-dark" style="border: none; font-size: 14px; letter-spacing: 0.5px;">
                            KIRIM REVISI <i class="fas fa-check-circle ms-3"></i>
                        </button>
                    <?php else: ?>
                        <button type="submit" class="btn btn-danger px-5 py-3 rounded-pill shadow fw-bold d-flex align-items-center" style="background: #e53935; border: none; font-size: 14px; letter-spacing: 0.5px;">
                            KIRIM LAPORAN / PENGAJUAN <i class="fas fa-paper-plane ms-3"></i>
                        </button>
                    <?php endif; ?>
                </div>
            </div>

        </form>
    </div>
</div>

<style>
    .cursor-pointer {
        cursor: pointer;
    }

    .option-card {
        border-color: #eee;
        background-color: #fff;
        transition: all 0.25s ease-in-out;
    }

    .option-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
        border-color: #e53935;
    }

    .option-card.active {
        border-color: #e53935 !important;
        background-color: #fff8f8;
    }

    .option-card.active .icon-box {
        background-color: #e53935 !important;
        color: #fff !important;
    }
</style>

<script>
    function selectOption(type) {
        // Uncheck and check correct radios
        if (document.getElementById('tujuanIzin')) document.getElementById('tujuanIzin').checked = (type === 'izin');
        if (document.getElementById('tujuanUpload')) document.getElementById('tujuanUpload').checked = (type === 'upload');

        // Update styles
        const cardIzin = document.getElementById('cardIzin');
        const cardUpload = document.getElementById('cardUpload');

        if (type === 'izin') {
            if (cardIzin) cardIzin.classList.add('active');
            if (cardUpload) cardUpload.classList.remove('active');

            // Show and set required for izin-specific documents
            document.getElementById('containerPermohonan').style.display = 'block';
            document.getElementById('containerIzinPenelitian').style.display = 'block';
            document.getElementById('containerAnonimitas').style.display = 'block';
            <?php if (!isset($data['id'])): ?>
            document.getElementById('permohonanIzinInput').required = true;
            document.getElementById('izinPenelitianInput').required = true;
            document.getElementById('anonimitasInput').required = true;
            <?php endif; ?>

            // Show Registrasi Asal dan Waktu
            document.getElementById('containerRegistrasiAsal').style.display = 'block';
            document.getElementById('containerWaktuMulai').style.display = 'block';
            document.getElementById('containerWaktuSelesai').style.display = 'block';
            document.getElementById('waktuMulaiInput').required = true;
            document.getElementById('waktuSelesaiInput').required = true;

            // Tampilkan metadata jurnal lengkap
            document.getElementById('containerKategori').style.display = 'block';
            document.getElementById('containerIssn').style.display = 'block';
            document.getElementById('containerScope').style.display = 'block';
            document.getElementById('kategoriInput').required = true;
            document.getElementById('issnInput').required = true;
            document.getElementById('scopeInput').required = true;

            // Tampilkan kembali semua bintang wajib
            document.querySelectorAll('.req-star').forEach(el => el.style.display = 'inline');
            const starJudul = document.querySelector('.req-star-judul');
            if(starJudul) starJudul.style.display = 'inline';

            // Label update
            document.getElementById('labelDraftArtikel').innerHTML = 'Draft Jurnal / Artikel <?= !isset($data['id']) ? '<span class="text-danger req-star-artikel">*</span>' : '' ?>';
            
            <?php if (!isset($data['id'])): ?>
            document.getElementById('draftArtikelInput').required = true;
            <?php endif; ?>

            document.getElementById('abstrakInput').required = true;

            // Sesuaikan kembali judul manual
            toggleJudulInput();
        } else {
            if (cardUpload) cardUpload.classList.add('active');
            if (cardIzin) cardIzin.classList.remove('active');

            // Hide and remove required for izin-specific documents
            document.getElementById('containerPermohonan').style.display = 'none';
            document.getElementById('containerIzinPenelitian').style.display = 'none';
            document.getElementById('containerAnonimitas').style.display = 'none';
            document.getElementById('permohonanIzinInput').required = false;
            document.getElementById('izinPenelitianInput').required = false;
            document.getElementById('anonimitasInput').required = false;

            // Hide Registrasi Asal but keep Waktu
            document.getElementById('containerRegistrasiAsal').style.display = 'none';
            document.getElementById('containerWaktuMulai').style.display = 'block';
            document.getElementById('containerWaktuSelesai').style.display = 'block';
            document.getElementById('waktuMulaiInput').required = false;
            document.getElementById('waktuSelesaiInput').required = false;

            // Kategori, ISSN, dan Scope tetap ditampilkan untuk keperluan arsip (Upload)
            document.getElementById('containerKategori').style.display = 'block';
            document.getElementById('containerIssn').style.display = 'block';
            document.getElementById('containerScope').style.display = 'block';
            // Hanya buat opsional jika sekadar upload (tidak wajib)
            document.getElementById('kategoriInput').required = false;
            document.getElementById('issnInput').required = false;
            document.getElementById('scopeInput').required = false;

            // Tampilkan langsung Judul Manual
            document.getElementById('judulManualContainer').classList.remove('d-none');
            document.getElementById('judulPenelitianInput').required = true;

            // Sembunyikan bintang wajib untuk yg lain, TAPI judul tetap tampil
            document.querySelectorAll('.req-star').forEach(el => el.style.display = 'none');
            const starJudul2 = document.querySelector('.req-star-judul');
            if(starJudul2) starJudul2.style.display = 'inline';

            // Label update
            document.getElementById('labelDraftArtikel').innerHTML = 'Laporan Akhir Penelitian / Artikel <?= !isset($data['id']) ? '<span class="text-danger req-star-artikel">*</span>' : '' ?>';
            
            <?php if (!isset($data['id'])): ?>
            document.getElementById('draftArtikelInput').required = true;
            <?php endif; ?>

            document.getElementById('abstrakInput').required = false;
        }

        // Pastikan metadata jurnal selalu required untuk kebutuhan portal publik
        document.getElementById('sectionJurnal').style.display = 'block';
        if (type === 'izin') {
            document.getElementById('jenisJurnalInput').required = true;
            document.getElementById('namaPublikasiInput').required = true;
            document.getElementById('alamatWebInput').required = true;
        } else {
            document.getElementById('jenisJurnalInput').required = false;
            document.getElementById('namaPublikasiInput').required = false;
            document.getElementById('alamatWebInput').required = false;
        }
    }

    function toggleJudulInput() {
        // Hanya berlaku jika mode Izin
        if (document.getElementById('tujuanIzin').checked) {
            const select = document.getElementById('risetSelect');
            const container = document.getElementById('judulManualContainer');
            const input = document.getElementById('judulPenelitianInput');
            const waktuMulai = document.getElementById('waktuMulaiInput');
            const waktuSelesai = document.getElementById('waktuSelesaiInput');

            if (select.value === 'other' || select.value === '') {
                container.classList.remove('d-none');
                input.required = true;
                if (!waktuMulai.getAttribute('data-has-value')) {
                    waktuMulai.value = '';
                    waktuSelesai.value = '';
                }
            } else {
                container.classList.add('d-none');
                input.required = false;
                
                // Auto fill dates from selected option
                const selectedOption = select.options[select.selectedIndex];
                if (selectedOption && selectedOption.getAttribute('data-mulai')) {
                    waktuMulai.value = selectedOption.getAttribute('data-mulai');
                    waktuSelesai.value = selectedOption.getAttribute('data-selesai');
                }
            }
        }
    }

    // Run on load to set initial state
    document.addEventListener("DOMContentLoaded", function() {
        // Mark if inputs already have value from server (e.g. during revisi)
        const waktuMulai = document.getElementById('waktuMulaiInput');
        if (waktuMulai && waktuMulai.value) {
            waktuMulai.setAttribute('data-has-value', 'true');
        }
        const uploadEl = document.getElementById('tujuanUpload');
        const isUploadChecked = uploadEl ? uploadEl.checked : false;
        if (isUploadChecked) {
            selectOption('upload');
        } else {
            selectOption('izin');
        }
        toggleJudulInput();
    });
</script>

<?= $this->endSection() ?>
