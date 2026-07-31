<?= $this->extend('Pelatihan/layout/admin_layout') ?>

<?= $this->section('content') ?>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<div class="row mb-4">
    <div class="col-md-12">
        <div class="card border-0 shadow-sm rounded-custom bg-white overflow-hidden border-top border-danger border-4">
            <div class="card-body p-0 pt-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 w-100" id="tableMasterPelatihan">
                        <thead class="bg-light text-muted fw-bold uppercase-tracking">
                            <tr>
                                <th class="ps-4 py-3" style="white-space: nowrap; font-size: 0.65rem;">JUDUL PELATIHAN</th>
                                <th class="py-3" style="white-space: nowrap; font-size: 0.65rem; padding-right: 15px;">PROGRAM & CAKUPAN</th>
                                <th class="py-3" style="white-space: nowrap; font-size: 0.65rem; padding-right: 15px;">TANGGAL PELAKSANAAN</th>
                                <th class="py-3" style="white-space: nowrap; font-size: 0.65rem; padding-right: 15px;">METODE & BIAYA</th>
                                <th class="py-3" style="white-space: nowrap; font-size: 0.65rem; padding-right: 15px;">KUOTA & STATUS</th>
                                <th class="pe-4 py-3 text-center" style="white-space: nowrap; font-size: 0.65rem;">TINDAKAN KELOLA</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($pelatihan)): foreach ($pelatihan as $p) : 
                                $status_p = empty($p['status']) ? 'Draft' : $p['status'];
                            ?>
                            <tr id="pel_row_<?= $p['id'] ?? 0 ?>" class="transition-row">
                                <td class="ps-4 py-3">
                                    <div class="fw-bold small text-dark mb-1 text-truncate" style="max-width: 240px;" title="<?= esc($p['nama'] ?? '-') ?>"><?= esc($p['nama'] ?? '-') ?></div>
                                    <div class="text-muted d-flex align-items-center gap-1 flex-wrap" style="font-size: 0.65rem;">
                                        <span class="fw-bold text-secondary">ID: #<?= str_pad($p['id'] ?? 0, 4, '0', STR_PAD_LEFT) ?></span>
                                        <span class="text-slate-300">•</span>
                                        <span><?= esc($p['jpl'] ?? '-') ?> JPL</span>
                                        <span class="text-slate-300">•</span>
                                        <span class="text-truncate" style="max-width: 120px;"><i class="fas fa-user-tie me-1 text-danger"></i> <?= esc($p['narasumber'] ?? '-') ?></span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-dark text-white small fw-bold mb-1" style="font-size:0.6rem;"><?= esc($p['program'] ?? 'Umum') ?></span>
                                    <div class="text-muted" style="font-size: 0.7rem;"><i class="fas fa-globe me-1 text-secondary"></i> <?= esc($p['cakupan'] ?? 'Lokal') ?></div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2 text-dark small fw-semibold">
                                        <i class="far fa-calendar-alt text-danger" style="font-size: 0.8rem;"></i>
                                        <div style="font-size: 0.75rem; line-height: 1.3;">
                                            <div><?= !empty($p['jadwal_mulai']) ? tanggal_indo($p['jadwal_mulai']) : '-' ?></div>
                                            <div class="text-muted" style="font-size: 0.65rem;">s.d <?= !empty($p['jadwal_selesai']) ? tanggal_indo($p['jadwal_selesai']) : '-' ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column gap-1">
                                        <span class="badge bg-light text-dark border border-secondary border-opacity-25 small fw-bold" style="width: fit-content; font-size:0.6rem;"><?= esc($p['metode'] ?? '-') ?> (<?= esc($p['mekanisme'] ?? 'Terbuka') ?>)</span>
                                        <span class="badge bg-<?= ($p['biaya'] ?? 'Gratis') == 'Gratis' ? 'dark text-white' : 'danger text-white' ?> small fw-bold" style="width: fit-content; font-size:0.6rem;">
                                            <?= ($p['biaya'] ?? 'Gratis') == 'Gratis' ? 'Gratis' : 'Rp ' . number_format($p['biaya_nominal'] ?? 0, 0, ',', '.') ?>
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <div class="small fw-bold mb-1 text-dark" style="font-size: 0.75rem;"><?= esc($p['peserta'] ?? 0) ?> / <?= esc($p['kuota'] ?? 0) ?> Peserta</div>
                                    <div class="progress mb-2" style="height: 5px; width: 110px; border-radius: 3px; background-color: #f1f5f9;">
                                        <?php $persenKuota = (!empty($p['kuota']) && ($p['peserta'] ?? 0) > 0) ? (($p['peserta'] / $p['kuota']) * 100) : 0; ?>
                                        <div class="progress-bar bg-danger" style="width: <?= $persenKuota ?>%"></div>
                                    </div>
                                    <?php if($status_p == 'Draft'): ?>
                                        <span class="badge bg-secondary text-white rounded-pill px-2 py-1 fw-bold" style="font-size: 0.6rem; letter-spacing: 0.3px;"><i class="fas fa-file-edit me-1"></i> DRAFT</span>
                                    <?php elseif($status_p == 'Aktif'): ?>
                                        <span class="badge bg-dark text-white border border-danger border-opacity-50 rounded-pill px-2 py-1 fw-bold" style="font-size: 0.6rem; letter-spacing: 0.3px;"><i class="fas fa-check-circle me-1 text-danger"></i> AKTIF</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary text-white rounded-pill px-2 py-1 fw-bold" style="font-size: 0.6rem; letter-spacing: 0.3px;"><i class="fas fa-check-double me-1"></i> SELESAI</span>
                                    <?php endif; ?>
                                </td>
                                <td class="pe-4 text-center">
                                    <div class="d-flex justify-content-center align-items-center gap-1 flex-nowrap">
                                        <?php if($status_p == 'Selesai' || $status_p == 'Batal'): ?>
                                            <button class="btn btn-white btn-action-custom text-muted border border-opacity-50" title="Selesai" disabled><i class="fas fa-cog small-icon"></i></button>
                                        <?php else: ?>
                                            <a href="<?= base_url('pelatihan/admin/pelatihan/kelola/'.($p['id'] ?? 0)) ?>" class="btn btn-white btn-action-custom text-dark border shadow-sm" title="Kelola Pelatihan"><i class="fas fa-cog small-icon"></i></a>
                                        <?php endif; ?>
                                        
                                        <?php if($status_p == 'Draft'): ?>
                                            <button type="button" class="btn btn-dark btn-status-toggle fw-bold" onclick="changeStatus(<?= $p['id'] ?? 0 ?>, 'Aktif')" title="Publikasikan Diklat">AKTIF</button>
                                        <?php elseif($status_p == 'Aktif'): ?>
                                            <button type="button" class="btn btn-outline-secondary btn-status-toggle fw-bold" onclick="changeStatus(<?= $p['id'] ?? 0 ?>, 'Selesai')" title="Tutup / Selesaikan Diklat">SELESAI</button>
                                        <?php else: ?>
                                            <button type="button" class="btn btn-dark btn-status-toggle fw-bold" onclick="changeStatus(<?= $p['id'] ?? 0 ?>, 'Draft')" title="Kembalikan ke Draft">DRAFT</button>
                                        <?php endif; ?>
                                        
                                        <button type="button" class="btn btn-white btn-action-custom text-dark border shadow-sm btn-edit-trigger" data-pelatihan="<?= htmlspecialchars(json_encode($p), ENT_QUOTES, "UTF-8") ?>" title="Edit Data"><i class="fas fa-edit small-icon"></i></button>
                                        <button type="button" class="btn btn-danger btn-action-custom text-white border-0 shadow-sm" onclick="confirmDelete(<?= $p['id'] ?? 0 ?>)" title="Hapus"><i class="fas fa-trash small-icon"></i></button>
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
</div>

