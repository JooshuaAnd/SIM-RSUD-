<?= $this->extend('Pelatihan/layout/admin_layout') ?>

<?= $this->section('content') ?>

<div class="mb-4 d-flex justify-content-between align-items-center">
    <div class="bg-white p-4 rounded-lg shadow-sm border-start border-danger border-5 flex-grow-1">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h3 class="fw-bold mb-1 text-uppercase"><i class="fas fa-certificate me-2 text-danger"></i> Pengaturan Template Sertifikat (JPL)</h3>
                <div class="text-muted small">Kustomisasi desain sertifikat RSUD Kota Yogyakarta & manajemen tanda tangan PNG</div>
            </div>
            <a href="<?= base_url('pelatihan/admin/sertifikat') ?>" class="btn btn-outline-dark rounded-pill px-4 fw-bold shadow-sm small">
                <i class="fas fa-arrow-left me-1"></i> KEMBALI
            </a>
        </div>
    </div>
</div>

<form action="<?= base_url('pelatihan/admin/sertifikat/template/save') ?>" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-lg bg-white mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold text-dark mb-3"><i class="fas fa-layer-group text-danger me-2"></i> Ruang Lingkup Template</h5>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">Pilih Program Pelatihan</label>
                        <select name="pelatihan_id" class="form-select rounded-pill border-light bg-light px-3 py-2 fw-bold text-dark shadow-sm">
                            <option value="default">-- TEMPLATE DEFAULT GLOBAL (SEMUA PELATIHAN) --</option>
                            <option value="1">Pelatihan UI/UX Dasar</option>
                            <option value="2">Workshop Frontend Advanced</option>
                            <option value="3">Pelatihan Manajemen RS</option>
                            <option value="4">Seminar Kedokteran Modern</option>
                        </select>
                        <div class="text-muted mt-2" style="font-size: 0.65rem;">Pilih nama pelatihan jika ingin membuat desain/tanda tangan khusus hanya untuk pelatihan tersebut.</div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-lg bg-white h-100">
                <div class="card-header bg-white p-4 border-bottom-0 pb-0">
                    <h5 class="fw-bold text-dark mb-0"><i class="fas fa-sliders-h me-2 text-danger"></i> Konfigurasi Teks & Desain</h5>
                    <p class="text-muted small mb-0 mt-1">Sesuaikan elemen utama sertifikat resmi diklat</p>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">Judul Utama / Heading</label>
                        <input type="text" name="heading" class="form-control rounded-pill border-light bg-light px-3 py-2 fw-bold text-dark shadow-sm" value="<?= esc($template['heading'] ?? 'SERTIFIKAT KELULUSAN') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">Penyelenggara / Sub-Heading</label>
                        <input type="text" name="subheading" class="form-control rounded-pill border-light bg-light px-3 py-2 fw-bold text-dark shadow-sm" value="<?= esc($template['subheading'] ?? 'RSUD Kota Yogyakarta') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">Format Nomor Sertifikat</label>
                        <input type="text" name="nomor_format" class="form-control rounded-pill border-light bg-light px-3 py-2 fw-bold text-dark shadow-sm" value="<?= esc($template['nomor_format'] ?? '800/DIKLAT/{id}/{tahun}') ?>" placeholder="800/DIKLAT/{id}/{tahun}" required>
                        <div class="text-muted mt-1" style="font-size: 0.65rem;">Gunakan tag <code class="text-danger fw-bold">{id}</code> atau <code class="text-danger fw-bold">{tahun}</code></div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Warna Background</label>
                            <select name="background" class="form-select rounded-pill border-light bg-light px-3 py-2 fw-bold text-dark shadow-sm">
                                <option value="cream" <?= ($template['background'] ?? '') == 'cream' ? 'selected' : '' ?>>Krem Klasik (Kemenkes)</option>
                                <option value="white" <?= ($template['background'] ?? '') == 'white' ? 'selected' : '' ?>>Putih Bersih</option>
                                <option value="classic" <?= ($template['background'] ?? '') == 'classic' ? 'selected' : '' ?>>Klasik Bermotif</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Logo Header</label>
                            <select name="logo_pilihan" class="form-select rounded-pill border-light bg-light px-3 py-2 fw-bold text-dark shadow-sm">
                                <option value="kemenkes_rsud" <?= ($template['logo_pilihan'] ?? '') == 'kemenkes_rsud' ? 'selected' : '' ?>>Kemenkes + RSUD</option>
                                <option value="rsud" <?= ($template['logo_pilihan'] ?? '') == 'rsud' ? 'selected' : '' ?>>RSUD Yogyakarta Saja</option>
                                <option value="kemenkes" <?= ($template['logo_pilihan'] ?? '') == 'kemenkes' ? 'selected' : '' ?>>Kemenkes Saja</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-4 p-3 bg-light rounded-4 border border-warning border-opacity-25">
                        <div class="small fw-bold text-dark mb-1"><i class="fas fa-info-circle text-warning me-1"></i> Data Otomatis JPL</div>
                        <div class="text-muted small" style="font-size: 0.65rem; line-height: 1.4;">
                            Sertifikat otomatis menarik data: Nama Peserta, Kategori Kegiatan, Nama Pelatihan, dan <strong>Jumlah Jam Pelajaran (JPL)</strong> dari master data. Beban SKP tidak lagi dihitung.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-lg bg-white h-100">
                <div class="card-header bg-white p-4 border-bottom-0 pb-0 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold text-dark mb-0"><i class="fas fa-file-signature me-2 text-danger"></i> Tanda Tangan Digital</h5>
                        <p class="text-muted small mb-0 mt-1">Upload berkas .PNG transparan (Mendukung multi tanda tangan)</p>
                    </div>
                    <button type="button" class="btn btn-danger btn-sm rounded-pill px-3 fw-bold shadow-sm" onclick="addNewSignatureRow()">
                        <i class="fas fa-plus me-1"></i> TAMBAH
                    </button>
                </div>
                <div class="card-body p-4">
                    <div id="signatures_container" class="d-flex flex-column gap-3">
                        </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4 text-end mb-5">
        <button type="submit" class="btn btn-dark rounded-pill px-5 py-3 fw-bold shadow-lg">
            <i class="fas fa-save me-2"></i> SIMPAN KONFIGURASI TEMPLATE
        </button>
    </div>
