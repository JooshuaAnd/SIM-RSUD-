<?= $this->extend('Pelatihan/layout/admin_layout') ?>

<?= $this->section('content') ?>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

<form id="formPlottingKolektif" action="<?= base_url('pelatihan/admin/save_peserta') ?>" method="POST" class="needs-validation" novalidate>
    <?= csrf_field() ?>
    <div class="row g-4 mt-2">
        
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-custom p-4 bg-white h-100 border-top border-dark border-4">
                <h6 class="fw-bold mb-4 border-bottom pb-2 text-dark small text-uppercase"><i class="fas fa-graduation-cap me-2 text-danger"></i> 1. Diklat Tujuan</h6>
                
                <div class="mb-4">
                    <label class="form-label small fw-bold text-muted text-uppercase" style="font-size:0.65rem;">Program Pelatihan Target</label>
                    <select name="pelatihan_id" id="targetPelatihan" class="form-select border-2 fw-semibold text-dark px-3" required style="border-radius: 8px;">
                        <option value="">-- Pilih Program Pelatihan --</option>
                        <?php 
                        if (!empty($pelatihan)): 
                            foreach($pelatihan as $p): 
                        ?>
                            <option value="<?= $p['id'] ?>" <?= (isset($selectedId) && $selectedId == $p['id']) ? 'selected' : '' ?>><?= $p['nama'] ?></option>
                        <?php 
                            endforeach; 
                        endif; 
                        ?>
                    </select>
                    <div class="invalid-feedback">Anda wajib memilih satu target program pelatihan!</div>
                </div>
                
                <div class="alert bg-light text-dark border-start border-danger border-4 rounded-custom p-3 shadow-sm">
                    <div class="fw-bold text-danger mb-1" style="font-size: 0.8rem;"><i class="fas fa-info-circle me-1"></i> Kebijakan Plotting</div>
                    <div style="font-size: 0.75rem; line-height: 1.4;">Karyawan terpilih akan langsung berstatus <b class="text-danger">DISETUJUI</b>. Hak akses kelas materi pelatihan akan terbuka secara otomatis dan instan.</div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-custom bg-white overflow-hidden border-top border-danger border-4">
                <div class="card-header bg-white p-4 border-bottom d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <div class="fw-bold text-dark small text-uppercase me-1 d-flex align-items-center gap-1"><i class="fas fa-filter text-danger"></i> Saring:</div>
                        
                        <select id="filterProfesi" class="form-select form-select-custom rounded-pill border shadow-sm px-3 fw-semibold text-dark" style="width: 170px;">
                            <option value="">SEMUA PROFESI</option>
                            <option value="Dokter">DOKTER</option>
                            <option value="Perawat">PERAWAT</option>
                            <option value="Bidan">BIDAN</option>
                            <option value="Tenaga Medis Lain">LAINNYA</option>
                        </select>

                        <select id="filterRuangan" class="form-select form-select-custom rounded-pill border shadow-sm px-3 fw-semibold text-dark" style="width: 170px;">
                            <option value="">SEMUA UNIT KERJA</option>
                            <option value="ugd">UGD</option>
                            <option value="icu">ICU</option>
                            <option value="bangsal">BANGSAL</option>
                            <option value="umum">UMUM</option>
                        </select>
                    </div>


                </div>

                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 520px;">
                        <table id="tablePlotting" class="table table-hover align-middle mb-0 w-100">
                            <thead class="bg-light text-muted fw-bold uppercase-tracking">
                                <tr>
                                    <th class="ps-4 py-3" width="60" style="white-space: nowrap;">PILIH</th>
                                    <th class="py-3" style="white-space: nowrap;">NAMA KARYAWAN / AKUN</th>
                                    <th class="py-3" style="white-space: nowrap;">PROFESI</th>
                                    <th class="py-3" style="white-space: nowrap;">UNIT KERJA</th>
                                    <th class="pe-4 py-3" style="white-space: nowrap;">IDENTIFIKASI NIK</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                if (!empty($users)):
                                    foreach($users as $u): 
                                        $isRegistered = in_array($u['nik'], $registeredNiks ?? []);
                                ?>
                                <tr class="transition-row <?= $isRegistered ? 'bg-light text-muted opacity-75' : '' ?>">
                                    <td class="ps-4">
                                        <input type="checkbox" name="user_ids[]" value="<?= esc($u['nik']) ?>" class="form-check-input user-check border-secondary shadow-sm" <?= $isRegistered ? 'disabled checked' : '' ?>>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-dark text-white rounded-circle me-3 d-flex align-items-center justify-content-center fw-bold avatar-letter" style="border: 2px solid #ce2127;">
                                                <?= strtoupper(substr($u['nama_lengkap'], 0, 1)) ?>
                                            </div>
                                            <div class="text-truncate" style="max-width: 260px;">
                                                <div class="fw-bold small text-dark mb-0 text-truncate" title="<?= esc($u['nama_lengkap']) ?>"><?= esc(strtoupper($u['nama_lengkap'])) ?></div>
                                                <div class="text-muted font-monospace text-truncate" style="font-size: 0.68rem;"><?= esc($u['email']) ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border rounded-pill px-3 py-1 fw-bold" style="font-size:0.65rem;"><?= esc(strtoupper($u['profesi'])) ?></span>
                                    </td>
                                    <td class="text-uppercase small font-monospace text-secondary fw-semibold"><?= esc($u['ruangan'] ?? 'umum') ?></td>
                                    <td class="pe-4 fw-semibold small text-muted font-monospace">
                                        <?= esc($u['nik']) ?>
                                    </td>
                                </tr>
                                <?php 
                                    endforeach; 
                                endif; 
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="p-4 bg-light border-top text-end d-flex justify-content-between align-items-center">
                    <div class="small fw-bold text-muted ms-2"><i class="fas fa-check-square text-danger me-1"></i> Terpilih: <span id="selectedCount" class="text-danger">0</span> Pegawai</div>
                    <button type="submit" class="btn btn-register-submit rounded-pill px-4 fw-bold border-0 d-inline-flex align-items-center gap-2">
                        <i class="fas fa-save small"></i> PLOTTING KOLEKTIF
                    </button>
                </div>
            </div>
        </div>

    </div>
