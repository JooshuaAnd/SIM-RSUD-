<?= $this->extend('Pelatihan/layout/admin_layout') ?>

<?= $this->section('content') ?>
<div class="card border-0 shadow-sm rounded-lg overflow-hidden mt-1">
    <div class="card-header bg-white border-bottom p-3 d-flex justify-content-between align-items-center">
       
        <button class="btn btn-danger btn-sm rounded-pill px-3 fw-bold shadow-sm" onclick="showModalTambah()">
            <i class="fas fa-plus-circle me-1"></i> TAMBAH PENYELENGGARA
        </button>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light small fw-bold text-muted">
                <tr>
                    <th class="ps-4 py-3" style="width: 50px;">NO</th>
                    <th>NAMA</th>
                    <th>ALAMAT</th>
                    <th>FOKUS BIDANG</th>
                    <th>KONTAK</th>
                    <th>STATUS</th>
                    <th class="text-center" style="width: 140px;">AKSI</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach($list as $item): ?>
                    <tr>
                        <td class="ps-4 fw-bold text-muted"><?= $no++ ?></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <?php if(!empty($item['logo'])): ?>
                                    <img src="<?= base_url($item['logo']) ?>" class="rounded me-2" width="32" height="32" style="object-fit:cover;">
                                <?php else: ?>
                                    <div class="rounded bg-warning-subtle text-warning d-flex align-items-center justify-content-center me-2 fw-bold" style="width:32px;height:32px;font-size:0.7rem;">
                                        <i class="fas fa-building"></i>
                                    </div>
                                <?php endif; ?>
                                <div>
                                    <div class="fw-bold text-dark"><?= esc($item['nama']) ?></div>
                                    <?php if(!empty($item['email'])): ?>
                                        <div class="text-muted small"><?= esc($item['email']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td><span class="text-dark small"><?= esc($item['alamat'] ?? '-') ?></span></td>
                        <td><span class="badge bg-primary-subtle text-primary rounded-pill px-3"><?= esc($item['fokus_bidang'] ?? '-') ?></span></td>
                        <td><span class="text-muted small"><?= esc($item['kontak'] ?? '-') ?></span></td>
                        <td>
                            <?php if (($item['status'] ?? 'Aktif') == 'Aktif'): ?>
                                <span class="badge bg-success-subtle text-success rounded-pill px-3">Aktif</span>
                            <?php else: ?>
                                <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3">Nonaktif</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <div class="d-flex gap-1 justify-content-center">
                                <button class="btn btn-light btn-sm rounded-pill px-2 text-primary fw-bold shadow-sm border" onclick='editPenyelenggara(<?= json_encode($item) ?>)' title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-light btn-sm rounded-pill px-2 text-danger fw-bold shadow-sm border" onclick="confirmDelete(<?= $item['id'] ?>)" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if(empty($list)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted fw-bold italic">Belum ada data penyelenggara.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah/Edit Penyelenggara -->
<div class="modal fade" id="modalPenyelenggara" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-lg">
            <div class="modal-header bg-dark text-white border-0">
                <h5 class="modal-title fw-bold" id="modalPenyelenggaraTitle"><i class="fas fa-plus-circle me-2 text-warning"></i> TAMBAH PENYELENGGARA</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('pelatihan/admin/master/simpan_penyelenggara') ?>" method="POST" id="formPenyelenggara">
                <div class="modal-body p-4 bg-light">
                    <input type="hidden" name="id" id="p_id" value="">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">NAMA PENYELENGGARA <span class="text-danger">*</span></label>
                            <input type="text" name="nama" id="p_nama" class="form-control rounded-pill border shadow-sm px-4 py-2" placeholder="Nama instansi/organisasi" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">FOKUS BIDANG</label>
                            <input type="text" name="fokus_bidang" id="p_fokus" class="form-control rounded-pill border shadow-sm px-4 py-2" placeholder="Kesehatan, Pendidikan, dll.">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-dark">ALAMAT</label>
                            <textarea name="alamat" id="p_alamat" class="form-control border shadow-sm p-3" rows="2" placeholder="Alamat lengkap" style="border-radius: 15px;"></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark">KONTAK / NO. TELP</label>
                            <input type="text" name="kontak" id="p_kontak" class="form-control rounded-pill border shadow-sm px-4 py-2" placeholder="021-xxxx">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark">EMAIL</label>
                            <input type="email" name="email" id="p_email" class="form-control rounded-pill border shadow-sm px-4 py-2" placeholder="info@contoh.com">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark">STATUS</label>
                            <select name="status" id="p_status" class="form-select rounded-pill border shadow-sm px-4 py-2">
                                <option value="Aktif">Aktif</option>
                                <option value="Nonaktif">Nonaktif</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-white">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">BATAL</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">SIMPAN DATA</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function showModalTambah() {
        document.getElementById('modalPenyelenggaraTitle').innerHTML = '<i class="fas fa-plus-circle me-2 text-warning"></i> TAMBAH PENYELENGGARA';
        document.getElementById('formPenyelenggara').reset();
        document.getElementById('p_id').value = '';
        new bootstrap.Modal(document.getElementById('modalPenyelenggara')).show();
    }

    function editPenyelenggara(data) {
        document.getElementById('modalPenyelenggaraTitle').innerHTML = '<i class="fas fa-edit me-2 text-warning"></i> EDIT PENYELENGGARA';
        document.getElementById('p_id').value = data.id;
        document.getElementById('p_nama').value = data.nama || '';
        document.getElementById('p_fokus').value = data.fokus_bidang || '';
        document.getElementById('p_alamat').value = data.alamat || '';
        document.getElementById('p_kontak').value = data.kontak || '';
        document.getElementById('p_email').value = data.email || '';
        document.getElementById('p_status').value = data.status || 'Aktif';
        new bootstrap.Modal(document.getElementById('modalPenyelenggara')).show();
    }

    function confirmDelete(id) {
        confirmAction('Hapus Penyelenggara?', 'Data penyelenggara akan dihapus permanen dari sistem.', function() {
            location.href = "<?= base_url('pelatihan/admin/master/hapus_penyelenggara/') ?>" + id;
        });
    }
</script>
<?= $this->endSection() ?>
