<?= $this->extend('Pelatihan/layout/admin_layout') ?>
<?php
/**
 * @var array $pelatihan
 * @var array $peserta
 * @var array $sesi_list
 */
?>
<?= $this->section('content') ?>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

<div class="container-fluid px-0">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb bg-transparent p-0 m-0">
            <li class="breadcrumb-item"><a href="<?= base_url('pelatihan/admin/monitoring_peserta') ?>" class="text-secondary text-decoration-none small fw-bold">MONITORING KELAS</a></li>
            <li class="breadcrumb-item active text-danger small fw-bold" aria-current="page">MONITORING PRESENSI</li>
        </ol>
    </nav>

    <div class="bg-white p-4 rounded-custom shadow-sm border-start border-danger border-5 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1 text-uppercase text-dark"><?= esc($pelatihan['nama']) ?></h3>
            <div class="text-muted small">
                <span class="badge bg-dark me-2">ID KELAS: #<?= str_pad($pelatihan['id'], 4, '0', STR_PAD_LEFT) ?></span>
                <i class="fas fa-calendar-alt me-1 text-danger"></i> Kelola Presensi Kehadiran & Validasi Progres Kelulusan Peserta
            </div>
        </div>
        <div class="text-md-end">
            <div class="badge bg-dark text-white rounded-pill px-4 py-3 fw-bold border border-danger border-opacity-25 shadow-sm" style="font-size: 0.85rem;">
                <i class="fas fa-users me-2 text-danger"></i> <?= count($peserta) ?> PESERTA TERDAFTAR
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-custom bg-white h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-dark text-white p-3 rounded-circle me-3 d-flex align-items-center justify-content-center" style="border: 2px solid #ce2127; width: 50px; height: 50px;"><i class="fas fa-book-reader text-danger"></i></div>
                    <div>
                        <div class="text-muted small fw-bold" style="font-size:0.65rem;">TOTAL SESI DIKLAT</div>
                        <h5 class="fw-bold mb-0 text-dark"><?= count($sesi_list) ?> <span class="fs-6 text-muted fw-normal">Pertemuan</span></h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-custom bg-white h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-dark text-white p-3 rounded-circle me-3 d-flex align-items-center justify-content-center" style="border: 2px solid #ce2127; width: 50px; height: 50px;"><i class="fas fa-graduation-cap text-danger"></i></div>
                    <div>
                        <div class="text-muted small fw-bold" style="font-size:0.65rem;">RATA-RATA PROGRES KELAS</div>
                        <?php 
                            $total_prog = count($peserta) > 0 ? array_sum(array_column($peserta, 'progress')) / count($peserta) : 0;
                        ?>
                        <h5 class="fw-bold mb-0 text-dark" id="class-average-progress"><?= number_format($total_prog, 1) ?>% <span class="fs-6 text-muted fw-normal">Selesai</span></h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-custom bg-white h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-dark text-white p-3 rounded-circle me-3 d-flex align-items-center justify-content-center" style="border: 2px solid #ce2127; width: 50px; height: 50px;"><i class="fas fa-user-check text-danger"></i></div>
                    <div>
                        <div class="text-muted small fw-bold" style="font-size:0.65rem;">METODE DIKLAT</div>
                        <h5 class="fw-bold mb-0 text-dark text-uppercase"><span class="badge bg-dark text-white rounded-pill px-3 border border-danger border-opacity-50" style="font-size:0.75rem;"><?= esc($pelatihan['metode'] ?? 'OFFLINE') ?></span></h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-custom overflow-hidden bg-white mb-5 border-top border-danger border-4">

        
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 w-100" id="tablePresensi">
                    <thead class="bg-light text-muted fw-bold uppercase-tracking border-bottom-0">
                        <tr>
                            <th class="ps-4 py-3" style="min-width: 220px; white-space: nowrap;">PESERTA KARYAWAN</th>
                            <th style="white-space: nowrap;">IDENTIFIKASI NIK</th>
                            <th style="white-space: nowrap;">PROFESI</th>
                            
                            <?php foreach($sesi_list as $index => $s): ?>
                                <th class="text-center text-uppercase border-start border-end text-truncate bg-light bg-opacity-50" style="max-width: 120px; white-space: nowrap;" title="<?= esc($s['nama_sesi']) ?>">
                                    <div style="font-size: 0.6rem;" class="text-muted">Sesi <?= $index + 1 ?></div>
                                    <div class="text-danger fw-bold mb-1" style="font-size: 0.7rem;"><?= esc($s['nama_sesi']) ?></div>
                                    <div class="text-muted font-monospace mt-1" style="font-size: 0.58rem; letter-spacing: -0.2px;">
                                        <i class="far fa-clock"></i> <?= !empty($s['waktu']) ? date('H:i', strtotime($s['waktu'])) : '-' ?>-<?= !empty($s['jam_tutup']) ? date('H:i', strtotime($s['jam_tutup'])) : '-' ?>
                                    </div>
                                </th>
                            <?php endforeach; ?>
                            
                            <th class="text-center" style="min-width: 130px; white-space: nowrap;">PROGRES ABSEN</th>
                            <th class="pe-4 text-center" style="white-space: nowrap;">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($peserta)): foreach($peserta as $u): ?>
                        <tr class="transition-row">
                            <td class="ps-4 py-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-dark text-white rounded-circle d-flex align-items-center justify-content-center fw-bold avatar-letter" style="border: 2px solid #ce2127;">
                                        <?= strtoupper(substr($u['nama'], 0, 1)) ?>
                                    </div>
                                    <div class="text-truncate" style="max-width: 200px;">
                                        <div class="fw-bold small text-dark mb-0 text-truncate" title="<?= esc($u['nama']) ?>"><?= esc(strtoupper($u['nama'])) ?></div>
                                        <div class="text-muted text-truncate" style="font-size: 0.68rem;"><?= esc($u['email']) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="small fw-bold text-dark font-monospace"><?= esc($u['user_id']) ?></div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border rounded-pill px-3 py-1 fw-bold" style="font-size:0.65rem;"><?= esc(strtoupper($u['profesi'])) ?></span>
                            </td>
                            
                            <?php foreach($sesi_list as $s): 
                                $status = $u['kehadiran'][$s['id']] ?? 'Alfa';
                            ?>
                                <td class="text-center border-start border-end bg-light bg-opacity-25">
                                    <select class="form-select form-select-sm attendance-select border-0 text-center fw-bold shadow-sm rounded-pill"
                                            style="font-size: 0.7rem; max-width: 110px; cursor: pointer; <?= $status == 'Hadir' ? 'background-color: #d1e7dd; color: #0f5132;' : ($status == 'Izin' ? 'background-color: #fff3cd; color: #664d03;' : 'background-color: #f8d7da; color: #842029;') ?>"
                                            data-user="<?= esc($u['user_id']) ?>"
                                            data-pelatihan="<?= $pelatihan['id'] ?>"
                                            data-sesi="<?= esc($s['id']) ?>">
                                        <option value="alfa" <?= $status == 'Alfa' ? 'selected' : '' ?>>Alfa</option>
                                        <option value="hadir" <?= $status == 'Hadir' ? 'selected' : '' ?>>Hadir</option>
                                        <option value="izin" <?= $status == 'Izin' ? 'selected' : '' ?>>Izin</option>
                                    </select>
                                </td>
                            <?php endforeach; ?>
                            
                            <td class="text-center bg-light bg-opacity-10">
                                <div class="progress shadow-sm mx-auto" style="height: 6px; border-radius: 3px; max-width: 100px; background-color: #f1f5f9;">
                                    <div class="progress-bar bg-danger progress-bar-text" style="width: <?= $u['progress'] ?>%"></div>
                                </div>
                                <div class="small fw-bold text-dark mt-1 progress-percentage" style="font-size: 0.7rem;"><?= number_format($u['progress'], 0) ?>%</div>
                            </td>
                            
                            <td class="pe-4 text-center">
                                <div class="d-flex justify-content-center align-items-center gap-1">
                                    <a href="<?= base_url('pelatihan/admin/hapus_pendaftaran/'.$u['user_id'].'/'.$pelatihan['id']) ?>" class="btn btn-danger btn-action-custom text-white border-0 shadow-sm" title="Keluarkan Dari Kelas" onclick="return confirm('Apakah Anda yakin ingin membatalkan keanggotaan peserta ini dari program pelatihan?')">
                                        <i class="fas fa-trash small-icon"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    /* Dasar Utilitas Premium */
    .rounded-custom { border-radius: 1rem !important; }
    .uppercase-tracking { letter-spacing: 0.7px; font-size: 0.75rem !important; }
    
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

    /* Standarisasi Presisi Tombol Aksi Kelola */
    .btn-action-custom {
        width: 28px !important;
        height: 28px !important;
        padding: 0 !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        border-radius: 50% !important;
        transition: all 0.2s ease-in-out;
        flex-shrink: 0;
    }
    .btn-action-custom:hover { 
        transform: translateY(-1px) scale(1.05); 
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1) !important; 
    }
    .btn-white { background: #fff; border: 1px solid #e2e8f0; color: #475569; }
    .btn-white:hover { background: #f8fafc; color: #1e293b; }
    .small-icon { font-size: 0.68rem !important; }

    .transition-row { transition: all 0.2s ease; }
    .table-hover tbody tr:hover { 
        background-color: #f8fafc !important; 
    }

    .avatar-letter {
        width: 36px !important; 
        height: 36px !important; 
        font-size: 0.8rem;
        flex-shrink: 0;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    
    /* Custom Styling Select */
    .attendance-select {
        border: none !important;
        cursor: pointer;
        font-weight: 700;
        padding: 4px 10px;
        appearance: auto;
    }
    .attendance-select:focus {
        box-shadow: none !important;
    }
    
    /* DataTables Pagination Override (Merah & Hitam) */
    .page-item.active .page-link {
        background-color: #ce2127 !important;
        border-color: #ce2127 !important;
        color: #fff !important;
    }
    .page-link { color: #212529 !important; }
    .page-link:hover {
        color: #ce2127 !important;
        background-color: #fff5f5 !important;
        border-color: #dee2e6 !important;
    }
    .page-link:focus { box-shadow: 0 0 0 0.25rem rgba(206, 33, 39, 0.15) !important; }
</style>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        var totalSesi = <?= count($sesi_list) ?>;
        var table = $('#tablePresensi').DataTable({
            pageLength: 25,
            responsive: false, // Diganti ke false agar tidak menyembunyikan kolom sesi dinamis ke dalam baris anak
            dom: '<"px-4 pt-4 pb-2 d-flex justify-content-between align-items-center flex-wrap"l<"custom-action-btn">>rt<"px-4 pb-3 d-flex justify-content-between align-items-center"ip>',
            autoWidth: false,
            language: {
                emptyTable: "Belum ada karyawan yang di-plotting pada program diklat ini.",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ karyawan",
                paginate: { previous: "‹", next: "›" }
            },
            initComplete: function() {
                $("div.custom-action-btn").html(`
                    <div class="d-flex align-items-center gap-3">
                        <span class="text-muted small fw-bold d-none d-md-inline-block border-end pe-3 border-2"><i class="fas fa-info-circle text-danger me-1"></i> Pilih status kehadiran: Hadir, Izin (dihitung progres), atau Alfa.</span>
                        <a href="<?= base_url('pelatihan/admin/add_peserta/'.$pelatihan['id']) ?>" class="btn btn-register-submit rounded-pill px-4 fw-bold border-0 d-inline-flex align-items-center gap-2 shadow-sm"><i class="fas fa-user-plus text-white small"></i> PLOTTING PESERTA</a>
                    </div>
                `);
                // Memaksa penyesuaian ulang kolom setelah DataTables siap sepenuhnya
                setTimeout(function(){
                    table.columns.adjust().draw();
                }, 200);
            }
        });



        $(document).on('change', '.attendance-select', function() {
            var selectEl = $(this);
            var userId = selectEl.data('user');
            var pelId = selectEl.data('pelatihan');
            var sesi = selectEl.data('sesi');
            var status = selectEl.val();

            // Color update
            var colors = { hadir: ['#d1e7dd','#0f5132'], izin: ['#fff3cd','#664d03'], alfa: ['#f8d7da','#842029'] };
            var c = colors[status] || colors['alfa'];
            selectEl.css({ 'background-color': c[0], 'color': c[1] });

            // Live progress: Hadir & Izin count
            var trNode = selectEl.closest('tr');
            if(totalSesi > 0) {
                var hadirCount = 0;
                trNode.find('.attendance-select').each(function() {
                    if($(this).val() === 'hadir' || $(this).val() === 'izin') hadirCount++;
                });
                var currentProgressPercentage = Math.round((hadirCount / totalSesi) * 100);
                trNode.find('.progress-bar').css('width', currentProgressPercentage + '%');
                trNode.find('.progress-percentage').text(currentProgressPercentage + '%');

                setTimeout(function() {
                    let totalProgressSum = 0, totalRowsCount = 0;
                    $('.progress-percentage').each(function() {
                        totalProgressSum += parseFloat($(this).text().replace('%', '')) || 0;
                        totalRowsCount++;
                    });
                    if (totalRowsCount > 0) {
                        let average = (totalProgressSum / totalRowsCount).toFixed(1);
                        $('#class-average-progress').html(average + '% <span class="fs-6 text-muted fw-normal">Selesai</span>');
                    }
                }, 50);
            }

            var prevVal = selectEl.data('prev') || 'alfa';
            selectEl.data('prev', status);

            $.ajax({
                url: '<?= base_url('pelatihan/admin/toggle_presensi') ?>',
                method: 'POST',
                data: { user_id: userId, pelatihan_id: pelId, sesi: sesi, status: status },
                success: function(res) {
                    const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 1500, timerProgressBar: true });
                    Toast.fire({ icon: 'success', title: 'Presensi berhasil diperbarui' }).then(() => { location.reload(); });
                },
                error: function() {
                    Swal.fire({ title: 'Koneksi Gagal', text: 'Sistem gagal menyinkronkan status presensi ke server DB.', icon: 'error', confirmButtonColor: '#ce2127' });
                    selectEl.val(prevVal);
                    var c2 = colors[prevVal] || colors['alfa'];
                    selectEl.css({ 'background-color': c2[0], 'color': c2[1] });
                }
            });
        });
        
        table.on('draw', function() {
            $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
        });
    });
</script>
<?= $this->endSection() ?>
