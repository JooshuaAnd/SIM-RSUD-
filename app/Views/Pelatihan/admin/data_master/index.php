<?= $this->extend('Pelatihan/layout/admin_layout') ?>

<?= $this->section('content') ?>
<?php
/**
 * @var string $title
 * @var string $type
 * @var array $list
 */
$title = $title ?? 'Master Data';
$type  = $type ?? 'profesi';
$list  = $list ?? [];
?>
<div class="card border-0 shadow-sm rounded-lg overflow-hidden mt-1">
    <div class="card-header bg-white border-bottom p-3 d-flex justify-content-end align-items-center">
        <button class="btn btn-danger btn-sm rounded-pill px-3 fw-bold shadow-sm" onclick="showModalTambah()">
            <i class="fas fa-plus-circle me-1"></i> TAMBAH DATA
        </button>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light small fw-bold text-muted">
                <tr>
                    <th class="ps-4 py-3" style="width: 80px;">NO</th>
                    <th>NAMA <?= strtoupper($type) ?></th>
                    <?php if ($type == 'profesi'): ?>
                        <th>KELOMPOK TARGET</th>
                        <th>TARGET JPL</th>
                    <?php endif; ?>
                    <th class="text-center" style="width: 120px;">AKSI</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach($list as $index => $item): ?>
                    <tr>
                        <td class="ps-4 fw-bold text-muted"><?= $no++ ?></td>
                        <?php if ($type == 'profesi'): ?>
                            <td><div class="fw-bold text-dark"><?= esc($item['nama_profesi']) ?></div></td>
                            <td><span class="badge bg-<?= $item['kategori_target'] == 'Named' ? 'danger' : 'secondary' ?> text-white"><?= esc($item['kategori_target']) ?></span></td>
                            <td><div class="fw-bold text-dark"><?= esc($item['target_jpl']) ?> JPL</div></td>
                            <td class="text-center">
                                <button class="btn btn-light btn-sm rounded-pill px-3 text-danger fw-bold shadow-sm border" onclick="confirmDelete(<?= $item['id_profesi'] ?>)">
                                    <i class="fas fa-trash me-1"></i> HAPUS
                                </button>
                            </td>
                        <?php elseif ($type == 'ruangan'): ?>
                            <td><div class="fw-bold text-dark"><?= esc($item['nama_unit']) ?></div></td>
                            <td class="text-center">
                                <button class="btn btn-light btn-sm rounded-pill px-3 text-danger fw-bold shadow-sm border" onclick="confirmDelete(<?= $item['id_unit_kerja'] ?>)">
                                    <i class="fas fa-trash me-1"></i> HAPUS
                                </button>
                            </td>
                        <?php else: ?>
                            <td><div class="fw-bold text-dark"><?= esc($item) ?></div></td>
                            <td class="text-center">
                                <button class="btn btn-light btn-sm rounded-pill px-3 text-danger fw-bold shadow-sm border" onclick="confirmDelete(<?= $index ?>)">
                                    <i class="fas fa-trash me-1"></i> HAPUS
                                </button>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
                <?php if(empty($list)): ?>
                    <tr>
                        <td colspan="<?= $type == 'profesi' ? 5 : 3 ?>" class="text-center py-5 text-muted fw-bold italic">Belum ada data tersedia.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-lg">
            <div class="modal-header bg-dark text-white border-0">
                <h5 class="modal-title fw-bold"><i class="fas fa-plus-circle me-2 text-warning"></i> TAMBAH <?= strtoupper($type) ?></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('pelatihan/admin/master/simpan/'.$type) ?>" method="POST">
                <div class="modal-body p-4 bg-light">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">NAMA <?= strtoupper($type) ?></label>
                        <input type="text" name="name" class="form-control rounded-pill border shadow-sm px-4 py-2" placeholder="Masukkan nama..." required>
                    </div>
                    <?php if ($type == 'profesi'): ?>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-dark">KELOMPOK TARGET</label>
                            <select name="kategori_target" class="form-select rounded-pill border shadow-sm px-4 py-2" required>
                                <option value="Named">Tenaga Kesehatan (Named)</option>
                                <option value="Non-Named">Tenaga Non-Medis (Non-Named)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-dark">TARGET JPL</label>
                            <input type="number" name="target_jpl" class="form-control rounded-pill border shadow-sm px-4 py-2" value="20" required>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer border-0 bg-white">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">BATAL</button>
                    <button type="submit" class="btn btn-primary-custom rounded-pill px-4 fw-bold">SIMPAN DATA</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function showModalTambah() {
        new bootstrap.Modal(document.getElementById('modalTambah')).show();
    }

    function confirmDelete(id) {
        confirmAction('Hapus Data?', 'Data ini akan dihapus permanen dari sistem.', function() {
            location.href = "<?= base_url('pelatihan/admin/master/hapus/'.$type.'/') ?>" + id;
        });
    }
</script>
<?= $this->endSection() ?>