<?= $this->include('Pelatihan/admin/pelatihan/modals/form_pelatihan') ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        $('#modalPelatihan').on('shown.bs.modal', function () {
            $('#f_target_khusus_profesi').select2({
                placeholder: "Pilih Target Profesi",
                allowClear: true,
                width: '100%',
                dropdownParent: $('#modalPelatihan')
            });
            $('#f_target_khusus_unit').select2({
                placeholder: "Pilih Target Unit Kerja",
                allowClear: true,
                width: '100%',
                dropdownParent: $('#modalPelatihan')
            });
            $('#f_narasumber').select2({
                placeholder: "Ketik nama narasumber lalu Enter",
                tags: true,
                tokenSeparators: [','],
                width: '100%',
                dropdownParent: $('#modalPelatihan')
            });
            $('#f_penyelenggara').select2({
                placeholder: "Ketik nama penyelenggara lalu Enter",
                tags: true,
                tokenSeparators: [','],
                width: '100%',
                dropdownParent: $('#modalPelatihan')
            });
        });
    });

    // Dynamic categories from DB
    const trainingCategories = <?= json_encode($kategori_skp_grouped ?? []) ?>;

    function updateTrainingCategories(ranah) {
        const select = document.getElementById('f_kategori_kegiatan');
        if(!select) return;
        select.innerHTML = '<option value="">-- Pilih Kategori --</option>';
        
        const list = trainingCategories[ranah] || [];
        list.forEach(item => {
            const opt = document.createElement('option');
            opt.value = item;
            opt.text = item;
            select.appendChild(opt);
        });
    }

    function togglePaidFields(val) {
        const div = document.getElementById('paid_fields');
        if (val === 'Berbayar') {
            div.classList.remove('d-none');
            $('#f_nama_bank').attr('required', true);
            $('#f_rekening').attr('required', true);
            $('#f_atas_nama').attr('required', true);
            $('#f_biaya_nominal').attr('required', true);
        } else {
            div.classList.add('d-none');
            $('#f_nama_bank').removeAttr('required').val('');
            $('#f_rekening').removeAttr('required').val('');
            $('#f_atas_nama').removeAttr('required').val('');
            $('#f_biaya_nominal').removeAttr('required').val('');
        }
    }

    function toggleClosedFields(val) {
        const div = document.getElementById('closed_fields');
        if (val === 'Tertutup') {
            div.classList.remove('d-none');
            $('#f_target_khusus_profesi').attr('required', true);
            $('#f_target_khusus_unit').attr('required', true);
        } else {
            div.classList.add('d-none');
            $('#f_target_khusus_profesi').removeAttr('required').val(null).trigger('change');
            $('#f_target_khusus_unit').removeAttr('required').val(null).trigger('change');
        }
    }

    let modalPelatihan;
    $(document).ready(function() {
        var table = $('#tableMasterPelatihan').DataTable({
            pageLength: 10,
            responsive: true,
            dom: '<"d-flex flex-wrap justify-content-between align-items-center px-4 mb-3"l<"custom-action-btn">>rtip',
            language: {
                emptyTable: "Belum ada data master pelatihan.",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                paginate: { previous: "‹", next: "›" }
            },
            initComplete: function() {
                $("div.custom-action-btn").html('<button class="btn btn-register-submit rounded-pill px-4 fw-bold border-0 d-inline-flex align-items-center gap-2" onclick="showModalTambah()"><i class="fas fa-plus-circle small"></i> TAMBAH PELATIHAN BARU</button>');
            }
        });

        modalPelatihan = new bootstrap.Modal(document.getElementById('modalPelatihan'));
        
        window.showModalTambah = function() {
            document.getElementById('modalTitle').innerHTML = '<i class="fas fa-plus-circle me-2 text-danger"></i> INPUT MASTER DIKLAT BARU';
            document.getElementById('formPelatihan').action = "<?= base_url('pelatihan/admin/pelatihan/simpan') ?>";
            document.getElementById('formPelatihan').reset();
            document.getElementById('f_gambar_pelatihan').value = '';
            document.getElementById('gambar_pelatihan_info').classList.add('d-none');
            document.getElementById('gambar_pelatihan_info').textContent = '';
            $('.target-profesi-checkbox').prop('checked', false);
            $('#profesi-error').hide();
            $('#formPelatihan').removeClass('was-validated');
            document.getElementById('f_id').value = "";
            togglePaidFields('Gratis');
            toggleClosedFields('Terbuka');
            $('#f_target_khusus_profesi').val(null).trigger('change');
            $('#f_target_khusus_unit').val(null).trigger('change');
            $('#f_narasumber').empty().trigger('change');
            $('#f_penyelenggara').empty().trigger('change');
            // Update categories for the currently selected (first) ranah
            const firstRanah = document.getElementById('f_ranah_skp');
            if (firstRanah && firstRanah.value) updateTrainingCategories(firstRanah.value);
            modalPelatihan.show();
        }

        $(document).on('click', '.btn-edit-trigger', function() {
            const data = $(this).data('pelatihan');
            
            document.getElementById('modalTitle').innerHTML = '<i class="fas fa-edit me-2 text-danger"></i> EDIT MASTER DATA PELATIHAN';
            document.getElementById('formPelatihan').action = "<?= base_url('pelatihan/admin/pelatihan/update') ?>";
            $('#formPelatihan').removeClass('was-validated');
            
            document.getElementById('f_id').value = data.id;
            document.getElementById('f_gambar_pelatihan').value = '';
            const gambarInfo = document.getElementById('gambar_pelatihan_info');
            if (data.gambar_pelatihan) {
                gambarInfo.textContent = 'Gambar saat ini sudah tersimpan. Pilih file baru hanya jika ingin mengganti.';
                gambarInfo.classList.remove('d-none');
            } else {
                gambarInfo.textContent = '';
                gambarInfo.classList.add('d-none');
            }
            document.getElementById('f_tema').value = data.tema || '';
            document.getElementById('f_nama').value = data.nama;
            document.getElementById('f_program').value = data.program;
            document.getElementById('f_kategori').value = data.kategori;
            
            const k_biaya = data.biaya || 'Gratis';
            document.getElementById('f_biaya').value = k_biaya;
            togglePaidFields(k_biaya);
            if(k_biaya === 'Berbayar') {
                document.getElementById('f_nama_bank').value = data.nama_bank || '';
                document.getElementById('f_rekening').value = data.no_rekening || '';
                document.getElementById('f_atas_nama').value = data.atas_nama || '';
                document.getElementById('f_biaya_nominal').value = data.biaya_nominal || '';
            }

            document.getElementById('f_level').value = data.level;
            document.getElementById('f_cakupan').value = data.cakupan;
            document.getElementById('f_jpl').value = data.jpl;
            document.getElementById('f_skp').value = data.skp || 0;
            
            const mek = data.mekanisme || 'Terbuka';
            document.getElementById('f_mekanisme').value = mek;
            toggleClosedFields(mek);
            if(mek === 'Tertutup') {
                let targetsProfesi = [];
                if (data.target_khusus_profesi) {
                    targetsProfesi = data.target_khusus_profesi.split(',');
                }
                $('#f_target_khusus_profesi').val(targetsProfesi).trigger('change');

                let targetsUnit = [];
                if (data.target_khusus_unit) {
                    targetsUnit = data.target_khusus_unit.split(',');
                }
                $('#f_target_khusus_unit').val(targetsUnit).trigger('change');
            } else {
                $('#f_target_khusus_profesi').val(null).trigger('change');
                $('#f_target_khusus_unit').val(null).trigger('change');
            }
            document.getElementById('f_metode').value = data.metode;
            
            let narasumberIds = data.narasumber_ids || [];
            if (typeof data.narasumber_ids === 'string') {
                try { narasumberIds = JSON.parse(data.narasumber_ids); } catch(e) { narasumberIds = []; }
            }
            $('#f_narasumber').val(narasumberIds).trigger('change');

            let penyelenggaraIds = data.penyelenggara_ids || [];
            if (typeof data.penyelenggara_ids === 'string') {
                try { penyelenggaraIds = JSON.parse(data.penyelenggara_ids); } catch(e) { penyelenggaraIds = []; }
            }
            $('#f_penyelenggara').val(penyelenggaraIds).trigger('change');
            
            document.getElementById('f_kontak').value = data.kontak;
            document.getElementById('f_jadwal_mulai').value = data.jadwal_mulai;
            document.getElementById('f_jam_mulai').value = data.jam_mulai;
            document.getElementById('f_jadwal_selesai').value = data.jadwal_selesai;
            document.getElementById('f_jam_selesai').value = data.jam_selesai;
            
            document.getElementById('f_reg_buka_tgl').value = data.reg_buka_tgl || "";
            document.getElementById('f_reg_buka_jam').value = data.reg_buka_jam || "";
            document.getElementById('f_reg_tutup_tgl').value = data.reg_tutup_tgl || "";
            document.getElementById('f_reg_tutup_jam').value = data.reg_tutup_jam || "";

            document.getElementById('f_kuota').value = data.kuota;
            
            $('.target-profesi-checkbox').prop('checked', false);
            let targetProfesi = data.target_profesi;
            if (typeof targetProfesi === 'string' && targetProfesi !== '') {
                if (targetProfesi.startsWith('[') && targetProfesi.endsWith(']')) {
                    try { targetProfesi = JSON.parse(targetProfesi); } catch(e) {}
                } else {
                    targetProfesi = targetProfesi.split(',').map(s => s.trim());
                }
            }
            if (Array.isArray(targetProfesi)) {
                targetProfesi.forEach(function(val) {
                    $('.target-profesi-checkbox[value="'+val+'"]').prop('checked', true);
                });
                let total = $('.target-profesi-checkbox').length;
                let checked = $('.target-profesi-checkbox:checked').length;
                $('#checkAllProfesi').prop('checked', total === checked && total > 0);
            } else {
                $('#checkAllProfesi').prop('checked', false);
            }

            document.getElementById('f_pengumuman').value = data.pengumuman || "";
            document.getElementById('f_tujuan').value = data.tujuan || "";
            document.getElementById('f_deskripsi').value = data.deskripsi || "";
            document.getElementById('f_ranah_skp').value = data.ranah_skp || "Pembelajaran";
            
            updateTrainingCategories(data.ranah_skp || "Pembelajaran");
            setTimeout(() => {
                document.getElementById('f_kategori_kegiatan').value = data.kategori_kegiatan || "";
            }, 50);
            
            document.getElementById('f_kompetensi').value = Array.isArray(data.kompetensi) ? data.kompetensi.join(',') : (data.kompetensi || "");

            modalPelatihan.show();
        });

        $('#checkAllProfesi').on('change', function() {
            $('.target-profesi-checkbox').prop('checked', this.checked);
            if (this.checked) {
                $('#profesi-error').hide();
            }
        });
        
        $(document).on('change', '.target-profesi-checkbox', function() {
            let total = $('.target-profesi-checkbox').length;
            let checked = $('.target-profesi-checkbox:checked').length;
            $('#checkAllProfesi').prop('checked', total === checked && total > 0);
            if (checked > 0) {
                $('#profesi-error').hide();
            }
        });

        $('#formPelatihan').on('submit', function(e) {
            var form = this;
            var profesiChecked = $('.target-profesi-checkbox:checked').length > 0;
            if (!profesiChecked) {
                $('#profesi-error').show();
            } else {
                $('#profesi-error').hide();
            }

            if (!form.checkValidity() || !profesiChecked) {
                e.preventDefault();
                e.stopPropagation();
                
                // Trigger the browser's native tooltip
                form.reportValidity();
                
                // Collect invalid field labels
                let invalidLabels = [];
                $(form).find(':invalid').each(function() {
                    let label = $(this).closest('div').find('label').text();
                    if(label) invalidLabels.push(label);
                });
                
                let errorMsg = 'Silakan periksa kembali seluruh inputan wajib. ';
                if(invalidLabels.length > 0) {
                    errorMsg += '<br><br><b>Field yang belum lengkap:</b><br>- ' + invalidLabels.join('<br>- ');
                }
                if (!profesiChecked) {
                    errorMsg += '<br>- TARGET PROFESI (Pilih Minimal Satu)';
                }

                Swal.fire({
                    title: 'Form Belum Lengkap', 
                    html: errorMsg, 
                    icon: 'warning',
                    confirmButtonColor: '#ce2127'
                });
            } else {
                e.preventDefault();
                Swal.fire({
                    title: 'Simpan Data Pelatihan?',
                    text: "Detail pengaturan program master diklat akan disimpan.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#ce2127',
                    confirmButtonText: 'Ya, Simpan!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Menyimpan...',
                            text: 'Mohon tunggu, data sedang disimpan ke database.',
                            allowOutsideClick: false,
                            didOpen: () => { Swal.showLoading(); }
                        });
                        form.submit();
                    }
                });
            }
            $(form).addClass('was-validated');
        });

        window.changeStatus = function(id, targetStatus) {
            let textPrompt = '';
            let urlTarget = '';
            
            if (targetStatus === 'Aktif') {
                textPrompt = 'Mempublikasikan program ini ke publik?';
                urlTarget = "<?= base_url('pelatihan/admin/pelatihan/publish') ?>";
            } else if (targetStatus === 'Selesai') {
                textPrompt = 'Menandai program pelatihan ini sebagai selesai?';
                urlTarget = "<?= base_url('pelatihan/admin/pelatihan/selesai') ?>";
            } else if (targetStatus === 'Draft') {
                textPrompt = 'Mengembalikan program ini ke status Draft?';
                urlTarget = "<?= base_url('pelatihan/admin/pelatihan/draft') ?>";
            }
            
            Swal.fire({
                title: 'Ubah Publikasi?',
                text: textPrompt,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ce2127',
                confirmButtonText: 'Ya, Ubah!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = urlTarget + "/" + id;
                }
            });
        }

        window.confirmDelete = function(id) {
            Swal.fire({
                title: 'Hapus Pelatihan?',
                text: "Seluruh data pendaftaran dan progres kelas terkait pelatihan ini akan hilang permanen.",
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#ce2127',
                cancelButtonColor: '#212529',
                confirmButtonText: 'Ya, Hapus Tetap!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "<?= base_url('pelatihan/admin/pelatihan/hapus') ?>/" + id;
                }
            });
        }
    });
