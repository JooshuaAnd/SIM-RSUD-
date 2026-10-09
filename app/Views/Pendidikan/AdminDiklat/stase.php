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
<style>
    @media (min-width: 992px) {
        #addStaseModal .modal-dialog { max-width: 1100px; }
    }

    #addStaseModal .modal-content {
        border: 0;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(0, 0, 0, .16);
    }

    #addStaseModal .modal-header {
        padding: 24px;
        border-bottom: 1px solid #edf0f3;
        gap: 16px;
    }

    #addStaseModal .stase-form-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 48px;
        height: 48px;
        flex-shrink: 0;
        border-radius: 14px;
        background: #fff0f0;
        color: var(--primary-red, #c62828);
        font-size: 20px;
    }

    #addStaseModal .modal-title { font-size: 18px; }
    #addStaseModal #staseForm {
        display: flex;
        flex-direction: column;
        min-height: 0;
        overflow: hidden;
    }

    #addStaseModal .modal-body {
        padding: 24px;
        overflow-y: auto;
    }
    #addStaseModal .stase-section-title {
        font-size: 13px;
        font-weight: 700;
        color: #343a40;
        margin: 0;
    }

    #addStaseModal .form-label { color: #495057; }
    #addStaseModal .form-control,
    #addStaseModal .form-select {
        min-height: 46px;
        border-color: #dee2e6;
        border-radius: 10px;
        font-size: 14px;
        padding: 10px 12px;
    }

    #addStaseModal .form-control:focus,
    #addStaseModal .form-select:focus {
        border-color: var(--primary-red, #c62828);
        box-shadow: 0 0 0 3px rgba(198, 40, 40, .1);
    }

    #addStaseModal .stase-room-list {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
        max-height: 220px;
        overflow-y: auto;
        padding: 12px;
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 12px;
    }

    #addStaseModal .stase-room-list .form-check {
        position: relative;
        padding: 0;
        margin: 0;
    }

    #addStaseModal .stase-room-list .form-check-input {
        position: absolute;
        top: 16px;
        left: 12px;
        margin: 0;
    }

    #addStaseModal .stase-room-list .form-check-label {
        display: block;
        height: 100%;
        padding: 12px 12px 12px 38px;
        border: 1px solid #e3e7eb;
        border-radius: 9px;
        background: #fff;
        font-size: 13px;
        cursor: pointer;
        overflow-wrap: anywhere;
    }

    #addStaseModal .stase-room-list .form-check-label:hover { border-color: #d7a5a5; }
    #addStaseModal .form-check-input:checked {
        background-color: var(--primary-red, #c62828);
        border-color: var(--primary-red, #c62828);
    }

    #addStaseModal .form-check-input:focus {
        border-color: var(--primary-red, #c62828);
        box-shadow: 0 0 0 3px rgba(198, 40, 40, .1);
    }

    #addStaseModal .form-check-input:checked + .form-check-label {
        background: #fff0f0;
        border-color: #e6aaaa;
        color: #a62020;
    }

    #addStaseModal .modal-footer {
        padding: 16px 24px;
        background: #f8f9fa;
        border-top: 1px solid #edf0f3;
        gap: 8px;
    }

    #addStaseModal .modal-footer .btn {
        padding: 10px 20px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 600;
        margin: 0;
    }

    @media (max-width: 575.98px) {
        #addStaseModal .modal-header,
        #addStaseModal .modal-body { padding: 20px 16px; }
        #addStaseModal .modal-footer { padding: 16px; }
        #addStaseModal .stase-room-list { grid-template-columns: 1fr; }
    }
</style>
<div class="modal fade" id="addStaseModal" tabindex="-1" aria-labelledby="staseModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div class="stase-form-icon"><i class="fas fa-route" aria-hidden="true"></i></div>
                <div class="flex-grow-1">
                    <h6 class="modal-title fw-bold text-dark" id="staseModalTitle">Tambah Stase</h6>
                    <p class="small text-muted mb-0 mt-1">Atur informasi, ruangan, dan periode stase.</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <form id="staseForm">
                <input type="hidden" name="id" id="staseId" value="">
                <div class="modal-body">
                    <div class="row g-4">
                        <div class="col-lg-6">
                        <div class="row g-3">
                        <div class="col-12"><h6 class="stase-section-title">Informasi Stase</h6></div>
                        <div class="col-md-6">
                            <label for="staseNama" class="form-label small fw-bold">Nama Stase <span class="text-danger">*</span></label>
                            <input type="text" name="nama_stase" id="staseNama" class="form-control" required placeholder="Keperawatan Kritis">
                        </div>
                        <div class="col-md-6">
                            <label for="staseProfesi" class="form-label small fw-bold">Profesi</label>
                            <select name="profesi_id" id="staseProfesi" class="form-select">
                                <option value="">Pilih Profesi</option>
                                <?php if (!empty($profesiList)): ?>
                                    <?php foreach ($profesiList as $p): ?>
                                    <option value="<?= $p['id_profesi'] ?>"><?= esc($p['nama_profesi']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-12 mt-4"><h6 class="stase-section-title">Periode Stase</h6></div>
                        <div class="col-md-6">
                            <label for="staseMulai" class="form-label small fw-bold">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai" id="staseMulai" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label for="staseAkhir" class="form-label small fw-bold">Tanggal Akhir</label>
                            <input type="date" name="tanggal_akhir" id="staseAkhir" class="form-control">
                        </div>
                        </div>
                        </div>
                        <div class="col-lg-6">
                            <h6 class="stase-section-title" id="staseRoomsLabel">Ruangan</h6>
                            <p class="small text-muted mt-1 mb-2">Pilih satu atau beberapa ruangan untuk stase ini.</p>
                            <div class="stase-room-list" role="group" aria-labelledby="staseRoomsLabel">
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
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-check me-2" aria-hidden="true"></i>Simpan Stase</button>
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
