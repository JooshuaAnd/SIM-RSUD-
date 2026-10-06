<?= $this->include('Pendidikan/AdminDiklat/layout/header') ?>
<?= $this->include('Pendidikan/AdminDiklat/layout/sidebar') ?>

<div class="mb-3">
    <a href="<?= base_url('pendidikan/admin/diklat/user/detail/' . $institusi['id']) ?>" class="btn btn-sm btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar Profesi
    </a>
</div>

<div class="card p-4 mb-4">
    <div class="d-flex align-items-center gap-4">
        <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width:64px;height:64px;">
            <i class="fas fa-user-md fa-2x text-muted"></i>
        </div>
        <div>
            <h5 class="fw-bold mb-1"><?= esc($profesi) ?></h5>
            <small class="text-muted"><?= esc($institusi['nama_institusi'] ?? '-') ?></small>
        </div>
    </div>
</div>

<div class="card p-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Mahasiswa</th>
                    <th>NIM</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($mahasiswaList)): ?>
                    <?php $no = 1; ?>
                    <?php foreach ($mahasiswaList as $m): ?>
                    <tr>
                        <td><small class="text-muted"><?= $no++ ?></small></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="bg-primary bg-opacity-10 rounded d-flex align-items-center justify-content-center" style="width:36px;height:36px;">
                                    <span class="fw-bold text-primary"><?= esc(strtoupper(substr($m['nama_lengkap'] ?? '-', 0, 1))) ?></span>
                                </div>
                                <span class="fw-semibold"><?= esc($m['nama_lengkap'] ?? '-') ?></span>
                            </div>
                        </td>
                        <td><small><?= esc($m['nim'] ?? '-') ?></small></td>
                        <td>
                            <?php $mStatus = $m['status'] ?? 'Menunggu'; ?>
                            <?php if (in_array($mStatus, ['Disetujui', 'Aktif', '1'])): ?>
                                <span class="badge badge-disetujui">Aktif</span>
                            <?php else: ?>
                                <span class="badge badge-menunggu">Menunggu</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center text-muted py-5">
                            <i class="fas fa-users fa-3x mb-3 d-block"></i>
                            Tidak ada mahasiswa
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->include('Pendidikan/AdminDiklat/layout/footer') ?>
