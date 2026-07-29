<?= $this->extend('Pelatihan/layout/admin_layout') ?>

<?= $this->section('content') ?>
<?php
$grouped    = $grouped ?? [];
$ranah_list = $ranah_list ?? [];
?>

<div class="d-flex justify-content-end mb-2 mt-1">
    <button class="btn btn-danger btn-sm rounded-pill px-3 fw-bold shadow-sm" onclick="showModalTambah()">
        <i class="fas fa-plus-circle me-1"></i> TAMBAH KATEGORI UMUM
    </button>
</div>

<?php if (empty($grouped)): ?>
<div class="card border-0 shadow-sm rounded-lg p-5 text-center text-muted">
    <i class="fas fa-database fa-3x mb-3 opacity-25"></i>
    <p class="fw-bold">Belum ada data Ranah &amp; Kategori SKP. Silakan tambah menggunakan tombol di atas.</p>
</div>
<?php else: ?>

<?php foreach ($grouped as $ranah => $items): ?>
<div class="card border-0 shadow-sm rounded-lg overflow-hidden mb-4">
    <div class="card-header bg-dark text-white py-3 px-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center flex-wrap gap-2">
            <i class="fas fa-tag text-warning"></i>
            <span class="fw-bold text-uppercase" style="letter-spacing: 1px;"><?= esc($ranah) ?></span>
            <span class="badge bg-danger ms-2"><?= count($items) ?> Kategori</span>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <button class="btn btn-sm btn-outline-light rounded-pill px-3 fw-bold flex-grow-1 flex-md-grow-0" onclick="showModalTambah('<?= esc($ranah) ?>')">
                <i class="fas fa-plus me-1"></i> Tambah Kategori
            </button>
            <button class="btn btn-sm btn-warning rounded-pill px-3 fw-bold text-dark flex-grow-1 flex-md-grow-0" onclick="showModalEditRanah('<?= esc($ranah, 'js') ?>')">
                <i class="fas fa-edit me-1"></i> Edit Ranah
            </button>
            <button class="btn btn-sm btn-danger rounded-pill px-3 fw-bold flex-grow-1 flex-md-grow-0" onclick="confirmHapusRanah('<?= esc($ranah, 'js') ?>', <?= count($items) ?>)">
                <i class="fas fa-trash me-1"></i> Hapus Ranah
            </button>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light small fw-bold text-muted">
                <tr>
                    <th class="ps-4 py-3" style="width: 60px;">NO</th>
                    <th>NAMA KATEGORI KEGIATAN</th>
                    <th class="text-center" style="width: 120px;">AKSI</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $i => $item): ?>
                <tr>
                    <td class="ps-4 fw-bold text-muted"><?= $i + 1 ?></td>
                    <td>
                        <div class="fw-bold text-dark small"><?= esc($item['nama_kategori']) ?></div>
                    </td>
                    <td class="text-center">
                        <button class="btn btn-light btn-sm rounded-pill px-3 text-danger fw-bold shadow-sm border" onclick="confirmHapus(<?= $item['id'] ?>, '<?= esc($item['nama_kategori'], 'js') ?>')">
                            <i class="fas fa-trash me-1"></i> HAPUS
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endforeach; ?>
<?php endif; ?>

<!-- Modal Tambah Kategori -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-lg">
            <div class="modal-header bg-dark text-white border-0 p-4">
                <h5 class="modal-title fw-bold"><i class="fas fa-plus-circle me-2 text-warning"></i> TAMBAH KATEGORI SKP</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('pelatihan/admin/master/simpan_kategori_skp') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body p-4 bg-light">

                    <div class="mb-3" id="container_ranah_select">
                        <label class="form-label small fw-bold text-dark">RANAH</label>
                        <select name="ranah" id="f_ranah" class="form-select rounded-pill border shadow-sm px-4 py-2" onchange="toggleRanahBaru(this.value)" required>
                            <option value="">-- Pilih Ranah --</option>
                            <?php foreach ($ranah_list as $r): ?>
                            <option value="<?= esc($r) ?>"><?= esc($r) ?></option>
                            <?php endforeach; ?>
                            <option value="__baru__">+ Buat Ranah Baru</option>
                        </select>
                    </div>

                    <div class="mb-3 d-none" id="container_ranah_baru">
                        <label class="form-label small fw-bold text-dark">NAMA RANAH BARU</label>
                        <input type="text" name="ranah_baru" id="f_ranah_baru" class="form-control rounded-pill border shadow-sm px-4 py-2" placeholder="Contoh: Pelayanan, Penelitian...">
                        <div class="form-text text-muted small mt-1">Ranah baru akan ditambahkan otomatis ke sistem.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">NAMA KATEGORI KEGIATAN</label>
                        <input type="text" name="nama_kategori" class="form-control rounded-pill border shadow-sm px-4 py-2" placeholder="Masukkan nama kategori kegiatan..." required>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-white">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">BATAL</button>
                    <button type="submit" class="btn btn-primary-custom rounded-pill px-4 fw-bold">SIMPAN DATA</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit / Rename Ranah -->
