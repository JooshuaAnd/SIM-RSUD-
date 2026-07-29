<?= $this->include('Pendidikan/Institusi/layout/header') ?>
<?= $this->include('Pendidikan/Institusi/layout/sidebar') ?>

<div class="row">
    <div class="col-12 mb-4">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h4 class="fw-bold">Update Dokumen Pendukung</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('pendidikan/institusi/dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item active">Update Dokumen</li>
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

<form action="<?= base_url('pendidikan/institusi/pengajuan/dokumen/upload') ?>" method="POST" enctype="multipart/form-data">
    <div class="row">
        <!-- Pilih Pengajuan -->
        <div class="col-lg-12">
            <div class="card mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold text-danger"><i class="fas fa-list me-2"></i> Pilih Pengajuan</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nomor Pengajuan</label>
                            <select class="form-select" name="pengajuan_id" required>
                                <option value="" disabled selected>-- Pilih Pengajuan --</option>
                                <?php foreach($pengajuan_list as $key => $val): ?>
                                    <option value="<?= $key ?>"><?= $val ?></option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Pilih pengajuan yang sudah di-ACC / aktif untuk update dokumen</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dokumen Pendukung -->
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold text-danger"><i class="fas fa-file-upload me-2"></i> Update Dokumen Pendukung</h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> Upload hanya dokumen yang ingin di-update. Kosongkan jika tidak ada perubahan.
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label small fw-bold">1. Proposal (PDF)</label>
                            <input type="file" class="form-control" name="doc_proposal">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label small fw-bold">2. Surat Pengantar (PDF)</label>
                            <input type="file" class="form-control" name="doc_pengantar">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label small fw-bold">3. Log Book (PDF)</label>
                            <input type="file" class="form-control" name="doc_logbook">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label small fw-bold">4. Buku Panduan (PDF)</label>
                            <input type="file" class="form-control" name="doc_panduan">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label small fw-bold">5. Bukti Pembayaran (Kolektif)</label>
                            <input type="file" class="form-control" name="doc_bayar">
                            <small class="text-muted d-block mt-1">Bisa diunggah kolektif di sini, atau per-mahasiswa di menu Edit Pengajuan.</small>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label small fw-bold">6. Surat Ket. Mahasiswa Aktif (Kolektif)</label>
                            <input type="file" class="form-control" name="doc_sk_peserta">
                            <small class="text-muted d-block mt-1">Bisa diunggah kolektif di sini, atau per-mahasiswa di menu Edit Pengajuan.</small>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label small fw-bold">7. Daftar Nama Mahasiswa (PDF/XLS)</label>
                            <input type="file" class="form-control" name="doc_daftar_mhs">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label small fw-bold">8. Surat Level Kompetensi (PDF)</label>
                            <input type="file" class="form-control" name="doc_kompetensi">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label small fw-bold">9. SK Pembimbing (PDF)</label>
                            <input type="file" class="form-control" name="doc_sk_pembimbing">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-12 mb-5 mt-4">
            <div class="card border-0 bg-transparent">
                <div class="card-body p-0 d-flex justify-content-end gap-2">
                    <button type="submit" class="btn btn-danger px-5 fw-bold"><i class="fas fa-upload me-2"></i> UPDATE DOKUMEN</button>
                </div>
            </div>
        </div>
    </div>
</form>

<?= $this->include('Pendidikan/Institusi/layout/footer') ?>
