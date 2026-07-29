<?= $this->extend('Pelatihan/layout/admin_layout') ?>

<?= $this->section('content') ?>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

<?php
$list_aktif = [];
$list_draft = [];
$list_berakhir = [];

$today = date('Y-m-d');

if (!empty($list)) {
    foreach ($list as $p) {
        $selesai = !empty($p['jadwal_selesai']) ? $p['jadwal_selesai'] : '2099-12-31';
        
        $status_p = empty($p['status']) ? 'Draft' : $p['status'];
        
        if ($status_p === 'Selesai' || $status_p === 'Batal' || $selesai < $today) {
            $list_berakhir[] = $p;
        } elseif ($status_p === 'Draft') {
            $list_draft[] = $p;
        } else {
            $list_aktif[] = $p;
        }
    }
}
?>

<div class="row g-3 mb-3">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm bg-white border-start border-success border-4 rounded-custom">
            <div class="card-body d-flex align-items-center justify-content-between py-3">
                <div>
                    <div class="text-muted fw-bold uppercase-tracking mb-1" style="font-size:0.65rem;">PELATIHAN AKTIF</div>
                    <h4 class="fw-bold mb-0 text-dark"><?= count($list_aktif) ?> <span class="fs-6 text-muted fw-normal">Program</span></h4>
                </div>
                <div class="bg-success bg-opacity-10 text-success p-2-5 rounded-3">
                    <i class="fas fa-play small-icon-stat"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm bg-white border-start border-warning border-4 rounded-custom">
            <div class="card-body d-flex align-items-center justify-content-between py-3">
                <div>
                    <div class="text-muted fw-bold uppercase-tracking mb-1" style="font-size:0.65rem;">DRAFT DIKLAT</div>
                    <h4 class="fw-bold mb-0 text-dark"><?= count($list_draft) ?> <span class="fs-6 text-muted fw-normal">Program</span></h4>
                </div>
                <div class="bg-warning bg-opacity-10 text-warning p-2-5 rounded-3">
                    <i class="fas fa-file-signature small-icon-stat"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm bg-white border-start border-secondary border-4 rounded-custom">
            <div class="card-body d-flex align-items-center justify-content-between py-3">
                <div>
                    <div class="text-muted fw-bold uppercase-tracking mb-1" style="font-size:0.65rem;">BERAKHIR / BATAL</div>
                    <h4 class="fw-bold mb-0 text-dark"><?= count($list_berakhir) ?> <span class="fs-6 text-muted fw-normal">Selesai</span></h4>
                </div>
                <div class="bg-light text-secondary p-2-5 rounded-3 border">
                    <i class="fas fa-history small-icon-stat"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-custom overflow-hidden bg-white mb-5">
    <div class="card-header bg-white p-0 border-bottom">
        <ul class="nav nav-tabs nav-fill border-0 m-0" id="pelatihanTab" role="tablist">
            <li class="nav-item">
                <button class="nav-link active py-3 fw-bold small text-uppercase toggle-tab-custom" id="aktif-tab" data-bs-toggle="tab" data-bs-target="#aktif" type="button">
                    <i class="fas fa-dot-circle text-success me-1-5 small"></i> Aktif (<?= count($list_aktif) ?>)
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link py-3 fw-bold small text-uppercase toggle-tab-custom" id="draft-tab" data-bs-toggle="tab" data-bs-target="#draft" type="button">
                    <i class="fas fa-stop-circle text-warning me-1-5 small"></i> Draft (<?= count($list_draft) ?>)
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link py-3 fw-bold small text-uppercase toggle-tab-custom" id="berakhir-tab" data-bs-toggle="tab" data-bs-target="#berakhir" type="button">
                    <i class="fas fa-minus-circle text-secondary me-1-5 small"></i> Selesai / Batal (<?= count($list_berakhir) ?>)
                </button>
            </li>
        </ul>
    </div>

    <div class="p-3 bg-light bg-opacity-50 border-bottom">
        <div class="small fw-bold text-secondary uppercase-tracking"><i class="fas fa-stream me-1"></i> Daftar Manajemen Plotting</div>
    </div>

    <div class="tab-content" id="pelatihanTabContent">
        
        <div class="tab-pane fade show active p-3" id="aktif" role="tabpanel">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 w-100 table-diklat" id="tableAktif">
                    <?= renderTableHeader() ?>
                    <tbody>
                        <?php foreach($list_aktif as $l): ?>
                            <?= renderTableRow($l, 'success', 'Aktif') ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="tab-pane fade p-3" id="draft" role="tabpanel">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 w-100 table-diklat" id="tableDraft">
                    <?= renderTableHeader() ?>
                    <tbody>
                        <?php foreach($list_draft as $l): ?>
                            <?= renderTableRow($l, 'warning', 'Draft') ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="tab-pane fade p-3" id="berakhir" role="tabpanel">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 w-100 table-diklat" id="tableBerakhir">
                    <?= renderTableHeader() ?>
                    <tbody>
                        <?php foreach($list_berakhir as $l): ?>
                            <?= renderTableRow($l, 'secondary', 'Selesai/Batal', true) ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<?php
