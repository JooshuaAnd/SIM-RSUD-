<?= $this->extend('Pelatihan/layout/admin_layout') ?>

<?= $this->section('content') ?>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

<?php
$profesi = $profesi ?? [];
$unit_kerja = $unit_kerja ?? [];
$users_named = $users_named ?? [];
$users_non_named = $users_non_named ?? [];
?>

<div class="mb-4 d-flex flex-column flex-md-row justify-content-between align-items-center bg-white p-3 rounded-custom shadow-sm border gap-3">
    <div class="btn-group p-1 bg-light rounded-pill border">
        <button class="btn btn-dark rounded-pill px-4 active fw-bold btn-switch" id="btn_named" onclick="switchCategory('named')">
            <i class="fas fa-building me-2-custom"></i> NAMED (PEGAWAI)
        </button>
        <button class="btn btn-light rounded-pill px-4 fw-bold text-muted border-0 btn-switch" id="btn_non_named" onclick="switchCategory('non-named')">
            <i class="fas fa-globe me-2-custom"></i> NON-NAMED (UMUM)
        </button>
    </div>
    <button class="btn btn-register-submit rounded-pill px-4 fw-bold border-0 d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalRegistrasi">
        <i class="fas fa-user-plus text-white small"></i> TAMBAH AKUN BARU
    </button>
</div>

<!-- SECTION NAMED (INTERNAL) -->
<div class="card border-0 shadow-sm rounded-custom overflow-hidden bg-white mb-5 border-top border-danger border-4" id="named_section">
    <div class="card-header bg-white p-4 border-bottom d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <div class="fw-bold text-dark small text-uppercase me-2 d-flex align-items-center gap-1">
                <i class="fas fa-filter text-danger"></i> Filter:
            </div>
            <select id="filterProfesiNamed" class="form-select form-select-custom rounded-pill border shadow-sm px-3 fw-semibold text-dark" style="width: 190px;">
                <option value="">SEMUA PROFESI</option>
                <?php foreach($profesi as $p): ?>
                <option value="<?= esc($p['nama_profesi']) ?>"><?= esc(strtoupper($p['nama_profesi'])) ?></option>
                <?php endforeach; ?>
            </select>
            <select id="filterRuanganNamed" class="form-select form-select-custom rounded-pill border shadow-sm px-3 fw-semibold text-dark" style="width: 190px;">
                <option value="">SEMUA UNIT KERJA</option>
                <?php foreach($unit_kerja as $uk): ?>
                <option value="<?= esc($uk['nama_unit']) ?>"><?= esc(strtoupper($uk['nama_unit'])) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

    </div>
    
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 w-100" id="userTableNamed">
                <thead class="bg-light text-muted small fw-bold uppercase-tracking">
                    <tr>
                        <th class="ps-4 py-3">DATA PESERTA (INTERNAL)</th>
                        <th>PROFESI</th>
                        <th>UNIT KERJA</th>
                        <th class="text-center">CAPAIAN JPL</th>
                        <th class="text-center">STATUS</th>
                        <th class="pe-4 text-center">TINDAKAN KELOLA</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users_named as $u) : ?>
                    <tr id="user_row_<?= $u['id'] ?>" class="transition-row">
                        <td class="ps-4 py-3">
                            <div class="d-flex align-items-center">
                                <div class="bg-dark text-white rounded-circle me-3 d-flex align-items-center justify-content-center fw-bold avatar-letter" style="border: 2px solid #ce2127;">
                                    <?= strtoupper(substr($u['nama'], 0, 1)) ?>
                                </div>
                                <div class="text-truncate" style="max-width: 280px;">
                                    <div class="fw-bold small user-name text-dark mb-0 text-truncate" title="<?= strtoupper($u['nama']) ?>"><?= strtoupper($u['nama']) ?></div>
                                    <div class="text-muted d-flex align-items-center gap-1 text-truncate" style="font-size: 0.7rem;">
                                        <i class="fas fa-envelope text-danger" style="font-size:0.65rem;"></i> <?= $u['email'] ?> 
                                        <span class="text-slate-300">•</span> 
                                        <span class="fw-bold text-secondary">NIK: <?= $u['nik'] ?></span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="fw-semibold text-dark small"><?= $u['profesi'] ?></td>
                        <td class="text-uppercase small font-monospace text-secondary"><?= $u['ruangan'] ?></td>
                        <td class="text-center fw-bold text-dark small"><?= $u['capaian_jpl'] ?> / <?= $u['target_jpl'] ?> JPL</td>
                        <td class="text-center">
                            <span class="badge bg-<?= $u['status'] == 'aktif' ? 'danger' : 'secondary' ?> text-white rounded-pill px-3 fw-bold border border-light border-opacity-25" style="font-size: 0.65rem; letter-spacing: 0.5px;">
                                <?= strtoupper($u['status']) ?>
                            </span>
                        </td>
                        <td class="pe-4 text-center">
                            <div class="d-flex justify-content-center align-items-center gap-1">
                                <button class="btn btn-dark btn-sm rounded-pill px-3 fw-bold btn-detail-custom d-flex align-items-center gap-1" onclick="showUserDetail(<?= htmlspecialchars(json_encode($u)) ?>)">
                                    <i class="fas fa-id-badge text-white small"></i> DETAIL
                                </button>
                                <button type="button" class="btn btn-white btn-action-custom text-danger border shadow-sm" onclick="editUser(<?= htmlspecialchars(json_encode($u)) ?>)" title="Edit Akun"><i class="fas fa-edit small"></i></button>
                                <button type="button" class="btn btn-white btn-action-custom text-dark border shadow-sm" onclick="toggleStatus(<?= $u['id'] ?>)" title="Toggle Kunci Akses"><i class="fas fa-lock small"></i></button>
                                <button type="button" class="btn btn-danger btn-action-custom text-white border-0 shadow-sm" onclick="deleteUser(<?= $u['id'] ?>)" title="Hapus Akun"><i class="fas fa-trash small"></i></button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- SECTION NON-NAMED (EKSTERNAL) -->
