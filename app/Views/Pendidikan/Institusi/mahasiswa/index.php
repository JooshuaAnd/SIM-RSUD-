<?= $this->include('Pendidikan/Institusi/layout/header') ?>
<?= $this->include('Pendidikan/Institusi/layout/sidebar') ?>

<div class="row">
    <div class="col-12 mb-4">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h4 class="fw-bold">Daftar Mahasiswa Diterima</h4>
                <p class="text-muted small mb-0">Berikut adalah daftar mahasiswa dari institusi Anda yang telah disetujui untuk mengikuti praktek di RSUD.</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-danger"><i class="fas fa-user-check me-2"></i> Akun Mahasiswa Aktif</h6>
                    <div class="badge bg-light text-dark border">
                        <i class="fas fa-info-circle text-info me-1"></i> Password default adalah tanggal lahir (DDMMYYYY)
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 text-nowrap">
                        <thead class="table-light">
                            <tr class="small text-uppercase">
                                <th class="ps-4">Mahasiswa</th>
                                <th>NIM</th>
                                <th>Email</th>
                                <th>No. HP</th>
                                <th>Tanggal Lahir</th>
                                <th>Prodi</th>
                                <th>Periode</th>
                                <th class="text-center">Pembayaran</th>
                                <th class="text-center">Logbook</th>
                                <th class="text-center">Nilai Akhir</th>
                                <th class="text-center">Status Akun</th>
                                <th class="text-center pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($list_mahasiswa)) : ?>
                                <tr>
                                    <td colspan="12" class="text-center py-5 text-muted">
                                        <i class="fas fa-user-clock fa-3x mb-3 d-block"></i>
                                        Belum ada mahasiswa yang disetujui. Silakan tunggu verifikasi pengajuan.
                                    </td>
                                </tr>
                            <?php else : ?>
                            <?php foreach ($list_mahasiswa as $row) : ?>
                                <?php
                                    $paymentStatus = $row['payment_status'] ?? 'Belum Bayar';
                                    $hasPaid = ($paymentStatus == 'Lunas');
                                ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <?php if($row['file_foto']): ?>
                                                <img src="<?= base_url('uploads/dokumen_mahasiswa/' . $row['file_foto']) ?>" class="rounded-circle me-3 shadow-sm" style="width: 45px; height: 45px; object-fit: cover; border: 2px solid #fff;" alt="Foto">
                                            <?php else: ?>
                                                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center text-secondary me-3 shadow-sm" style="width: 45px; height: 45px; border: 2px solid #fff;">
                                                    <i class="fas fa-user"></i>
                                                </div>
                                            <?php endif; ?>
                                            <span class="fw-bold text-dark"><?= $row['nama'] ?></span>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-light text-secondary"><?= $row['nim'] ?></span></td>
                                    <td class="small text-muted"><i class="far fa-envelope text-danger me-1"></i><?= $row['email'] ?></td>
                                    <td class="small text-muted"><i class="fas fa-phone fa-xs text-primary me-1"></i><?= $row['hp'] ?></td>
                                    <td><span class="badge bg-light text-dark border"><i class="far fa-calendar-alt text-success me-1"></i><?= isset($row['dob']) ? date('d-m-Y', strtotime($row['dob'])) : '-' ?></span></td>
                                    <td><?= $row['prodi'] ?></td>
                                    <td class="small text-muted"><?= $row['periode'] ?></td>
                                    <td class="text-center">
                                        <?php $paymentStatus = $row['payment_status'] ?? 'Belum Bayar'; ?>
                                        <div class="d-flex flex-column align-items-center justify-content-center gap-1">
                                            <?php if ($paymentStatus == 'Lunas') : ?>
                                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1 fw-semibold w-100" style="font-size: 0.7rem;">
                                                    <i class="fas fa-check-circle me-1 text-success"></i> Lunas
                                                </span>
                                                <button type="button" class="btn btn-sm btn-outline-success py-1 px-2 w-100" style="font-size: 0.7rem;" onclick="viewPaymentModalMhs('<?= $row['nama'] ?>', '<?= $row['nim'] ?>', '<?= $row['prodi'] ?>', 'Lunas', '<?= $row['nominal'] ?>', '<?= $row['invoice_file'] ?>')" title="Lihat Bukti">
                                                    <i class="fas fa-file-invoice-dollar me-1"></i> Bukti
                                                </button>
                                            <?php elseif ($paymentStatus == 'Menunggu Verifikasi') : ?>
                                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2 py-1 fw-semibold w-100" style="font-size: 0.7rem;">
                                                    <i class="fas fa-clock me-1 text-warning"></i> Menunggu Verif
                                                </span>
                                                <button type="button" class="btn btn-sm btn-outline-warning py-1 px-2 w-100" style="font-size: 0.7rem;" onclick="viewPaymentModalMhs('<?= $row['nama'] ?>', '<?= $row['nim'] ?>', '<?= $row['prodi'] ?>', 'Menunggu Verifikasi', '<?= $row['nominal'] ?>', '<?= $row['invoice_file'] ?>')" title="Lihat Bukti">
                                                    <i class="fas fa-eye me-1"></i> Bukti Upload
                                                </button>
                                            <?php elseif ($paymentStatus == 'Belum Invoice') : ?>
                                                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-2 py-1 fw-semibold w-100" style="font-size: 0.7rem;">
                                                    <i class="fas fa-hourglass-half me-1 text-secondary"></i> Menunggu Invoice
                                                </span>
                                                <button type="button" class="btn btn-sm btn-secondary opacity-50 py-1 px-2 w-100" style="font-size: 0.7rem; cursor: not-allowed;" title="Admin belum menerbitkan tagihan">
                                                    <i class="fas fa-ban me-1"></i> Belum Bisa Bayar
                                                </button>
                                            <?php elseif ($paymentStatus == 'Ditolak') : ?>
                                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1 fw-semibold w-100" style="font-size: 0.7rem;">
                                                    <i class="fas fa-times-circle me-1 text-danger"></i> Ditolak
                                                </span>
                                                <button type="button" class="btn btn-sm btn-danger py-1 px-2 w-100" style="font-size: 0.7rem;" onclick="openPaymentModalMhs('<?= $row['id'] ?>', '<?= $row['nama'] ?>', '<?= $row['nim'] ?>', '<?= $row['prodi'] ?>', '<?= $row['nominal'] ?>', '<?= $row['invoice_file'] ?>', '<?= addslashes($row['alasan_penolakan'] ?? '') ?>')" title="Bayar Ulang">
                                                    <i class="fas fa-credit-card me-1"></i> Bayar
                                                </button>
                                            <?php else : ?>
                                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1 fw-semibold w-100" style="font-size: 0.7rem;">
                                                    <i class="fas fa-exclamation-triangle me-1 text-danger"></i> Belum Bayar
                                                </span>
                                                <button type="button" class="btn btn-sm btn-danger py-1 px-2 w-100" style="font-size: 0.7rem;" onclick="openPaymentModalMhs('<?= $row['id'] ?>', '<?= $row['nama'] ?>', '<?= $row['nim'] ?>', '<?= $row['prodi'] ?>', '<?= $row['nominal'] ?>', '<?= $row['invoice_file'] ?>', '<?= addslashes($row['alasan_penolakan'] ?? '') ?>')" title="Bayar Tagihan">
                                                    <i class="fas fa-credit-card me-1"></i> Bayar
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($hasPaid) : ?>
                                            <button class="btn btn-sm btn-outline-primary fw-bold" data-bs-toggle="modal" data-bs-target="#modalLogbook" onclick="showLogbook('<?= $row['id'] ?>', '<?= esc($row['nama'], 'js') ?>', '<?= esc($row['nim'], 'js') ?>')" title="Lihat Logbook Mahasiswa">
                                                <i class="fas fa-book-medical me-1"></i> Logbook
                                            </button>
                                        <?php else : ?>
                                            <button class="btn btn-sm btn-outline-secondary opacity-50" style="cursor: not-allowed;" title="Akses Logbook terkunci (Belum Lunas)">
                                                <i class="fas fa-lock me-1"></i> Logbook
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($hasPaid) : ?>
                                            <button class="btn btn-sm btn-outline-success fw-bold" data-bs-toggle="modal" data-bs-target="#modalInputNilai" 
                                                onclick="prepareNilai('<?= $row['id'] ?>', '<?= esc($row['nama'], 'js') ?>', '<?= esc($row['nim'], 'js') ?>')">
                                                <i class="fas fa-plus-circle me-1"></i> Input Nilai
                                            </button>
                                        <?php else : ?>
                                            <button class="btn btn-sm btn-outline-secondary fw-bold opacity-60 px-2" style="cursor: not-allowed;" onclick="alert('Pembayaran belum lunas! Silakan selesaikan biaya administrasi stase mahasiswa ini terlebih dahulu.')" title="Pembayaran Belum Lunas">
                                                <i class="fas fa-lock me-1"></i> Input Nilai
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-success px-2 py-1" style="font-size: 0.7rem;">
                                            <?= $row['status'] ?>
                                        </span>
                                    </td>
                                    <td class="text-center pe-4">
                                        <?php 
                                            $nama_profesi = '-';
                                            foreach($list_profesi as $p) {
                                                if($p['id_profesi'] == ($row['id_profesi'] ?? '')) {
                                                    $nama_profesi = $p['nama_profesi'];
                                                    break;
                                                }
                                            }
                                        ?>
                                        <div class="d-flex gap-1 justify-content-center">
                                            <button type="button" class="btn btn-sm btn-info text-white" onclick="showDetailMhs('<?= esc($row['nama'], 'js') ?>', '<?= esc($row['nim'], 'js') ?>', '<?= esc($row['prodi'], 'js') ?>', '<?= esc($row['jk'] ?? '', 'js') ?>', '<?= esc($row['email'], 'js') ?>', '<?= esc($row['hp'], 'js') ?>', '<?= esc($row['dob'], 'js') ?>', '<?= esc($row['file_foto'] ?? '', 'js') ?>', '<?= esc($row['file_ijazah'] ?? '', 'js') ?>', '<?= esc($row['file_sk'] ?? '', 'js') ?>', '<?= esc($nama_profesi, 'js') ?>')" title="Detail Mahasiswa">
                                                <i class="fas fa-user-circle"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-warning text-dark" onclick="showEditMhs('<?= $row['id'] ?>', '<?= esc($row['nama'], 'js') ?>', '<?= esc($row['nim'], 'js') ?>', '<?= esc($row['prodi'], 'js') ?>', '<?= esc($row['jk'] ?? '', 'js') ?>', '<?= esc($row['email'], 'js') ?>', '<?= esc($row['hp'], 'js') ?>', '<?= esc($row['dob'], 'js') ?>', '<?= esc($row['semester'] ?? '', 'js') ?>', '<?= esc($row['id_profesi'] ?? '', 'js') ?>')" title="Edit Data">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <?php if ($hasPaid) : ?>
                                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#modalNilai" onclick="showGrades('<?= $row['id'] ?>', '<?= esc($row['nama'], 'js') ?>', '<?= esc($row['nim'], 'js') ?>')" title="Detail Nilai">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            <?php else : ?>
                                                <button type="button" class="btn btn-sm btn-secondary opacity-50" onclick="alert('Pembayaran belum lunas! Akses lembar penilaian dan sertifikat mahasiswa ini terkunci.'); return false;" title="Pembayaran Belum Lunas (Detail Nilai Terkunci)">
                                                    <i class="fas fa-lock"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Input Nilai Akhir -->
