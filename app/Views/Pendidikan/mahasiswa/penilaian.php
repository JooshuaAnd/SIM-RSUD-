<?= $this->include('Pendidikan/mahasiswa/layout/header') ?>
<?= $this->include('Pendidikan/mahasiswa/layout/sidebar') ?>

<div class="row">
    <div class="col-12 mb-4">
        <h4 class="fw-bold">Daftar Nilai Mahasiswa</h4>
        <p class="text-muted small mb-0">Berikut adalah daftar nilai dan evaluasi dari setiap stase yang telah dan sedang Anda jalani.</p>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-4 p-3 bg-light rounded-3">
                    <div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center fw-bold me-3" style="width: 50px; height: 50px; font-size: 1.2rem;">
                        <span><?= isset($mahasiswa['nama']) ? substr($mahasiswa['nama'], 0, 1) : 'B' ?></span>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold text-dark"><?= $mahasiswa['nama'] ?? 'Budi Sudarsono' ?></h6>
                        <span class="text-muted small">NIM: <?= $mahasiswa['nim'] ?? '123458' ?></span>
                    </div>
                    <div class="ms-auto">
                        <a href="<?= base_url('pendidikan/mahasiswa/penilaian/download') ?>" class="btn btn-danger btn-sm fw-bold">
                            <i class="fas fa-download me-1"></i> Download Semua Nilai (PDF)
                        </a>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr class="small text-uppercase">
                                <th>Stase & Ruangan</th>
                                <th>Pembimbing (CI)</th>
                                <th class="text-center">File Nilai (CI)</th>
                                <th class="text-center">Tugas</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($staseList)): ?>
                                <?php foreach ($staseList as $stase): ?>
                                    <?php 
                                        $now = time();
                                        $end = strtotime($stase['tanggal_akhir']);
                                        $isSelesai = $now > $end;
                                    ?>
                                    <tr>
                                        <td>
                                            <span class="fw-bold d-block"><?= esc($stase['nama_stase']) ?></span>
                                            <span class="text-muted small"><?= esc($stase['nama_ruangan'] ?: 'Ruangan Utama') ?></span>
                                        </td>
                                        <td class="fw-medium text-primary"><?= esc($stase['ci_name'] ?: 'Belum Ditugaskan') ?></td>
                                        <td class="text-center">
                                            <?php if ($stase['file_penilaian_ci']): ?>
                                                <a href="<?= base_url('uploads/penilaian/' . $stase['file_penilaian_ci']) ?>" target="_blank" class="btn btn-sm btn-outline-danger px-3 fw-bold">
                                                    <i class="fas fa-file-download me-1"></i> Download
                                                </a>
                                            <?php elseif ($isSelesai): ?>
                                                <a href="#" class="btn btn-sm btn-outline-danger px-3 fw-bold" onclick="alert('File penilaian CI belum diunggah.')">
                                                    <i class="fas fa-file-download me-1"></i> Download
                                                </a>
                                            <?php else: ?>
                                                <span class="badge bg-light text-muted border">Belum Tersedia</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="small">
                                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalTugas<?= $stase['id'] ?>">
                                                <i class="fas fa-list me-1"></i> Nilai Tugas
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">Belum ada data stase.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($staseList)): ?>
    <?php foreach ($staseList as $stase): ?>
    <div class="modal fade" id="modalTugas<?= $stase['id'] ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light">
                    <h6 class="modal-title fw-bold text-dark"><i class="fas fa-tasks text-primary me-2"></i> Nilai Tugas: <?= esc($stase['nama_stase']) ?></h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <ul class="list-group list-group-flush">
                        <?php if (!empty($stase['tasks'])): ?>
                            <?php foreach ($stase['tasks'] as $tugas): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                                    <div>
                                        <h6 class="mb-0 fw-bold"><?= esc($tugas['nama_tugas']) ?></h6>
                                        <span class="small text-muted">Status: <?= esc($tugas['status'] ?? 'Belum Dikerjakan') ?></span>
                                    </div>
                                    <div>
                                        <span class="badge <?= ($tugas['nilai'] !== null) ? 'bg-success' : 'bg-secondary' ?> rounded-pill fs-6 px-3 py-2">
                                            <?= $tugas['nilai'] !== null ? esc($tugas['nilai']) : '-' ?>
                                        </span>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li class="list-group-item p-4 text-center text-muted">
                                Belum ada tugas pada stase ini.
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
<?php endif; ?>

<?= $this->include('Pendidikan/mahasiswa/layout/footer') ?>
