<?= $this->include('Pendidikan/Institusi/layout/header') ?>
<?= $this->include('Pendidikan/Institusi/layout/sidebar') ?>

<div class="row">
    <div class="col-12 mb-4">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h4 class="fw-bold">Form Pengajuan Mahasiswa</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('pendidikan/institusi/dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item active">Ajukan Mahasiswa</li>
                    </ol>
                </nav>
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

<form action="<?= base_url('pendidikan/institusi/pengajuan/store') ?>" method="POST" enctype="multipart/form-data">
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
                <div class="card-header bg-light py-3 border-0 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-danger"><i class="fas fa-users me-2"></i> Data Mahasiswa</h6>
                    <button type="button" class="btn btn-sm btn-danger shadow-sm" id="btnAddMahasiswa">
                        <i class="fas fa-plus me-1"></i> Tambah Mahasiswa
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle" id="tableMahasiswa" style="min-width: 1100px;">
                            <thead class="table-light text-muted text-center small text-uppercase fw-bold text-nowrap">
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>Nama Lengkap <span class="text-danger">*</span></th>
                                    <th>NIM <span class="text-danger">*</span></th>
                                    <th>Tanggal Lahir <span class="text-danger">*</span></th>
                                    <th>Jenis Kelamin <span class="text-danger">*</span></th>
                                    <th style="width: 80px;">Semester</th>
                                    <th>No. Handphone</th>
                                    <th>Email <span class="text-danger">*</span></th>
                                    <th>Pas Foto <span class="text-danger">*</span></th>
                                    <th>Ijazah Terakhir <span class="text-danger">*</span></th>
                                    <th>Surat Ket. Aktif <span class="text-danger">*</span></th>
                                    <th style="width: 50px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="mahasiswaList">
                                <tr class="student-row">
                                    <td class="text-center student-number">1</td>
                                    <td><input type="text" class="form-control form-control-sm" name="mhs_nama[]" required></td>
                                    <td><input type="text" class="form-control form-control-sm" name="mhs_nim[]" required></td>
                                    <td><input type="date" class="form-control form-control-sm" name="mhs_tgl_lahir[]" required></td>
                                    <td>
                                        <select class="form-select form-select-sm" name="mhs_jk[]" style="min-width: 100px;">
                                            <option value="L">Laki-laki</option>
                                            <option value="P">Perempuan</option>
                                        </select>
                                    </td>
                                    <td><input type="number" class="form-control form-control-sm" name="mhs_semester[]" min="1"></td>
                                    <td><input type="text" class="form-control form-control-sm" name="mhs_hp[]" placeholder="08xxx"></td>
                                    <td><input type="email" class="form-control form-control-sm" name="mhs_email[]" placeholder="Email" required></td>
                                    <td>
                                        <input type="file" class="form-control form-control-sm" name="mhs_foto_1" required>
                                        <small class="text-muted" style="font-size: 9px;">JPG/PNG, Maks 2MB</small>
                                    </td>
                                    <td>
                                        <input type="file" class="form-control form-control-sm" name="mhs_ijazah_1" required>
                                        <small class="text-muted" style="font-size: 9px;">PDF, Maks 2MB</small>
                                    </td>
                                    <td>
                                        <input type="file" class="form-control form-control-sm" name="mhs_sk_1" required>
                                        <small class="text-muted" style="font-size: 9px;">PDF, Maks 2MB</small>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-light text-danger btn-remove-mhs disabled">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
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
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="p-3 border rounded bg-light h-100">
                                <label class="form-label small fw-bold">1. Proposal (PDF) <span class="text-danger">*</span></label>
                                <input type="file" class="form-control form-control-sm" name="doc_proposal" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 border rounded bg-light h-100">
                                <label class="form-label small fw-bold">2. Surat Pengantar (PDF) <span class="text-danger">*</span></label>
                                <input type="file" class="form-control form-control-sm" name="doc_pengantar" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 border rounded bg-light h-100">
                                <label class="form-label small fw-bold">3. Log Book (PDF) <span class="text-danger">*</span></label>
                                <input type="file" class="form-control form-control-sm" name="doc_logbook" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 border rounded bg-light h-100">
                                <label class="form-label small fw-bold">4. Buku Panduan (PDF) <span class="text-danger">*</span></label>
                                <input type="file" class="form-control form-control-sm" name="doc_panduan" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 border rounded bg-light h-100">
                                <label class="form-label small fw-bold">5. Daftar Nama Mahasiswa (PDF/XLS) <span class="text-danger">*</span></label>
                                <input type="file" class="form-control form-control-sm" name="doc_daftar_mhs" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 border rounded bg-light h-100">
                                <label class="form-label small fw-bold">6. Surat Level Kompetensi (PDF) <span class="text-danger">*</span></label>
                                <input type="file" class="form-control form-control-sm" name="doc_kompetensi" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 border rounded bg-light h-100">
                                <label class="form-label small fw-bold">7. SK Pembimbing (PDF)</label>
                                <input type="file" class="form-control form-control-sm" name="doc_sk_pembimbing">
                                <div class="text-muted mt-1" style="font-size: 0.75rem;">Opsional</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 border rounded bg-light h-100">
                                <label class="form-label small fw-bold">8. Bukti Pembayaran Batch (PDF/IMG)</label>
                                <input type="file" class="form-control form-control-sm" name="doc_bukti_bayar">
                                <div class="text-muted mt-1" style="font-size: 0.75rem;">Opsional (bisa dikosongkan dahulu, bayar nanti via dashboard)</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 border rounded bg-light h-100">
                                <label class="form-label small fw-bold">9. Dokumen Penilaian (PDF) <span class="text-danger">*</span></label>
                                <input type="file" class="form-control form-control-sm" name="doc_penilaian" required>
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
                alert('Data teks & dokumen berhasil diisi secara otomatis menggunakan gambar testing!');
            })
            .catch(err => {
                console.error(err);
                alert('Data teks berhasil diisi! Tetapi gagal memuat gambar testing_dummy.png');
            });
    }
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const list = document.getElementById('mahasiswaList');
        const btnAdd = document.getElementById('btnAddMahasiswa');
        let count = 1;

        btnAdd.addEventListener('click', function() {
            count++;
            
            const rowHtml = `
                <tr class="student-row">
                    <td class="text-center student-number">${count}</td>
                    <td><input type="text" class="form-control form-control-sm" name="mhs_nama[]" required></td>
                    <td><input type="text" class="form-control form-control-sm" name="mhs_nim[]" required></td>
                    <td><input type="date" class="form-control form-control-sm" name="mhs_tgl_lahir[]" required></td>
                    <td>
                        <select class="form-select form-select-sm" name="mhs_jk[]" style="min-width: 100px;">
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </td>
                    <td><input type="number" class="form-control form-control-sm" name="mhs_semester[]" min="1"></td>
                    <td><input type="text" class="form-control form-control-sm" name="mhs_hp[]" placeholder="08xxx"></td>
                    <td><input type="email" class="form-control form-control-sm" name="mhs_email[]" placeholder="Email" required></td>
                    <td>
                        <input type="file" class="form-control form-control-sm" name="mhs_foto_${count}" required>
                        <small class="text-muted" style="font-size: 9px;">JPG/PNG, Maks 2MB</small>
                    </td>
                    <td>
                        <input type="file" class="form-control form-control-sm" name="mhs_ijazah_${count}" required>
                        <small class="text-muted" style="font-size: 9px;">PDF, Maks 2MB</small>
                    </td>
                    <td>
                        <input type="file" class="form-control form-control-sm" name="mhs_sk_${count}" required>
                        <small class="text-muted" style="font-size: 9px;">PDF, Maks 2MB</small>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-light text-danger btn-remove-mhs">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;

            list.insertAdjacentHTML('beforeend', rowHtml);
            updateRemoveButtons();
        });

        list.addEventListener('click', function(e) {
            if (e.target.classList.contains('btn-remove-mhs') || e.target.closest('.btn-remove-mhs')) {
                const btn = e.target.classList.contains('btn-remove-mhs') ? e.target : e.target.closest('.btn-remove-mhs');
                if (!btn.classList.contains('disabled')) {
                    btn.closest('tr').remove();
                    updateRowNumbers();
                    updateRemoveButtons();
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