<div class="modal fade" id="modalInputNilai" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Input Nilai Akhir Mahasiswa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formInputNilai" onsubmit="return submitNilaiAkhir(event)">
                <input type="hidden" id="inputMhsId" name="id">
                <div class="modal-body">
                    <div class="p-3 bg-light rounded-3 mb-3 d-flex align-items-center">
                        <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <div>
                            <h6 id="inputName" class="mb-0 fw-bold">Nama Mahasiswa</h6>
                            <span id="inputNIM" class="text-muted small">NIM: 123456</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Nilai Akhir (0-100)</label>
                        <input type="number" step="0.1" class="form-control form-control-lg text-center fw-bold" placeholder="Contoh: 90.5" required min="0" max="100" id="finalScoreInput">
                    </div>
                    <div class="alert alert-info small border-0 mb-0">
                        <i class="fas fa-info-circle me-1"></i> Setelah nilai diinput, mahasiswa akan otomatis dipindahkan ke daftar **Mahasiswa Lulus**.
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success px-4 fw-bold">Simpan & Loloskan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Daftar Nilai -->
<div class="modal fade" id="modalNilai" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Daftar Nilai Mahasiswa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex align-items-center mb-4 p-3 bg-light rounded-3">
                    <div class="w-12 h-12 rounded-circle bg-danger text-white d-flex align-items-center justify-content-center fw-bold me-3" style="width: 50px; height: 50px; font-size: 1.2rem;">
                        <span id="initials">M</span>
                    </div>
                    <div>
                        <h6 id="studentName" class="mb-0 fw-bold text-dark">Nama Mahasiswa</h6>
                        <span id="studentNIM" class="text-muted small">NIM: 123456</span>
                    </div>
                    <div class="ms-auto">
                        <button class="btn btn-danger btn-sm fw-bold">
                            <i class="fas fa-download me-1"></i> Download Semua Nilai (PDF)
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr class="small text-uppercase">
                                <th>Stase & Ruangan</th>
                                <th>Pembimbing (CI)</th>
                                <th>Tugas Mahasiswa</th>
                                <th class="text-center">Nilai Tugas</th>
                            </tr>
                        </thead>
                        <tbody id="nilaiTbody">
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">Memuat data...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
            </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Logbook Mahasiswa -->