<div id="non_named_section" class="card border-0 shadow-sm rounded-custom overflow-hidden bg-white mb-5 border-top border-dark border-4 d-none">
    <div class="card-header bg-white p-4 border-bottom d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <div class="fw-bold text-dark small text-uppercase me-2 d-flex align-items-center gap-1"><i class="fas fa-filter text-dark"></i> Filter:</div>
            <select id="filterProfesiNonNamed" class="form-select form-select-custom rounded-pill border shadow-sm px-3 fw-semibold text-dark" style="width: 190px;">
                <option value="">SEMUA PROFESI</option>
                <?php foreach($profesi as $p): ?>
                <option value="<?= esc($p['nama_profesi']) ?>"><?= esc(strtoupper($p['nama_profesi'])) ?></option>
                <?php endforeach; ?>
            </select>
            <select id="filterInstansiNonNamed" class="form-select form-select-custom rounded-pill border shadow-sm px-3 fw-semibold text-dark" style="width: 190px;">
                <option value="">SEMUA INSTANSI</option>
                <?php 
                $instansiList = [];
                foreach($users_non_named as $un) {
                    if(!empty($un['instansi']) && !in_array($un['instansi'], $instansiList)) {
                        $instansiList[] = $un['instansi'];
                    }
                }
                foreach($instansiList as $instansi): ?>
                <option value="<?= esc($instansi) ?>"><?= esc(strtoupper($instansi)) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

    </div>
    
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 w-100" id="userTableNonNamed">
                <thead class="bg-light text-muted small fw-bold uppercase-tracking">
                    <tr>
                        <th class="ps-4 py-3">DATA PESERTA (EKSTERNAL)</th>
                        <th>PROFESI</th>
                        <th>UNIT KERJA</th>
                        <th class="text-center">CAPAIAN JPL</th>
                        <th class="text-center">STATUS</th>
                        <th class="pe-4 text-center">TINDAKAN KELOLA</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users_non_named as $un) : ?>
                    <tr id="user_row_<?= $un['id'] ?>" class="transition-row">
                        <td class="ps-4 py-3">
                            <div class="d-flex align-items-center">
                                <div class="bg-secondary text-white rounded-circle me-3 d-flex align-items-center justify-content-center fw-bold avatar-letter" style="border: 2px solid #343a40;">
                                    <?= strtoupper(substr($un['nama'], 0, 1)) ?>
                                </div>
                                <div class="text-truncate" style="max-width: 280px;">
                                    <div class="fw-bold small user-name text-dark mb-0 text-truncate" title="<?= strtoupper($un['nama']) ?>"><?= strtoupper($un['nama']) ?></div>
                                    <div class="text-muted d-flex align-items-center gap-1 text-truncate" style="font-size: 0.7rem;">
                                        <i class="fas fa-envelope text-muted" style="font-size:0.65rem;"></i> <?= $un['email'] ?> 
                                        <span class="text-slate-300">•</span> 
                                        <span class="fw-bold text-secondary">NIK: <?= $un['nik'] ?></span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="small fw-semibold text-dark"><?= $un['profesi'] ?></td>
                        <td class="small text-muted fw-bold text-uppercase"><?= $un['instansi'] ?></td>
                        <td class="text-center fw-bold text-dark small"><?= $un['capaian_jpl'] ?> / <?= $un['target_jpl'] ?> JPL</td>
                        <td class="text-center">
                            <span class="badge bg-<?= $un['status'] == 'aktif' ? 'danger' : 'secondary' ?> text-white rounded-pill px-3 fw-bold border border-light border-opacity-25" style="font-size: 0.65rem; letter-spacing: 0.5px;">
                                <?= strtoupper($un['status']) ?>
                            </span>
                        </td>
                        <td class="pe-4 text-center">
                            <div class="d-flex justify-content-center align-items-center gap-1">
                                <button class="btn btn-dark btn-sm rounded-pill px-3 fw-bold btn-detail-custom d-flex align-items-center gap-1" onclick="showUserDetail(<?= htmlspecialchars(json_encode($un)) ?>)">
                                    <i class="fas fa-id-badge text-white small"></i> DETAIL
                                </button>
                                <button type="button" class="btn btn-white btn-action-custom text-danger border shadow-sm" onclick="editUser(<?= htmlspecialchars(json_encode($un)) ?>)" title="Edit Akun"><i class="fas fa-edit small"></i></button>
                                <button type="button" class="btn btn-white btn-action-custom text-dark border shadow-sm" onclick="toggleStatus(<?= $un['id'] ?>)" title="Toggle Kunci Akses"><i class="fas fa-lock small"></i></button>
                                <button type="button" class="btn btn-danger btn-action-custom text-white border-0 shadow-sm" onclick="deleteUser(<?= $un['id'] ?>)" title="Hapus Akun"><i class="fas fa-trash small"></i></button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->include('Pelatihan/admin/manajemen_peserta/modals/modal_detail_user') ?>
