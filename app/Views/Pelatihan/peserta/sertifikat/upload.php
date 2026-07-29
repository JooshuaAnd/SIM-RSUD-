<?= $this->extend('Pelatihan/layout/peserta_layout') ?>

<?= $this->section('content') ?>
<div class="pt-1 mb-5 glass-wrapper-global">
    <?php
    $categories = $categories ?? [];
    $ranah_list = $ranah_list ?? [];
    // Build lowercase-key map for JS
    $ranah_options = [];
    foreach ($ranah_list as $r) {
        $ranah_options[] = ['value' => strtolower($r), 'label' => $r];
    }
    ?>
    <div class="mb-4 animate__animated animate__fadeIn">
        <h3 class="fw-bold mb-1 text-white"><i class="fas fa-file-upload me-2 text-warning"></i> Pencatatan Kegiatan Mandiri / Surat Tugas</h3>
        <p class="text-white opacity-75 mb-0 fw-medium">Input data sertifikat/kegiatan eksternal untuk pemenuhan JPL</p>
    </div>

    <form action="<?= base_url('pelatihan/peserta/submit_upload_sertifikat') ?>" method="POST" enctype="multipart/form-data">
        <div class="row g-4">
            <!-- Step 1: Pilih Ranah & Kategori -->
            <div class="col-lg-4">
                <div class="glass-card-global h-100 p-4">
                    <h6 class="fw-bold mb-4 border-bottom pb-2 text-white" style="border-color: rgba(255,255,255,0.2) !important;">STEP 1: JENIS KEGIATAN</h6>
                    
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-white">RANAH</label>
                        <select name="ranah" id="select_ranah" class="form-select rounded-pill border-2" onchange="updateCategories(this.value)" required>
                            <?php foreach ($ranah_options as $ro): ?>
                            <option value="<?= esc($ro['value']) ?>"><?= esc($ro['label']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-white">KATEGORI KEGIATAN</label>
                        <select name="kategori_kegiatan" id="select_kategori" class="form-select rounded-pill border-2" onchange="showForms()" required>
                            <option value="">-- Pilih Kategori Kegiatan --</option>
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label small fw-bold d-block mb-3 text-white">TIPE PENGAJUAN :</label>
                        <div class="d-flex flex-column gap-2 text-white">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="jenis_dokumen" value="Mandiri" id="type1" checked onchange="toggleST(false)">
                                <label class="form-check-label fw-bold small" for="type1">MANDIRI</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="jenis_dokumen" value="Surat Tugas" id="type2" onchange="toggleST(true)">
                                <label class="form-check-label fw-bold small" for="type2">SURAT TUGAS</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-warning border-0 rounded-4 small fw-bold shadow-sm text-dark bg-warning">
                        <i class="fas fa-info-circle me-2"></i> Pastikan semua data sesuai dengan dokumen bukti yang diunggah.
                    </div>
                </div>
            </div>

            <!-- Step 2: Form Detail -->
            <div class="col-lg-8">
                <div id="welcome_message" class="glass-card-global p-4 mb-4 text-center py-5">
                    <i class="fas fa-hand-pointer text-white fa-3x mb-3 opacity-25"></i>
                    <p class="fw-bold text-white opacity-75">Silakan pilih Kategori Kegiatan di Step 1 <br>untuk menampilkan formulir detail.</p>
                </div>

                <div id="main_form" class="d-none">
                    <div class="glass-card-global p-4 mb-4">
                        <h6 class="fw-bold mb-4 border-bottom pb-2 text-white" style="border-color: rgba(255,255,255,0.2) !important;">STEP 2: DETAIL SERTIFIKAT & DATA KEGIATAN</h6>
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label small fw-bold text-white">JUDUL KEGIATAN / SERTIFIKAT (Wajib)</label>
                                <input type="text" name="judul" class="form-control rounded-pill border shadow-sm" placeholder="Contoh: Seminar Kesehatan Nasional" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-white">NOMOR SERTIFIKAT</label>
                                <input type="text" name="no_sertifikat" class="form-control rounded-pill border shadow-sm" placeholder="Nomor pada sertifikat">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-white">PENERBIT / PENYELENGGARA</label>
                                <input type="text" name="penerbit" class="form-control rounded-pill border shadow-sm" placeholder="Contoh: Kemenkes RI">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-white">TANGGAL MULAI</label>
                                <input type="date" name="tgl_mulai" class="form-control rounded-pill border shadow-sm">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-white">TANGGAL SELESAI</label>
                                <input type="date" name="tgl_selesai" class="form-control rounded-pill border shadow-sm">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-white">TOTAL JPL / SKP (Wajib)</label>
                                <input type="number" step="0.01" name="jpl" class="form-control rounded-pill border shadow-sm" placeholder="Contoh: 10" required>
                            </div>
                        </div>
                    </div>

                    <div class="glass-card-global p-4">
                        <h6 class="fw-bold mb-4 border-bottom pb-2 text-white" style="border-color: rgba(255,255,255,0.2) !important;">STEP 3: UNGGAH DOKUMEN BUKTI</h6>
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label small fw-bold text-white" id="label_dokumen_utama">DOKUMEN SERTIFIKAT / BUKTI (Wajib)</label>
                                <div class="p-4 rounded-4 text-center mb-3" style="background: rgba(255,255,255,0.1); border: 2px dashed rgba(255,255,255,0.3);">
                                    <i class="fas fa-cloud-upload-alt fa-3x text-white opacity-50 mb-3"></i>
                                    <div class="small fw-bold text-white mb-1">File format PDF/JPG/PNG (Max 2MB)</div>
                                    <input type="file" name="dokumen" class="form-control form-control-sm mt-2" required>
                                </div>
                            </div>

                            <div id="container_surat_tugas" class="col-md-12 d-none">
                                <label class="form-label small fw-bold text-warning">UNGGAH SURAT TUGAS (Wajib)</label>
                                <div class="p-4 rounded-4 text-center mb-3" style="background: rgba(245, 158, 11, 0.1); border: 2px dashed #f59e0b;">
                                    <i class="fas fa-file-contract fa-3x text-warning mb-3"></i>
                                    <div class="small fw-bold text-warning mb-1">Surat Tugas Resmi PDF/JPG (Max 2MB)</div>
                                    <input type="file" name="dokumen_st" id="input_st" class="form-control form-control-sm mt-2" style="border-color: #f59e0b;">
                                </div>
                            </div>

                            <div class="col-md-12 mt-4">
                                <button type="submit" class="btn btn-action-global w-100 py-3 rounded-pill fw-bold fs-5 shadow" style="background-color: #2563eb; color: white;">
                                    SIMPAN & KIRIM VERIFIKASI <i class="fas fa-paper-plane ms-2"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    const categories = <?= json_encode($categories) ?>;
    
    function updateCategories(ranah) {
        const select = document.getElementById('select_kategori');
        select.innerHTML = '<option value="">-- Pilih Kategori Kegiatan --</option>';
        
        if (categories[ranah]) {
            categories[ranah].forEach(item => {
                const opt = document.createElement('option');
                opt.value = item;
                opt.text = item;
                select.appendChild(opt);
            });
        }
        
        document.getElementById('welcome_message').classList.remove('d-none');
        document.getElementById('main_form').classList.add('d-none');
        // If ranah is pengabdian, force jenis_dokumen to 'pengabdian' and hide type selector
        const typeContainer = document.querySelector('[name="jenis_dokumen"]').closest('.mb-4');
        const typeRadios = document.querySelectorAll('input[name="jenis_dokumen"]');
        if (ranah === 'pengabdian') {
            // hide radio options
            if (typeContainer) typeContainer.style.display = 'none';
            // create or set hidden input
            let hidden = document.querySelector('input[name="jenis_dokumen_hidden"]');
            if (!hidden) {
                hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = 'jenis_dokumen';
                hidden.value = 'pengabdian';
                hidden.id = 'jenis_dokumen_hidden';
                document.querySelector('form').appendChild(hidden);
            } else {
                hidden.value = 'pengabdian';
            }
            // hide surat tugas container if any
            document.getElementById('container_surat_tugas').classList.add('d-none');
        } else {
            if (typeContainer) typeContainer.style.display = '';
            // remove hidden override if present
            const hidden = document.querySelector('#jenis_dokumen_hidden');
            if (hidden) hidden.remove();
        }
    }

    function showForms() {
        const cat = document.getElementById('select_kategori').value;
        if(cat !== "") {
            document.getElementById('welcome_message').classList.add('d-none');
            document.getElementById('main_form').classList.remove('d-none');
        } else {
            document.getElementById('welcome_message').classList.remove('d-none');
            document.getElementById('main_form').classList.add('d-none');
        }
    }

    function toggleST(show) {
        const container = document.getElementById('container_surat_tugas');
        const input = document.getElementById('input_st');
        const label = document.getElementById('label_dokumen_utama');
        
        if (show) {
            container.classList.remove('d-none');
            input.required = true;
            label.innerText = "DOKUMEN SERTIFIKAT (Wajib Diisi)";
        } else {
            container.classList.add('d-none');
            input.required = false;
            label.innerText = "DOKUMEN SERTIFIKAT / BUKTI (Wajib Diisi)";
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const firstRanah = document.getElementById('select_ranah');
        if (firstRanah && firstRanah.value) {
            updateCategories(firstRanah.value);
        }
    });
</script>
<?= $this->endSection() ?>
