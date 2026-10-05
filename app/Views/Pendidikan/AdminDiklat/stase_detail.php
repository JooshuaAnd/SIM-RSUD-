<?= $this->include('Pendidikan/AdminDiklat/layout/header') ?>
<?= $this->include('Pendidikan/AdminDiklat/layout/sidebar') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-3">
        <a href="<?= base_url('pendidikan/admin/diklat/stase') ?>" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
        <h5 class="fw-bold text-dark mb-0">Mapping Detail Stase: <?= esc($stase['nama_stase'] ?? '-') ?></h5>
    </div>
</div>

<div class="card p-4 mb-4 bg-primary bg-opacity-10 border-0 rounded-3">
    <div class="row">
        <div class="col-md-4">
            <p class="mb-1 text-muted small fw-bold text-uppercase">Nama Stase</p>
            <p class="mb-0 fw-semibold text-dark"><?= esc($stase['nama_stase'] ?? '-') ?></p>
        </div>
        <div class="col-md-4">
            <p class="mb-1 text-muted small fw-bold text-uppercase">Profesi</p>
            <p class="mb-0 fw-semibold text-primary"><?= esc($stase['nama_profesi'] ?? '-') ?></p>
        </div>
        <div class="col-md-4">
            <p class="mb-1 text-muted small fw-bold text-uppercase">Periode</p>
            <p class="mb-0 fw-semibold text-dark">
                <?= $stase['tanggal_mulai'] ? date('d/m/Y', strtotime($stase['tanggal_mulai'])) : '-' ?> 
                &rarr; 
                <?= $stase['tanggal_akhir'] ? date('d/m/Y', strtotime($stase['tanggal_akhir'])) : '-' ?>
            </p>
        </div>
    </div>
</div>

<h6 class="fw-bold text-dark mb-3"><i class="fas fa-map-marker-alt me-2 text-danger"></i> Mapping Per Ruangan</h6>

<?php if (empty($ruanganList)): ?>
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle me-2"></i> Stase ini belum memiliki ruangan yang didaftarkan.
    </div>
