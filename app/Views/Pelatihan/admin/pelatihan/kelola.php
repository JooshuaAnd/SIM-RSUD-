<?php
$p = $p ?? [];
$materi = $materi ?? [];
$sesi_online = $sesi_online ?? [];
$sesi_offline = $sesi_offline ?? [];
$sesiList = $sesiList ?? [];
$kuis = $kuis ?? [];
$presensi = $presensi ?? [];
$kuesioner = $kuesioner ?? [];
?>

<?= $this->extend('pelatihan/layout/admin_layout') ?>

<?= $this->section('content') ?>

<style>
    .toast-container {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
    }
    .custom-toast {
        background: #fff;
        border-radius: 12px;
        padding: 16px 24px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        display: flex;
        align-items: center;
        margin-bottom: 10px;
        border-left: 5px solid #ce2127;
        transform: translateX(120%);
        transition: transform 0.3s ease-in-out;
    }
    .custom-toast.show { transform: translateX(0); }
    .custom-toast.success { border-left-color: #198754; }
    .custom-toast.warning { border-left-color: #ffc107; }
</style>

<div class="toast-container" id="toastContainer"></div>

<div class="mb-4 animate-up">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb small fw-bold text-uppercase">
            <li class="breadcrumb-item"><a href="<?= base_url('pelatihan/admin/pelatihan') ?>" class="text-decoration-none text-danger">Daftar Pelatihan</a></li>
            <li class="breadcrumb-item active">Manajemen Konten</li>
        </ol>
    </nav>
    <div class="card border-0 shadow-sm rounded-lg overflow-hidden border-top border-danger border-4">
        <div class="card-body p-4 bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="fw-bold mb-1 text-dark"><?= strtoupper($p['nama']) ?></h3>
                    <div class="d-flex align-items-center gap-3">
                        <span class="badge bg-dark rounded-pill px-3 py-2 fw-bold" style="font-size: 0.65rem;"><i class="fas fa-medal text-warning me-1"></i> <?= $p['skp'] ?> SKP</span>
                        <span class="text-muted fw-bold small"><i class="fas fa-id-card me-1 text-danger"></i> ID: #<?= str_pad($p['id'], 4, '0', STR_PAD_LEFT) ?></span>
                        <span class="badge bg-danger-subtle text-danger rounded-pill px-3 fw-bold" style="font-size: 0.6rem;"><?= strtoupper($p['metode']) ?></span>
                    </div>
                </div>
                <div class="text-end">
                    <div class="text-muted small fw-bold mb-1">STATUS PROGRAM</div>
                    <?php if(($p['status'] ?? 'Draft') == 'Aktif'): ?>
                        <span class="badge bg-success text-white rounded-pill px-4 py-2 fw-bold shadow-sm" style="border: 1px solid var(--primary-yellow);"><i class="fas fa-check-circle me-1"></i> AKTIF</span>
                    <?php elseif(($p['status'] ?? 'Draft') == 'Selesai'): ?>
                        <span class="badge bg-secondary text-white rounded-pill px-4 py-2 fw-bold shadow-sm"><i class="fas fa-check-double me-1"></i> SELESAI</span>
                    <?php else: ?>
                        <span class="badge bg-secondary text-white rounded-pill px-4 py-2 fw-bold shadow-sm"><i class="fas fa-file-alt me-1"></i> DRAFT</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 animate-up" style="animation-delay: 0.1s;">
    <!-- Navigation Sidebar -->
    <div class="col-lg-3">
        <div class="card border-0 shadow-sm rounded-lg overflow-hidden sticky-top" style="top: 20px;">
            <div class="list-group list-group-flush" id="manageTabs" role="tablist">
                <button class="list-group-item list-group-item-action active p-4 border-0 d-flex align-items-center transition-all" id="tab-sesi-btn" data-bs-toggle="pill" data-bs-target="#tab-sesi" type="button">
                    <div class="icon-circle bg-dark-subtle text-dark me-3"><i class="fas fa-video"></i></div>
                    <div>
                        <div class="fw-bold text-dark small">Sesi Pelatihan</div>
                        <div class="text-muted fw-bold" style="font-size: 0.6rem;">JADWAL & SESI</div>
                    </div>
                </button>

                <button class="list-group-item list-group-item-action p-4 border-0 d-flex align-items-center transition-all" id="tab-materi-btn" data-bs-toggle="pill" data-bs-target="#tab-materi" type="button">
                    <div class="icon-circle bg-danger-subtle text-danger me-3"><i class="fas fa-play-circle"></i></div>
                    <div>
                        <div class="fw-bold text-dark small">MATERI</div>
                        <div class="text-muted fw-bold" style="font-size: 0.6rem;">MODUL BELAJAR</div>
                    </div>
                </button>

                <button class="list-group-item list-group-item-action p-4 border-0 d-flex align-items-center transition-all" id="tab-evaluasi-btn" data-bs-toggle="pill" data-bs-target="#tab-evaluasi" type="button">
                    <div class="icon-circle bg-warning-subtle text-dark me-3"><i class="fas fa-tasks"></i></div>
                    <div>
                        <div class="fw-bold text-dark small">Kuis Mandiri</div>
                        <div class="text-muted fw-bold" style="font-size: 0.6rem;">PRE & POST TEST</div>
                    </div>
                </button>

                <button class="list-group-item list-group-item-action p-4 border-0 d-flex align-items-center transition-all" id="tab-feedback-btn" data-bs-toggle="pill" data-bs-target="#tab-feedback" type="button">
                    <div class="icon-circle bg-secondary-subtle text-secondary me-3"><i class="fas fa-poll-h"></i></div>
                    <div>
                        <div class="fw-bold text-dark small">Kuesioner</div>
                        <div class="text-muted fw-bold" style="font-size: 0.6rem;">EVALUASI KEPUASAN</div>
                    </div>
                </button>

            </div>
        </div>
    </div>

    <!-- Content Area -->
    <div class="col-lg-9">
        <div class="tab-content" id="manageTabsContent">
            
<?= $this->include('pelatihan/admin/pelatihan/tabs/tab_materi') ?>
<?= $this->include('pelatihan/admin/pelatihan/tabs/tab_sesi') ?>

<?= $this->include('pelatihan/admin/pelatihan/tabs/tab_evaluasi') ?>
<?= $this->include('pelatihan/admin/pelatihan/tabs/tab_feedback') ?>
        </div>
    </div>
</div>

<!-- Modal Tambah Materi -->
<?= $this->include('pelatihan/admin/pelatihan/modals/kelola/modal_tambah_materi') ?>
<?= $this->include('pelatihan/admin/pelatihan/modals/kelola/modal_edit_materi') ?>
<?= $this->include('pelatihan/admin/pelatihan/modals/kelola/modal_kelola_quiz') ?>
<?= $this->include('pelatihan/admin/pelatihan/modals/kelola/modal_kelola_feedback') ?>
<?= $this->include('pelatihan/admin/pelatihan/modals/kelola/modal_preview_kuesioner') ?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Tab persistence logic
        const triggerTabList = document.querySelectorAll('#manageTabs button');
        triggerTabList.forEach(triggerEl => {
            triggerEl.addEventListener('shown.bs.tab', event => {
                const targetId = event.target.getAttribute('data-bs-target');
                localStorage.setItem('activeManageTab', targetId);
            });
        });

        const activeTabId = localStorage.getItem('activeManageTab');
        if (activeTabId) {
            const tabButton = document.querySelector(`#manageTabs button[data-bs-target="${activeTabId}"]`);
            if (tabButton) {
                const tab = new bootstrap.Tab(tabButton);
                tab.show();
            }
        }
    });

    function editMateri(materi) {
        document.getElementById('edit_id_materi').value = materi.id;
        document.getElementById('edit_judul').value = materi.judul;
        document.getElementById('edit_segmen').value = materi.segmen;
        document.getElementById('edit_sesi_id').value = materi.sesi_id || "";
        document.getElementById('edit_tipe').value = materi.tipe;
        document.getElementById('edit_tipe').dispatchEvent(new Event('change'));
        document.getElementById('edit_deskripsi').value = materi.deskripsi;
        document.getElementById('editFileName').innerText = "Pilih file materi baru...";
        document.getElementById('editFileInput').value = "";
        
        if (materi.tipe === 'link') {
            document.getElementById('editLinkMateriInput').value = materi.file_path;
        } else {
            document.getElementById('editLinkMateriInput').value = '';
        }
        
        var editModal = new bootstrap.Modal(document.getElementById('modalEditMateri'));
        editModal.show();
    }

    function showToast(message, type = 'success') {
        const container = document.getElementById('toastContainer');
        const toast = document.createElement('div');
        toast.className = `custom-toast ${type}`;
        
        const icon = type === 'success' ? 'fa-check-circle text-success' : 'fa-exclamation-triangle text-warning';
        toast.innerHTML = `
            <i class="fas ${icon} me-3 fa-lg"></i>
            <div>
                <div class="fw-bold small">System Notification</div>
                <div class="text-muted small" style="font-size: 0.7rem;">${message}</div>
            </div>
        `;
        
        container.appendChild(toast);
        setTimeout(() => toast.classList.add('show'), 10);
        
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    function setupQuiz(type) {
        document.getElementById('quizModalTitle').innerHTML = '<i class="fas fa-tasks me-2"></i> Pengaturan ' + type;
        document.getElementById('current_tipe_evaluasi').value = type;
        document.getElementById('soalContainer').innerHTML = '<div class="text-center text-muted small py-4"><i class="fas fa-spinner fa-spin me-2"></i> Memuat soal...</div>';
        
        let pelatihan_id = <?= $p['id'] ?>;
        fetch(`<?= base_url('pelatihan/admin/pelatihan/evaluasi_soal') ?>/${pelatihan_id}/${type}`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('current_evaluasi_id').value = data.evaluasi.id;
                document.getElementById('evaluasi_kkm').value = data.evaluasi.kkm;
                
                renderSoal(data.soal);
            });
    }

    function renderSoal(soalList) {
        const container = document.getElementById('soalContainer');
        container.innerHTML = '';
        if (soalList.length === 0) {
            container.innerHTML = '<div class="text-center text-muted small py-4">Belum ada soal. Silakan tambah soal.</div>';
            return;
        }
        
        soalList.forEach((soal, index) => {
            let imgOrPdf = '';
            if (soal.file_path) {
                imgOrPdf = `<div class="mb-2"><a href="<?= base_url() ?>${soal.file_path}" target="_blank" class="btn btn-sm btn-info text-white"><i class="fas fa-file me-1"></i> Lihat File Terlampir</a> <button type="button" class="btn btn-sm btn-danger" onclick="hapusFileSoal(${soal.id})"><i class="fas fa-times"></i></button></div>`;
            }
            
            let checkedA = soal.jawaban_benar === 'A' ? 'checked' : '';
            let checkedB = soal.jawaban_benar === 'B' ? 'checked' : '';
            let checkedC = soal.jawaban_benar === 'C' ? 'checked' : '';
            let checkedD = soal.jawaban_benar === 'D' ? 'checked' : '';

            let materiOptions = '<option value="">-- Umum / Tidak Terkait Materi --</option>';
            <?php foreach($materi as $m): ?>
                <?php 
                $sesiText = "Umum";
                foreach($sesiList as $s) {
                    if ($s['id'] == $m['sesi_id']) {
                        $sesiText = $s['nama_sesi'];
                        break;
                    }
                }
                ?>
                materiOptions += `<option value="<?= $m['id'] ?>" ${soal.materi_id == <?= $m['id'] ?> ? 'selected' : ''}>ID: <?= $m['id'] ?> - <?= addslashes(htmlspecialchars($m['judul'])) ?> (${"<?= addslashes(htmlspecialchars($sesiText)) ?>"})</option>`;
            <?php endforeach; ?>

            const html = `
                <div class="card border-0 shadow-sm rounded-lg mb-3 p-4">
                    <form action="<?= base_url('pelatihan/admin/pelatihan/evaluasi_soal/simpan') ?>" method="POST" enctype="multipart/form-data" onsubmit="simpanSoalAjax(event, this)">
                        <input type="hidden" name="id_soal" value="${soal.id}">
                        <input type="hidden" name="ujian_id" value="${soal.ujian_id}">
                        
                        <div class="d-flex justify-content-between mb-3 align-items-center">
                            <span class="badge bg-primary rounded-pill">Pertanyaan #${index+1}</span>
                            <div class="btn-group">
                                <button type="button" class="btn btn-link text-primary p-0 me-3 small fw-bold text-decoration-none" onclick="tambahSoalSetelah(this)"><i class="fas fa-plus me-1"></i> Tambah</button>
                                <button type="submit" class="btn btn-link text-success p-0 me-3 small fw-bold text-decoration-none"><i class="fas fa-save me-1"></i> Simpan</button>
                                <button type="button" class="btn btn-link text-danger p-0 small fw-bold text-decoration-none" onclick="hapusSoal(${soal.id})"><i class="fas fa-trash me-1"></i> Hapus</button>
                            </div>
                        </div>
                        
                        ${imgOrPdf}
                        
                        <div class="mb-3">
                            <label class="small text-muted mb-1">Terkait Materi (Opsional)</label>
                            <select name="materi_id" class="form-select form-select-sm mb-2">
                                ${materiOptions}
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="small text-muted mb-1">Upload Lampiran Soal (Gambar/PDF/Word/Excel/PPT/Video, Opsional)</label>
                            <input type="file" name="file_soal" class="form-control form-control-sm mb-2" accept=".jpg,.jpeg,.png,.webp,.pdf,.mp4,.webm,.ogg">
                        </div>
                        
                        <div class="mb-3">
                            <textarea name="pertanyaan" class="form-control form-control-sm border bg-white" rows="2" required>${soal.pertanyaan}</textarea>
                        </div>
                        <div class="row g-2">
                            <div class="col-6"><div class="input-group input-group-sm"><span class="input-group-text bg-white fw-bold text-primary">A</span><input type="text" name="opsi_a" class="form-control" value="${soal.opsi_a}" required></div></div>
                            <div class="col-6"><div class="input-group input-group-sm"><span class="input-group-text bg-white fw-bold text-primary">B</span><input type="text" name="opsi_b" class="form-control" value="${soal.opsi_b}" required></div></div>
                            <div class="col-6"><div class="input-group input-group-sm"><span class="input-group-text bg-white fw-bold text-primary">C</span><input type="text" name="opsi_c" class="form-control" value="${soal.opsi_c}" required></div></div>
                            <div class="col-6"><div class="input-group input-group-sm"><span class="input-group-text bg-white fw-bold text-primary">D</span><input type="text" name="opsi_d" class="form-control" value="${soal.opsi_d}" required></div></div>
                        </div>
                        <div class="mt-3 small fw-bold text-muted">Kunci Jawaban:</div>
                        <div class="d-flex gap-3 mt-1">
                            <div class="form-check"><input class="form-check-input" type="radio" name="jawaban_benar" value="A" ${checkedA} required> A</div>
                            <div class="form-check"><input class="form-check-input" type="radio" name="jawaban_benar" value="B" ${checkedB}> B</div>
                            <div class="form-check"><input class="form-check-input" type="radio" name="jawaban_benar" value="C" ${checkedC}> C</div>
                            <div class="form-check"><input class="form-check-input" type="radio" name="jawaban_benar" value="D" ${checkedD}> D</div>
                        </div>
                    </form>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
        });
    }

    function saveKkm() {
        let evaluasi_id = document.getElementById('current_evaluasi_id').value;
        let kkm = document.getElementById('evaluasi_kkm').value;
        
        let formData = new FormData();
        formData.append('ujian_id', evaluasi_id);
        formData.append('kkm', kkm);
        
        fetch('<?= base_url('pelatihan/admin/pelatihan/evaluasi_soal/simpan_kkm') ?>', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            Swal.fire({
                title: 'Berhasil!',
                text: data.message || 'KKM berhasil disimpan.',
                icon: 'success',
                showConfirmButton: true,
                confirmButtonText: 'OK',
                confirmButtonColor: '#ce2127',
                padding: '2rem',
                customClass: {
                    popup: 'rounded-4 shadow-lg border-0',
                    confirmButton: 'rounded-pill px-5 py-2 fw-bold text-uppercase'
                }
            }).then(() => {
                location.reload();
            });
        });
    }

    function hapusSoal(id) {
        Swal.fire({
            title: 'Hapus Soal?',
            text: 'Yakin ingin menghapus soal ini? Tindakan ini tidak dapat dibatalkan.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ce2127',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`<?= base_url('pelatihan/admin/pelatihan/evaluasi_soal/hapus') ?>/${id}`)
                    .then(res => res.json())
                    .then(data => {
                        showToast(data.message, data.status);
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    });
            }
        });
    }

    function hapusFileSoal(id) {
        Swal.fire({
            title: 'Hapus File?',
            text: 'Yakin ingin menghapus file terlampir?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ce2127',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`<?= base_url('pelatihan/admin/pelatihan/evaluasi_soal/hapus_file') ?>/${id}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.status == 'success') {
                            showToast('File berhasil dihapus', 'success');
                            setTimeout(() => {
                                location.reload();
                            }, 1000);
                        }
                    });
            }
        });
    }

    function loadKuesioner() {
        let pelatihan_id = <?= $p['id'] ?>;
        fetch(`<?= base_url('pelatihan/admin/pelatihan/kuesioner') ?>/${pelatihan_id}`)
            .then(res => res.json())
            .then(data => {
                // Update dropdown kategori
                let select = document.getElementById('kuesioner_kategori');
                select.innerHTML = '<option value="">-- Pilih Kategori --</option><option value="baru">+ Buat Kategori Baru</option>';
                data.allKategori.forEach(k => {
                    select.innerHTML += `<option value="${k}">${k}</option>`;
                });

                // Render kuesioner
                let container = document.getElementById('kuesionerContainer');
                container.innerHTML = '';
                
                if (data.kategori.length === 0) {
                    container.innerHTML = `
                        <div class="text-center text-muted small py-4">
                            <i class="fas fa-inbox fa-3x mb-3 text-light"></i><br>
                            Belum ada pertanyaan kuesioner evaluasi.<br><br>
                            <button class="btn btn-sm btn-outline-primary rounded-pill" onclick="loadTemplateKuesioner()">
                                <i class="fas fa-magic me-1"></i> Gunakan Template Kuesioner Standar
                            </button>
                        </div>
                    `;
                    return;
                }

                let kuesionerData = data.kuesioner;
                let previewHtml = '';
                let tabHtml = '';

                let idxKategori = 1;
                for (let kat in kuesionerData) {
                    let questions = kuesionerData[kat];
                    
                    // Render for Kelola
                    let html = `<div class="mb-4">
                        <h6 class="fw-bold text-primary border-bottom pb-2 text-uppercase small">Kategori ${idxKategori}: ${kat}</h6>`;
                    
                    // Render for Preview
                    previewHtml += `<div class="mb-4">
                        <h6 class="fw-bold text-primary border-bottom pb-2 text-uppercase small">${kat}</h6>`;

                    questions.forEach((q, idx) => {
                        html += `
                            <div class="card border-0 shadow-sm p-3 mb-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="w-100 me-3">
                                        <label class="form-label small fw-bold mb-1">${idx+1}. Pertanyaan</label>
                                        <input type="text" class="form-control form-control-sm mb-2" value="${q.pertanyaan}" onchange="updateKuesioner(${q.id}, this.value)">
                                        <div class="d-flex gap-2 small text-muted">
                                            <span>Skala: 1 (Sangat Kurang) - 5 (Sangat Baik)</span>
                                        </div>
                                    </div>
                                    <button class="btn btn-sm btn-outline-danger" onclick="hapusKuesioner(${q.id})"><i class="fas fa-trash"></i></button>
                                </div>
                            </div>
                        `;

                        previewHtml += `
                            <div class="card border-0 shadow-sm p-3 mb-3">
                                <label class="form-label fw-bold mb-3">${idx+1}. ${q.pertanyaan}</label>
                                <div class="d-flex justify-content-between px-3">
                                    <div class="text-center">
                                        <input type="radio" class="form-check-input" name="preview_${q.id}" value="1"><br>
                                        <small class="text-muted">1<br>Sangat Kurang</small>
                                    </div>
                                    <div class="text-center">
                                        <input type="radio" class="form-check-input" name="preview_${q.id}" value="2"><br>
                                        <small class="text-muted">2</small>
                                    </div>
                                    <div class="text-center">
                                        <input type="radio" class="form-check-input" name="preview_${q.id}" value="3"><br>
                                        <small class="text-muted">3</small>
                                    </div>
                                    <div class="text-center">
                                        <input type="radio" class="form-check-input" name="preview_${q.id}" value="4"><br>
                                        <small class="text-muted">4</small>
                                    </div>
                                    <div class="text-center">
                                        <input type="radio" class="form-check-input" name="preview_${q.id}" value="5"><br>
                                        <small class="text-muted">5<br>Sangat Baik</small>
                                    </div>
                                </div>
                            </div>
                        `;
                    });

                    html += `</div>`;
                    previewHtml += `</div>`;
                    
                    tabHtml += `
                        <div class="col-md-4">
                            <div class="p-3 border rounded-lg bg-light h-100">
                                <h6 class="fw-bold small text-primary text-uppercase mb-3"><i class="fas fa-list-ul me-2"></i> ${kat}</h6>
                                <ul class="list-unstyled small text-muted">
                    `;
                    questions.forEach(q => {
                        tabHtml += `<li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> ${q.pertanyaan}</li>`;
                    });
                    tabHtml += `</ul></div></div>`;
                    
                    container.innerHTML += html;
                    idxKategori++;
                }

                document.getElementById('previewContainer').innerHTML = previewHtml;
                
                let tabContainer = document.getElementById('kuesionerTabContainer');
                if(tabContainer) {
                    if (data.kategori.length === 0) {
                        tabContainer.innerHTML = `
                            <div class="col-12 text-center text-muted py-5">
                                <i class="fas fa-inbox fa-3x mb-3 text-light"></i>
                                <h6>Belum ada pertanyaan kuesioner.</h6>
                                <p class="small mb-0">Klik "Edit Kuesioner" untuk menambahkan atau menggunakan template.</p>
                            </div>
                        `;
                    } else {
                        tabContainer.innerHTML = tabHtml;
                    }
                }
            });
    }

    function toggleKategoriBaru(val) {
        let container = document.getElementById('kategori_baru_container');
        let input = document.getElementById('kuesioner_kategori_baru');
        if (val === 'baru') {
            container.style.display = 'block';
            input.required = true;
        } else {
            container.style.display = 'none';
            input.required = false;
            input.value = '';
        }
    }

    function simpanKuesioner(event) {
        event.preventDefault();
        let form = event.target;
        let formData = new FormData(form);
        formData.append('pelatihan_id', <?= $p['id'] ?>);

        let submitBtn = form.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Menyimpan...';

        fetch('<?= base_url('pelatihan/admin/pelatihan/kuesioner/simpan') ?>', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                showToast(data.message, 'success');
                form.reset();
                toggleKategoriBaru('');
                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else {
                showToast(data.message, 'danger');
            }
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-save me-1"></i> Simpan Pertanyaan';
        });
    }

    function hapusKuesioner(id) {
        Swal.fire({
            title: 'Hapus Pertanyaan?',
            text: 'Yakin ingin menghapus pertanyaan ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ce2127',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`<?= base_url('pelatihan/admin/pelatihan/kuesioner/hapus') ?>/${id}`)
                    .then(res => res.json())
                    .then(data => {
                        showToast(data.message, data.status);
                        if(data.status === 'success') {
                            setTimeout(() => {
                                location.reload();
                            }, 1000);
                        }
                    });
            }
        });
    }

    function updateKuesioner(id, pertanyaan) {
        let formData = new FormData();
        formData.append('id', id);
        formData.append('pertanyaan', pertanyaan);
        
        fetch('<?= base_url('pelatihan/admin/pelatihan/kuesioner/update') ?>', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if(data.status === 'success') {
                setTimeout(() => {
                    location.reload();
                }, 1000);
            }
        });
    }

    function loadTemplateKuesioner() {
        Swal.fire({
            title: 'Gunakan Template Standar?',
            text: 'Ini akan mereset dan menimpa seluruh kuesioner yang ada.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ce2127',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Gunakan',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                let pelatihan_id = <?= $p['id'] ?>;
                fetch(`<?= base_url('pelatihan/admin/pelatihan/kuesioner/template') ?>/${pelatihan_id}`)
                    .then(res => res.json())
                    .then(data => {
                        showToast(data.message, data.status);
                        if(data.status === 'success') {
                            setTimeout(() => {
                                location.reload();
                            }, 1000);
                        }
                    });
            }
        });
    }

    function previewKuesioner() {
        var myModal = new bootstrap.Modal(document.getElementById('modalPreviewKuesioner'));
        myModal.show();
    }

    // Panggil saat modal dibuka
    document.getElementById('modalKelolaFeedback').addEventListener('show.bs.modal', function () {
        loadKuesioner();
    });

    function addSesi() {
        const container = document.getElementById('sesiContainer');
        const index = container.children.length + 1;
        const html = `
            <div class="card border bg-light mb-3">
                <div class="card-body p-3">
                    <div class="row g-2 align-items-center">
                        <div class="col-md-1 text-center"><span class="badge bg-success rounded-circle p-2">${index}</span></div>
                        <div class="col-md-3">
                            <label class="small text-muted d-block">Nama Sesi</label>
                            <input type="text" class="form-control form-control-sm" placeholder="Sesi Baru">
                        </div>
                        <div class="col-md-2">
                            <label class="small text-muted d-block">Tanggal</label>
                            <input type="date" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-2">
                            <label class="small text-muted d-block">Waktu</label>
                            <input type="time" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-3">
                            <label class="small text-muted d-block">Lokasi/Ruang</label>
                            <input type="text" class="form-control form-control-sm" placeholder="Ruang ...">
                        </div>
                        <div class="col-md-1 text-end">
                            <button class="btn btn-link text-danger p-0" onclick="this.closest('.card').remove()"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
        showToast('Sesi baru ditambahkan.', 'success');
    }

    function tambahSoal() {
        const container = document.getElementById('soalContainer');
        const index = container.children.length + 1;
        let evaluasi_id = document.getElementById('current_evaluasi_id').value;
        
        // Remove empty state message if exists
        if(container.innerHTML.includes('Belum ada soal')) container.innerHTML = '';
        
        const html = `
            <div class="card border-0 shadow-sm rounded-lg mb-3 p-4 animate__animated animate__fadeInUp">
                <form action="<?= base_url('pelatihan/admin/pelatihan/evaluasi_soal/simpan') ?>" method="POST" enctype="multipart/form-data" onsubmit="simpanSoalAjax(event, this)">
                    <input type="hidden" name="id_soal" value="">
                    <input type="hidden" name="ujian_id" value="${evaluasi_id}">
                    
                    <div class="d-flex justify-content-between mb-3 align-items-center">
                        <span class="badge bg-primary rounded-pill">Pertanyaan Baru</span>
                        <div class="btn-group">
                            <button type="button" class="btn btn-link text-primary p-0 me-3 small fw-bold text-decoration-none" onclick="tambahSoalSetelah(this)"><i class="fas fa-plus me-1"></i> Tambah</button>
                            <button type="submit" class="btn btn-link text-success p-0 me-3 small fw-bold text-decoration-none"><i class="fas fa-save me-1"></i> Simpan</button>
                            <button type="button" class="btn btn-link text-danger p-0 small fw-bold text-decoration-none" onclick="this.closest('.card').remove()"><i class="fas fa-times me-1"></i> Batal</button>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="small text-muted mb-1">Terkait Materi (Opsional)</label>
                        <select name="materi_id" class="form-select form-select-sm mb-2">
                            <option value="">-- Umum / Tidak Terkait Materi --</option>
                            <?php foreach($materi as $m): ?>
                                <?php 
                                $sesiText = "Umum";
                                foreach($sesiList as $s) {
                                    if ($s['id'] == $m['sesi_id']) {
                                        $sesiText = $s['nama_sesi'];
                                        break;
                                    }
                                }
                                ?>
                                <option value="<?= $m['id'] ?>">ID: <?= $m['id'] ?> - <?= addslashes(htmlspecialchars($m['judul'])) ?> (<?= addslashes(htmlspecialchars($sesiText)) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="small text-muted mb-1">Upload File (Gambar/PDF, Opsional)</label>
                        <input type="file" name="file_soal" class="form-control form-control-sm mb-2">
                    </div>
                    
                    <div class="mb-3">
                        <textarea name="pertanyaan" class="form-control form-control-sm border bg-white" rows="2" placeholder="Masukkan pertanyaan baru..." required></textarea>
                    </div>
                    <div class="row g-2">
                        <div class="col-6"><div class="input-group input-group-sm"><span class="input-group-text bg-white fw-bold text-primary">A</span><input type="text" name="opsi_a" class="form-control" required></div></div>
                        <div class="col-6"><div class="input-group input-group-sm"><span class="input-group-text bg-white fw-bold text-primary">B</span><input type="text" name="opsi_b" class="form-control" required></div></div>
                        <div class="col-6"><div class="input-group input-group-sm"><span class="input-group-text bg-white fw-bold text-primary">C</span><input type="text" name="opsi_c" class="form-control" required></div></div>
                        <div class="col-6"><div class="input-group input-group-sm"><span class="input-group-text bg-white fw-bold text-primary">D</span><input type="text" name="opsi_d" class="form-control" required></div></div>
                    </div>
                    <div class="mt-3 small fw-bold text-muted">Kunci Jawaban:</div>
                    <div class="d-flex gap-3 mt-1">
                        <div class="form-check"><input class="form-check-input" type="radio" name="jawaban_benar" value="A" required> A</div>
                        <div class="form-check"><input class="form-check-input" type="radio" name="jawaban_benar" value="B"> B</div>
                        <div class="form-check"><input class="form-check-input" type="radio" name="jawaban_benar" value="C"> C</div>
                        <div class="form-check"><input class="form-check-input" type="radio" name="jawaban_benar" value="D"> D</div>
                    </div>
                </form>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
        container.scrollTop = container.scrollHeight;
    }

    function tambahSoalSetelah(btn) {
        let evaluasi_id = document.getElementById('current_evaluasi_id').value;
        const html = `
            <div class="card border-0 shadow-sm rounded-lg mb-3 p-4 animate__animated animate__fadeInUp">
                <form action="<?= base_url('pelatihan/admin/pelatihan/evaluasi_soal/simpan') ?>" method="POST" enctype="multipart/form-data" onsubmit="simpanSoalAjax(event, this)">
                    <input type="hidden" name="id_soal" value="">
                    <input type="hidden" name="ujian_id" value="${evaluasi_id}">
                    
                    <div class="d-flex justify-content-between mb-3 align-items-center">
                        <span class="badge bg-primary rounded-pill">Pertanyaan Baru</span>
                        <div class="btn-group">
                            <button type="button" class="btn btn-link text-primary p-0 me-3 small fw-bold text-decoration-none" onclick="tambahSoalSetelah(this)"><i class="fas fa-plus me-1"></i> Tambah</button>
                            <button type="submit" class="btn btn-link text-success p-0 me-3 small fw-bold text-decoration-none"><i class="fas fa-save me-1"></i> Simpan</button>
                            <button type="button" class="btn btn-link text-danger p-0 small fw-bold text-decoration-none" onclick="this.closest('.card').remove()"><i class="fas fa-times me-1"></i> Batal</button>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="small text-muted mb-1">Terkait Materi (Opsional)</label>
                        <select name="materi_id" class="form-select form-select-sm mb-2">
                            <option value="">-- Umum / Tidak Terkait Materi --</option>
                            <?php foreach($materi as $m): ?>
                                <?php 
                                $sesiText = "Umum";
                                foreach($sesiList as $s) {
                                    if ($s['id'] == $m['sesi_id']) {
                                        $sesiText = $s['nama_sesi'];
                                        break;
                                    }
                                }
                                ?>
                                <option value="<?= $m['id'] ?>">ID: <?= $m['id'] ?> - <?= addslashes(htmlspecialchars($m['judul'])) ?> (<?= addslashes(htmlspecialchars($sesiText)) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="small text-muted mb-1">Upload File (Gambar/PDF, Opsional)</label>
                        <input type="file" name="file_soal" class="form-control form-control-sm mb-2">
                    </div>
                    
                    <div class="mb-3">
                        <textarea name="pertanyaan" class="form-control form-control-sm border bg-white" rows="2" placeholder="Masukkan pertanyaan baru..." required></textarea>
                    </div>
                    <div class="row g-2">
                        <div class="col-6"><div class="input-group input-group-sm"><span class="input-group-text bg-white fw-bold text-primary">A</span><input type="text" name="opsi_a" class="form-control" required></div></div>
                        <div class="col-6"><div class="input-group input-group-sm"><span class="input-group-text bg-white fw-bold text-primary">B</span><input type="text" name="opsi_b" class="form-control" required></div></div>
                        <div class="col-6"><div class="input-group input-group-sm"><span class="input-group-text bg-white fw-bold text-primary">C</span><input type="text" name="opsi_c" class="form-control" required></div></div>
                        <div class="col-6"><div class="input-group input-group-sm"><span class="input-group-text bg-white fw-bold text-primary">D</span><input type="text" name="opsi_d" class="form-control" required></div></div>
                    </div>
                    <div class="mt-3 small fw-bold text-muted">Kunci Jawaban:</div>
                    <div class="d-flex gap-3 mt-1">
                        <div class="form-check"><input class="form-check-input" type="radio" name="jawaban_benar" value="A" required> A</div>
                        <div class="form-check"><input class="form-check-input" type="radio" name="jawaban_benar" value="B"> B</div>
                        <div class="form-check"><input class="form-check-input" type="radio" name="jawaban_benar" value="C"> C</div>
                        <div class="form-check"><input class="form-check-input" type="radio" name="jawaban_benar" value="D"> D</div>
                    </div>
                </form>
            </div>
        `;
        btn.closest('.card').insertAdjacentHTML('afterend', html);
    }

    function simpanSoalAjax(event, formElement) {
        event.preventDefault();
        
        let formData = new FormData(formElement);
        let submitBtn = formElement.querySelector('button[type="submit"]');
        let originalText = submitBtn.innerHTML;
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Menyimpan...';
        
        fetch(formElement.action, {
            method: 'POST',
            body: formData
        })
        .then(response => {
            Swal.fire({
                title: 'Berhasil!',
                text: 'Soal berhasil disimpan.',
                icon: 'success',
                showConfirmButton: true,
                confirmButtonText: 'OK',
                confirmButtonColor: '#ce2127',
                padding: '2rem',
                customClass: {
                    popup: 'rounded-4 shadow-lg border-0',
                    confirmButton: 'rounded-pill px-5 py-2 fw-bold text-uppercase'
                }
            }).then(() => {
                location.reload();
            });
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                title: 'Gagal!',
                text: 'Terjadi kesalahan saat menyimpan soal.',
                icon: 'error',
                showConfirmButton: true,
                confirmButtonText: 'OK',
                confirmButtonColor: '#ce2127',
                padding: '2rem',
                customClass: {
                    popup: 'rounded-4 shadow-lg border-0',
                    confirmButton: 'rounded-pill px-5 py-2 fw-bold text-uppercase'
                }
            });
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        });
    }

    function confirmSaveAll() {
        let forms = document.getElementById('soalContainer').querySelectorAll('form');
        if(forms.length === 0) {
            Swal.fire({
                title: 'Peringatan',
                text: 'Tidak ada soal untuk disimpan.',
                icon: 'warning',
                showConfirmButton: true,
                confirmButtonText: 'OK',
                confirmButtonColor: '#ce2127',
                customClass: { popup: 'rounded-4 shadow-lg border-0', confirmButton: 'rounded-pill px-5 py-2 fw-bold text-uppercase' }
            });
            return;
        }
        
        Swal.fire({
            title: 'Simpan Seluruh Soal?',
            text: 'Anda yakin ingin menyimpan seluruh perubahan pada soal kuis ini?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#ce2127',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Simpan',
            cancelButtonText: 'Batal',
            customClass: { popup: 'rounded-4 shadow-lg border-0', confirmButton: 'rounded-pill px-5 py-2 fw-bold text-uppercase', cancelButton: 'rounded-pill px-5 py-2 fw-bold text-uppercase ms-3' }
        }).then((result) => {
            if (result.isConfirmed) {
                let promises = [];
                forms.forEach(form => {
                    let formData = new FormData(form);
                    promises.push(
                        fetch(form.action, {
                            method: 'POST',
                            body: formData
                        })
                    );
                });

                Promise.all(promises).then(() => {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: 'Seluruh perubahan berhasil disimpan.',
                        icon: 'success',
                        showConfirmButton: true,
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#ce2127',
                        padding: '2rem',
                        customClass: {
                            popup: 'rounded-4 shadow-lg border-0',
                            confirmButton: 'rounded-pill px-5 py-2 fw-bold text-uppercase'
                        }
                    }).then(() => {
                        location.reload();
                    });
                }).catch(err => {
                    Swal.fire({
                        title: 'Gagal!',
                        text: 'Sebagian soal gagal disimpan.',
                        icon: 'error',
                        showConfirmButton: true,
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#ce2127',
                        customClass: { popup: 'rounded-4 shadow-lg border-0', confirmButton: 'rounded-pill px-5 py-2 fw-bold text-uppercase' }
                    });
                });
            }
        });
    }

    function autofillMateri() {
        var form = document.getElementById('formMateri');
        if (!form) return;

        form.querySelector('[name="judul"]').value = 'Modul 1 - Pengenalan K3 di Rumah Sakit';
        form.querySelector('[name="segmen"]').value = '1';

        var sesiSelect = form.querySelector('[name="sesi_id"]');
        if (sesiSelect && sesiSelect.options.length > 1) {
            sesiSelect.value = sesiSelect.options[1].value;
        }

        var tipeSelect = form.querySelector('[name="tipe"]');
        tipeSelect.value = 'link';
        tipeSelect.dispatchEvent(new Event('change'));

        form.querySelector('[name="deskripsi"]').value = 'Materi pengenalan dasar keselamatan dan kesehatan kerja (K3) di lingkungan rumah sakit, termasuk identifikasi bahaya dan prosedur keselamatan.';

        setTimeout(function() {
            var linkInput = document.getElementById('linkMateriInput');
            if (linkInput) {
                linkInput.value = 'https://drive.google.com/file/d/1234567890/view';
            }
        }, 100);

        showToast('Form materi berhasil diisi dengan data testing!', 'success');
    }

    function autofillQuizSoal() {
        var container = document.getElementById('soalContainer');
        if (!container) return;

        var evaluasi_id = document.getElementById('current_evaluasi_id').value;

        var soalData = [
            {
                pertanyaan: 'Apa yang dimaksud dengan K3 (Keselamatan dan Kesehatan Kerja)?',
                opsi_a: 'Sistem pengelolaan risiko di tempat kerja',
                opsi_b: 'Program pelatihan untuk karyawan baru',
                opsi_c: 'Jenis asuransi kerja',
                opsi_d: 'Prosedur administrasi rumah sakit',
                jawaban: 'A'
            },
            {
                pertanyaan: 'Alat Pelindung Diri (APD) wajib digunakan di area mana?',
                opsi_a: 'Hanya di kantor administrasi',
                opsi_b: 'Di area rawat inap dan ruang operasi',
                opsi_c: 'Di parkiran rumah sakit',
                opsi_d: 'Di kantin dan area istirahat',
                jawaban: 'B'
            },
            {
                pertanyaan: 'Apa langkah pertama yang harus dilakukan saat terjadi kebakaran di rumah sakit?',
                opsi_a: 'Menelepon direktur',
                opsi_b: 'Memadamkan api dengan alat seadanya',
                opsi_c: 'Mengaktifkan alarm dan evakuasi pasien',
                opsi_d: 'Mengambil barang berharga terlebih dahulu',
                jawaban: 'C'
            },
            {
                pertanyaan: 'Bentuk-bentuk energi berbahaya di rumah sakit meliputi...',
                opsi_a: 'Hanya listrik dan panas',
                opsi_b: 'Listrik, panas, bunyi, radiasi, dan bahan kimia',
                opsi_c: 'Hanya bahan kimia dan radiasi',
                opsi_d: 'Hanya panas dan bunyi',
                jawaban: 'B'
            },
            {
                pertanyaan: 'Siapa yang bertanggung jawab atas penerapan K3 di unit kerja?',
                opsi_a: 'Hanya bagian HRD',
                opsi_b: 'Hanya pimpinan unit kerja',
                opsi_c: 'Seluruh pegawai dan pimpinan unit kerja',
                opsi_d: 'Hanya petugas keamanan',
                jawaban: 'C'
            }
        ];

        container.innerHTML = '';
        var evaluasiId = document.getElementById('current_evaluasi_id').value;

        soalData.forEach(function(soal, index) {
            var html = `
                <div class="card border-0 shadow-sm rounded-lg mb-3 p-4">
                    <form action="<?= base_url('pelatihan/admin/pelatihan/evaluasi_soal/simpan') ?>" method="POST" enctype="multipart/form-data" onsubmit="simpanSoalAjax(event, this)">
                        <input type="hidden" name="id_soal" value="">
                        <input type="hidden" name="ujian_id" value="${evaluasiId}">
                        
                        <div class="d-flex justify-content-between mb-3 align-items-center">
                            <span class="badge bg-primary rounded-pill">Pertanyaan #${index + 1}</span>
                            <div class="btn-group">
                                <button type="button" class="btn btn-link text-primary p-0 me-3 small fw-bold text-decoration-none" onclick="tambahSoalSetelah(this)"><i class="fas fa-plus me-1"></i> Tambah</button>
                                <button type="submit" class="btn btn-link text-success p-0 me-3 small fw-bold text-decoration-none"><i class="fas fa-save me-1"></i> Simpan</button>
                                <button type="button" class="btn btn-link text-danger p-0 small fw-bold text-decoration-none" onclick="this.closest('.card').remove()"><i class="fas fa-trash me-1"></i> Hapus</button>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <textarea name="pertanyaan" class="form-control form-control-sm border bg-white" rows="2" required>${soal.pertanyaan}</textarea>
                        </div>
                        <div class="row g-2">
                            <div class="col-6"><div class="input-group input-group-sm"><span class="input-group-text bg-white fw-bold text-primary">A</span><input type="text" name="opsi_a" class="form-control" value="${soal.opsi_a}" required></div></div>
                            <div class="col-6"><div class="input-group input-group-sm"><span class="input-group-text bg-white fw-bold text-primary">B</span><input type="text" name="opsi_b" class="form-control" value="${soal.opsi_b}" required></div></div>
                            <div class="col-6"><div class="input-group input-group-sm"><span class="input-group-text bg-white fw-bold text-primary">C</span><input type="text" name="opsi_c" class="form-control" value="${soal.opsi_c}" required></div></div>
                            <div class="col-6"><div class="input-group input-group-sm"><span class="input-group-text bg-white fw-bold text-primary">D</span><input type="text" name="opsi_d" class="form-control" value="${soal.opsi_d}" required></div></div>
                        </div>
                        <div class="mt-3 small fw-bold text-muted">Kunci Jawaban:</div>
                        <div class="d-flex gap-3 mt-1">
                            <div class="form-check"><input class="form-check-input" type="radio" name="jawaban_benar" value="A" ${soal.jawaban === 'A' ? 'checked' : ''} required> A</div>
                            <div class="form-check"><input class="form-check-input" type="radio" name="jawaban_benar" value="B" ${soal.jawaban === 'B' ? 'checked' : ''}> B</div>
                            <div class="form-check"><input class="form-check-input" type="radio" name="jawaban_benar" value="C" ${soal.jawaban === 'C' ? 'checked' : ''}> C</div>
                            <div class="form-check"><input class="form-check-input" type="radio" name="jawaban_benar" value="D" ${soal.jawaban === 'D' ? 'checked' : ''}> D</div>
                        </div>
                    </form>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
        });

        document.getElementById('evaluasi_kkm').value = '70';
        showToast('5 soal K3 berhasil ditambahkan (data testing)!', 'success');
    }
</script>
<?= $this->endSection() ?>
