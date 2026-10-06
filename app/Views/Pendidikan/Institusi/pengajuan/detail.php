<?= $this->include('Pendidikan/Institusi/layout/header') ?>
<?= $this->include('Pendidikan/Institusi/layout/sidebar') ?>

<div class="row">
    <div class="col-12 mb-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <h4 class="fw-bold mb-0">Detail Pengajuan</h4>
            <a href="<?= base_url('pendidikan/institusi/pengajuan/status') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>
</div>

<div class="row">
    <!-- Informasi Utama -->
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold">Informasi Institusi & Pengajuan</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12">
                        <div class="bg-light rounded p-3">
                            <div class="text-muted small mb-1">Nama Institusi</div>
                            <div class="fw-bold text-dark mb-2 text-break"><?= $pengajuan['institusi'] ?></div>
                            <div class="text-muted small mb-1">Program Studi</div>
                            <div class="fw-semibold text-dark text-break"><?= $pengajuan['prodi'] ?></div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-4">
                        <div class="border rounded p-3 h-100">
                            <div class="text-muted small mb-1">Periode Koas</div>
                            <div class="fw-semibold text-dark"><?= $pengajuan['periode'] ?></div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-4">
                        <div class="border rounded p-3 h-100">
                            <div class="text-muted small mb-1">Tanggal Mulai</div>
                            <div class="fw-semibold text-dark"><?= date('d M Y', strtotime($pengajuan['tgl_mulai'])) ?></div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-4">
                        <div class="border rounded p-3 h-100">
                            <div class="text-muted small mb-1">Tanggal Selesai</div>
                            <div class="fw-semibold text-dark"><?= date('d M Y', strtotime($pengajuan['tgl_selesai'])) ?></div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6">
                        <div class="border rounded p-3 h-100">
                            <div class="text-muted small mb-1">Penanggung Jawab</div>
                            <div class="fw-semibold text-dark text-break"><?= $pengajuan['penanggung_jawab'] ?></div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6">
                        <div class="border rounded p-3 h-100">
                            <div class="text-muted small mb-1">Kontak PJ</div>
                            <div class="fw-semibold text-dark text-break"><?= $pengajuan['hp_pj'] ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold">Daftar Mahasiswa (<?= count($pengajuan['mahasiswa']) ?> Orang)</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-3">
                    <?php foreach ($pengajuan['mahasiswa'] as $mhs) : ?>
                        <div class="border rounded p-3">
                            <div class="d-flex align-items-center gap-3 pb-3 mb-3 border-bottom">
                                <?php if ($mhs['file_foto']): ?>
                                    <img src="<?= base_url('uploads/dokumen_mahasiswa/' . $mhs['file_foto']) ?>" class="rounded-circle flex-shrink-0" style="width: 48px; height: 48px; object-fit: cover;" alt="Foto">
                                <?php else: ?>
                                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center text-secondary flex-shrink-0" style="width: 48px; height: 48px;">
                                        <i class="fas fa-user"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="min-w-0">
                                    <div class="fw-bold text-dark text-break"><?= $mhs['nama'] ?></div>
                                    <div class="text-muted small text-break">NIM: <?= $mhs['nim'] ?></div>
                                </div>
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-12 col-sm-4">
                                    <div class="text-muted small mb-1">Tanggal Lahir</div>
                                    <div class="fw-semibold"><?= isset($mhs['dob']) ? date('d-m-Y', strtotime($mhs['dob'])) : '-' ?></div>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <div class="text-muted small mb-1">Jenis Kelamin</div>
                                    <div class="fw-semibold"><?= $mhs['jk'] ?></div>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <div class="text-muted small mb-1">Semester</div>
                                    <div class="fw-semibold"><?= $mhs['semester'] ?></div>
                                </div>
                            </div>
                            <div class="d-flex flex-wrap align-items-center gap-2">
                                <span class="text-muted small me-1">Dokumen:</span>
                                <?php if ($mhs['file_ijazah']): ?>
                                    <a href="<?= base_url('uploads/dokumen_mahasiswa/' . $mhs['file_ijazah']) ?>" target="_blank" class="btn btn-sm btn-outline-danger"><i class="fas fa-file-pdf me-1"></i> Ijazah Terakhir</a>
                                <?php else: ?>
                                    <span class="text-muted small">Ijazah belum tersedia</span>
                                <?php endif; ?>
                                <?php if ($mhs['file_sk']): ?>
                                    <a href="<?= base_url('uploads/dokumen_mahasiswa/' . $mhs['file_sk']) ?>" target="_blank" class="btn btn-sm btn-outline-danger"><i class="fas fa-file-pdf me-1"></i> Surat Ket. Mahasiswa</a>
                                <?php else: ?>
                                    <span class="text-muted small">Surat keterangan belum tersedia</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar Status -->
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold">Status Pengajuan</h6>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <?php 
                        $badge_class = 'badge-menunggu';
                        if ($pengajuan['status'] == 'Disetujui') $badge_class = 'badge-disetujui';
                        if ($pengajuan['status'] == 'Ditolak') $badge_class = 'badge-ditolak';
                        if ($pengajuan['status'] == 'Revisi') $badge_class = 'badge-revisi';
                    ?>
                    <h3 class="badge <?= $badge_class ?> px-4 py-3 w-100" style="font-size: 1.2rem;">
                        <?= $pengajuan['status'] ?>
                    </h3>
                </div>
                
                <div class="p-3 bg-light rounded mb-3">
                    <p class="fw-bold small mb-2"><i class="fas fa-comment-dots me-2"></i> Catatan Diklat:</p>
                    <p class="small text-muted mb-0 italic">"<?= $pengajuan['catatan'] ?? 'Belum ada catatan.' ?>"</p>
                </div>

                <div class="d-grid gap-2">
                    <?php if ($pengajuan['status'] == 'Revisi') : ?>
                        <a href="<?= base_url('pendidikan/institusi/pengajuan/edit/' . $pengajuan['id']) ?>" class="btn btn-danger fw-bold">
                            <i class="fas fa-edit me-1"></i> Edit Pengajuan
                        </a>
                    <?php endif; ?>
                    <a href="<?= base_url('pendidikan/institusi/pengajuan/status') ?>" class="btn btn-outline-secondary">Kembali ke Daftar</a>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold">Dokumen Terlampir</h6>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <div class="small"><i class="fas fa-file-pdf text-danger me-2"></i> 1. Proposal</div>
                        <a href="<?= $pengajuan['file_proposal'] ? base_url('uploads/dokumen_pengajuan/' . $pengajuan['file_proposal']) : '#' ?>" class="btn btn-sm btn-light" <?= $pengajuan['file_proposal'] ? 'target="_blank"' : 'disabled' ?>><i class="fas fa-eye"></i></a>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <div class="small"><i class="fas fa-file-pdf text-danger me-2"></i> 2. Surat Pengantar</div>
                        <a href="<?= $pengajuan['file_surat_pengantar'] ? base_url('uploads/dokumen_pengajuan/' . $pengajuan['file_surat_pengantar']) : '#' ?>" class="btn btn-sm btn-light" <?= $pengajuan['file_surat_pengantar'] ? 'target="_blank"' : 'disabled' ?>><i class="fas fa-eye"></i></a>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <div class="small"><i class="fas fa-file-pdf text-danger me-2"></i> 3. Log Book</div>
                        <a href="<?= $pengajuan['file_logbook'] ? base_url('uploads/dokumen_pengajuan/' . $pengajuan['file_logbook']) : '#' ?>" class="btn btn-sm btn-light" <?= $pengajuan['file_logbook'] ? 'target="_blank"' : 'disabled' ?>><i class="fas fa-eye"></i></a>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <div class="small"><i class="fas fa-file-pdf text-danger me-2"></i> 4. Buku Panduan</div>
                        <a href="<?= $pengajuan['file_panduan'] ? base_url('uploads/dokumen_pengajuan/' . $pengajuan['file_panduan']) : '#' ?>" class="btn btn-sm btn-light" <?= $pengajuan['file_panduan'] ? 'target="_blank"' : 'disabled' ?>><i class="fas fa-eye"></i></a>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <div class="small"><i class="fas fa-file-excel text-success me-2"></i> 5. Daftar Nama Mahasiswa</div>
                        <a href="<?= $pengajuan['file_daftar_mhs'] ? base_url('uploads/dokumen_pengajuan/' . $pengajuan['file_daftar_mhs']) : '#' ?>" class="btn btn-sm btn-light" <?= $pengajuan['file_daftar_mhs'] ? 'target="_blank"' : 'disabled' ?>><i class="fas fa-eye"></i></a>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <div class="small"><i class="fas fa-file-pdf text-danger me-2"></i> 6. Surat Level Kompetensi</div>
                        <a href="<?= $pengajuan['file_kompetensi'] ? base_url('uploads/dokumen_pengajuan/' . $pengajuan['file_kompetensi']) : '#' ?>" class="btn btn-sm btn-light" <?= $pengajuan['file_kompetensi'] ? 'target="_blank"' : 'disabled' ?>><i class="fas fa-eye"></i></a>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <div class="small"><i class="fas fa-file-pdf text-secondary me-2"></i> 7. SK Pembimbing</div>
                        <a href="<?= $pengajuan['file_sk_pembimbing'] ? base_url('uploads/dokumen_pengajuan/' . $pengajuan['file_sk_pembimbing']) : '#' ?>" class="btn btn-sm btn-light" <?= $pengajuan['file_sk_pembimbing'] ? 'target="_blank"' : 'disabled' ?>><i class="fas fa-eye"></i></a>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <div class="small"><i class="fas fa-file-invoice-dollar text-success me-2"></i> 8. Bukti Pembayaran</div>
                        <a href="<?= $pengajuan['file_bukti_bayar'] ? base_url('uploads/dokumen_pengajuan/' . $pengajuan['file_bukti_bayar']) : '#' ?>" class="btn btn-sm btn-light" <?= $pengajuan['file_bukti_bayar'] ? 'target="_blank"' : 'disabled' ?>><i class="fas fa-eye"></i></a>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <div class="small"><i class="fas fa-file-pdf text-danger me-2"></i> 9. Dokumen Penilaian</div>
                        <?php if (!empty($pengajuan['file_dokumen_penilaian'])): ?>
                            <a href="<?= esc(base_url('uploads/dokumen_pengajuan/' . $pengajuan['file_dokumen_penilaian']), 'attr') ?>" target="_blank" rel="noopener" class="btn btn-sm btn-light" aria-label="Lihat dokumen penilaian"><i class="fas fa-eye"></i></a>
                        <?php else: ?>
                            <span class="text-muted small">Belum ada file</span>
                        <?php endif; ?>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?= $this->include('Pendidikan/Institusi/layout/footer') ?>
