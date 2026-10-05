<?= $this->include('Pendidikan/AdminDiklat/layout/header') ?>
<?= $this->include('Pendidikan/AdminDiklat/layout/sidebar') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold text-dark">Manajemen Stase</h5>
    <button class="btn btn-primary btn-sm" onclick="$('#addStaseModal').modal('show')">
        <i class="fas fa-plus me-1"></i> Tambah Stase
    </button>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card p-3">
            <small class="text-uppercase fw-bold text-muted">Total Stase</small>
            <h4 class="fw-bold mb-0"><?= $stats['total'] ?? 0 ?></h4>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3">
            <small class="text-uppercase fw-bold text-muted">Jenis Stase</small>
            <h4 class="fw-bold mb-0"><?= $stats['uniqueNames'] ?? 0 ?></h4>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3">
            <small class="text-uppercase fw-bold text-muted">Ruangan Terpakai</small>
            <h4 class="fw-bold mb-0"><?= $stats['uniqueRooms'] ?? 0 ?></h4>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3">
            <small class="text-uppercase fw-bold text-muted">Dengan CI</small>
            <h4 class="fw-bold mb-0"><?= $stats['assigned'] ?? 0 ?></h4>
        </div>
    </div>
</div>

<div class="card p-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Stase</th>
                    <th>Profesi</th>
                    <th>Ruangan</th>
                    <th>Periode</th>
                    <th>Clinical Instructor</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($staseList)): ?>
                    <?php $no = 1; ?>
                    <?php foreach ($staseList as $st): ?>
                    <tr>
                        <td><small class="text-muted"><?= $no++ ?></small></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="bg-primary bg-opacity-10 rounded d-flex align-items-center justify-content-center" style="width:36px;height:36px;">
                                    <i class="fas fa-route text-primary"></i>
                                </div>
                                <span class="fw-semibold"><?= esc($st['nama_stase'] ?? '-') ?></span>
                            </div>
                        </td>
                        <td>
                            <?php if (!empty($st['nama_profesi'])): ?>
                                <span class="badge bg-primary bg-opacity-10 text-primary"><?= esc($st['nama_profesi']) ?></span>
                            <?php else: ?>
                                <small class="text-muted">-</small>
                            <?php endif; ?>
                        </td>
                        <td><small><i class="fas fa-map-pin text-muted me-1"></i><?= esc($st['ruangan'] ?? '-') ?></small></td>
                        <td>
                            <?php if (!empty($st['tanggal_mulai']) || !empty($st['tanggal_akhir'])): ?>
                                <small><?= $st['tanggal_mulai'] ? date('d/m/Y', strtotime($st['tanggal_mulai'])) : '-' ?> → <?= $st['tanggal_akhir'] ? date('d/m/Y', strtotime($st['tanggal_akhir'])) : '-' ?></small>
                            <?php else: ?>
                                <small class="text-muted">Belum diatur</small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php $mappings = $st['ci_mappings'] ?? []; ?>
                            <?php if (!empty($mappings)): ?>
                                <div class="d-flex flex-column gap-1">
                                <?php foreach ($mappings as $m): ?>
                                    <div>
                                        <small class="fw-semibold text-dark"><?= $m['ci_name'] ?? 'CI' ?></small><br>
                                        <small class="text-muted" style="font-size:10px;"><i class="fas fa-map-pin me-1"></i><?= $m['ruangan_name'] ?? 'Ruangan' ?></small>
                                    </div>
                                <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <small class="text-muted italic">Belum ditugaskan</small>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <a href="<?= base_url('pendidikan/admin/diklat/stase/detail/' . $st['id']) ?>" class="btn btn-sm btn-outline-info me-1">
                                <i class="fas fa-eye"></i> Detail
                            </a>
                            <button class="btn btn-sm btn-outline-warning me-1" onclick='editStase(<?= htmlspecialchars(json_encode($st), ENT_QUOTES, "UTF-8") ?>)'>
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick='deleteStase(<?= (int) $st['id'] ?>, <?= esc(json_encode($st['nama_stase'] ?? '', JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT), 'attr') ?>)'>
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="fas fa-route fa-3x mb-3 d-block"></i>
                            Data stase tidak ditemukan
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add/Edit Stase Modal -->
<div class="modal fade" id="addStaseModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h6 class="modal-title fw-bold"><i class="fas fa-route me-2"></i><span id="staseModalTitle">Tambah Stase</span></h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="staseForm">
                <input type="hidden" name="id" id="staseId" value="">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nama Stase <span class="text-danger">*</span></label>
                            <input type="text" name="nama_stase" id="staseNama" class="form-control" required placeholder="Keperawatan Kritis">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Profesi</label>
                            <select name="profesi_id" id="staseProfesi" class="form-control">
                                <option value="">Pilih Profesi</option>
                                <?php if (!empty($profesiList)): ?>
                                    <?php foreach ($profesiList as $p): ?>
                                    <option value="<?= $p['id_profesi'] ?>"><?= esc($p['nama_profesi']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Ruangan</label>
                            <div class="border rounded p-3" style="max-height:160px;overflow-y:auto;">
                                <?php if (!empty($unitKerjaList)): ?>
                                    <?php foreach ($unitKerjaList as $u): ?>
                                    <div class="form-check">
                                        <input class="form-check-input stase-ruangan" type="checkbox" name="ruangan_ids[]" value="<?= $u['id_unit_kerja'] ?>" id="ruang_<?= $u['id_unit_kerja'] ?>">
                                        <label class="form-check-label" for="ruang_<?= $u['id_unit_kerja'] ?>"><?= esc($u['nama_unit']) ?></label>
                                    </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <small class="text-muted">Tidak ada data ruangan</small>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai" id="staseMulai" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Tanggal Akhir</label>
                            <input type="date" name="tanggal_akhir" id="staseAkhir" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Stase</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Stase Date Validation Modal -->
<div class="modal fade" id="staseDateErrorModal" tabindex="-1" aria-labelledby="staseDateErrorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h6 class="modal-title fw-bold" id="staseDateErrorModalLabel"><i class="fas fa-calendar-times me-2"></i>Tanggal Tidak Valid</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0" id="staseDateErrorMessage">Tanggal akhir tidak boleh lebih awal dari tanggal mulai.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Mengerti</button>
            </div>
        </div>
    </div>
</div>

<script>
function showStaseDateError(message) {
    $('#staseDateErrorMessage').text(message || 'Tanggal akhir tidak boleh lebih awal dari tanggal mulai.');
    $('#staseDateErrorModal').modal('show');
}

function isStaseDateRangeValid() {
    var mulai = $('#staseMulai').val();
    var akhir = $('#staseAkhir').val();
    return !(mulai && akhir && akhir < mulai);
}

function syncStaseAkhirMin() {
    var mulai = $('#staseMulai').val();
    if (mulai) {
        $('#staseAkhir').attr('min', mulai);
    } else {
        $('#staseAkhir').removeAttr('min');
    }
}

$('#staseMulai').on('change', function() {
    syncStaseAkhirMin();
    if (!isStaseDateRangeValid()) {
        $('#staseAkhir').val('');
        showStaseDateError();
    }
});

$('#staseAkhir').on('change', function() {
    if (!isStaseDateRangeValid()) {
        $(this).val('');
        showStaseDateError();
    }
});

function editStase(stase) {
    $('#staseId').val(stase.id);
    $('#staseModalTitle').text('Edit Stase');
    $('#staseNama').val(stase.nama_stase);
    $('#staseProfesi').val(stase.profesi_id || '');
    $('#staseMulai').val(stase.tanggal_mulai || '');
    $('#staseAkhir').val(stase.tanggal_akhir || '');
    syncStaseAkhirMin();
    
    $('.stase-ruangan').prop('checked', false);
    if (stase.ruangan) {
        var ruangIds = stase.ruangan.split(',');
        ruangIds.forEach(function(rid) {
            $('#ruang_' + rid.trim()).prop('checked', true);
        });
    }
    
    $('#addStaseModal').modal('show');
}

function deleteStase(id, name) {
    confirmDeleteAdminDiklat(
        'Hapus Stase?',
        'Stase "' + (name || 'ini') + '" akan dihapus permanen dan tidak dapat dikembalikan.'
    ).then(function(result) {
        if (!result.isConfirmed) return;

        $.post('<?= base_url('pendidikan/admin/diklat/api/stase/delete') ?>/' + id, function(res) {
            if (res.success) {
                showAdminDiklatNotification('success', 'Berhasil', res.message || 'Stase berhasil dihapus.').then(function() {
                    location.reload();
                });
            } else {
                showAdminDiklatNotification('error', 'Gagal', res.message || 'Gagal menghapus stase');
            }
        }).fail(function(xhr) {
            showAdminDiklatNotification('error', 'Gagal', xhr.responseJSON?.message || 'Server error');
        });
    });
}

$('#staseForm').submit(function(e) {
    e.preventDefault();

    if (!isStaseDateRangeValid()) {
        showStaseDateError();
        return;
    }

    var id = $('#staseId').val();
    var isEdit = id ? true : false;
    var url = isEdit ? '<?= base_url('pendidikan/admin/diklat/api/stase/update') ?>/' + id : '<?= base_url('pendidikan/admin/diklat/api/stase') ?>';

    var ruanganIds = [];
    $('.stase-ruangan:checked').each(function() {
        ruanganIds.push($(this).val());
    });

    var data = {
        nama_stase: $('#staseNama').val(),
        profesi_id: $('#staseProfesi').val() || null,
        ruangan: ruanganIds.join(','),
        tanggal_mulai: $('#staseMulai').val() || null,
        tanggal_akhir: $('#staseAkhir').val() || null
    };

    $.ajax({
        url: url,
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify(data),
        success: function(res) {
            if (res.success) {
                reloadAdminDiklatAfterModal('#addStaseModal', res.message || 'Stase berhasil disimpan.');
            } else {
                showAdminDiklatNotification('error', 'Gagal', res.message || 'Gagal menyimpan stase');
            }
        },
        error: function(xhr) {
            var message = xhr.responseJSON?.message || 'Server error';
            if (xhr.status === 422 && message === 'Tanggal akhir tidak boleh lebih awal dari tanggal mulai.') {
                showStaseDateError(message);
                return;
            }
            showAdminDiklatNotification('error', 'Gagal', message);
        }
    });
});

$('#addStaseModal').on('hidden.bs.modal', function() {
    $('#staseId').val('');
    $('#staseForm')[0].reset();
    $('.stase-ruangan').prop('checked', false);
    $('#staseAkhir').removeAttr('min');
    $('#staseModalTitle').text('Tambah Stase');
});
</script>

<?= $this->include('Pendidikan/AdminDiklat/layout/footer') ?>
