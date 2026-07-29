<?= $this->extend('Pelatihan/layout/admin_layout') ?>

<?= $this->section('content') ?>
<div class="row align-items-center mb-1"></div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
            <div class="card-header bg-dark text-white p-3 border-0">
                <h6 class="fw-bold mb-0"><i class="fas fa-upload me-2 text-danger"></i> Upload Logo Baru</h6>
            </div>
            <div class="card-body p-4 bg-light bg-opacity-50">
                <form action="<?= site_url('pelatihan/admin/pengaturan_logo/update') ?>" method="POST" enctype="multipart/form-data">
                    <div class="mb-4 text-center">
                        <label class="form-label small fw-bold d-block text-start mb-3">Preview Logo Utama</label>
                        <div class="p-3 bg-white rounded-4 shadow-sm border d-inline-block position-relative mb-3">
                            <img id="logoPreview" src="<?= get_system_logo() ?>" alt="Preview Logo" style="height: 120px; max-width: 100%; object-fit: contain;">
                        </div>
                        <div class="mb-3">
                            <input type="file" name="logo_sistem" id="logoInput" class="form-control rounded-pill border" accept="image/*" onchange="previewImage(this, 'logoPreview')">
                            <div class="form-text text-muted small mt-1">Disarankan menggunakan file PNG/JPG dengan background transparan/putih.</div>
                        </div>
                    </div>

                    <div class="mb-4 text-center border-top pt-4">
                        <label class="form-label small fw-bold d-block text-start mb-3">Preview Favicon Browser</label>
                        <div class="p-3 bg-white rounded-4 shadow-sm border d-inline-block position-relative mb-3">
                            <img id="faviconPreview" src="<?= get_system_favicon() ?>" alt="Preview Favicon" style="height: 60px; width: 60px; object-fit: contain;">
                        </div>
                        <div class="mb-3">
                            <input type="file" name="favicon_sistem" id="faviconInput" class="form-control rounded-pill border" accept="image/*" onchange="previewImage(this, 'faviconPreview')">
                            <div class="form-text text-muted small mt-1">Gunakan file gambar ikon (PNG/ICO) berukuran persegi (misal: 64x64 px).</div>
                        </div>
                    </div>

                    <div class="text-end border-top pt-3">
                        <button type="submit" class="btn btn-danger btn-primary-custom rounded-pill px-5 py-2.5 fw-bold shadow"><i class="fas fa-save me-2"></i> Perbarui Logo Sistem</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-4 bg-white h-100">
            <div class="card-header bg-dark text-white p-3 border-0">
                <h6 class="fw-bold mb-0"><i class="fas fa-info-circle me-2 text-danger"></i> Panduan Logo Terpadu</h6>
            </div>
            <div class="card-body p-4">
                <div class="mb-4">
                    <h6 class="fw-bold text-dark mb-2"><i class="fas fa-check-circle text-success me-2"></i> Sinkronisasi Otomatis</h6>
                    <p class="text-secondary small lh-lg mb-0">Logo dan favicon yang Anda unggah di sini akan langsung menggantikan seluruh gambar logo bawaan di:</p>
                    <ul class="text-secondary small lh-lg mt-2 ps-3">
                        <li>Navigasi Sidebar Admin & Peserta</li>
                        <li>Favicon di tab browser seluruh modul</li>
                        <li>Header utama template sertifikat pelatihan resmi</li>
                    </ul>
                </div>

                <div class="mb-4">
                    <h6 class="fw-bold text-dark mb-2"><i class="fas fa-check-circle text-success me-2"></i> File yang Direkomendasikan</h6>
                    <p class="text-secondary small lh-lg mb-0">Untuk hasil terbaik, gunakan logo dalam format PNG transparan dengan resolusi yang memadai agar terlihat tajam pada layar desktop maupun sertifikat cetak.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
<?= $this->endSection() ?>