function renderTableHeader() {
    return '
    <thead class="bg-light text-muted fw-bold uppercase-tracking border-bottom">
        <tr>
            <th class="ps-4 py-3" style="white-space: nowrap; font-size: 0.65rem;">PROGRAM DIKLAT</th>
            <th style="white-space: nowrap; font-size: 0.65rem;">METODE & PROGRAM</th>
            <th class="text-center" style="white-space: nowrap; font-size: 0.65rem;">KUOTA</th>
            <th class="text-center" style="white-space: nowrap; font-size: 0.65rem;">STATUS & SELESAI</th>
            <th class="text-center" style="white-space: nowrap; font-size: 0.65rem;">PESERTA TERPLOT</th>
            <th class="pe-4 text-center" style="white-space: nowrap; font-size: 0.65rem;">AKSI KELOLA</th>
        </tr>
    </thead>';
}

function renderTableRow(array $l, string $badgeColor, string $statusLabel, bool $isClosed = false) {
    $disableClass = $isClosed ? 'disabled btn-light text-muted border border-opacity-75' : 'btn-danger-custom';
    $disableClick = $isClosed ? 'onclick="alertClosed(event)"' : '';
    $btnRouteTambah = $isClosed ? '#' : base_url('pelatihan/admin/add_peserta/'.$l['id']);
    
    return '
    <tr class="transition-row">
        <td class="ps-4 py-3">
            <div class="fw-bold text-dark small mb-1 text-truncate" style="max-width: 300px;" title="'.esc($l['nama']).'">'.esc($l['nama']).'</div>
            <div class="text-muted d-flex align-items-center gap-1" style="font-size: 0.65rem;">
                <span class="fw-bold text-secondary">ID: #'.str_pad($l['id'], 4, '0', STR_PAD_LEFT).'</span>
            </div>
        </td>
        <td>
            <span class="badge bg-light text-dark border border-secondary border-opacity-25 rounded-pill px-2-5 py-1 fw-bold" style="font-size: 0.6rem;">
                '.strtoupper(esc($l['metode'])).'
            </span>
            <div class="text-muted mt-1 small" style="font-size: 0.65rem;"><i class="fas fa-layer-group text-muted me-1"></i> '.esc($l['program'] ?? 'Umum').'</div>
        </td>
        <td class="text-center fw-bold text-dark small">'.esc($l['kuota']).' Mhs</td>
        <td class="text-center">
            <span class="badge bg-'.$badgeColor.' rounded-pill px-2 py-1 fw-bold text-uppercase mb-1" style="font-size: 0.58rem; letter-spacing: 0.3px;">'.$statusLabel.'</span>
            <div class="text-muted mt-1" style="font-size: 0.65rem;"><i class="far fa-clock me-1"></i> '.(!empty($l['jadwal_selesai']) ? tanggal_indo($l['jadwal_selesai']) : '-').'</div>
        </td>
        <td class="text-center">
            <div class="d-inline-flex align-items-center gap-1 rounded-pill px-2-5 py-1 fw-bold border border-danger border-opacity-10" style="font-size: 0.65rem; background-color: #fff5f5; color: #ce2127;">
                <i class="fas fa-users" style="font-size:0.6rem;"></i> '.esc($l['total_peserta']).' Terplot
            </div>
        </td>
        <td class="pe-4 text-center">
            <div class="d-flex gap-1-5 justify-content-center flex-wrap">
                <a href="'.$btnRouteTambah.'" class="btn btn-action-pill text-uppercase '.$disableClass.'" '.$disableClick.' title="Plot Peserta">
                    <i class="fas fa-user-plus me-1 small"></i> PLOT
                </a>
                <a href="'.base_url('pelatihan/admin/presensi/'.$l['id']).'" class="btn btn-action-pill btn-white text-uppercase shadow-sm" title="Monitoring Kelas">
                    <i class="fas fa-desktop me-1 small"></i> MONITOR
                </a>
                <a href="'.base_url('pelatihan/admin/monitoring/export_excel/'.$l['id']).'" class="btn btn-action-pill btn-white text-uppercase shadow-sm" title="Export Excel">
                    <i class="fas fa-file-excel text-success me-1 small"></i> EXCEL
                </a>
                <a href="'.base_url('pelatihan/admin/grading/detail/'.$l['id']).'" class="btn btn-action-pill btn-white text-uppercase shadow-sm" title="Nilai Post-test">
                    <i class="fas fa-poll me-1 small"></i> NILAI
                </a>
                <a href="'.base_url('pelatihan/admin/feedback/detail/'.$l['id']).'" class="btn btn-action-pill btn-white text-uppercase shadow-sm" title="Feedback Pelatihan">
                    <i class="fas fa-comment-dots me-1 small"></i> FEEDBACK
                </a>
            </div>
        </td>
    </tr>';
}
?>