</form>

<style>
    .rounded-lg { border-radius: 1.25rem !important; }
    .signature-card { transition: 0.3s; }
    .signature-card:hover { transform: translateY(-2px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.08)!important; }
</style>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    const container = document.getElementById('signatures_container');
    const existingSignatures = <?= json_encode($template['signatures'] ?? []) ?>;

    function addNewSignatureRow(an = '', jabatan = '', nama = '', nip = '', signImg = '', qr = 'VERIFIED_SIGNATURE') {
        const id = 'sig_' + Math.random().toString(36).substr(2, 9);
        const card = document.createElement('div');
        card.id = id;
        card.className = 'card border bg-light bg-opacity-50 rounded-4 shadow-sm signature-card overflow-hidden';
        
        let imgPreviewHtml = '';
        if(signImg) {
            imgPreviewHtml = `<div class="mt-2 text-center bg-white border rounded p-2"><img src="<?= base_url('uploads/pelatihan/') ?>${signImg}" style="height:40px; mix-blend-mode:multiply;"> <br><small class="text-success fw-bold">File TTD Tersimpan</small></div>`;
        }

        card.innerHTML = `
            <div class="card-header bg-white p-3 border-0 d-flex justify-content-between align-items-center">
                <span class="badge bg-danger-subtle text-danger fw-bold rounded-pill px-3"><i class="fas fa-user-tie me-1"></i> Pejabat Penandatangan</span>
                <button type="button" class="btn btn-outline-danger btn-sm rounded-circle p-1 border-0" onclick="removeSignatureRow('${id}')" style="width: 30px; height: 30px;">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <div class="card-body p-3 pt-0">
                <div class="row g-2">
                    <div class="col-md-4">
                        <label class="form-label small text-muted mb-1 fw-bold text-uppercase" style="font-size: 0.6rem;">Atas Nama (a.n)</label>
                        <input type="text" name="sig_an[]" class="form-control form-control-sm rounded-pill px-3 bg-white border-light shadow-sm" value="${an}" placeholder="a.n Direktur">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label small text-muted mb-1 fw-bold text-uppercase" style="font-size: 0.6rem;">Jabatan / Kedudukan</label>
                        <input type="text" name="sig_jabatan[]" class="form-control form-control-sm rounded-pill px-3 bg-white border-light shadow-sm" value="${jabatan}" placeholder="Direktur RSUD" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small text-muted mb-1 fw-bold text-uppercase" style="font-size: 0.6rem;">Nama Pejabat</label>
                        <input type="text" name="sig_nama[]" class="form-control form-control-sm rounded-pill px-3 bg-white border-light shadow-sm fw-bold" value="${nama}" placeholder="Dr. Nama Pejabat" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small text-muted mb-1 fw-bold text-uppercase" style="font-size: 0.6rem;">NIP Pejabat</label>
                        <input type="text" name="sig_nip[]" class="form-control form-control-sm rounded-pill px-3 bg-white border-light shadow-sm" value="${nip}" placeholder="196901241999031003" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small text-muted mb-1 fw-bold text-uppercase" style="font-size: 0.6rem;">Upload TTD (Wajib .PNG Transparan)</label>
                        <input type="file" name="sig_file[]" class="form-control form-control-sm rounded-pill px-3 bg-white border-light shadow-sm" accept="image/png">
                        <input type="hidden" name="sig_file_old[]" value="${signImg}">
                        ${imgPreviewHtml}
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small text-muted mb-1 fw-bold text-uppercase" style="font-size: 0.6rem;">Teks QR Code Verifikasi</label>
                        <input type="text" name="sig_qr[]" class="form-control form-control-sm rounded-pill px-3 bg-white border-light shadow-sm" value="${qr}" placeholder="Tautan verifikasi keaslian" required>
                    </div>
                </div>
            </div>
        `;
        container.appendChild(card);
    }

    function removeSignatureRow(id) {
        const el = document.getElementById(id);
        if (el) el.remove();
    }

    if (existingSignatures && existingSignatures.length > 0) {
        existingSignatures.forEach(s => {
            addNewSignatureRow(s.an, s.jabatan, s.nama, s.nip, s.ttd_image, s.qr_data);
        });
    } else {
        addNewSignatureRow();
    }
</script>
<?= $this->endSection() ?>
