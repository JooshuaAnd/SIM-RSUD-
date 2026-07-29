<?= $this->include('Pendidikan/Institusi/layout/header') ?>
<?= $this->include('Pendidikan/Institusi/layout/sidebar') ?>

<div class="row">
    <div class="col-12 mb-4">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h4 class="fw-bold">Profil Institusi</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('pendidikan/institusi/dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item active">Profil Institusi</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <?php if(session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-1"></i> <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold text-danger"><i class="fas fa-building me-2"></i> Data Pendaftaran Institusi</h6>
            </div>
            <div class="card-body">
                <form action="<?= base_url('pendidikan/institusi/profil/update') ?>" method="POST" enctype="multipart/form-data">
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Email Akun</label>
                        <input type="email" class="form-control bg-light" value="<?= $user['email'] ?>" readonly>
                        <small class="text-muted">Email tidak dapat diubah dari halaman ini.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Institusi</label>
                        <input type="text" class="form-control" name="nama_institusi" value="<?= $institusi['nama_institusi'] ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Alamat Institusi</label>
                        <textarea class="form-control" name="alamat" rows="3" required><?= $institusi['alamat'] ?></textarea>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nomor Telepon Institusi</label>
                            <input type="text" class="form-control" name="no_telp" value="<?= $institusi['no_telp'] ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nama Kontak (Penanggung Jawab)</label>
                            <input type="text" class="form-control" name="nama_kontak" value="<?= $institusi['nama_kontak'] ?>" required>
                        </div>
                    </div>

                    <hr class="my-4">

                    <h6 class="fw-bold mb-3"><i class="fas fa-folder-open me-2 text-danger"></i> Dokumen Persyaratan</h6>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="p-3 border rounded bg-light h-100">
                                <label class="form-label small fw-bold">Dokumen MoU / PKS</label>
                                <input type="file" class="form-control form-control-sm mb-2" name="file_mou" accept=".pdf">
                                <?php if(!empty($institusi['file_mou'])): ?>
                                    <div class="mt-2">
                                        <small class="text-muted d-block mb-1">Dokumen saat ini:</small>
                                        <a href="<?= base_url('uploads/institusi/' . $institusi['file_mou']) ?>" target="_blank" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-file-pdf me-1"></i> Lihat MoU
                                        </a>
                                    </div>
                                <?php else: ?>
                                    <small class="text-danger d-block mt-1">Belum ada file</small>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="p-3 border rounded bg-light h-100">
                                <label class="form-label small fw-bold">Surat Permohonan</label>
                                <input type="file" class="form-control form-control-sm mb-2" name="file_permohonan" accept=".pdf">
                                <?php if(!empty($institusi['file_permohonan'])): ?>
                                    <div class="mt-2">
                                        <small class="text-muted d-block mb-1">Dokumen saat ini:</small>
                                        <a href="<?= base_url('uploads/institusi/' . $institusi['file_permohonan']) ?>" target="_blank" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-file-pdf me-1"></i> Lihat Permohonan
                                        </a>
                                    </div>
                                <?php else: ?>
                                    <small class="text-danger d-block mt-1">Belum ada file</small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <button type="submit" class="btn btn-danger px-4">
                            <i class="fas fa-save me-1"></i> Simpan Perubahan
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <!-- Ganti Password Card -->
    <div class="col-lg-4 mt-4 mt-lg-0">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold text-danger"><i class="fas fa-lock me-2"></i> Ganti Password</h6>
            </div>
            <div class="card-body">
                <form action="<?= base_url('pendidikan/institusi/profil/update_password') ?>" method="POST">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Password Lama</label>
                        <input type="password" class="form-control" name="old_password" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Password Baru</label>
                        <input type="password" class="form-control" name="new_password" required minlength="6">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Konfirmasi Password Baru</label>
                        <input type="password" class="form-control" name="confirm_password" required minlength="6">
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="fas fa-key me-1"></i> Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->include('Pendidikan/Institusi/layout/footer') ?>
