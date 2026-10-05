<?= $this->include('Pendidikan/Institusi/layout/header') ?>
<?= $this->include('Pendidikan/Institusi/layout/sidebar') ?>

<div class="row">
    <div class="col-12 mb-4">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h4 class="fw-bold">Form Pengajuan Mahasiswa</h4>
                <p class="text-muted mb-0">Isi data pengajuan dan mahasiswa, lalu unggah dokumen pendukung untuk diajukan ke Admin Diklat.</p>
            </div>
            <a href="<?= base_url('pendidikan/institusi/pengajuan/status') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-list me-1"></i> Daftar Pengajuan
            </a>
        </div>
    </div>
</div>

<?php if(session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if(session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<form id="formPengajuanMahasiswa" action="<?= base_url('pendidikan/institusi/pengajuan/store') ?>" method="POST" enctype="multipart/form-data" novalidate>
    <div class="row">
        <!-- Data Pengajuan -->
        <div class="col-lg-12">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-light py-3 border-0">
                    <h6 class="mb-0 fw-bold text-danger"><i class="fas fa-info-circle me-2"></i> Data Pengajuan</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nama Institusi</label>
                            <input type="text" class="form-control" name="institusi" value="<?= esc($profil['nama_institusi']) ?>" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Jenis Program</label>
                            <select class="form-select" name="jenis_program" required>
                                <option value="" disabled selected>Pilih Program...</option>
                                <option value="Akademik">Akademik</option>
                                <option value="Spesialis (Residen)">Spesialis (Residen)</option>
                                <option value="Profesi (D1)">Profesi (D1)</option>
                                <option value="Koas">Koas</option>
                                <option value="Magang">Magang</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Profesi</label>
                            <select class="form-select" name="profesi_id" required>
                                <option value="" disabled selected>Pilih Profesi...</option>
                                <?php foreach($list_profesi as $profesi): ?>
                                    <option value="<?= $profesi['id_profesi'] ?>"><?= esc($profesi['nama_profesi']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Program Studi</label>
                            <input type="text" class="form-control" name="prodi_asal" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Tanggal Mulai</label>
                            <input type="date" class="form-control" name="tgl_mulai" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Tanggal Selesai</label>
                            <input type="date" class="form-control" name="tgl_selesai" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Nama Penanggung Jawab</label>
                            <input type="text" class="form-control" name="nama_pj" value="<?= esc($profil['nama_kontak']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nomor HP Penanggung Jawab</label>
                            <input type="text" class="form-control" name="hp_pj" value="<?= esc($profil['no_telp']) ?>" required>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Mahasiswa -->
        <div class="col-lg-12">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-light py-3 border-0 d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <h6 class="mb-0 fw-bold text-danger"><i class="fas fa-users me-2"></i> Data Mahasiswa</h6>
                    <button type="button" class="btn btn-sm btn-danger shadow-sm" id="btnAddMahasiswa">
                        <i class="fas fa-plus me-1"></i> Tambah Mahasiswa
                    </button>
                </div>
                <div class="card-body" id="mahasiswaList">
                    <div class="student-row border rounded p-3 mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="fw-bold">Mahasiswa <span class="student-number">1</span></span>
                            <button type="button" class="btn btn-sm btn-light text-danger btn-remove-mhs disabled" aria-label="Hapus mahasiswa">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        <div class="row g-3">
                            <div class="col-12 col-md-6 col-xl-4"><label class="form-label small fw-bold">Nama Lengkap <span class="text-danger">*</span></label><input type="text" class="form-control" name="mhs_nama[]" required></div>
                            <div class="col-12 col-md-6 col-xl-4"><label class="form-label small fw-bold">NIM <span class="text-danger">*</span></label><input type="text" class="form-control" name="mhs_nim[]" required></div>
                            <div class="col-12 col-md-6 col-xl-4"><label class="form-label small fw-bold">Tanggal Lahir <span class="text-danger">*</span></label><input type="date" class="form-control" name="mhs_tgl_lahir[]" required></div>
                            <div class="col-12 col-md-6 col-xl-4"><label class="form-label small fw-bold">Jenis Kelamin <span class="text-danger">*</span></label><select class="form-select" name="mhs_jk[]"><option value="L">Laki-laki</option><option value="P">Perempuan</option></select></div>
                            <div class="col-12 col-md-6 col-xl-4"><label class="form-label small fw-bold">Semester</label><input type="number" class="form-control" name="mhs_semester[]" min="1"></div>
                            <div class="col-12 col-md-6 col-xl-4"><label class="form-label small fw-bold">No. Handphone</label><input type="text" class="form-control" name="mhs_hp[]" placeholder="08xxx"></div>
                            <div class="col-12"><label class="form-label small fw-bold">Email <span class="text-danger">*</span></label><input type="email" class="form-control" name="mhs_email[]" placeholder="Email" required></div>
                            <div class="col-12 col-lg-4"><label class="form-label small fw-bold">Pas Foto <span class="text-danger">*</span></label><input type="file" class="form-control" name="mhs_foto_1" accept=".jpg,.jpeg,.png" required><small class="text-muted">JPG/PNG, Maks 2MB</small></div>
                            <div class="col-12 col-lg-4"><label class="form-label small fw-bold">Ijazah Terakhir <span class="text-danger">*</span></label><input type="file" class="form-control" name="mhs_ijazah_1" accept=".pdf" required><small class="text-muted">PDF, Maks 2MB</small></div>
                            <div class="col-12 col-lg-4"><label class="form-label small fw-bold">Surat Ket. Aktif <span class="text-danger">*</span></label><input type="file" class="form-control" name="mhs_sk_1" accept=".pdf" required><small class="text-muted">PDF, Maks 2MB</small></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dokumen Pendukung -->
        <div class="col-lg-12">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-light py-3 border-0">
                    <h6 class="mb-0 fw-bold text-danger"><i class="fas fa-file-upload me-2"></i> Dokumen Pendukung</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">Ukuran maksimal setiap berkas: 2 MB.</p>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="p-3 border rounded bg-light h-100">
                                <label class="form-label small fw-bold">1. Proposal (PDF) <span class="text-danger">*</span></label>
                                <input type="file" class="form-control form-control-sm" name="doc_proposal" accept=".pdf" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 border rounded bg-light h-100">
                                <label class="form-label small fw-bold">2. Surat Pengantar (PDF) <span class="text-danger">*</span></label>
                                <input type="file" class="form-control form-control-sm" name="doc_pengantar" accept=".pdf" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 border rounded bg-light h-100">
                                <label class="form-label small fw-bold">3. Log Book (PDF) <span class="text-danger">*</span></label>
                                <input type="file" class="form-control form-control-sm" name="doc_logbook" accept=".pdf" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 border rounded bg-light h-100">
                                <label class="form-label small fw-bold">4. Buku Panduan (PDF) <span class="text-danger">*</span></label>
                                <input type="file" class="form-control form-control-sm" name="doc_panduan" accept=".pdf" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 border rounded bg-light h-100">
                                <label class="form-label small fw-bold">5. Daftar Nama Mahasiswa (PDF/XLS/XLSX) <span class="text-danger">*</span></label>
                                <input type="file" class="form-control form-control-sm" name="doc_daftar_mhs" accept=".pdf,.xls,.xlsx" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 border rounded bg-light h-100">
                                <label class="form-label small fw-bold">6. Surat Level Kompetensi (PDF) <span class="text-danger">*</span></label>
                                <input type="file" class="form-control form-control-sm" name="doc_kompetensi" accept=".pdf" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 border rounded bg-light h-100">
                                <label class="form-label small fw-bold">7. SK Pembimbing (PDF)</label>
                                <input type="file" class="form-control form-control-sm" name="doc_sk_pembimbing" accept=".pdf">
                                <div class="text-muted mt-1" style="font-size: 0.75rem;">Opsional</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 border rounded bg-light h-100">
                                <label class="form-label small fw-bold">8. Bukti Pembayaran Batch (PDF/JPG/PNG)</label>
                                <input type="file" class="form-control form-control-sm" name="doc_bukti_bayar" accept=".pdf,.jpg,.jpeg,.png">
                                <div class="text-muted mt-1" style="font-size: 0.75rem;">Opsional (bisa dikosongkan dahulu, bayar nanti via dashboard)</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 border rounded bg-light h-100">
                                <label class="form-label small fw-bold">9. Dokumen Penilaian (PDF) <span class="text-danger">*</span></label>
                                <input type="file" class="form-control form-control-sm" name="doc_penilaian" accept=".pdf" required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-12 mb-5">
            <div class="card border-0 bg-transparent">
                <div class="card-body p-0 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-light px-4">Batal</button>
                    <button type="submit" class="btn btn-danger px-5 fw-bold">KIRIM PENGAJUAN</button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    function autofillPengajuan() {
        document.querySelector('select[name="jenis_program"]').value = 'Koas';
        document.querySelector('input[name="prodi_asal"]').value = 'Profesi Dokter';
        document.querySelector('input[name="tgl_mulai"]').value = '2026-07-01';
        document.querySelector('input[name="tgl_selesai"]').value = '2026-08-31';
        
        const randomNum = Math.floor(Math.random() * 10000);
        document.querySelector('input[name="mhs_nama[]"]').value = 'Mahasiswa Testing ' + randomNum;
        document.querySelector('input[name="mhs_nim[]"]').value = 'NIM' + randomNum;
        document.querySelector('input[name="mhs_tgl_lahir[]"]').value = '2000-01-01';
        document.querySelector('select[name="mhs_jk[]"]').value = 'L';
        document.querySelector('input[name="mhs_semester[]"]').value = '7';
        document.querySelector('input[name="mhs_hp[]"]').value = '081234' + randomNum;
        document.querySelector('input[name="mhs_email[]"]').value = 'mhs' + randomNum + '@testing.com';
        
        fetch('<?= base_url('testing_dummy.png') ?>')
            .then(res => res.blob())
            .then(blob => {
                const file = new File([blob], 'dummy_testing.png', { type: 'image/png' });
                
                const fileInputs = document.querySelectorAll('input[type="file"]');
                fileInputs.forEach(input => {
                    const dt = new DataTransfer();
                    dt.items.add(file);
                    input.files = dt.files;
                });
                Swal.fire({icon: 'success', title: 'Berhasil', text: 'Data teks & dokumen berhasil diisi secara otomatis menggunakan gambar testing!'});
            })
            .catch(err => {
                console.error(err);
                Swal.fire({icon: 'warning', title: 'Gambar testing gagal dimuat', text: 'Data teks berhasil diisi! Tetapi gagal memuat gambar testing_dummy.png'});
            });
    }
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('formPengajuanMahasiswa');
        const list = document.getElementById('mahasiswaList');
        const btnAdd = document.getElementById('btnAddMahasiswa');
        const submitButton = form.querySelector('button[type="submit"]');
        let count = 1;

        const maxFileSize = 2 * 1024 * 1024;
        const fileMimeTypes = {
            '.jpg': 'image/jpeg', '.jpeg': 'image/jpeg', '.png': 'image/png',
            '.pdf': 'application/pdf', '.xls': 'application/vnd.ms-excel',
            '.xlsx': 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        };

        function inputLabel(input) {
            const label = input.parentElement.querySelector('label')?.textContent.replace('*', '').trim() || 'Input';
            const studentNumber = input.closest('.student-row')?.querySelector('.student-number')?.textContent;
            return studentNumber ? `${label} mahasiswa ke-${studentNumber}` : label;
        }

        function fileError(input) {
            const file = input.files[0];
            if (!file) return null;

            const extensions = input.accept.toLowerCase().split(',');
            const extension = file.name.slice(file.name.lastIndexOf('.')).toLowerCase();
            const label = inputLabel(input);
            const formats = extensions.map(value => value.slice(1).toUpperCase()).join('/');

            if (!extensions.includes(extension) || (file.type && file.type !== fileMimeTypes[extension])) {
                return `${label}: pilih file ${formats}.`;
            }
            if (file.size > maxFileSize) {
                return `${label}: ukuran file maksimal 2 MB.`;
            }
            return null;
        }

        function clearFieldError(input) {
            input.classList.remove('is-invalid');
            if (input.nextElementSibling?.classList.contains('field-error')) {
                input.nextElementSibling.remove();
            }
        }

        function showFieldError(input, message) {
            if (!input) {
                Swal.fire({ icon: 'error', title: 'Periksa pengajuan', text: message });
                return;
            }
            clearFieldError(input);
            input.classList.add('is-invalid');
            const feedback = document.createElement('div');
            feedback.className = 'invalid-feedback d-block field-error';
            feedback.textContent = message;
            input.insertAdjacentElement('afterend', feedback);
            input.scrollIntoView({ behavior: 'smooth', block: 'center' });
            input.focus({ preventScroll: true });
        }

        function validateInput(input) {
            if (input.readOnly) return null;
            const value = input.value.trim();
            const label = inputLabel(input);
            if (input.required && (input.type === 'file' ? !input.files.length : !value)) {
                return `${label} wajib ${input.type === 'file' ? 'diunggah' : 'diisi'}.`;
            }
            if (input.type === 'file') return fileError(input);
            if (input.validity.typeMismatch || input.validity.badInput) return `${label} tidak valid.`;

            const name = input.name;
            const personName = /^\p{L}[\p{L}\s.,'-]{1,149}$/u;
            const phone = /^[0-9]{8,20}$/;
            if ((name === 'nama_pj' || name === 'mhs_nama[]') && !personName.test(value)) return `${label} harus berisi 2–150 karakter berupa huruf dan tanda baca nama.`;
            if (name === 'prodi_asal' && value.length > 150) return 'Program Studi maksimal 150 karakter.';
            if ((name === 'hp_pj' || name === 'mhs_hp[]') && value && !phone.test(value)) return `${label} harus berisi 8–20 digit angka.`;
            if (name === 'mhs_nim[]' && !/^[A-Za-z0-9.\/-]{3,30}$/.test(value)) return `${label} harus berisi 3–30 karakter: huruf, angka, titik, garis miring, atau tanda hubung.`;
            if (name === 'mhs_semester[]' && value && (!Number.isInteger(Number(value)) || Number(value) < 1 || Number(value) > 20)) return `${label} harus berupa angka 1–20.`;
            if (name === 'tgl_selesai' && value < form.elements.namedItem('tgl_mulai').value) return 'Tanggal Selesai tidak boleh lebih awal dari Tanggal Mulai.';
            if (name === 'mhs_tgl_lahir[]') {
                const today = new Date();
                const todayLocal = `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`;
                if (value >= todayLocal) return `${label} harus sebelum hari ini.`;
            }
            return null;
        }

        function serverFieldInput(field) {
            const indexedField = /^(mhs_[a-z_]+)\[(\d+)\]$/.exec(field || '');
            if (indexedField) {
                return form.querySelectorAll(`[name="${indexedField[1]}[]"]`)[Number(indexedField[2])];
            }
            return form.elements.namedItem(field);
        }

        form.addEventListener('beforeinput', function(event) {
            if ((event.target.name === 'nama_pj' || event.target.name === 'mhs_nama[]') && event.data && /^\p{N}+$/u.test(event.data)) {
                event.preventDefault();
            }
        });

        form.addEventListener('input', function(event) {
            const input = event.target;
            if (!input.matches('input, select')) return;
            if (input.name === 'nama_pj' || input.name === 'mhs_nama[]') {
                const original = input.value;
                const withoutNumbers = original.replace(/\p{N}/gu, '');
                if (withoutNumbers !== original) {
                    const cursor = input.selectionStart ?? original.length;
                    const newCursor = original.slice(0, cursor).replace(/\p{N}/gu, '').length;
                    input.value = withoutNumbers;
                    input.setSelectionRange(newCursor, newCursor);
                }
            }
            clearFieldError(input);
        });

        form.addEventListener('change', function(event) {
            const input = event.target;
            if (!input.matches('input, select')) return;
            clearFieldError(input);
            if (input.type === 'file') {
                const error = fileError(input);
                if (error) showFieldError(input, error);
            }
        });

        form.addEventListener('submit', async function(event) {
            event.preventDefault();
            if (submitButton.disabled) return;

            const students = list.querySelectorAll('.student-row');
            if (students.length > 200) {
                showFieldError(students[200].querySelector('[name="mhs_nama[]"]'), 'Jumlah mahasiswa maksimal 200.');
                return;
            }
            for (const input of form.querySelectorAll('input, select')) {
                const error = validateInput(input);
                if (!error) continue;
                showFieldError(input, error);
                return;
            }

            submitButton.disabled = true;
            try {
                const confirmation = await Swal.fire({
                    icon: 'question',
                    title: 'Kirim pengajuan ini?',
                    text: 'Pastikan data mahasiswa dan dokumen pendukung sudah benar sebelum dikirim ke Admin.',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, kirim pengajuan',
                    cancelButtonText: 'Periksa lagi',
                    confirmButtonColor: '#c62828',
                    cancelButtonColor: '#6c757d'
                });
                if (!confirmation.isConfirmed) return;

                const response = await fetch(form.action, {
                    method: 'POST',
                    body: new FormData(form),
                    credentials: 'same-origin',
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                });
                const result = await response.json();
                if (result.success && result.redirect) {
                    window.location.assign(result.redirect);
                    return;
                }
                if (result.redirect && response.status === 401) {
                    Swal.fire({ icon: 'error', title: 'Sesi berakhir', text: result.message }).then(() => window.location.assign(result.redirect));
                    return;
                }
                showFieldError(serverFieldInput(result.field), result.message || 'Pengajuan belum dapat dikirim. Periksa kembali data yang diisi.');
            } catch (error) {
                Swal.fire({ icon: 'error', title: 'Gagal mengirim pengajuan', text: 'Koneksi atau server bermasalah. Data yang sudah diisi tetap tersedia; coba lagi.' });
            } finally {
                submitButton.disabled = false;
            }
        });

        btnAdd.addEventListener('click', function() {
            count++;
            
            const rowHtml = `
                <div class="student-row border rounded p-3 mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="fw-bold">Mahasiswa <span class="student-number">${count}</span></span>
                        <button type="button" class="btn btn-sm btn-light text-danger btn-remove-mhs" aria-label="Hapus mahasiswa">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    <div class="row g-3">
                        <div class="col-12 col-md-6 col-xl-4"><label class="form-label small fw-bold">Nama Lengkap <span class="text-danger">*</span></label><input type="text" class="form-control" name="mhs_nama[]" required></div>
                        <div class="col-12 col-md-6 col-xl-4"><label class="form-label small fw-bold">NIM <span class="text-danger">*</span></label><input type="text" class="form-control" name="mhs_nim[]" required></div>
                        <div class="col-12 col-md-6 col-xl-4"><label class="form-label small fw-bold">Tanggal Lahir <span class="text-danger">*</span></label><input type="date" class="form-control" name="mhs_tgl_lahir[]" required></div>
                        <div class="col-12 col-md-6 col-xl-4"><label class="form-label small fw-bold">Jenis Kelamin <span class="text-danger">*</span></label><select class="form-select" name="mhs_jk[]"><option value="L">Laki-laki</option><option value="P">Perempuan</option></select></div>
                        <div class="col-12 col-md-6 col-xl-4"><label class="form-label small fw-bold">Semester</label><input type="number" class="form-control" name="mhs_semester[]" min="1"></div>
                        <div class="col-12 col-md-6 col-xl-4"><label class="form-label small fw-bold">No. Handphone</label><input type="text" class="form-control" name="mhs_hp[]" placeholder="08xxx"></div>
                        <div class="col-12"><label class="form-label small fw-bold">Email <span class="text-danger">*</span></label><input type="email" class="form-control" name="mhs_email[]" placeholder="Email" required></div>
                        <div class="col-12 col-lg-4"><label class="form-label small fw-bold">Pas Foto <span class="text-danger">*</span></label><input type="file" class="form-control" name="mhs_foto_${count}" accept=".jpg,.jpeg,.png" required><small class="text-muted">JPG/PNG, Maks 2MB</small></div>
                        <div class="col-12 col-lg-4"><label class="form-label small fw-bold">Ijazah Terakhir <span class="text-danger">*</span></label><input type="file" class="form-control" name="mhs_ijazah_${count}" accept=".pdf" required><small class="text-muted">PDF, Maks 2MB</small></div>
                        <div class="col-12 col-lg-4"><label class="form-label small fw-bold">Surat Ket. Aktif <span class="text-danger">*</span></label><input type="file" class="form-control" name="mhs_sk_${count}" accept=".pdf" required><small class="text-muted">PDF, Maks 2MB</small></div>
                    </div>
                </div>
            `;

            list.insertAdjacentHTML('beforeend', rowHtml);
            updateRemoveButtons();
        });

        list.addEventListener('click', function(e) {
            if (e.target.classList.contains('btn-remove-mhs') || e.target.closest('.btn-remove-mhs')) {
                const btn = e.target.classList.contains('btn-remove-mhs') ? e.target : e.target.closest('.btn-remove-mhs');
                if (!btn.classList.contains('disabled')) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Hapus mahasiswa?',
                        text: 'Data mahasiswa ini akan dikeluarkan dari pengajuan.',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, hapus',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: '#c62828',
                        cancelButtonColor: '#6c757d'
                    }).then(function(result) {
                        if (!result.isConfirmed) return;
                        btn.closest('.student-row').remove();
                        updateRowNumbers();
                        updateRemoveButtons();
                    });
                }
            }
        });

        function updateRowNumbers() {
            const rows = list.querySelectorAll('.student-row');
            count = 0;
            rows.forEach((row, index) => {
                row.querySelector('.student-number').innerText = index + 1;
                count = index + 1;
                
                const fotoInput = row.querySelector('input[name^="mhs_foto_"]');
                const ijazahInput = row.querySelector('input[name^="mhs_ijazah_"]');
                const skInput = row.querySelector('input[name^="mhs_sk_"]');
                
                if (fotoInput) fotoInput.name = `mhs_foto_${count}`;
                if (ijazahInput) ijazahInput.name = `mhs_ijazah_${count}`;
                if (skInput) skInput.name = `mhs_sk_${count}`;
            });
        }

        function updateRemoveButtons() {
            const btns = list.querySelectorAll('.btn-remove-mhs');
            if (btns.length === 1) {
                btns[0].classList.add('disabled');
            } else {
                btns.forEach(btn => btn.classList.remove('disabled'));
            }
        }
    });
</script>

<?= $this->include('Pendidikan/Institusi/layout/footer') ?>
