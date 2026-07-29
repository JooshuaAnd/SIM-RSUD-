<?= $this->include('Pendidikan/Institusi/layout/header') ?>
<?= $this->include('Pendidikan/Institusi/layout/sidebar') ?>

<div class="row">
    <div class="col-12 mb-4">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h4 class="fw-bold">Alumni & Mahasiswa Lulus</h4>
                <p class="text-muted small mb-0">Daftar mahasiswa yang telah menyelesaikan masa praktek dan mendapatkan nilai akhir.</p>
            </div>
            <button class="btn btn-outline-danger btn-sm">
                <i class="fas fa-file-excel me-1"></i> Export Data Alumni
            </button>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold text-success"><i class="fas fa-graduation-cap me-2"></i> Mahasiswa Telah Lulus</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 text-nowrap">
                        <thead class="table-light">
                            <tr class="small text-uppercase fw-bold">
                                <th class="ps-4">Nama Mahasiswa</th>
                                <th>NIM / ID</th>
                                <th>Prodi</th>
                                <th>Periode</th>
                                <th class="text-center">Nilai Akhir</th>
                                <th class="text-center">Status</th>
                                <th class="text-center pe-4">Sertifikat</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($list_mahasiswa)) : ?>
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="fas fa-graduation-cap fa-3x mb-3 d-block"></i>
                                        Belum ada mahasiswa yang lulus.
                                    </td>
                                </tr>
                            <?php else : ?>
                            <?php foreach ($list_mahasiswa as $row) : ?>
                                <tr>
                                    <td class="ps-4 fw-bold text-dark"><?= $row['nama'] ?></td>
                                    <td><span class="badge bg-light text-secondary"><?= $row['nim'] ?></span></td>
                                    <td><?= $row['prodi'] ?></td>
                                    <td class="small text-muted"><?= $row['periode'] ?></td>
                                    <td class="text-center">
                                        <span class="fw-bold text-success fs-5"><?= $row['nilai_akhir'] ?></span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success px-2 py-1" style="font-size: 0.7rem;">
                                            <i class="fas fa-check-circle me-1"></i> <?= $row['status'] ?>
                                        </span>
                                    </td>
                                    <td class="text-center pe-4">
                                        <a href="<?= base_url('pendidikan/institusi/mahasiswa/sertifikat/' . $row['id']) ?>" class="btn btn-sm btn-primary" target="_blank">
                                            <i class="fas fa-certificate me-1"></i> Download
                                        </a>
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

<?= $this->include('Pendidikan/Institusi/layout/footer') ?>
