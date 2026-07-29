<?= $this->include('Pendidikan/Institusi/layout/header') ?>
<?= $this->include('Pendidikan/Institusi/layout/sidebar') ?>

<div class="row">
    <div class="col-12 mb-4">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h4 class="fw-bold">Status Pengajuan Mahasiswa</h4>
                <p class="text-muted small mb-0">Daftar riwayat pengajuan mahasiswa dari institusi Anda.</p>
            </div>
            <a href="<?= base_url('pendidikan/institusi/pengajuan/create') ?>" class="btn btn-danger">
                <i class="fas fa-plus me-1"></i> Ajukan Baru
            </a>
        </div>
    </div>
</div>



<ul class="nav nav-tabs mb-4" id="statusTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active fw-bold text-primary" id="aktif-tab" data-bs-toggle="tab" data-bs-target="#aktif" type="button" role="tab" aria-controls="aktif" aria-selected="true">
            <i class="fas fa-clock me-1"></i> Menunggu & Revisi
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link fw-bold text-success" id="selesai-tab" data-bs-toggle="tab" data-bs-target="#selesai" type="button" role="tab" aria-controls="selesai" aria-selected="false">
            <i class="fas fa-check-circle me-1"></i> Disetujui & Riwayat
        </button>
    </li>
</ul>

<div class="tab-content" id="statusTabsContent">
    <!-- Tab Aktif -->
    <div class="tab-pane fade show active" id="aktif" role="tabpanel" aria-labelledby="aktif-tab">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">No. Pengajuan</th>
                                <th>Periode</th>
                                <th class="text-center">Jml Mahasiswa</th>
                                <th>Tgl Pengajuan</th>
                                <th>Status Pengajuan</th>
                                <th class="text-center pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($pengajuan_aktif)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted"><i class="fas fa-info-circle me-1"></i> Belum ada pengajuan aktif.</td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($pengajuan_aktif as $row) : ?>
                                <tr id="row-pengajuan-<?= $row['id'] ?>">
                                    <td class="ps-4 fw-bold text-dark no-pengajuan">REQ-<?= str_pad($row['id'], 4, '0', STR_PAD_LEFT) ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light rounded p-2 me-2">
                                                <i class="fas fa-calendar-alt text-primary"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold" style="font-size: 0.85rem;"><?= date('d M Y', strtotime($row['tanggal_mulai'])) ?> - <?= date('d M Y', strtotime($row['tanggal_selesai'])) ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center jumlah-mhs"><?= $row['jumlah_peserta'] ?> Orang</td>
                                    <td><?= date('d M Y', strtotime($row['created_at'] ?? date('Y-m-d'))) ?></td>
                                    <td>
                                        <?php 
                                            $badge_class = 'badge-menunggu';
                                            if ($row['status'] == 'Revisi') $badge_class = 'badge-revisi';
                                        ?>
                                        <span class="badge <?= $badge_class ?> px-3 py-2 animate__animated animate__fadeIn" style="font-size: 0.75rem; min-width: 80px;">
                                            <?= $row['status'] ?>
                                        </span>
                                    </td>
                                    <td class="text-center pe-4 action-cell">
                                        <div class="d-flex align-items-center justify-content-center gap-1">
                                            <a href="<?= base_url('pendidikan/institusi/pengajuan/detail/' . $row['id']) ?>" class="btn btn-sm btn-outline-dark">
                                                <i class="fas fa-eye me-1"></i> Detail
                                            </a>
                                            <a href="<?= base_url('pendidikan/institusi/pengajuan/edit/' . $row['id']) ?>" class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-edit me-1"></i> Edit
                                            </a>
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

    <!-- Tab Selesai -->
    <div class="tab-pane fade" id="selesai" role="tabpanel" aria-labelledby="selesai-tab">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">No. Pengajuan</th>
                                <th>Periode</th>
                                <th class="text-center">Jml Mahasiswa</th>
                                <th>Tgl Pengajuan</th>
                                <th>Status Pengajuan</th>
                                <th class="text-center pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($pengajuan_selesai)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted"><i class="fas fa-info-circle me-1"></i> Belum ada riwayat pengajuan disetujui.</td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($pengajuan_selesai as $row) : ?>
                                <tr id="row-pengajuan-<?= $row['id'] ?>">
                                    <td class="ps-4 fw-bold text-dark no-pengajuan">REQ-<?= str_pad($row['id'], 4, '0', STR_PAD_LEFT) ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light rounded p-2 me-2">
                                                <i class="fas fa-calendar-alt text-success"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold" style="font-size: 0.85rem;"><?= date('d M Y', strtotime($row['tanggal_mulai'])) ?> - <?= date('d M Y', strtotime($row['tanggal_selesai'])) ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center jumlah-mhs"><?= $row['jumlah_peserta'] ?> Orang</td>
                                    <td><?= date('d M Y', strtotime($row['created_at'] ?? date('Y-m-d'))) ?></td>
                                    <td>
                                        <?php 
                                            $badge_class = 'badge-disetujui';
                                            if ($row['status'] == 'Ditolak') $badge_class = 'badge-ditolak';
                                        ?>
                                        <span class="badge <?= $badge_class ?> px-3 py-2 animate__animated animate__fadeIn" style="font-size: 0.75rem; min-width: 80px;">
                                            <?= $row['status'] ?>
                                        </span>
                                    </td>
                                    <td class="text-center pe-4 action-cell">
                                        <div class="d-flex align-items-center justify-content-center gap-1">
                                            <a href="<?= base_url('pendidikan/institusi/pengajuan/detail/' . $row['id']) ?>" class="btn btn-sm btn-outline-dark">
                                                <i class="fas fa-eye me-1"></i> Detail
                                            </a>
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

<script>
</script>

<?= $this->include('Pendidikan/Institusi/layout/footer') ?>