<?php else: ?>
    <div class="row g-4">
        <?php foreach ($ruanganList as $ruangan): ?>
            <?php 
                $rId = $ruangan['id_unit_kerja'];
                $mappedData = $mappedRooms[$rId] ?? ['ci_id' => 0, 'mahasiswa_ids' => []];
                $currentCiId = $mappedData['ci_id'];
                $currentMhsIds = $mappedData['mahasiswa_ids'];
            ?>
            <div class="col-12">
                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold text-dark">
                            <i class="fas fa-door-open me-2 text-primary"></i> <?= esc($ruangan['nama_unit']) ?>
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <form id="form-mapping-<?= $rId ?>" class="mapping-form" onsubmit="saveMapping(event, <?= $rId ?>)">
                            <div class="row g-4">
                                <!-- Bagian CI -->
                                <div class="col-md-4 border-end">
                                    <h6 class="fw-bold mb-3 small text-muted text-uppercase">1. Pilih Clinical Instructor (CI)</h6>
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <span class="badge bg-success bg-opacity-10 text-success small px-2 py-1">
                                            <i class="fas fa-circle me-1" style="font-size:6px;"></i> Tersedia
                                        </span>
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary small px-2 py-1">
                                            <i class="fas fa-circle me-1" style="font-size:6px;"></i> Periode bertabrakan
                                        </span>
                                    </div>
                                    <div class="form-group">
                                        <select name="ci_id" id="ci_id_<?= $rId ?>" class="form-select">
                                            <option value="">-- Pilih CI --</option>
                                            <?php 
                                            $sortedCi = $ciList;
                                            usort($sortedCi, function($a, $b) {
                                                return ($a['available'] ?? true) <=> ($b['available'] ?? true);
                                            });
                                            ?>
                                            <?php foreach ($sortedCi as $ci): ?>
                                                <?php if ($ci['id_unit_kerja'] == $rId): ?>
                                                    <?php $isCurrent = $currentCiId == $ci['id']; ?>
                                                    <option value="<?= $ci['id'] ?>" <?= $isCurrent ? 'selected' : '' ?> <?= ($ci['has_overlap'] && !$isCurrent) ? 'disabled' : '' ?>>
                                                        <?= esc($ci['nama_lengkap']) ?>
                                                        <?php if ($ci['has_overlap'] && !$isCurrent): ?> (Periode bertabrakan)<?php endif; ?>
                                                        <?php if ($isCurrent): ?> (CI saat ini)<?php endif; ?>
                                                    </option>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </select>
                                        <small class="text-muted mt-2 d-block">* CI difilter berdasarkan profesi <b><?= esc($stase['nama_profesi'] ?? '-') ?></b>.</small>
                                        <small class="text-warning fw-bold mt-1 d-none" id="ci-warning-<?= $rId ?>"><i class="fas fa-exclamation-triangle me-1"></i>Pilih CI terlebih dahulu sebelum menyimpan mahasiswa</small>
                                    </div>
                                </div>
                                
                                <!-- Bagian Mahasiswa -->
                                <div class="col-md-8">
                                    <h6 class="fw-bold mb-3 small text-muted text-uppercase">2. Assign Mahasiswa</h6>
                                    
                                    <!-- Filter Institusi -->
                                    <div class="mb-3 d-flex align-items-center gap-2">
                                        <label class="form-label mb-0 small fw-bold">Filter Institusi:</label>
                                        <select class="form-select form-select-sm w-auto filter-institusi" data-target="mhs-container-<?= $rId ?>">
                                            <option value="all">Tampilkan Semua Institusi</option>
                                            <?php foreach ($institusiList as $inst): ?>
                                                <option value="<?= $inst['id'] ?>"><?= esc($inst['nama_institusi']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    
                                    <!-- List Mahasiswa Checkboxes -->
                                    <div class="border rounded-3 p-3 bg-light" style="max-height: 250px; overflow-y: auto;">
                                        <div class="row g-2" id="mhs-container-<?= $rId ?>">
                                            <?php if(empty($mahasiswaList)): ?>
                                                <div class="col-12 text-center text-muted fst-italic py-3">Tidak ada mahasiswa yang sesuai dengan profesi ini.</div>
                                            <?php endif; ?>
                                            
                                            <?php foreach ($mahasiswaList as $mhs): ?>
                                                <?php 
                                                $isChecked = in_array($mhs['id'], $currentMhsIds);
                                                $hasOverlap = $mhs['has_overlap'] ?? false;
                                                ?>
                                                <div class="col-md-6 mhs-item" data-institusi="<?= $mhs['institusi_id'] ?>">
                                                    <div class="form-check p-2 border rounded bg-white <?= $isChecked ? 'border-primary bg-primary bg-opacity-10' : '' ?> <?= $hasOverlap ? 'border-warning bg-warning bg-opacity-5' : '' ?>" style="transition: all 0.2s;">
                                                        <input class="form-check-input ms-1 me-2 mhs-checkbox" type="checkbox" name="mahasiswa_ids[]" value="<?= $mhs['id'] ?>" id="mhs_<?= $rId ?>_<?= $mhs['id'] ?>" <?= $isChecked ? 'checked' : '' ?> <?= $hasOverlap ? 'disabled' : '' ?> onchange="toggleMhsHighlight(this)">
                                                        <label class="form-check-label w-100 cursor-pointer" for="mhs_<?= $rId ?>_<?= $mhs['id'] ?>">
                                                            <span class="fw-semibold text-dark d-block" style="font-size:13px;"><?= esc($mhs['nama_lengkap']) ?></span>
                                                            <span class="text-muted d-block" style="font-size:11px;"><?= esc($mhs['nim']) ?></span>
                                                            <?php if ($hasOverlap): ?>
                                                                <span class="d-block text-warning small fw-bold mt-1" style="font-size:10px;"><i class="fas fa-clock me-1"></i>Periode bertabrakan dengan stase lain</span>
                                                            <?php endif; ?>
                                                        </label>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="text-end mt-4 pt-3 border-top">
                                <button type="submit" class="btn btn-primary px-4" id="btn-save-<?= $rId ?>">
                                    <i class="fas fa-save me-1"></i> Simpan Mapping Ruangan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<script>
// Filter Institusi
$('.filter-institusi').on('change', function() {
    let targetId = $(this).data('target');
    let instId = $(this).val();
    
    if (instId === 'all') {
        $('#' + targetId + ' .mhs-item').show();
    } else {
        $('#' + targetId + ' .mhs-item').hide();
        $('#' + targetId + ' .mhs-item[data-institusi="'+instId+'"]').show();
    }
});

// Highlight checkbox UI
function toggleMhsHighlight(el) {
    let container = $(el).closest('.form-check');
    if ($(el).is(':checked')) {
        container.addClass('border-primary bg-primary bg-opacity-10');
    } else {
        container.removeClass('border-primary bg-primary bg-opacity-10');
    }
}

// Warning CI wajib dipilih jika ada mahasiswa
function toggleCiWarning(ruanganId) {
    let ciSelected = $('#ci_id_' + ruanganId).val() !== '';
    let mhsChecked = $('#form-mapping-' + ruanganId + ' input.mhs-checkbox:checked').length > 0;
    let warning = $('#ci-warning-' + ruanganId);
    
    if (!ciSelected && mhsChecked) {
        warning.removeClass('d-none');
    } else {
        warning.addClass('d-none');
    }
}

// Toggle warning when CI changes
$(document).on('change', '[id^="ci_id_"]', function() {
    let id = $(this).attr('id').replace('ci_id_', '');
    toggleCiWarning(id);
});

// Toggle warning when mahasiswa checkbox changes
$(document).on('change', '.mhs-checkbox', function() {
    let form = $(this).closest('.mapping-form');
    let ruanganId = form.attr('id').replace('form-mapping-', '');
    toggleCiWarning(ruanganId);
});

// Run on page load to show warning if any
$(function() {
    $('[id^="form-mapping-"]').each(function() {
        let ruanganId = $(this).attr('id').replace('form-mapping-', '');
        toggleCiWarning(ruanganId);
    });
});

// Save Mapping AJAX
function saveMapping(e, ruanganId) {
    e.preventDefault();
    let btn = $('#btn-save-' + ruanganId);
    let originalHtml = btn.html();
    
    let staseId = <?= $stase['id'] ?>;
    let ciId = $('#ci_id_' + ruanganId).val();
    
    let mhsIds = [];
    $('#form-mapping-' + ruanganId + ' input.mhs-checkbox:checked').each(function() {
        mhsIds.push(parseInt($(this).val()));
    });
    
    // Client-side validation: CI wajib jika ada mahasiswa
    if (!ciId && mhsIds.length > 0) {
        showAdminDiklatNotification('warning', 'Data belum lengkap', 'Pilih CI terlebih dahulu sebelum menyimpan mahasiswa.');
        return;
    }
    
    let payload = {
        ruangan_id: parseInt(ruanganId),
        ci_id: parseInt(ciId) || 0,
        mahasiswa_ids: mhsIds
    };
    
    btn.html('<i class="fas fa-spinner fa-spin me-1"></i> Menyimpan...').prop('disabled', true);
    
    $.ajax({
        url: '<?= base_url("pendidikan/admin/diklat/api/stase/save-mapping/") ?>' + staseId,
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify(payload),
        success: function(res) {
            if(res.success) {
                showAdminDiklatNotification('success', 'Berhasil', res.message || 'Mapping mahasiswa berhasil disimpan.');
                // Tampilkan status tersimpan pada tombol
                let successBtn = $('<button type="button" class="btn btn-success px-4" disabled><i class="fas fa-check me-1"></i> Tersimpan!</button>');
                btn.replaceWith(successBtn);
                setTimeout(() => {
                    successBtn.replaceWith(btn);
                    btn.html(originalHtml).prop('disabled', false);
                }, 2000);
            } else {
                showAdminDiklatNotification('error', 'Gagal', res.message || 'Terjadi kesalahan.');
                btn.html(originalHtml).prop('disabled', false);
            }
        },
        error: function(xhr) {
            showAdminDiklatNotification('error', 'Gagal menyimpan', xhr.responseJSON?.message || 'Server Error');
            btn.html(originalHtml).prop('disabled', false);
        }
    });
}
</script>

<?= $this->include('Pendidikan/AdminDiklat/layout/footer') ?>
