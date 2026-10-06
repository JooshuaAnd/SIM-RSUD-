<?= $this->include('Pendidikan/AdminDiklat/layout/header') ?>
<?= $this->include('Pendidikan/AdminDiklat/layout/sidebar') ?>

<div class="mb-3">
    <a href="<?= base_url('pendidikan/admin/diklat/user') ?>" class="btn btn-sm btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar Institusi
    </a>
</div>

<div class="card p-4 mb-4">
    <div class="d-flex align-items-center gap-4">
        <div class="bg-primary rounded d-flex align-items-center justify-content-center text-white" style="width:64px;height:64px;">
            <i class="fas fa-building fa-2x"></i>
        </div>
        <div>
            <h5 class="fw-bold mb-1"><?= esc($institusi['nama_institusi'] ?? '-') ?></h5>
            <small class="text-muted"><?= esc($institusi['tipe_institusi'] ?? 'Institusi') ?></small>
        </div>
    </div>
</div>

<div class="row g-3">
    <?php if (!empty($profesiList)): ?>
        <?php foreach ($profesiList as $prof): ?>
        <div class="col-md-4 col-lg-3">
            <div class="card p-3" onclick="location.href='<?= base_url('pendidikan/admin/diklat/user/detail/' . $institusi['id'] . '/profesi/' . urlencode($prof)) ?>'" style="cursor:pointer;">
                <div class="d-flex align-items-center gap-3 mb-2">
                    <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width:40px;height:40px;">
                        <i class="fas fa-user-md text-muted"></i>
                    </div>
                    <h6 class="fw-bold mb-0"><?= esc($prof) ?></h6>
                </div>
                <small class="text-muted">
                    <?php
                    $count = 0;
                    if (!empty($mahasiswaByProfesi[$prof])) {
                        $count = count($mahasiswaByProfesi[$prof]);
                    }
                    ?>
                    <?= $count ?> Mahasiswa
                </small>
            </div>
        </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12">
            <div class="card p-5 text-center">
                <i class="fas fa-user-md fa-3x mb-3 text-muted"></i>
                <p class="text-muted">Tidak ada data profesi</p>
            </div>
        </div>
    <?php endif; ?>
</div>

<?= $this->include('Pendidikan/AdminDiklat/layout/footer') ?>
