<?php
    $totalSesiPelatihan = count($sesiList ?? []);
    $preTestSesi = count(array_filter($sesiList ?? [], fn ($s) => !empty($s['pre_test'])));
    $postTestSesi = count(array_filter($sesiList ?? [], fn ($s) => !empty($s['post_test'])));
?>

<div class="tab-pane fade" id="tab-evaluasi" role="tabpanel">
    <div class="alert alert-light border rounded-lg d-flex gap-3 align-items-start mb-4" role="alert">
        <i class="fas fa-info-circle text-primary mt-1"></i>
        <div class="small text-muted">
            Pre-test dan post-test sekarang diatur per sesi. Pilih sesi terlebih dahulu, lalu lanjutkan ke pengaturan soal dan KKM untuk tes tersebut.
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-lg p-4 h-100 border-top border-danger border-4">
                <div class="d-flex justify-content-between mb-3">
                    <h6 class="fw-bold text-danger mb-0"><i class="fas fa-file-signature me-2"></i> PRE-TEST PER SESI</h6>
                    <span class="badge bg-light text-dark border small"><?= $preTestSesi ?>/<?= $totalSesiPelatihan ?> Sesi</span>
                </div>
                <p class="small text-muted mb-4">Ujian awal untuk mengukur kompetensi peserta sebelum mengikuti masing-masing sesi.</p>
                <div class="d-grid">
                    <button class="btn btn-light border btn-sm py-2 rounded-pill" onclick="bukaModalPilihEvaluasi('Pre-Test')" <?= empty($sesiList) ? 'disabled' : '' ?>>
                        <i class="fas fa-plus-circle me-2"></i> Tambah / Kelola Pre-Test
                    </button>
                </div>
                <?php if (empty($sesiList)) : ?>
                    <small class="text-danger text-center mt-3">Tambahkan sesi pelatihan terlebih dahulu.</small>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-lg p-4 h-100 border-top border-warning border-4">
                <div class="d-flex justify-content-between mb-3">
                    <h6 class="fw-bold text-warning mb-0"><i class="fas fa-award me-2"></i> POST-TEST PER SESI</h6>
                    <span class="badge bg-warning-subtle text-warning small"><?= $postTestSesi ?>/<?= $totalSesiPelatihan ?> Sesi</span>
                </div>
                <p class="small text-muted mb-4">Ujian akhir untuk mengukur pemahaman peserta setelah menyelesaikan masing-masing sesi.</p>
                <div class="d-grid">
                    <button class="btn btn-light border btn-sm py-2 rounded-pill" onclick="bukaModalPilihEvaluasi('Post-Test')" <?= empty($sesiList) ? 'disabled' : '' ?>>
                        <i class="fas fa-plus-circle me-2"></i> Tambah / Kelola Post-Test
                    </button>
                </div>
                <?php if (empty($sesiList)) : ?>
                    <small class="text-danger text-center mt-3">Tambahkan sesi pelatihan terlebih dahulu.</small>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