</form>

<style>
    /* Dasar Utilitas Merah Hitam Putih */
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

    .form-select-custom {
        font-size: 0.75rem !important;
        padding: 6px 12px !important;
        border: 1px solid #e2e8f0 !important;
        cursor: pointer;
    }

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
        var tablePlotting = $('#tablePlotting').DataTable({
            pageLength: 25,
            responsive: true,
            dom: 'lrtip',
            columnDefs: [
                { targets: 'no-sort', orderable: false }
            ],
            language: { 
                emptyTable: "Tidak ada data karyawan yang cocok dengan filter.",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ karyawan",
                paginate: { previous: "‹", next: "›" }
            }
        });


        $('#filterProfesi').on('change', function() { tablePlotting.column(2).search(this.value).draw(); });
        $('#filterRuangan').on('change', function() { tablePlotting.column(3).search(this.value).draw(); });

        $('#tablePlotting tbody').on('change', '.user-check', function() {
            hitungTerpilih();
        });

        function hitungTerpilih() {
            var count = tablePlotting.$('input.user-check:checked').length;
            $('#selectedCount').text(count);
        }

        $('#formPlottingKolektif').on('submit', function(e) {
            var form = this;
            
            var idPelatihan = $('#targetPelatihan').val();
            if(!idPelatihan) {
                e.preventDefault();
                Swal.fire({ 
                    title: 'Pelatihan Kosong!', 
                    text: 'Silakan tentukan terlebih dahulu program diklat tujuan.', 
                    icon: 'warning', 
                    confirmButtonColor: '#ce2127' 
                });
                return false;
            }

            var checkedCount = tablePlotting.$('input.user-check:checked').length;
            if(checkedCount === 0) {
                e.preventDefault();
                Swal.fire({ 
                    title: 'Peserta Belum Dipilih!', 
                    text: 'Harap centang minimal satu karyawan pada tabel calon peserta.', 
                    icon: 'warning', 
                    confirmButtonColor: '#ce2127' 
                });
                return false;
            }

            e.preventDefault();
            var namaPelatihan = $("#targetPelatihan option:selected").text();

            Swal.fire({
                title: 'Konfirmasi Plotting',
                html: `Anda akan mendaftarkan secara masal sebanyak <b>${checkedCount} pegawai</b> langsung ke dalam program:<br><br><span class="text-danger fw-bold">${namaPelatihan}</span>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#ce2127',
                cancelButtonColor: '#212529',
                confirmButtonText: 'Ya, Daftarkan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>