<div class="modal fade" id="modalLogbook" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Daftar Logbook Mahasiswa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex align-items-center mb-4 p-3 bg-light rounded-3">
                    <div class="w-12 h-12 rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold me-3" style="width: 50px; height: 50px; font-size: 1.2rem;">
                        <span id="lbInitials">M</span>
                    </div>
                    <div>
                        <h6 id="lbStudentName" class="mb-0 fw-bold text-dark">Nama Mahasiswa</h6>
                        <span id="lbStudentNIM" class="text-muted small">NIM: 123456</span>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr class="small text-uppercase">
                                <th>Stase & Ruangan</th>
                                <th>Pembimbing (CI)</th>
                                <th>Logbook Mahasiswa</th>
                            </tr>
                        </thead>
                        <tbody id="logbookTbody">
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">Memuat data...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Pembayaran Mahasiswa -->
<div class="modal fade" id="paymentModalMhs" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-danger text-white py-3">
                <h5 class="modal-title fw-bold"><i class="fas fa-credit-card me-2"></i> Pembayaran Tagihan Mahasiswa</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3 p-3 bg-light rounded border">
                    <div class="row small text-muted mb-2">
                        <div class="col-5">Nama Mahasiswa:</div>
                        <div class="col-7 text-end fw-bold text-dark" id="payMhsName">-</div>
                    </div>
                    <div class="row small text-muted mb-2">
                        <div class="col-5">NIM:</div>
                        <div class="col-7 text-end fw-bold text-dark" id="payMhsNIM">-</div>
                    </div>
                    <div class="row small text-muted mb-2">
                        <div class="col-5">Program Studi:</div>
                        <div class="col-7 text-end fw-bold text-dark" id="payMhsProdi">-</div>
                    </div>
                    <div class="row small text-muted mb-2 border-bottom pb-2">
                        <div class="col-12 fw-bold text-dark mb-1"><i class="fas fa-file-invoice me-1"></i> Invoice dari Admin Diklat</div>
                        <div class="col-5">File Invoice:</div>
                        <div class="col-7 text-end">
                            <a href="#" class="btn btn-sm btn-outline-danger py-0 px-2 fw-bold" style="font-size: 0.75rem;" id="payMhsInvoiceLink" target="_blank">
                                <i class="fas fa-file-pdf me-1"></i> File Invoice
                            </a>
                        </div>
                    </div>
                    <div class="row small text-muted pt-1 mt-1">
                        <div class="col-5 fw-bold text-danger">Total Tagihan:</div>
                        <div class="col-7 text-end fw-bold text-danger fs-6" id="payMhsTotal">-</div>
                    </div>
                </div>

                <div id="payAlasanSection" class="mb-3 d-none">
                    <div class="p-3 border rounded bg-danger bg-opacity-10 border-danger border-opacity-25">
                        <h6 class="fw-bold text-danger small mb-2"><i class="fas fa-exclamation-circle me-1"></i> Pembayaran Sebelumnya Ditolak</h6>
                        <p class="small text-dark mb-0" id="payAlasanText">-</p>
                    </div>
                </div>

                <div class="mb-4">
                    <h6 class="fw-bold mb-2 small text-uppercase text-muted">Rekening Transfer:</h6>
                    <div class="p-3 border rounded bg-white shadow-sm d-flex align-items-center gap-3">
                        <div class="bg-light p-2 rounded text-center fw-bold text-danger" style="width: 60px; font-size: 14px; border: 1px solid #ddd;">
                            BCA
                        </div>
                        <div>
                            <div class="fw-bold text-dark fs-5" style="letter-spacing: 0.5px;">123-456-7890</div>
                            <div class="small text-muted">a.n. SIM DIKLAT KESEHATAN</div>
                        </div>
                    </div>
                </div>

                <form id="paymentFormMhs" onsubmit="submitPaymentMhs(event)" enctype="multipart/form-data">
                    <input type="hidden" name="mahasiswa_id" id="payMhsId" value="">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small">Unggah Bukti Transfer <span class="text-danger">*</span></label>
                        <input type="file" class="form-control form-control-sm" name="bukti_bayar" accept=".pdf,.jpg,.jpeg,.png" required>
                        <div class="form-text" style="font-size: 0.7rem;">Maksimal 2MB. Format: PDF/JPG/PNG.</div>
                    </div>
                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-danger fw-bold py-2 shadow-sm">Kirim Bukti Pembayaran</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Bukti Pembayaran Mahasiswa -->
