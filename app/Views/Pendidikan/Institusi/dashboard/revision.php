<?= $this->include('Pendidikan/Institusi/layout/header') ?>
<?= $this->include('Pendidikan/Institusi/layout/sidebar') ?>

<div class="row">
    <div class="col-12 mb-4">
        <div class="alert alert-danger border-0 shadow-sm py-4">
            <div class="d-flex">
                <i class="fas fa-exclamation-triangle fa-3x me-4 text-danger"></i>
                <div>
                    <h4 class="alert-heading fw-bold">Revisi Diperlukan</h4>
                    <p class="mb-3">Admin Diklat menemukan ketidaksesuaian pada data atau dokumen Anda. Silakan lakukan perbaikan sesuai catatan di bawah ini agar proses verifikasi dapat dilanjutkan.</p>
                    <div class="p-3 bg-white rounded border border-danger">
                        <p class="fw-bold text-danger mb-1"><i class="fas fa-comment-dots me-2"></i> Catatan Admin:</p>
                        <p class="mb-0 italic text-dark"><?= $notes ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-danger text-white py-3">
                <h6 class="mb-0 fw-bold"><i class="fas fa-edit me-2"></i> Form Perbaikan Data</h6>
            </div>
            <div class="card-body p-4">
                <form action="<?= base_url('pendidikan/institusi/dashboard/update') ?>" method="POST">
                    <!-- Data Institusi -->
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label class="form-label fw-bold">Nama Institusi</label>
                            <input type="text" class="form-control" name="nama" value="<?= $profile['nama'] ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Jenis</label>
                            <select class="form-select" name="jenis">
                                <option <?= ($profile['jenis'] == 'Negeri') ? 'selected' : '' ?>>Negeri</option>
                                <option <?= ($profile['jenis'] == 'Swasta') ? 'selected' : '' ?>>Swasta</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Alamat Institusi</label>
                        <textarea class="form-control" name="alamat" rows="2"><?= $profile['alamat'] ?></textarea>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Email Institusi</label>
                            <input type="email" class="form-control" name="email_institusi" value="<?= $profile['email'] ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nomor Telepon Kantor</label>
                            <input type="text" class="form-control" name="telp_institusi" value="<?= $profile['telp'] ?>">
                        </div>
                    </div>

                    <!-- Penanggung Jawab -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nama Penanggung Jawab</label>
                            <input type="text" class="form-control" name="pj" value="<?= $profile['pj'] ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Jabatan</label>
                            <input type="text" class="form-control" name="jabatan_pj" value="<?= $profile['jabatan'] ?>">
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nomor HP / WhatsApp</label>
                            <input type="text" class="form-control" name="hp_pj" value="<?= $profile['hp_pj'] ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Email Penanggung Jawab</label>
                            <input type="email" class="form-control" name="email_pj" value="<?= $profile['email_pj'] ?>">
                        </div>
                    </div>

                    <!-- Dokumen -->
                    <div class="p-3 bg-light rounded mb-4">
                        <label class="form-label fw-bold"><i class="fas fa-file-upload me-2 text-danger"></i> Unggah Ulang Dokumen</label>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="small text-muted">MoU / PKS (PDF)</label>
                                <input type="file" class="form-control" name="file_mou">
                            </div>
                            <div class="col-md-6">
                                <label class="small text-muted">Surat Permohonan (PDF)</label>
                                <input type="file" class="form-control" name="file_permohonan">
                            </div>
                            <div class="col-12">
                                <label class="small text-muted">Dokumen Pendukung Lainnya</label>
                                <input type="file" class="form-control" name="file_lainnya">
                            </div>
                        </div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-danger py-3 fw-bold">KIRIM PERBAIKAN</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 bg-white mb-4 shadow-sm">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Riwayat Verifikasi</h6>
                <div class="timeline small">
                    <div class="d-flex mb-3">
                        <div class="text-danger me-3"><i class="fas fa-circle fa-xs"></i></div>
                        <div>
                            <p class="mb-0 fw-bold">Revisi Diminta</p>
                            <p class="text-muted mb-0"><?= date('d M Y') ?> - Admin Diklat</p>
                        </div>
                    </div>
                    <div class="d-flex">
                        <div class="text-success me-3"><i class="fas fa-circle fa-xs"></i></div>
                        <div>
                            <p class="mb-0 fw-bold">Registrasi Diterima</p>
                            <p class="text-muted mb-0"><?= date('d M Y', strtotime('-2 days')) ?> - Sistem</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </div>
</div>

<?= $this->include('Pendidikan/Institusi/layout/footer') ?>
