<?= $this->include('Pendidikan/AdminDiklat/layout/header') ?>
<?= $this->include('Pendidikan/AdminDiklat/layout/sidebar') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold text-dark">Dashboard Utama</h5>
    <small class="text-muted"><?= date('d/m/Y') ?></small>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card p-4">
            <p class="text-uppercase small fw-bold text-muted mb-1">Total Institusi</p>
            <h3 class="fw-bold mb-0"><?= $totalInstitusi ?? 0 ?></h3>
            <small class="text-warning fw-bold"><?= $pendingInstitusi ?? 0 ?> pending</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-4">
            <p class="text-uppercase small fw-bold text-muted mb-1">Mahasiswa Aktif</p>
            <h3 class="fw-bold mb-0"><?= $totalMahasiswa ?? 0 ?></h3>
            <small class="text-success fw-bold">dari <?= $totalKampus ?? 0 ?> kampus</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-4">
            <p class="text-uppercase small fw-bold text-muted mb-1">Total CI</p>
            <h3 class="fw-bold mb-0"><?= $totalCi ?? 0 ?></h3>
            <small class="text-primary fw-bold">Clinical Instructor</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-4">
            <p class="text-uppercase small fw-bold text-muted mb-1">Total Stase</p>
            <h3 class="fw-bold mb-0"><?= $totalStase ?? 0 ?></h3>
            <small class="text-info fw-bold">Stase tersedia</small>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0">Ringkasan Institusi</h6>
                <div>
                    <a href="<?= base_url('pendidikan/admin/diklat/institusi') ?>" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Institusi</th>
                            <th>Status</th>
                            <th>Tanggal Daftar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($institusiList)): ?>
                            <?php foreach ($institusiList as $inst): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width:36px;height:36px;">
                                            <span class="fw-bold text-muted"><?= esc(strtoupper(substr($inst['nama_institusi'] ?? '-', 0, 1))) ?></span>
                                        </div>
                                        <span class="fw-semibold"><?= esc($inst['nama_institusi'] ?? '-') ?></span>
                                    </div>
                                </td>
                                <td>
                                    <?php $status = $inst['status_verifikasi'] ?? 'pending'; ?>
                                    <?php if ($status === 'approved'): ?>
                                        <span class="badge badge-disetujui">Disetujui</span>
                                    <?php elseif ($status === 'rejected'): ?>
                                        <span class="badge badge-ditolak">Ditolak</span>
                                    <?php elseif ($status === 'revision'): ?>
                                        <span class="badge badge-revisi">Revisi</span>
                                    <?php else: ?>
                                        <span class="badge badge-menunggu">Menunggu</span>
                                    <?php endif; ?>
                                </td>
                                <td><small class="text-muted"><?= date('d/m/Y', strtotime($inst['created_at'] ?? 'now')) ?></small></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                    Belum ada data institusi
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0">Perlu Tindakan</h6>
                <?php if ($pendingCount > 0): ?>
                <span class="badge bg-danger rounded-pill"><?= $pendingCount ?></span>
                <?php endif; ?>
            </div>
            <div style="max-height:300px;overflow-y:auto;">
                <?php if (!empty($pendingList)): ?>
                    <?php foreach ($pendingList as $item): ?>
                    <?php $isSubmittedRevision = ($item['status_verifikasi'] ?? '') === 'revision' && !empty($item['revisi_dikirim_at']); ?>
                    <div class="d-flex align-items-center gap-3 py-2 border-bottom">
                        <div class="<?= $isSubmittedRevision ? 'bg-info' : 'bg-warning' ?> bg-opacity-10 rounded d-flex align-items-center justify-content-center" style="width:36px;height:36px;flex-shrink:0;">
                            <span class="fw-bold <?= $isSubmittedRevision ? 'text-info' : 'text-warning' ?>"><?= esc(strtoupper(substr($item['nama_institusi'] ?? '-', 0, 1))) ?></span>
                        </div>
                        <div class="flex-grow-1 min-width-0">
                            <small class="fw-semibold d-block text-truncate"><?= esc($item['nama_institusi'] ?? '-') ?></small>
                            <small class="<?= $isSubmittedRevision ? 'text-info fw-semibold' : 'text-muted' ?>">
                                <?= $isSubmittedRevision ? 'Revisi sudah dikirim institusi' : 'Menunggu verifikasi' ?>
                            </small>
                        </div>
                        <small class="text-muted flex-shrink-0"><?= date('d/m/Y', strtotime($item['revisi_dikirim_at'] ?? $item['created_at'] ?? 'now')) ?></small>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-check-circle fa-2x mb-2 text-success"></i>
                        <p class="mb-0">Semua sudah terverifikasi</p>
                    </div>
                <?php endif; ?>
            </div>
            <div class="mt-3 text-center">
                <a href="<?= base_url('pendidikan/admin/diklat/institusi') ?>" class="btn btn-sm btn-link text-decoration-none">
                    Lihat semua <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<?= $this->include('Pendidikan/AdminDiklat/layout/footer') ?>