<div class="modal fade" id="viewPaymentModalMhs" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-success text-white py-3">
                <h5 class="modal-title fw-bold"><i class="fas fa-file-invoice-dollar me-2"></i> Bukti Pembayaran Mahasiswa</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div id="viewMhsBadge"></div>
                <div class="mb-3 p-3 bg-light rounded border text-start">
                    <div class="row small text-muted mb-1">
                        <div class="col-5">Nama Mahasiswa:</div>
                        <div class="col-7 text-end fw-bold text-dark" id="viewMhsName">-</div>
                    </div>
                    <div class="row small text-muted mt-2">
                        <div class="col-5">NIM:</div>
                        <div class="col-7 text-end fw-bold text-dark" id="viewMhsNIM">-</div>
                    </div>
                    <div class="row small text-muted mt-2 d-none">
                        <div class="col-5">Status:</div>
                        <div class="col-7 text-end fw-bold text-dark" id="viewMhsStatus">-</div>
                    </div>
                </div>

                <div class="border rounded p-4 mb-3 bg-light d-flex flex-column align-items-center justify-content-center" style="min-height: 200px;">
                    <i class="fas fa-file-pdf text-danger fa-4x mb-3 animate__animated animate__pulse animate__infinite"></i>
                    <h6 class="fw-bold mb-1 text-dark">bukti_pembayaran_mhs.pdf</h6>
                    <small class="text-muted mb-3">File ini diunggah oleh institusi Anda.</small>
                    <a href="#" class="btn btn-sm btn-success fw-bold px-4 shadow-sm" onclick="alert('Mengunduh file bukti pembayaran... (Frontend Only)'); return false;">
                        <i class="fas fa-download me-1"></i> Unduh Bukti
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Mahasiswa -->
<div class="modal fade" id="modalDetailMhs" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title fw-bold"><i class="fas fa-user-circle me-2"></i> Detail Mahasiswa</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row">
                    <div class="col-md-8">
                        <table class="table table-sm table-borderless">
                            <tr><td width="35%" class="text-muted">Nama Lengkap</td><td class="fw-bold" id="detailMhsNama">-</td></tr>
                            <tr><td class="text-muted">NIM</td><td class="fw-bold" id="detailMhsNim">-</td></tr>
                            <tr><td class="text-muted">Program Studi</td><td class="fw-bold" id="detailMhsProdi">-</td></tr>
                            <tr><td class="text-muted">Profesi</td><td class="fw-bold" id="detailMhsProfesi">-</td></tr>
                            <tr><td class="text-muted">Jenis Kelamin</td><td class="fw-bold" id="detailMhsJk">-</td></tr>
                            <tr><td class="text-muted">Tanggal Lahir</td><td class="fw-bold" id="detailMhsDob">-</td></tr>
                            <tr><td class="text-muted">Email</td><td class="fw-bold" id="detailMhsEmail">-</td></tr>
                            <tr><td class="text-muted">No. HP</td><td class="fw-bold" id="detailMhsHp">-</td></tr>
                        </table>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="mb-2 text-muted small fw-bold">Pas Foto</div>
                        <img src="" id="detailMhsFoto" class="img-thumbnail rounded" style="width: 120px; height: 160px; object-fit: cover;" alt="Pas Foto">
                    </div>
                </div>
                <hr>
                <div class="row mt-3">
                    <div class="col-6">
                        <div class="text-muted small fw-bold mb-1">File Ijazah / Transkrip</div>
                        <a href="#" id="detailMhsIjazah" class="btn btn-sm btn-outline-primary" target="_blank"><i class="fas fa-file-pdf me-1"></i> Lihat Dokumen</a>
                    </div>
                    <div class="col-6">
                        <div class="text-muted small fw-bold mb-1">File SK / Surat Sehat</div>
                        <a href="#" id="detailMhsSk" class="btn btn-sm btn-outline-primary" target="_blank"><i class="fas fa-file-pdf me-1"></i> Lihat Dokumen</a>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Mahasiswa -->