<div class="modal fade" id="modalEditRanah" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-lg">
            <div class="modal-header bg-warning border-0 p-4">
                <h5 class="modal-title fw-bold text-dark"><i class="fas fa-edit me-2"></i> UBAH NAMA RANAH</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('pelatihan/admin/master/rename_ranah') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="ranah_lama" id="f_ranah_lama">
                <div class="modal-body p-4 bg-light">
                    <div class="alert alert-warning border-0 rounded-4 small fw-bold mb-3">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Mengubah nama ranah akan memperbarui <strong>semua kategori</strong> dalam ranah ini. Jika nama baru sudah ada, kategori akan digabungkan.
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">NAMA RANAH SAAT INI</label>
                        <input type="text" id="ranah_lama_display" class="form-control rounded-pill border shadow-sm px-4 py-2 bg-white fw-bold" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">NAMA RANAH BARU <span class="text-danger">*</span></label>
                        <input type="text" name="ranah_baru" id="f_ranah_baru_edit" class="form-control rounded-pill border shadow-sm px-4 py-2" placeholder="Masukkan nama ranah baru..." required>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-white">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">BATAL</button>
                    <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold text-dark">
                        <i class="fas fa-save me-1"></i> SIMPAN PERUBAHAN
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    let modalTambah, modalEditRanah;

    document.addEventListener('DOMContentLoaded', function() {
        modalTambah    = new bootstrap.Modal(document.getElementById('modalTambah'));
        modalEditRanah = new bootstrap.Modal(document.getElementById('modalEditRanah'));
    });

    function showModalTambah(ranahDefault = '') {
        const select = document.getElementById('f_ranah');
        if (ranahDefault && select) {
            select.value = ranahDefault;
            toggleRanahBaru(ranahDefault);
        } else {
            select.value = '';
            toggleRanahBaru('');
        }
        modalTambah.show();
    }

    function showModalEditRanah(ranahNama) {
        document.getElementById('f_ranah_lama').value      = ranahNama;
        document.getElementById('ranah_lama_display').value = ranahNama;
        document.getElementById('f_ranah_baru_edit').value  = ranahNama;
        modalEditRanah.show();
    }

    function toggleRanahBaru(val) {
        const container = document.getElementById('container_ranah_baru');
        const input     = document.getElementById('f_ranah_baru');
        if (val === '__baru__') {
            container.classList.remove('d-none');
            input.required = true;
        } else {
            container.classList.add('d-none');
            input.required = false;
            input.value = '';
        }
    }

    function confirmHapus(id, nama) {
        window.confirmAction(
            'Hapus Kategori?',
            'Kategori "<b>' + nama + '</b>" akan dihapus permanen. Pelatihan yang sudah menggunakan kategori ini tidak akan terpengaruh.',
            function() {
                location.href = "<?= base_url('pelatihan/admin/master/hapus_kategori_skp/') ?>" + id;
            }
        );
    }

    function confirmHapusRanah(ranahNama, jumlah) {
        window.confirmAction(
            'Hapus Seluruh Ranah?',
            'Ranah "<b>' + ranahNama + '</b>" beserta <b>' + jumlah + ' kategori</b> di dalamnya akan dihapus permanen!',
            function() {
                location.href = "<?= base_url('pelatihan/admin/master/hapus_ranah/') ?>" + encodeURIComponent(ranahNama);
            }
        );
    }
</script>
<?= $this->endSection() ?>
