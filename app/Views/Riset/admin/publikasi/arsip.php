<?= $this->extend('Riset/admin/layout/template') ?>

<?= $this->section('content') ?>

<style>
    .form-control:focus,
    .form-select:focus {
        border-color: #e53935 !important;
        box-shadow: 0 0 0 4px rgba(229, 57, 53, 0.1) !important;
    }
</style>

<div class="mb-4">
    <a href="<?= base_url('riset/admin/publikasi') ?>" class="text-decoration-none text-muted fw-bold d-inline-flex align-items-center" style="font-size: 13px;">
        <i class="fas fa-arrow-left me-2"></i> Kembali ke Review Publikasi
    </a>
</div>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1">Upload Arsip Publikasi</h4>
        <p class="text-muted small mb-0">Masukkan publikasi lama yang sudah ada sebelumnya. Seluruh data diketik manual oleh admin.</p>
    </div>
</div>

<div class="alert alert-info border-0 rounded-4 shadow-sm mb-4 d-flex" style="background-color: #e8f4fd;">
    <i class="fas fa-info-circle text-info me-3 mt-1" style="font-size: 20px;"></i>
    <div class="small text-dark">
        <strong>Semua kolom wajib diisi</strong>, termasuk dokumen, karena data ini ditampilkan di halaman depan repositori.
        Publikasi yang diunggah di sini <strong>langsung berstatus Selesai</strong> (tanpa draft, review, atau pembayaran) dan langsung tampil di
        tab <em>Katalog Arsip Publik</em> serta katalog repositori.
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
    <div class="card-header bg-white py-3 px-4 border-bottom border-light">
        <div class="d-flex align-items-center text-danger">
            <i class="fas fa-upload me-3" style="font-size: 18px;"></i>
            <h6 class="fw-bold mb-0 text-dark" style="font-size: 14px;">Formulir Arsip Publikasi</h6>
        </div>
    </div>
    <div class="card-body p-4">
        <form action="<?= base_url('riset/admin/publikasi/arsip/submit') ?>" method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <!-- Data Peneliti & Penelitian -->
            <div class="row g-3 mb-4">
                <div class="col-md-12">
                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3" style="font-size: 14px;"><i class="fas fa-book me-2 text-danger"></i> Data Peneliti & Penelitian</h6>
                </div>

                <div class="col-md-6">
                    <label class="fw-bold text-dark mb-1" style="font-size: 13px;">Nama Penulis <span class="text-danger">*</span></label>
                    <input type="text" name="nama" value="<?= esc(old('nama')) ?>" class="form-control rounded-3 py-2" style="font-size: 13px; border-color: #eee;" placeholder="Nama lengkap penulis / peneliti" required>
                </div>
                <div class="col-md-6">
                    <label class="fw-bold text-dark mb-1" style="font-size: 13px;">NIM / NIDN <span class="text-danger">*</span></label>
                    <input type="text" name="identitas" value="<?= esc(old('identitas')) ?>" class="form-control rounded-3 py-2" style="font-size: 13px; border-color: #eee;" placeholder="NIM atau NIDN penulis" required>
                </div>
                <div class="col-md-6">
                    <label class="fw-bold text-dark mb-1" style="font-size: 13px;">Program Studi <span class="text-danger">*</span></label>
                    <input type="text" name="prodi" value="<?= esc(old('prodi')) ?>" class="form-control rounded-3 py-2" style="font-size: 13px; border-color: #eee;" placeholder="Misal: Keperawatan" required>
                </div>
                <div class="col-md-6">
                    <label class="fw-bold text-dark mb-1" style="font-size: 13px;">Institusi / Universitas <span class="text-danger">*</span></label>
                    <input type="text" name="institusi" value="<?= esc(old('institusi')) ?>" class="form-control rounded-3 py-2" style="font-size: 13px; border-color: #eee;" placeholder="Nama institusi asal penulis" required>
                </div>
                <div class="col-md-12">
                    <label class="fw-bold text-dark mb-1" style="font-size: 13px;">Judul Penelitian / Riset <span class="text-danger">*</span></label>
                    <input type="text" name="judul" value="<?= esc(old('judul')) ?>" class="form-control rounded-3 py-2" style="font-size: 13px; border-color: #eee;" placeholder="Masukkan judul penelitian secara lengkap" required>
                </div>
                <div class="col-md-6">
                    <label class="fw-bold text-dark mb-1" style="font-size: 13px;">Waktu Mulai Penelitian <span class="text-danger">*</span></label>
                    <input type="date" name="waktu_mulai" value="<?= esc(old('waktu_mulai')) ?>" class="form-control rounded-3 py-2" style="font-size: 13px; border-color: #eee;" required>
                </div>
                <div class="col-md-6">
                    <label class="fw-bold text-dark mb-1" style="font-size: 13px;">Waktu Selesai Penelitian <span class="text-danger">*</span></label>
                    <input type="date" name="waktu_selesai" value="<?= esc(old('waktu_selesai')) ?>" class="form-control rounded-3 py-2" style="font-size: 13px; border-color: #eee;" required>
                </div>
            </div>

            <!-- Detail Jurnal -->
            <div class="row g-3 mb-4">
                <div class="col-md-12">
                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3" style="font-size: 14px;"><i class="fas fa-newspaper me-2 text-danger"></i> Detail Jurnal / Publikasi</h6>
                </div>

                <div class="col-md-6">
                    <label class="fw-bold text-dark mb-1" style="font-size: 13px;">Jenis Jurnal <span class="text-danger">*</span></label>
                    <input type="text" name="jenis_jurnal" value="<?= esc(old('jenis_jurnal')) ?>" class="form-control rounded-3 py-2" style="font-size: 13px; border-color: #eee;" placeholder="Misal: Nasional / Internasional / Terakreditasi Sinta" required>
                </div>
                <div class="col-md-6">
                    <label class="fw-bold text-dark mb-1" style="font-size: 13px;">Kategori / Bidang Jurnal <span class="text-danger">*</span></label>
                    <input type="text" name="kategori_jurnal" value="<?= esc(old('kategori_jurnal')) ?>" class="form-control rounded-3 py-2" style="font-size: 13px; border-color: #eee;" placeholder="Misal: Ilmiah, Populer, Profesional" required>
                </div>
                <div class="col-md-6">
                    <label class="fw-bold text-dark mb-1" style="font-size: 13px;">Nama Publikasi / Jurnal <span class="text-danger">*</span></label>
                    <input type="text" name="nama_publikasi" value="<?= esc(old('nama_publikasi')) ?>" class="form-control rounded-3 py-2" style="font-size: 13px; border-color: #eee;" placeholder="Misal: Jurnal Teknologi Informasi dan Komunikasi" required>
                </div>
                <div class="col-md-6">
                    <label class="fw-bold text-dark mb-1" style="font-size: 13px;">ISSN / E-ISSN <span class="text-danger">*</span></label>
                    <input type="text" name="issn" value="<?= esc(old('issn')) ?>" class="form-control rounded-3 py-2" style="font-size: 13px; border-color: #eee;" placeholder="Misal: 1234-5678" required>
                </div>
                <div class="col-md-6">
                    <label class="fw-bold text-dark mb-1" style="font-size: 13px;">Scope / Bidang <span class="text-danger">*</span></label>
                    <input type="text" name="scope" value="<?= esc(old('scope')) ?>" class="form-control rounded-3 py-2" style="font-size: 13px; border-color: #eee;" placeholder="Misal: Keperawatan, Kesehatan Masyarakat" required>
                </div>
                <div class="col-md-6">
                    <label class="fw-bold text-dark mb-1" style="font-size: 13px;">Alamat Web (URL) <span class="text-danger">*</span></label>
                    <input type="url" name="alamat_web" value="<?= esc(old('alamat_web')) ?>" class="form-control rounded-3 py-2" style="font-size: 13px; border-color: #eee;" placeholder="https://jurnal.namauniversitas.ac.id" required>
                </div>
            </div>

            <!-- Abstrak -->
            <div class="row g-3 mb-4">
                <div class="col-md-12">
                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3" style="font-size: 14px;"><i class="fas fa-align-left me-2 text-danger"></i> Abstrak Penelitian</h6>
                </div>
                <div class="col-md-12">
                    <label class="fw-bold text-dark mb-1" style="font-size: 13px;">Abstrak <span class="text-danger">*</span></label>
                    <textarea name="abstrak" rows="5" class="form-control rounded-3 py-2" style="font-size: 13px; border-color: #eee; line-height: 1.6;" placeholder="Tuliskan abstrak penelitian: latar belakang, metode, hasil, dan kesimpulan." required><?= esc(old('abstrak')) ?></textarea>
                </div>
            </div>

            <!-- Dokumen -->
            <div class="row g-3 mb-4">
                <div class="col-md-12">
                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3" style="font-size: 14px;"><i class="fas fa-file-alt me-2 text-danger"></i> Dokumen Publikasi</h6>
                </div>
                <div class="col-md-6">
                    <label class="fw-bold text-dark mb-1" style="font-size: 13px;">Laporan Akhir Penelitian / Artikel <span class="text-danger">*</span></label>
                    <input type="file" name="draft_artikel" class="form-control py-2" accept=".pdf,.doc,.docx" style="font-size: 12px; border-radius: 8px;" required>
                    <small class="text-muted d-block mt-1" style="font-size: 10px;">Format PDF / DOC / DOCX, maksimal 10MB.</small>
                </div>
            </div>

            <div class="d-flex justify-content-end border-top pt-4 mt-4">
                <button type="submit" class="btn btn-danger px-5 py-3 rounded-pill shadow fw-bold d-flex align-items-center" style="background: #e53935; border: none; font-size: 14px; letter-spacing: 0.5px;">
                    SIMPAN ARSIP PUBLIKASI <i class="fas fa-paper-plane ms-3"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