<div class="modal fade" id="modalEditMhs" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title fw-bold"><i class="fas fa-edit me-2"></i> Edit Data Mahasiswa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditMhs" onsubmit="submitEditMhs(event)" enctype="multipart/form-data">
                <div class="modal-body p-4">
                    <input type="hidden" name="mahasiswa_id" id="editMhsId">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nama Lengkap</label>
                            <input type="text" class="form-control" name="nama_lengkap" id="editMhsNama" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">NIM</label>
                            <input type="text" class="form-control" name="nim" id="editMhsNim" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Email</label>
                            <input type="email" class="form-control" name="email" id="editMhsEmail" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">No. HP</label>
                            <input type="text" class="form-control" name="no_hp" id="editMhsHp" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Tanggal Lahir</label>
                            <input type="date" class="form-control" name="tanggal_lahir" id="editMhsDob" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Jenis Kelamin</label>
                            <select class="form-select" name="jenis_kelamin" id="editMhsJk" required>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Program Studi</label>
                            <input type="text" class="form-control" name="program_studi" id="editMhsProdi" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Profesi</label>
                            <select class="form-select" name="id_profesi" id="editMhsProfesi" required>
                                <option value="" disabled selected>Pilih Profesi...</option>
                                <?php foreach($list_profesi as $profesi): ?>
                                    <option value="<?= $profesi['id_profesi'] ?>"><?= esc($profesi['nama_profesi']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Semester</label>
                            <input type="text" class="form-control" name="semester" id="editMhsSemester">
                        </div>
                    </div>
                    <hr>
                    <p class="text-muted small mb-3">Upload file baru jika ingin mengganti dokumen sebelumnya. Biarkan kosong jika tidak ada perubahan.</p>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Pas Foto</label>
                            <input type="file" class="form-control form-control-sm" name="file_foto" accept="image/*">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Ijazah / Transkrip</label>
                            <input type="file" class="form-control form-control-sm" name="file_ijazah" accept=".pdf">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">SK / Surat Sehat</label>
                            <input type="file" class="form-control form-control-sm" name="file_sk" accept=".pdf">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning fw-bold"><i class="fas fa-save me-1"></i> Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openPaymentModalMhs(id, name, nim, prodi, nominal, invoice_file, alasan_penolakan) {
    document.getElementById('payMhsId').value = id;
    document.getElementById('payMhsName').innerText = name;
    document.getElementById('payMhsNIM').innerText = nim;
    document.getElementById('payMhsProdi').innerText = prodi;
    
    // Determine total based on database
    let total = parseFloat(nominal);
    
    // Set invoice file link
    let invoiceLink = document.getElementById('payMhsInvoiceLink');
    if (invoice_file && invoice_file !== 'null' && invoice_file !== '') {
        invoiceLink.href = '<?= base_url('uploads/invoices/') ?>' + invoice_file;
        invoiceLink.innerHTML = '<i class="fas fa-file-pdf me-1"></i> ' + invoice_file;
        invoiceLink.removeAttribute('onclick');
        invoiceLink.target = '_blank';
    } else {
        invoiceLink.href = '#';
        invoiceLink.innerHTML = '<i class="fas fa-file-pdf me-1"></i> Belum ada file';
        invoiceLink.setAttribute('onclick', 'alert(\'Invoice belum tersedia\'); return false;');
        invoiceLink.removeAttribute('target');
    }

    const formattedTotal = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(total);
    document.getElementById('payMhsTotal').innerText = formattedTotal;

    // Tampilkan alasan penolakan jika ada
    var alasanSection = document.getElementById('payAlasanSection');
    var alasanText = document.getElementById('payAlasanText');
    if (alasan_penolakan && alasan_penolakan.trim() !== '') {
        alasanText.innerText = alasan_penolakan;
        alasanSection.classList.remove('d-none');
    } else {
        alasanSection.classList.add('d-none');
        alasanText.innerText = '';
    }

    const myModal = new bootstrap.Modal(document.getElementById('paymentModalMhs'));
    myModal.show();
}

