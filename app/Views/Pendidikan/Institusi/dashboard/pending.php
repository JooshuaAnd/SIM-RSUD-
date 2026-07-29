<?= $this->include('Pendidikan/Institusi/layout/header') ?>
<?= $this->include('Pendidikan/Institusi/layout/sidebar') ?>

<div class="row">
    <div class="col-12 mb-4">
        <div class="alert alert-warning border-0 shadow-sm py-4">
            <div class="d-flex align-items-center">
                <i class="fas fa-clock fa-3x me-4 text-warning"></i>
                <div>
                    <h4 class="alert-heading fw-bold">Akun Sedang Diverifikasi</h4>
                    <p class="mb-0">Terima kasih telah mendaftar. Akun institusi Anda saat ini sedang dalam proses pengecekan oleh admin Diklat RSUD. Anda akan mendapatkan akses penuh setelah akun disetujui.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i class="fas fa-university me-2 text-danger"></i> Data Registrasi Institusi</h6>
                <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#editModal">
                    <i class="fas fa-edit me-1"></i> Edit Data
                </button>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-sm-4 text-muted small">Nama Institusi</div>
                    <div class="col-sm-8 fw-bold"><?= $profile['nama'] ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4 text-muted small">Jenis / Alamat</div>
                    <div class="col-sm-8"><?= $profile['jenis'] ?> - <?= $profile['alamat'] ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4 text-muted small">Email / Telp</div>
                    <div class="col-sm-8"><?= $profile['email'] ?> / <?= $profile['telp'] ?></div>
                </div>
                <hr>
                <div class="row mb-3">
                    <div class="col-sm-4 text-muted small">Penanggung Jawab</div>
                    <div class="col-sm-8 fw-bold"><?= $profile['pj'] ?> (<?= $profile['jabatan'] ?>)</div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4 text-muted small">Kontak PJ</div>
                    <div class="col-sm-8"><?= $profile['hp_pj'] ?> / <?= $profile['email_pj'] ?></div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-4 text-muted small">Dokumen MoU</div>
                    <div class="col-sm-8">
                        <span class="badge bg-light text-dark border"><i class="fas fa-file-pdf text-danger me-1"></i> mou_pks_2026.pdf</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card border-0 bg-light">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Bantuan & Informasi</h6>
                <p class="small text-muted">Proses verifikasi biasanya memakan waktu 1-3 hari kerja. Jika Anda memiliki pertanyaan, silakan hubungi bagian Diklat melalui:</p>
                <ul class="list-unstyled small mb-0">
                    <li class="mb-2"><i class="fab fa-whatsapp text-success me-2"></i> 0812-xxxx-xxxx</li>
                    <li><i class="fas fa-envelope text-danger me-2"></i> diklat@rsud.go.id</li>
                </ul>
            </div>
        </div>
        

    </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="<?= base_url('pendidikan/institusi/dashboard/update') ?>" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Edit Data Registrasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Data Institusi -->
                    <div class="section-title-modal mb-3 fw-bold text-danger border-bottom pb-1">
                        <i class="fas fa-university me-2"></i> Data Institusi
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label class="form-label small fw-bold">Nama Institusi</label>
                            <input type="text" class="form-control" name="nama" value="<?= $profile['nama'] ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Jenis Institusi</label>
                            <select class="form-select" name="jenis">
                                <option <?= ($profile['jenis'] == 'Negeri') ? 'selected' : '' ?>>Negeri</option>
                                <option <?= ($profile['jenis'] == 'Swasta') ? 'selected' : '' ?>>Swasta</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Alamat Institusi</label>
                        <textarea class="form-control" name="alamat" rows="2"><?= $profile['alamat'] ?></textarea>
                    </div>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Email Institusi</label>
                            <input type="email" class="form-control" name="email_institusi" value="<?= $profile['email'] ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nomor Telepon Kantor</label>
                            <input type="text" class="form-control" name="telp_institusi" value="<?= $profile['telp'] ?>">
                        </div>
                    </div>

                    <!-- Data Penanggung Jawab -->
                    <div class="section-title-modal mb-3 fw-bold text-danger border-bottom pb-1">
                        <i class="fas fa-user-tie me-2"></i> Data Penanggung Jawab
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nama Lengkap</label>
                            <input type="text" class="form-control" name="nama_pj" value="<?= $profile['pj'] ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Jabatan</label>
                            <input type="text" class="form-control" name="jabatan_pj" value="<?= $profile['jabatan'] ?>">
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nomor HP / WhatsApp</label>
                            <input type="text" class="form-control" name="hp_pj" value="<?= $profile['hp_pj'] ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Email Penanggung Jawab</label>
                            <input type="email" class="form-control" name="email_pj" value="<?= $profile['email_pj'] ?>">
                        </div>
                    </div>

                    <!-- Dokumen -->
                    <div class="section-title-modal mb-3 fw-bold text-danger border-bottom pb-1">
                        <i class="fas fa-file-upload me-2"></i> Dokumen Pendukung
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">MoU / PKS (PDF)</label>
                            <input type="file" class="form-control" name="file_mou">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Surat Permohonan (PDF)</label>
                            <input type="file" class="form-control" name="file_permohonan">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Dokumen Pendukung Lainnya</label>
                        <input type="file" class="form-control" name="file_lainnya">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->include('Pendidikan/Institusi/layout/footer') ?>
