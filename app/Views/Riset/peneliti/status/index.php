<?php
/**
 * @var array<array{id: string, judul: string, status: string, tanggal: string}> $studi_pendahuluan
 * @var array<array{id: string, judul: string, status: string, tanggal: string}> $izin_penelitian
 * @var array<array{id: string, judul: string, status: string, tanggal: string}> $publikasi
 */
?>
<?= $this->extend('Riset/peneliti/layout/template') ?>

<?= $this->section('content') ?>

<div class="mb-4">
    <div class="d-flex align-items-center mb-1">
        <div class="bg-danger bg-opacity-10 text-danger rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
            <i class="fas fa-search-location"></i>
        </div>
        <h4 class="fw-bold text-dark mb-0">Portal Pelacakan Status Pengajuan</h4>
    </div>
    <p class="text-muted small mb-0 ms-5" style="padding-left: 12px;">Pantau proses administrasi, persetujuan dokumen, dan pembayaran secara real-time.</p>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4 py-3 d-flex align-items-center" style="background: rgba(40, 167, 69, 0.05); color: #28a745;">
        <i class="fas fa-check-circle me-3" style="font-size: 20px;"></i>
        <div class="fw-bold small"><?= session()->getFlashdata('success') ?></div>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4 py-3 d-flex align-items-center" style="background: rgba(220, 53, 69, 0.05); color: #dc3545;">
        <i class="fas fa-exclamation-circle me-3" style="font-size: 20px;"></i>
        <div class="fw-bold small"><?= session()->getFlashdata('error') ?></div>
    </div>
<?php endif; ?>

<!-- Tabs Navigation -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
    <div class="card-header bg-white py-3 px-4 border-bottom border-light">
        <ul class="nav nav-pills card-header-pills gap-2" id="statusTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-bold px-3 py-2 rounded-pill position-relative" id="studi-tab" data-bs-toggle="tab" data-bs-target="#studi" type="button" role="tab" style="font-size: 12px;">
                    Studi Pendahuluan
                    <span class="badge bg-secondary ms-1 text-white rounded-pill" style="font-size: 9px; padding: 3px 6px;"><?= count($studi_pendahuluan) ?></span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold px-3 py-2 rounded-pill position-relative" id="izin-tab" data-bs-toggle="tab" data-bs-target="#izin" type="button" role="tab" style="font-size: 12px;">
                    Izin Penelitian
                    <span class="badge bg-secondary ms-1 text-white rounded-pill" style="font-size: 9px; padding: 3px 6px;"><?= count($izin_penelitian) ?></span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold px-3 py-2 rounded-pill position-relative" id="publikasi-tab" data-bs-toggle="tab" data-bs-target="#publikasi" type="button" role="tab" style="font-size: 12px;">
                    Publikasi
                    <span class="badge bg-secondary ms-1 text-white rounded-pill" style="font-size: 9px; padding: 3px 6px;"><?= count($publikasi) ?></span>
                </button>
            </li>
        </ul>
    </div>

    <!-- Tab Contents -->
    <div class="card-body p-0">
        <div class="tab-content" id="statusTabsContent">
            
            <!-- 1. TAB STUDI PENDAHULUAN -->
            <div class="tab-pane fade show active" id="studi" role="tabpanel" aria-labelledby="studi-tab">
                <?= view('riset/peneliti/status/partials/table_submissions', [
                    'submissions' => $studi_pendahuluan,
                    'type' => 'studi',
                    'detail_route' => 'riset/peneliti/pengajuan/stupen/detail',
                    'print_route' => 'riset/peneliti/pengajuan/stupen/print',
                    'revisi_route' => 'riset/peneliti/pengajuan/stupen/baru'
                ]) ?>
            </div>

            <!-- 2. TAB IZIN PENELITIAN -->
            <div class="tab-pane fade" id="izin" role="tabpanel" aria-labelledby="izin-tab">
                <?= view('riset/peneliti/status/partials/table_submissions', [
                    'submissions' => $izin_penelitian,
                    'type' => 'izin',
                    'detail_route' => 'riset/peneliti/pengajuan/izin/detail',
                    'print_route' => 'riset/peneliti/pengajuan/izin/print',
                    'revisi_route' => 'riset/peneliti/pengajuan/izin/baru'
                ]) ?>
            </div>

            <!-- 5. TAB PUBLIKASI -->
            <div class="tab-pane fade" id="publikasi" role="tabpanel" aria-labelledby="publikasi-tab">
                <?= view('riset/peneliti/status/partials/table_submissions', [
                    'submissions' => $publikasi,
                    'type' => 'publikasi',
                    'detail_route' => 'riset/peneliti/publikasi/detail', // will pass ?id=
                    'print_route' => 'riset/peneliti/publikasi/print_izin',
                    'revisi_route' => 'riset/peneliti/publikasi/form'
                ]) ?>
            </div>

        </div>
    </div>
</div>

<style>
    .nav-pills .nav-link {
        color: #6c757d;
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }
    .nav-pills .nav-link.active, .nav-pills .show > .nav-link {
        color: #fff;
        background-color: #e53935;
        border-color: #e53935;
        box-shadow: 0 4px 10px rgba(229, 57, 53, 0.3);
    }
    .nav-pills .nav-link:hover:not(.active) {
        background-color: #ffebee;
        color: #e53935;
        border-color: #ffcdd2;
    }
</style>

<?= $this->endSection() ?>