function viewPaymentModalMhs(name, nim, prodi, status, nominal, invoice_file) {
    document.getElementById('viewMhsName').innerText = name;
    document.getElementById('viewMhsNIM').innerText = nim;
    document.getElementById('viewMhsStatus').innerText = status;
    
    const badge = document.getElementById('viewMhsBadge');
    if (status === 'Lunas') {
        badge.className = 'badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1 fw-semibold mb-3 d-inline-block';
        badge.innerHTML = '<i class="fas fa-check-circle me-1"></i> Pembayaran Lunas & Diverifikasi';
    } else {
        badge.className = 'badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2 py-1 fw-semibold mb-3 d-inline-block';
        badge.innerHTML = '<i class="fas fa-clock me-1"></i> Menunggu Verifikasi Admin';
    }

    const myModal = new bootstrap.Modal(document.getElementById('viewPaymentModalMhs'));
    myModal.show();
}

function submitPaymentMhs(event) {
    event.preventDefault();
    const form = document.getElementById('paymentFormMhs');
    const formData = new FormData(form);

    const submitBtn = form.querySelector('button[type="submit"]');
    const originalBtnHtml = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Mengirim...';
    submitBtn.disabled = true;

    fetch('<?= base_url('pendidikan/institusi/mahasiswa/submit_payment') ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        submitBtn.innerHTML = originalBtnHtml;
        submitBtn.disabled = false;
        if (data.success) {
            const modalEl = document.getElementById('paymentModalMhs');
            const modalInstance = bootstrap.Modal.getInstance(modalEl);
            modalInstance.hide();
            
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: data.message,
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                window.location.reload();
            });
        } else {
            Swal.fire('Error', data.message || 'Terjadi kesalahan.', 'error');
        }
    })
    .catch(error => {
        submitBtn.innerHTML = originalBtnHtml;
        submitBtn.disabled = false;
        console.error('Error:', error);
        Swal.fire('Error', 'Terjadi kesalahan sistem.', 'error');
    });
}
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        alert('Password berhasil disalin!');
    });
}

function showGrades(id, name, nim) {
    document.getElementById('studentName').innerText = name;
    document.getElementById('studentNIM').innerText = 'NIM: ' + nim;
    document.getElementById('initials').innerText = name.charAt(0);
    
    const tbody = document.getElementById('nilaiTbody');
    tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted py-4"><i class="fas fa-spinner fa-spin me-2"></i> Memuat data...</td></tr>';

    fetch('<?= base_url('pendidikan/institusi/mahasiswa/get_nilai/') ?>' + id)
        .then(response => response.json())
        .then(res => {
            if (res.success) {
                if (res.data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted py-4">Belum ada penempatan stase/ruangan untuk mahasiswa ini.</td></tr>';
                    return;
                }
                
                let html = '';
                let totalScoreSum = 0;
                let totalTasksWithScore = 0;

                res.data.forEach(room => {
                    let taskHtml = '';
                    let scoreHtml = '';
                    let roomScoreSum = 0;
                    let roomTasksWithScore = 0;

                    if (room.tugas && room.tugas.length > 0) {
                        taskHtml = '<ul class="mb-0 ps-3 small">';
                        scoreHtml = '<div class="d-flex flex-column gap-1 align-items-center">';
                        room.tugas.forEach(t => {
                            taskHtml += `<li>${t.nama_tugas}</li>`;
                            if (t.nilai !== null && t.nilai !== '') {
                                scoreHtml += `<span class="badge bg-primary w-100">${t.nilai}</span>`;
                                roomScoreSum += parseFloat(t.nilai);
                                roomTasksWithScore++;
                                totalScoreSum += parseFloat(t.nilai);
                                totalTasksWithScore++;
                            } else {
                                scoreHtml += `<span class="badge bg-secondary w-100">Belum dinilai</span>`;
                            }
                        });
                        taskHtml += '</ul>';
                        
                        if (roomTasksWithScore > 1) {
                            let avg = (roomScoreSum / roomTasksWithScore).toFixed(1);
                            scoreHtml += `<hr class="my-1 w-100"><span class="badge bg-success w-100">Rata-rata: ${avg}</span>`;
                        }
                        
                        scoreHtml += '</div>';
                    } else {
                        taskHtml = '<span class="small text-muted">Tidak ada tugas</span>';
                        scoreHtml = '<span class="small text-muted">-</span>';
                    }

                    html += `
                        <tr>
                            <td>
                                <span class="fw-bold d-block">${room.nama_stase}</span>
                                <span class="text-muted small">${room.nama_ruangan}</span>
                            </td>
                            <td class="fw-medium text-primary">${room.ci_name || '-'}</td>
                            <td>${taskHtml}</td>
                            <td class="text-center">${scoreHtml}</td>
                        </tr>
                    `;
                });

                if (totalTasksWithScore > 0) {
                    let finalAvg = (totalScoreSum / totalTasksWithScore).toFixed(1);
                    html += `
                        <tr class="table-light">
                            <td colspan="3" class="text-end fw-bold">Total Rata-rata Nilai Keseluruhan:</td>
                            <td class="text-center fw-bold text-success fs-6">${finalAvg}</td>
                        </tr>
                    `;
                }

                tbody.innerHTML = html;
            } else {
                tbody.innerHTML = '<tr><td colspan="4" class="text-center text-danger py-4">Gagal memuat data.</td></tr>';
            }
        })
        .catch(err => {
            console.error(err);
            tbody.innerHTML = '<tr><td colspan="4" class="text-center text-danger py-4">Terjadi kesalahan koneksi.</td></tr>';
        });
}