</script>

<style>
    /* Dasar Utilitas */
    .rounded-custom { border-radius: 1rem !important; }
    .text-slate-300 { color: #cbd5e1; }
    .uppercase-tracking { letter-spacing: 0.7px; font-size: 0.75rem !important; }
    
    /* Tombol Utama Registrasi */
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

    /* Ukuran Ultra-Kompak untuk Tombol Lingkaran */
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

    /* Ukuran Mikro Ikon */
    .small-icon {
        font-size: 0.68rem !important;
    }
    
    /* Ukuran Ultra-Kompak Tombol Status Teks agar Selaras Sempurna */
    .btn-status-toggle {
        font-size: 0.58rem !important;
        padding: 0.25rem 0.5rem !important;
        line-height: 1 !important;
        border-radius: 5px !important;
        letter-spacing: 0.2px;
        transition: all 0.2s ease;
        height: 28px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .btn-status-toggle:hover { transform: translateY(-1px); }

    /* Desain Interaktif Baris Tabel */
    .transition-row { transition: all 0.2s ease; }
    .table-hover tbody tr:hover { 
        background-color: #f8fafc !important; 
    }

    /* Override Paginasi DataTables (Merah & Hitam) */
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

    /* Form Input Focus */
    .form-control, .form-select {
        border-radius: 10px;
        padding: 10px 16px;
        border: 2px solid #f1f5f9;
        font-weight: 600;
        transition: all 0.3s;
        color: #1e293b;
    }
    .form-control:focus, .form-select:focus {
        background-color: #fff;
        border-color: #ce2127;
        box-shadow: 0 0 0 4px rgba(206, 33, 39, 0.08);
    }
</style>

<script>
    // Auto refresh every 60 seconds unless an admin modal is open
    setInterval(function() {
        if ($('.modal.show').length === 0) {
            window.location.reload();
        }
    }, 60000);



    function showToast(text, icon) {
        var Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true
        });
        Toast.fire({ icon: icon, title: text });
    }
</script>
<?= $this->endSection() ?>