<?= $this->include('Pelatihan/admin/manajemen_peserta/modals/modal_registrasi') ?>
<?= $this->include('Pelatihan/admin/manajemen_peserta/modals/modal_edit_user') ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    let dtNamed, dtNonNamed;

    $(document).ready(function() {
        dtNamed = $('#userTableNamed').DataTable({
            pageLength: 10,
            responsive: true,
            dom: 'lrtip',
            language: { info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ pegawai", paginate: { previous: "‹", next: "›" } }
        });
        $('#filterProfesiNamed').on('change', function() { dtNamed.column(1).search(this.value).draw(); });
        $('#filterRuanganNamed').on('change', function() { dtNamed.column(2).search(this.value).draw(); });

        dtNonNamed = $('#userTableNonNamed').DataTable({
            pageLength: 10,
            responsive: true,
            dom: 'lrtip',
            language: { info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ umum/mitra", paginate: { previous: "‹", next: "›" } }
        });
        $('#filterProfesiNonNamed').on('change', function() { dtNonNamed.column(1).search(this.value).draw(); });
        $('#filterInstansiNonNamed').on('change', function() { dtNonNamed.column(2).search(this.value).draw(); });

        $('#formRegistrasi').on('submit', function(e) {
            var form = this;
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }
            $(form).addClass('was-validated');
        });

        $('#formEditUser').on('submit', function(e) {
            var form = this;
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }
            $(form).addClass('was-validated');
        });

        $('input[name="nik"], input[name="wa"]').on('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        $('input[name="password"], input[name="confirm_password"]').on('input', function() {
            this.value = this.value.replace(/[^a-zA-Z0-9]/g, '');
        });

        $('.toggle-password').on('click', function() {
            let input = $(this).siblings('input');
            let icon = $(this).find('i');
            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
                icon.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                input.attr('type', 'password');
                icon.removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });
    });

    function switchCategory(cat) {
        if (cat === 'named') {
            $('#btn_named').addClass('active btn-dark text-white').removeClass('btn-light text-muted');
            $('#btn_non_named').removeClass('active btn-dark text-white').addClass('btn-light text-muted');
            $('#named_section').removeClass('d-none');
            $('#non_named_section').addClass('d-none');
        } else {
            $('#btn_non_named').addClass('active btn-dark text-white').removeClass('btn-light text-muted');
            $('#btn_named').removeClass('active btn-dark text-white').addClass('btn-light text-muted');
            $('#named_section').addClass('d-none');
            $('#non_named_section').removeClass('d-none');
        }
        setTimeout(() => { $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust(); }, 50);
    }

    function toggleStatus(id) {
        window.location.href = '<?= base_url('pelatihan/admin/akun_peserta/toggle-status') ?>/' + id;
    }

    function deleteUser(id) {
        Swal.fire({
            title: 'Hapus Akun Permanen?',
            text: "Semua riwayat pengajuan dan pelacakan JPL pegawai akan musnah!",
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#ce2127',
            confirmButtonText: 'Ya, Hapus Tetap!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '<?= base_url('pelatihan/admin/akun_peserta/delete') ?>/' + id;
            }
        });
    }

    function showUserDetail(u) {
        document.getElementById('det_role').value = (u.jenis_peserta === 'named' ? 'NAMED (INTERNAL)' : 'NON-NAMED (EKSTERNAL)');
        document.getElementById('det_nama').value = (u.nama || '-').toUpperCase();
        document.getElementById('det_email').value = u.email || '-';
        document.getElementById('det_nik').value = u.nik || '-';
        document.getElementById('det_wa').value = u.wa || '-';
        document.getElementById('det_instansi').value = (u.instansi || '-').toUpperCase();
        document.getElementById('det_profesi').value = (u.profesi || '-').toUpperCase();
        
        // Populate Target & Capaian JPL
        const detTarget = document.getElementById('det_target_jpl');
        const detCapaian = document.getElementById('det_capaian_jpl');
        if (detTarget) detTarget.value = u.target_jpl || '20';
        if (detCapaian) detCapaian.value = u.capaian_jpl || '0';

        if(u.jenis_peserta === 'named') {
            $('#jpl_tracking_box').removeClass('d-none');
            $('#jpl_tracking_title').removeClass('d-none');
            let pct = u.target_jpl > 0 ? (u.capaian_jpl / u.target_jpl) * 100 : 0;
            pct = Math.min(100, Math.max(0, pct));
            document.getElementById('det_jpl_val').innerText = Math.round(pct) + '%';
            document.getElementById('det_jpl_bar').style.width = pct + '%';
        } else {
            $('#jpl_tracking_box').addClass('d-none');
            $('#jpl_tracking_title').addClass('d-none');
        }
        new bootstrap.Modal(document.getElementById('modalDetailUser')).show();
    }

    function editUser(u) {
        document.getElementById('edit_id').value = u.id;
        document.getElementById('edit_nama').value = u.nama;
        document.getElementById('edit_email').value = u.email;
        document.getElementById('edit_nik').value = u.nik;
        document.getElementById('edit_wa').value = u.wa;
        
        const roleSelect = document.getElementById('edit_role');
        if (roleSelect) roleSelect.value = u.role || 'peserta';

        const jpSelect = document.getElementById('edit_jenis_peserta');
        if (jpSelect) jpSelect.value = (u.jenis_peserta === 'non-named' || u.jenis_peserta === 'non_named') ? 'non_named' : 'named';
        
        const ukSelect = document.getElementById('edit_unit_kerja');
        const pSelect = document.getElementById('edit_profesi');
        if (ukSelect) ukSelect.value = u.id_unit_kerja || '';
        if (pSelect) pSelect.value = u.id_profesi || '';
        
        const targetInput = document.getElementById('edit_target_jpl');
        if (targetInput) targetInput.value = u.target_jpl || '20';
        
        new bootstrap.Modal(document.getElementById('modalEditUser')).show();
    }
