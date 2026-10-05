<?= $this->include('Pendidikan/mahasiswa/layout/header') ?>
<?= $this->include('Pendidikan/mahasiswa/layout/sidebar') ?>

<div class="row">
    <div class="col-12 mb-4">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h4 class="fw-bold">Profil & Dokumen Mahasiswa</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('pendidikan/mahasiswa/dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item active">Profil & Dokumen</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<?php if (session()->getFlashdata('success')) : ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<form action="<?= base_url('pendidikan/mahasiswa/profil/update') ?>" method="POST" enctype="multipart/form-data">
    <div class="row">
        <!-- Profil Information (Read Only from Institusi/Registration) -->
        <div class="col-lg-12">
            <div class="card mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold text-danger"><i class="fas fa-user-circle me-2"></i> Data Pribadi</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold">Nama Lengkap</label>
                            <input type="text" class="form-control" value="<?= esc($mahasiswa['nama_lengkap'] ?? '') ?>" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold">NIM</label>
                            <input type="text" class="form-control" value="<?= esc($mahasiswa['nim'] ?? '') ?>" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold">Institusi</label>
                            <input type="text" class="form-control" value="<?= esc($institusiName ?? '') ?>" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold">Program Studi</label>
                            <input type="text" class="form-control" value="<?= esc($mahasiswa['program_studi'] ?? '') ?>" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dokumen Wajib (Diunggah oleh Institusi) -->
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold text-danger"><i class="fas fa-folder-open me-2"></i> Dokumen Wajib Mahasiswa</h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-4 d-flex align-items-center gap-3">
                        <div class="bg-white rounded-circle p-2 text-info shadow-sm">
                            <i class="fas fa-info-circle fa-lg"></i>
                        </div>
                        <div>
                            <strong class="d-block text-info">Kelola Dokumen Wajib</strong>
                            <span class="small">Dokumen awal Anda telah diunggah oleh <strong>Institusi</strong>. Anda dapat memperbarui dokumen (Pas Foto, Ijazah, dll) jika diperlukan dengan mengunggah file baru di bawah ini.</span>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="p-3 border rounded-3 bg-light">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="bg-danger text-white rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                            <i class="fas fa-image fa-lg"></i>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold mb-1 small">Pas Foto Terbaru</h6>
                                            <?php if (!empty($mahasiswa['file_foto'])): ?>
                                                <span class="badge bg-success small"><i class="fas fa-check me-1"></i> Tersedia</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger small"><i class="fas fa-times me-1"></i> Belum Ada</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <?php if (!empty($mahasiswa['file_foto'])): ?>
                                        <a href="<?= base_url('uploads/dokumen_mahasiswa/' . $mahasiswa['file_foto']) ?>" target="_blank" class="btn btn-sm btn-outline-danger fw-bold px-3"><i class="fas fa-eye me-1"></i> Lihat</a>
                                    <?php endif; ?>
                                </div>
                                <input type="file" name="pas_foto" class="form-control form-control-sm" accept="image/*">
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="p-3 border rounded-3 bg-light">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="bg-danger text-white rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                            <i class="fas fa-file-pdf fa-lg"></i>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold mb-1 small">Ijazah Terakhir</h6>
                                            <?php if (!empty($mahasiswa['file_ijazah'])): ?>
                                                <span class="badge bg-success small"><i class="fas fa-check me-1"></i> Tersedia</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger small"><i class="fas fa-times me-1"></i> Belum Ada</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <?php if (!empty($mahasiswa['file_ijazah'])): ?>
                                        <a href="<?= base_url('uploads/dokumen_mahasiswa/' . $mahasiswa['file_ijazah']) ?>" target="_blank" class="btn btn-sm btn-outline-danger fw-bold px-3"><i class="fas fa-eye me-1"></i> Lihat</a>
                                    <?php endif; ?>
                                </div>
                                <input type="file" name="ijazah" class="form-control form-control-sm" accept=".pdf">
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="p-3 border rounded-3 bg-light">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="bg-danger text-white rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                            <i class="fas fa-file-invoice fa-lg"></i>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold mb-1 small">Surat Keterangan Aktif</h6>
                                            <?php if (!empty($mahasiswa['file_sk'])): ?>
                                                <span class="badge bg-success small"><i class="fas fa-check me-1"></i> Tersedia</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger small"><i class="fas fa-times me-1"></i> Belum Ada</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <?php if (!empty($mahasiswa['file_sk'])): ?>
                                        <a href="<?= base_url('uploads/dokumen_mahasiswa/' . $mahasiswa['file_sk']) ?>" target="_blank" class="btn btn-sm btn-outline-danger fw-bold px-3"><i class="fas fa-eye me-1"></i> Lihat</a>
                                    <?php endif; ?>
                                </div>
                                <input type="file" name="surat_aktif" class="form-control form-control-sm" accept=".pdf">
                            </div>
                        </div>

                    </div>
                    <div class="d-flex justify-content-end mt-3 pt-3 border-top">
                        <button type="submit" class="btn btn-danger px-5 fw-bold"><i class="fas fa-save me-2"></i> SIMPAN PERUBAHAN</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<form action="<?= base_url('pendidikan/mahasiswa/profil/update_password') ?>" method="POST">
    <div class="row mt-4 mb-5">
        <div class="col-lg-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold text-danger"><i class="fas fa-lock me-2"></i> Keamanan Akun</h6>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('error')) : ?>
                        <div class="alert alert-danger mb-4">
                            <i class="fas fa-times-circle me-2"></i> <?= session()->getFlashdata('error') ?>
                        </div>
                    <?php endif; ?>
                    <div class="alert alert-warning mb-4">
                        <i class="fas fa-exclamation-triangle me-2"></i> Sangat disarankan untuk segera mengubah password bawaan (default) Anda menjadi password yang lebih aman dan mudah diingat.
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label small fw-bold">Password Lama (Default)</label>
                            <input type="password" class="form-control" name="password_lama" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label small fw-bold">Password Baru</label>
                            <input type="password" class="form-control" name="password_baru" required minlength="6">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label small fw-bold">Konfirmasi Password Baru</label>
                            <input type="password" class="form-control" name="konfirmasi_password" required minlength="6">
                        </div>
                    </div>
                    <div class="text-end mt-2">
                        <button type="submit" class="btn btn-danger fw-bold px-4"><i class="fas fa-key me-2"></i> UBAH PASSWORD</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<?= $this->include('Pendidikan/mahasiswa/layout/footer') ?>