function showLogbook(id, name, nim) {
    document.getElementById('lbStudentName').innerText = name;
    document.getElementById('lbStudentNIM').innerText = 'NIM: ' + nim;
    document.getElementById('lbInitials').innerText = name.charAt(0);
    
    const tbody = document.getElementById('logbookTbody');
    tbody.innerHTML = '<tr><td colspan="3" class="text-center text-muted py-4"><i class="fas fa-spinner fa-spin me-2"></i> Memuat data...</td></tr>';

    fetch('<?= base_url('pendidikan/institusi/mahasiswa/get_nilai/') ?>' + id)
        .then(response => response.json())
        .then(res => {
            if (res.success) {
                if (res.data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="3" class="text-center text-muted py-4">Belum ada penempatan stase/ruangan untuk mahasiswa ini.</td></tr>';
                    return;
                }
                
                let html = '';
                res.data.forEach(room => {
                    let lbHtml = '';
                    if (room.logbook && room.logbook.length > 0) {
                        lbHtml = '<ul class="mb-0 ps-3 small list-unstyled">';
                        room.logbook.forEach(lb => {
                            let statusBadge = '';
                            if (lb.status_validasi === 'Disetujui') {
                                statusBadge = '<span class="badge bg-success ms-2 px-2 py-1" style="font-size: 0.65rem;">Disetujui</span>';
                            } else if (lb.status_validasi === 'Revisi') {
                                statusBadge = '<span class="badge bg-warning text-dark ms-2 px-2 py-1" style="font-size: 0.65rem;">Revisi</span>';
                            } else {
                                statusBadge = '<span class="badge bg-secondary ms-2 px-2 py-1" style="font-size: 0.65rem;">Pending</span>';
                            }
                            
                            let fileLink = '';
                            if (lb.file_lampiran) {
                                fileLink = `<a href="<?= base_url('uploads/pendidikan/logbookmhs/') ?>${lb.file_lampiran}" target="_blank" class="text-danger fw-bold ms-2" title="Lihat/Download PDF"><i class="fas fa-file-pdf"></i></a>`;
                            }
                            
                            lbHtml += `<li class="mb-2 pb-2 border-bottom">
                                <div class="d-flex align-items-center mb-1">
                                    <strong>${lb.judul_kegiatan}</strong>
                                    ${fileLink}
                                </div>
                                <div class="text-muted" style="font-size: 0.75rem;"><i class="far fa-calendar-alt me-1"></i> ${lb.tanggal_kegiatan} ${statusBadge}</div>
                            </li>`;
                        });
                        lbHtml += '</ul>';
                    } else {
                        lbHtml = '<span class="small text-muted">Belum ada logbook</span>';
                    }

                    html += `
                        <tr>
                            <td>
                                <span class="fw-bold d-block">${room.nama_stase}</span>
                                <span class="text-muted small">${room.nama_ruangan}</span>
                            </td>
                            <td class="fw-medium text-primary">${room.ci_name || '-'}</td>
                            <td>${lbHtml}</td>
                        </tr>
                    `;
                });
                tbody.innerHTML = html;
            } else {
                tbody.innerHTML = '<tr><td colspan="3" class="text-center text-danger py-4">Gagal memuat data.</td></tr>';
            }
        })
        .catch(err => {
            console.error(err);
            tbody.innerHTML = '<tr><td colspan="3" class="text-center text-danger py-4">Terjadi kesalahan koneksi.</td></tr>';
        });
}

function prepareNilai(id, name, nim) {
    document.getElementById('inputMhsId').value = id;
    document.getElementById('inputName').innerText = name;
    document.getElementById('inputNIM').innerText = 'NIM: ' + nim;
}