<style>
    /* Dasar Utilitas Premium */
    .rounded-custom { border-radius: 1rem !important; }
    .uppercase-tracking { letter-spacing: 0.6px; font-size: 0.72rem !important; }
    
    /* Ukuran Mikro Miniatur Ikon Statistik */
    .small-icon-stat {
        font-size: 1rem !important;
        width: 16px;
        height: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .p-2-5 { padding: 0.65rem !important; }

    /* Tombol Desain Pill Aksi Baru */
    .btn-action-pill {
        font-size: 0.65rem !important;
        font-weight: 700;
        padding: 0.35rem 0.85rem !important;
        border-radius: 30px !important;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease-in-out;
        letter-spacing: 0.2px;
        text-decoration: none;
    }
    .btn-danger-custom {
        background: #ce2127;
        color: white;
        border: none;
    }
    .btn-danger-custom:hover {
        background: #a51a1f;
        color: white;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(206, 33, 39, 0.15);
    }
    .btn-white { 
        background: #fff; 
        border: 1px solid #dee2e6; 
        color: #495057; 
    }
    .btn-white:hover { 
        background: #f8fafc; 
        color: #212529; 
        border-color: #cbd5e1;
        transform: translateY(-1px);
    }

    /* Penyesuaian Style Nav Tabs Modern */
    .toggle-tab-custom {
        color: #64748b !important;
        border: none !important;
        border-bottom: 3px solid transparent !important;
        transition: all 0.2s ease;
        background: transparent !important;
    }
    .nav-tabs .nav-link.active {
        color: #ce2127 !important;
        border-bottom: 3px solid #ce2127 !important;
        font-weight: 800 !important;
        background-color: #fff !important;
    }
    .nav-tabs .nav-link:hover:not(.active) {
        color: #212529 !important;
        border-bottom: 3px solid #e2e8f0 !important;
    }
    
    /* Hover Baris Tabel */
    .transition-row { transition: all 0.15s ease; }
    .table-hover tbody tr:hover { 
        background-color: #f8fafc !important; 
    }

    /* Kustomisasi Input Form & Filter */
    .form-control:focus {
        box-shadow: none !important;
        border-color: transparent !important;
    }
    .gap-1-5 { gap: 0.35rem !important; }
    .me-1-5 { margin-right: 0.35rem !important; }

    /* Override Paginasi DataTables (Merah-Hitam Minimalis) */
    .page-item.active .page-link {
        background-color: #ce2127 !important;
        border-color: #ce2127 !important;
        color: #fff !important;
    }
    .page-link { color: #212529 !important; font-size: 0.75rem; padding: 0.35rem 0.65rem; }
    .page-link:hover {
        color: #ce2127 !important;
        background-color: #fff5f5 !important;
        border-color: #dee2e6 !important;
    }
</style>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        var dtConfig = {
            pageLength: 10,
            responsive: true,
            dom: 'lrtip',
            language: { 
                emptyTable: "Tidak ada data diklat pada kategori ini.", 
                info: "Menampilkan _START_ s/d _END_ dari _TOTAL_ diklat", 
                paginate: { previous: "‹", next: "›" } 
            }
        };

        var tAktif    = $('#tableAktif').DataTable(dtConfig);
        var tDraft    = $('#tableDraft').DataTable(dtConfig);
        var tBerakhir = $('#tableBerakhir').DataTable(dtConfig);



        $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
            $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
        });
    });

    function alertClosed(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Pendaftaran Ditutup!',
            text: 'Program diklat ini telah berakhir atau dibatalkan.',
            icon: 'error',
            confirmButtonColor: '#ce2127',
            confirmButtonText: 'Paham'
        });
    }
</script>
<?= $this->endSection() ?>
