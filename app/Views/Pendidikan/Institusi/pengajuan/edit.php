<?= $this->include('Pendidikan/Institusi/layout/header') ?>
<?= $this->include('Pendidikan/Institusi/layout/sidebar') ?>

<div class="row">
    <div class="col-12 mb-4">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h4 class="fw-bold">Edit Pengajuan Mahasiswa</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('pendidikan/institusi/dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('pendidikan/institusi/pengajuan/status') ?>">Status Pengajuan</a></li>
                        <li class="breadcrumb-item active">Edit Pengajuan</li>
                    </ol>
                </nav>
            </div>
            <a href="<?= base_url('pendidikan/institusi/pengajuan/status') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>
</div>

<?php if (isset($pengajuan['catatan']) && $pengajuan['catatan']) : ?>
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-warning bg-light-warning">
                <div class="card-body">
                    <h6 class="fw-bold text-warning mb-2"><i class="fas fa-exclamation-triangle me-2"></i> Catatan Revisi Admin:</h6>
                    <p class="mb-0 text-dark"><?= $pengajuan['catatan'] ?></p>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<form action="<?= base_url('pendidikan/institusi/pengajuan/update/' . $pengajuan['id']) ?>" method="POST" enctype="multipart/form-data">
    <div class="row">
        <!-- Data Pengajuan -->
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold text-danger"><i class="fas fa-info-circle me-2"></i> Data Pengajuan</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nama Institusi</label>
                            <input type="text" class="form-control" name="institusi" value="<?= $pengajuan['institusi'] ?>" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Jenis Program</label>
                            <select class="form-select" name="jenis_program" required>
                                <option value="Akademik" <?= isset($pengajuan['jenis_program']) && $pengajuan['jenis_program'] == 'Akademik' ? 'selected' : '' ?>>Akademik</option>
                                <option value="Spesialis (Residen)" <?= isset($pengajuan['jenis_program']) && $pengajuan['jenis_program'] == 'Spesialis (Residen)' ? 'selected' : '' ?>>Spesialis (Residen)</option>
                                <option value="Profesi (D1)" <?= isset($pengajuan['jenis_program']) && $pengajuan['jenis_program'] == 'Profesi (D1)' ? 'selected' : '' ?>>Profesi (D1)</option>
                                <option value="Koas" <?= isset($pengajuan['jenis_program']) && $pengajuan['jenis_program'] == 'Koas' ? 'selected' : '' ?>>Koas</option>
                                <option value="Magang" <?= isset($pengajuan['jenis_program']) && $pengajuan['jenis_program'] == 'Magang' ? 'selected' : '' ?>>Magang</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Profesi</label>
                            <select class="form-select" name="profesi_id" required>
                                <option value="" disabled>Pilih Profesi...</option>
                                <?php foreach($list_profesi as $profesi): ?>
                                    <option value="<?= $profesi['id_profesi'] ?>" <?= (isset($pengajuan['id_profesi']) && $pengajuan['id_profesi'] == $profesi['id_profesi']) ? 'selected' : '' ?>><?= esc($profesi['nama_profesi']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Program Studi</label>
                            <input type="text" class="form-control" name="prodi_asal" value="<?= $pengajuan['prodi'] ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Tanggal Mulai</label>
                            <input type="date" class="form-control" name="tgl_mulai" value="<?= $pengajuan['tgl_mulai'] ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Tanggal Selesai</label>
                            <input type="date" class="form-control" name="tgl_selesai" value="<?= $pengajuan['tgl_selesai'] ?>" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nama Penanggung Jawab</label>
                            <input type="text" class="form-control" name="nama_pj" value="<?= $pengajuan['penanggung_jawab'] ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nomor HP Penanggung Jawab</label>
                            <input type="text" class="form-control" name="hp_pj" value="<?= $pengajuan['hp_pj'] ?>" required>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Mahasiswa -->
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-danger"><i class="fas fa-users me-2"></i> Data Mahasiswa</h6>
                    <button type="button" class="btn btn-sm btn-danger" id="btnAddMahasiswa">
                        <i class="fas fa-plus me-1"></i> Tambah Mahasiswa
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle" id="tableMahasiswa" style="min-width: 1100px;">
                            <thead class="table-light text-center small text-uppercase fw-bold text-nowrap">
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>Nama Lengkap <span class="text-danger">*</span></th>
                                    <th>NIM <span class="text-danger">*</span></th>
                                    <th>Tanggal Lahir <span class="text-danger">*</span></th>
                                    <th>Jenis Kelamin <span class="text-danger">*</span></th>
                                    <th style="width: 80px;">Semester</th>
                                    <th>No. Handphone</th>
                                    <th>Email</th>
                                    <th>Pas Foto</th>
                                    <th>Ijazah Terakhir</th>
                                    <th>Surat Ket. Aktif</th>
                                    <th style="width: 50px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="mahasiswaList">
                                <?php foreach ($pengajuan['mahasiswa'] as $index => $mhs) : ?>
                                    <tr class="student-row">
                                        <td class="text-center student-number"><?= $index + 1 ?></td>
                                        <td><input type="text" class="form-control form-control-sm" name="mhs_nama[]" value="<?= $mhs['nama'] ?>" required></td>
                                        <td><input type="text" class="form-control form-control-sm" name="mhs_nim[]" value="<?= $mhs['nim'] ?>" required></td>
                                        <td><input type="date" class="form-control form-control-sm" name="mhs_tgl_lahir[]" value="<?= $mhs['dob'] ?? '' ?>" required></td>
                                        <td>
                                            <select class="form-select form-select-sm" name="mhs_jk[]" style="min-width: 100px;">
                                            <option value="L" <?= $mhs['jk'] == 'L' ? 'selected' : '' ?>>Laki-laki</option>
                                            <option value="P" <?= $mhs['jk'] == 'P' ? 'selected' : '' ?>>Perempuan</option>
                                        </select>
                                    </td>
                                    <td><input type="number" class="form-control form-control-sm" name="mhs_semester[]" value="<?= $mhs['semester'] ?>" min="1"></td>
                                    <td><input type="text" class="form-control form-control-sm" name="mhs_hp[]" value="<?= $mhs['hp'] ?>" placeholder="08xxx"></td>
                                        <td><input type="email" class="form-control form-control-sm" name="mhs_email[]" value="<?= $mhs['email'] ?>" placeholder="Email"></td>
                                        <td>
                                            <input type="file" class="form-control form-control-sm mb-1" name="mhs_foto_<?= $index + 1 ?>">
                                            <?php if(!empty($mhs['file_foto'])): ?>
                                            <small class="text-muted d-block" style="font-size: 9px;">File: <a href="<?= base_url('uploads/dokumen_mahasiswa/' . $mhs['file_foto']) ?>" target="_blank" class="text-decoration-none fw-semibold"><?= $mhs['file_foto'] ?></a></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <input type="file" class="form-control form-control-sm mb-1" name="mhs_ijazah_<?= $index + 1 ?>">
                                            <?php if(!empty($mhs['file_ijazah'])): ?>
                                            <small class="text-muted d-block" style="font-size: 9px;">File: <a href="<?= base_url('uploads/dokumen_mahasiswa/' . $mhs['file_ijazah']) ?>" target="_blank" class="text-decoration-none fw-semibold"><?= $mhs['file_ijazah'] ?></a></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <input type="file" class="form-control form-control-sm mb-1" name="mhs_sk_<?= $index + 1 ?>">
                                            <?php if(!empty($mhs['file_sk'])): ?>
                                            <small class="text-muted d-block" style="font-size: 9px;">File: <a href="<?= base_url('uploads/dokumen_mahasiswa/' . $mhs['file_sk']) ?>" target="_blank" class="text-decoration-none fw-semibold"><?= $mhs['file_sk'] ?></a></small>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-light text-danger btn-remove-mhs">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
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
                                <label class="form-label small fw-bold">1. Proposal (PDF)</label>
                                <input type="file" class="form-control form-control-sm" name="doc_proposal">
                                <?php if(!empty($pengajuan['file_proposal'])): ?>
                                <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">File: <a href="<?= base_url('uploads/dokumen_pengajuan/' . $pengajuan['file_proposal']) ?>" target="_blank" class="text-decoration-none fw-semibold"><?= $pengajuan['file_proposal'] ?></a></small>
                                <?php else: ?>
                                <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Belum ada file</small>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 border rounded bg-light h-100">
                                <label class="form-label small fw-bold">2. Surat Pengantar (PDF)</label>
                                <input type="file" class="form-control form-control-sm" name="doc_pengantar">
                                <?php if(!empty($pengajuan['file_surat_pengantar'])): ?>
                                <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">File: <a href="<?= base_url('uploads/dokumen_pengajuan/' . $pengajuan['file_surat_pengantar']) ?>" target="_blank" class="text-decoration-none fw-semibold"><?= $pengajuan['file_surat_pengantar'] ?></a></small>
                                <?php else: ?>
                                <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Belum ada file</small>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 border rounded bg-light h-100">
                                <label class="form-label small fw-bold">3. Log Book (PDF)</label>
                                <input type="file" class="form-control form-control-sm" name="doc_logbook">
                                <?php if(!empty($pengajuan['file_logbook'])): ?>
                                <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">File: <a href="<?= base_url('uploads/dokumen_pengajuan/' . $pengajuan['file_logbook']) ?>" target="_blank" class="text-decoration-none fw-semibold"><?= $pengajuan['file_logbook'] ?></a></small>
                                <?php else: ?>
                                <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Belum ada file</small>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 border rounded bg-light h-100">
                                <label class="form-label small fw-bold">4. Buku Panduan (PDF)</label>
                                <input type="file" class="form-control form-control-sm" name="doc_panduan">
                                <?php if(!empty($pengajuan['file_panduan'])): ?>
                                <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">File: <a href="<?= base_url('uploads/dokumen_pengajuan/' . $pengajuan['file_panduan']) ?>" target="_blank" class="text-decoration-none fw-semibold"><?= $pengajuan['file_panduan'] ?></a></small>
                                <?php else: ?>
                                <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Belum ada file</small>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 border rounded bg-light h-100">
                                <label class="form-label small fw-bold">5. Daftar Nama Mahasiswa (PDF/XLS)</label>
                                <input type="file" class="form-control form-control-sm" name="doc_daftar_mhs">
                                <?php if(!empty($pengajuan['file_daftar_mhs'])): ?>
                                <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">File: <a href="<?= base_url('uploads/dokumen_pengajuan/' . $pengajuan['file_daftar_mhs']) ?>" target="_blank" class="text-decoration-none fw-semibold"><?= $pengajuan['file_daftar_mhs'] ?></a></small>
                                <?php else: ?>
                                <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Belum ada file</small>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 border rounded bg-light h-100">
                                <label class="form-label small fw-bold">6. Surat Level Kompetensi (PDF)</label>
                                <input type="file" class="form-control form-control-sm" name="doc_kompetensi">
                                <?php if(!empty($pengajuan['file_kompetensi'])): ?>
                                <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">File: <a href="<?= base_url('uploads/dokumen_pengajuan/' . $pengajuan['file_kompetensi']) ?>" target="_blank" class="text-decoration-none fw-semibold"><?= $pengajuan['file_kompetensi'] ?></a></small>
                                <?php else: ?>
                                <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Belum ada file</small>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 border rounded bg-light h-100">
                                <label class="form-label small fw-bold">7. SK Pembimbing (PDF)</label>
                                <input type="file" class="form-control form-control-sm" name="doc_sk_pembimbing">
                                <?php if(!empty($pengajuan['file_sk_pembimbing'])): ?>
                                <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">File: <a href="<?= base_url('uploads/dokumen_pengajuan/' . $pengajuan['file_sk_pembimbing']) ?>" target="_blank" class="text-decoration-none fw-semibold"><?= $pengajuan['file_sk_pembimbing'] ?></a></small>
                                <?php else: ?>
                                <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Opsional / Belum diupload</small>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 border rounded bg-light h-100">
                                <label class="form-label small fw-bold">8. Bukti Pembayaran Batch (PDF/IMG)</label>
                                <input type="file" class="form-control form-control-sm" name="doc_bukti_bayar">
                                <?php if(!empty($pengajuan['file_bukti_bayar'])): ?>
                                <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">File: <a href="<?= base_url('uploads/dokumen_pengajuan/' . $pengajuan['file_bukti_bayar']) ?>" target="_blank" class="text-decoration-none fw-semibold"><?= $pengajuan['file_bukti_bayar'] ?></a></small>
                                <?php else: ?>
                                <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Opsional (bisa dikosongkan dahulu)</small>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 border rounded bg-light h-100">
                                <label class="form-label small fw-bold">9. Dokumen Penilaian (PDF)</label>
                                <input type="file" class="form-control form-control-sm" name="doc_penilaian">
                                <?php if(!empty($pengajuan['file_dokumen_penilaian'])): ?>
                                <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">File: <a href="<?= base_url('uploads/dokumen_pengajuan/' . $pengajuan['file_dokumen_penilaian']) ?>" target="_blank" class="text-decoration-none fw-semibold"><?= $pengajuan['file_dokumen_penilaian'] ?></a></small>
                                <?php else: ?>
                                <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Opsional</small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-12 mb-5">
            <div class="card border-0 bg-transparent">
                <div class="card-body p-0 d-flex justify-content-end gap-2">
                    <a href="<?= base_url('pendidikan/institusi/pengajuan/status') ?>" class="btn btn-light px-4">Batal</a>
                    <button type="submit" class="btn btn-danger px-5 fw-bold">UPDATE PENGAJUAN</button>
                </div>
            </div>
        </div>
    </div>
</form>

<style>
    .bg-light-warning {
        background-color: #fffaf0;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const list = document.getElementById('mahasiswaList');
        const btnAdd = document.getElementById('btnAddMahasiswa');
        let count = <?= count($pengajuan['mahasiswa']) ?>;

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
                    <td><input type="email" class="form-control form-control-sm" name="mhs_email[]" placeholder="Email"></td>
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
        
        updateRemoveButtons();
    });
</script>

<?= $this->include('Pendidikan/Institusi/layout/footer') ?>