</script>

<style>
    /* Utility & Interaktivitas Premium */
    .rounded-custom { border-radius: 1rem !important; }
    .text-slate-300 { color: #cbd5e1; }
    .uppercase-tracking { letter-spacing: 0.7px; font-size: 0.75rem !important; }
    
    .btn-switch { font-size: 0.8rem !important; transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1); }
    .me-2-custom { margin-right: 0.5rem; }
    
    /* Standarisasi Presisi Tombol Aksi Kelola */
    .btn-action-custom {
        width: 34px !important;
        height: 34px !important;
        padding: 0 !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        border-radius: 50% !important;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .btn-action-custom:hover { 
        transform: translateY(-2px) scale(1.05); 
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.12) !important; 
    }
    .btn-white { background: #fff; border: 1px solid #e2e8f0; color: #475569; }
    .btn-white:hover { background: #f8fafc; color: #1e293b; }
    
    .btn-detail-custom {
        font-size: 0.75rem !important;
        padding: 0.4rem 0.9rem !important;
        transition: all 0.2s ease;
    }
    .btn-detail-custom:hover { transform: translateY(-1px); box-shadow: 0 4px 6px rgba(0,0,0,0.08); }

    /* Desain Interaktif Baris Tabel */
    .transition-row { transition: all 0.2s ease; }
    .table-hover tbody tr:hover { 
        background-color: #f8fafc !important; 
        transform: scale(0.998);
    }
    
    /* Penyelarasan Avatar Huruf */
    .avatar-letter {
        width: 38px !important; 
        height: 38px !important; 
        font-size: 0.85rem;
        flex-shrink: 0;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    /* Custom Input & Select Filters */
    .form-select-custom {
        font-size: 0.75rem !important;
        padding: 6px 12px !important;
        border: 1px solid #e2e8f0 !important;
        cursor: pointer;
    }
    
    .form-control, .form-select {
        border-radius: 10px;
        padding: 10px 16px;
        border: 2px solid #f1f5f9;
        background-color: #ffffff;
        font-weight: 600;
        transition: all 0.3s;
        color: #1e293b;
        font-size: 0.85rem;
    }
    .form-control:focus, .form-select:focus {
        background-color: #fff;
        border-color: #ce2127;
        box-shadow: 0 0 0 4px rgba(206, 33, 39, 0.08);
        color: #1e293b;
    }
    .btn-register-submit {
        background: #ce2127;
        border: none;
        border-radius: 20px;
        padding: 10px 24px;
        font-weight: 700;
        color: white;
        transition: all 0.3s;
        box-shadow: 0 4px 12px rgba(206, 33, 39, 0.15);
        font-size: 0.85rem;
    }
    .btn-register-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(206, 33, 39, 0.25);
        background: #a51a1f;
        color: white;
    }
    .page-item.active .page-link {
        background-color: #ce2127 !important;
        border-color: #ce2127 !important;
        color: #fff !important;
    }
    .page-link {
        color: #212529 !important;
    }
    .page-link:hover {
        color: #ce2127 !important;
        background-color: #fff5f5 !important;
        border-color: #dee2e6 !important;
    }
    .page-link:focus {
        box-shadow: 0 0 0 0.25rem rgba(206, 33, 39, 0.15) !important;
    }
</style>
<script>
    // Auto refresh every 60 seconds unless an admin modal is open
    setInterval(function() {
        if ($('.modal.show').length === 0) {
            window.location.reload();
        }
    }, 60000);
</script>
<?= $this->endSection() ?>
