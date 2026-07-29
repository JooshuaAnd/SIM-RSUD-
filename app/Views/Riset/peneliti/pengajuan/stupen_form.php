<?= $this->extend('Riset/peneliti/layout/template') ?>

<?= $this->section('content') ?>

<style>
    .form-control:focus {
        border-color: #e53935 !important;
        box-shadow: 0 0 0 4px rgba(229, 57, 53, 0.1) !important;
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1">Form Studi Pendahuluan</h4>
        <p class="text-muted small mb-0">Lengkapi data pendaftaran studi pendahuluan riset Anda.</p>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="card-header bg-white py-3 px-4 border-bottom border-light">
        <div class="d-flex align-items-center text-danger">
            <i class="fas fa-file-alt me-3" style="font-size: 18px;"></i>
            <h6 class="fw-bold mb-0" style="font-size: 14px;">Pengajuan Studi Pendahuluan</h6>
        </div>
    </div>
    <div class="card-body p-4">

        <form action="<?= base_url('riset/peneliti/pengajuan/stupen/submit') ?>" method="post" enctype="multipart/form-data">

            <!-- Researcher Info -->
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <div class="mb-2">
                        <label class="fw-bold text-dark mb-1" style="font-size: 13px;">Nama Lengkap</label>
                        <input type="text" name="nama" value="<?= $data['nama'] ?? $user['nama'] ?? '' ?>" class="form-control rounded-3 py-2 bg-light" style="font-size: 13px; border-color: #eee; cursor: not-allowed;" placeholder="Otomatis terisi dari profil" readonly required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-2">
                        <label class="fw-bold text-dark mb-1" style="font-size: 13px;">NIM atau No. Identitas</label>
                        <input type="text" name="identitas" value="<?= $data['identitas'] ?? $user['identitas'] ?? '' ?>" class="form-control rounded-3 py-2 bg-light" style="font-size: 13px; border-color: #eee; cursor: not-allowed;" placeholder="Otomatis terisi dari profil" readonly required>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-3">
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
            </div>

            <!-- Research Title -->
            <div class="mb-3">
                <label class="fw-bold text-dark mb-1" style="font-size: 13px;">Judul Studi Pendahuluan</label>
                <input type="text" name="judul" value="<?= $data['judul'] ?? '' ?>" class="form-control rounded-3 py-2" style="font-size: 13px; border-color: #eee;" placeholder="Masukkan judul studi pendahuluan anda" required oninvalid="this.setCustomValidity('Mohon isi judul studi pendahuluan Anda.')" oninput="this.setCustomValidity('')">
            </div>



            <!-- Documents -->
            <h6 class="fw-bold text-dark mb-2" style="font-size: 14px;">Upload Dokumen Pendukung</h6>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="mb-2">
                        <label class="fw-bold text-dark mb-1" style="font-size: 12px;">1. Surat Permohonan</label>
                        <input type="file" name="surat_permohonan" accept=".pdf" class="form-control py-2" style="font-size: 12px; border-radius: 8px;" <?= isset($data['id']) ? '' : 'required' ?> oninvalid="this.setCustomValidity('Mohon unggah dokumen Surat Permohonan (Format PDF).')" oninput="this.setCustomValidity('')">
                        <p class="text-muted mt-1 mb-0" style="font-size: 10px;">Wajib ditandatangani oleh Dosen Pembimbing (PDF).</p>
                        <?php if (isset($data['id'])): ?><small class="text-warning fw-bold" style="font-size: 10px;">* Biarkan kosong jika tidak ingin mengubah berkas.</small><?php endif; ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-2">
                        <label class="fw-bold text-dark mb-1" style="font-size: 12px;">2. Proposal Studi</label>
                        <input type="file" name="proposal" accept=".pdf" class="form-control py-2" style="font-size: 12px; border-radius: 8px;" <?= isset($data['id']) ? '' : 'required' ?> oninvalid="this.setCustomValidity('Mohon unggah dokumen Proposal Studi (Format PDF, maksimal 5MB).')" oninput="this.setCustomValidity('')">
                        <p class="text-muted mt-1 mb-0" style="font-size: 10px;">Format PDF, maksimal 5MB.</p>
                        <?php if (isset($data['id'])): ?><small class="text-warning fw-bold" style="font-size: 10px;">* Biarkan kosong jika tidak ingin mengubah berkas.</small><?php endif; ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-2">
                        <label class="fw-bold text-dark mb-1" style="font-size: 12px;">3. Curriculum Vitae (CV)</label>
                        <input type="file" name="cv" accept=".pdf" class="form-control py-2" style="font-size: 12px; border-radius: 8px;" <?= isset($data['id']) ? '' : 'required' ?> oninvalid="this.setCustomValidity('Mohon unggah dokumen Curriculum Vitae (CV).')" oninput="this.setCustomValidity('')">
                        <p class="text-muted mt-1 mb-0" style="font-size: 10px;">Wajib ditandatangani oleh Dosen Pembimbing (PDF).</p>
                        <?php if (isset($data['id'])): ?><small class="text-warning fw-bold" style="font-size: 10px;">* Biarkan kosong jika tidak ingin mengubah berkas.</small><?php endif; ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-2">
                        <label class="fw-bold text-dark mb-1" style="font-size: 12px;">4. Draft Wawancara / Kuesioner</label>
                        <input type="file" name="draft_wawancara" accept=".pdf" class="form-control py-2" style="font-size: 12px; border-radius: 8px;" <?= isset($data['id']) ? '' : 'required' ?> oninvalid="this.setCustomValidity('Mohon unggah dokumen Draft Wawancara / Kuesioner (Format PDF).')" oninput="this.setCustomValidity('')">
                        <p class="text-muted mt-1 mb-0" style="font-size: 10px;">Format PDF.</p>
                        <?php if (isset($data['id'])): ?><small class="text-warning fw-bold" style="font-size: 10px;">* Biarkan kosong jika tidak ingin mengubah berkas.</small><?php endif; ?>
                    </div>
                </div>
            </div>

            <hr class="mb-4" style="color: #eee;">

            <div class="d-flex justify-content-end">
                <?php if (isset($data['id'])): ?>
                    <input type="hidden" name="is_revisi" value="1">
                    <input type="hidden" name="id" value="<?= $data['id'] ?>">
                    <button type="submit" class="btn btn-warning px-4 py-2 rounded-3 shadow fw-bold d-flex align-items-center text-dark" style="border: none; font-size: 14px; letter-spacing: 0.5px;">
                        Kirim Revisi
                    </button>
                <?php else: ?>
                    <button type="submit" class="btn btn-danger px-4 py-2 rounded-3 shadow fw-bold d-flex align-items-center" style="background: #e53935; border: none; font-size: 14px; letter-spacing: 0.5px;">
                        Ajukan Studi Pendahuluan
                    </button>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