function submitNilaiAkhir(event) {
    event.preventDefault();
    const id = document.getElementById('inputMhsId').value;
    const name = document.getElementById('inputName').innerText;
    const score = document.getElementById('finalScoreInput').value;
    
    const formData = new FormData();
    formData.append('id', id);
    formData.append('nilai_akhir', score);

    const submitBtn = event.target.querySelector('button[type="submit"]');
    const originalBtnHtml = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Menyimpan...';
    submitBtn.disabled = true;

    fetch('<?= base_url('pendidikan/institusi/mahasiswa/simpan_nilai_akhir') ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        submitBtn.innerHTML = originalBtnHtml;
        submitBtn.disabled = false;
        
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: data.message,
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                window.location.href = '<?= base_url('pendidikan/institusi/mahasiswa/lulus') ?>';
            });
        } else {
            Swal.fire('Error', data.message || 'Terjadi kesalahan saat menyimpan nilai.', 'error');
        }
    })
    .catch(error => {
        submitBtn.innerHTML = originalBtnHtml;
        submitBtn.disabled = false;
        console.error('Error:', error);
        Swal.fire('Error', 'Terjadi kesalahan pada sistem.', 'error');
    });
}

function showDetailMhs(nama, nim, prodi, jk, email, hp, dob, file_foto, file_ijazah, file_sk, nama_profesi) {
    document.getElementById('detailMhsNama').innerText = nama;
    document.getElementById('detailMhsNim').innerText = nim;
    document.getElementById('detailMhsProdi').innerText = prodi;
    document.getElementById('detailMhsProfesi').innerText = nama_profesi;
    document.getElementById('detailMhsJk').innerText = (jk === 'L') ? 'Laki-laki' : (jk === 'P' ? 'Perempuan' : '-');
    document.getElementById('detailMhsDob').innerText = dob;
    document.getElementById('detailMhsEmail').innerText = email;
    document.getElementById('detailMhsHp').innerText = hp;

    const baseUpload = '<?= base_url('uploads/dokumen_mahasiswa/') ?>';
    
    const fotoEl = document.getElementById('detailMhsFoto');
    if (file_foto) {
        fotoEl.src = baseUpload + file_foto;
    } else {
        fotoEl.src = 'https://ui-avatars.com/api/?name=' + encodeURIComponent(nama) + '&background=random';
    }

    const ijazahEl = document.getElementById('detailMhsIjazah');
    if (file_ijazah) {
        ijazahEl.href = baseUpload + file_ijazah;
        ijazahEl.classList.remove('disabled');
        ijazahEl.innerHTML = '<i class="fas fa-file-pdf me-1"></i> Lihat Dokumen';
    } else {
        ijazahEl.href = '#';
        ijazahEl.classList.add('disabled');
        ijazahEl.innerHTML = '<i class="fas fa-times me-1"></i> Belum ada file';
    }

    const skEl = document.getElementById('detailMhsSk');
    if (file_sk) {
        skEl.href = baseUpload + file_sk;
        skEl.classList.remove('disabled');
        skEl.innerHTML = '<i class="fas fa-file-pdf me-1"></i> Lihat Dokumen';
    } else {
        skEl.href = '#';
        skEl.classList.add('disabled');
        skEl.innerHTML = '<i class="fas fa-times me-1"></i> Belum ada file';
    }

    const myModal = new bootstrap.Modal(document.getElementById('modalDetailMhs'));
    myModal.show();
}

function showEditMhs(id, nama, nim, prodi, jk, email, hp, dob, semester, id_profesi) {
    document.getElementById('editMhsId').value = id;
    document.getElementById('editMhsNama').value = nama;
    document.getElementById('editMhsNim').value = nim;
    document.getElementById('editMhsProdi').value = prodi;
    document.getElementById('editMhsJk').value = jk;
    document.getElementById('editMhsEmail').value = email;
    document.getElementById('editMhsHp').value = hp;
    document.getElementById('editMhsDob').value = dob;
    document.getElementById('editMhsSemester').value = semester;
    document.getElementById('editMhsProfesi').value = id_profesi;
    
    const myModal = new bootstrap.Modal(document.getElementById('modalEditMhs'));
    myModal.show();
}

function submitEditMhs(event) {
    event.preventDefault();
    const form = document.getElementById('formEditMhs');
    const formData = new FormData(form);
    const id = document.getElementById('editMhsId').value;
    
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalBtnHtml = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Menyimpan...';
    submitBtn.disabled = true;

    fetch('<?= base_url('pendidikan/institusi/mahasiswa/update_mahasiswa/') ?>' + id, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        submitBtn.innerHTML = originalBtnHtml;
        submitBtn.disabled = false;
        if (data.success) {
            const modalEl = document.getElementById('modalEditMhs');
            const modalInstance = bootstrap.Modal.getInstance(modalEl);
            modalInstance.hide();
            
            alert('Berhasil: ' + data.message);
            window.location.reload();
        } else {
            alert('Error: ' + (data.message || 'Terjadi kesalahan.'));
        }
    })
    .catch(error => {
        submitBtn.innerHTML = originalBtnHtml;
        submitBtn.disabled = false;
        console.error('Error:', error);
        alert('Error: Terjadi kesalahan sistem.\n' + error);
    });
}
</script>

<?= $this->include('Pendidikan/Institusi/layout/footer') ?>
