<?= $this->extend('Pelatihan/layout/admin_layout') ?>

<?= $this->section('content') ?>

<?php
    $cards = [
        [
            'label' => 'Total Peserta',
            'value' => number_format($total_peserta ?? 0),
            'note' => number_format($peserta_bulan_ini ?? 0) . ' baru bulan ini',
            'url' => base_url('pelatihan/admin/akun_peserta'),
            'accent' => '#ce2127'
        ],
        [
            'label' => 'Pelatihan Aktif',
            'value' => number_format($total_pelatihan ?? 0),
            'note' => number_format($pelatihan_selesai_hari_ini ?? 0) . ' selesai hari ini',
            'url' => base_url('pelatihan/admin/pelatihan'),
            'accent' => '#0f172a'
        ],
        [
            'label' => 'Sertifikat Masuk',
            'value' => number_format($sertifikat_masuk ?? 0),
            'note' => number_format($sertifikat_pengabdian_masuk ?? 0) . ' pengabdian pending',
            'url' => base_url('pelatihan/admin/sertifikat'),
            'accent' => '#b45309'
        ],
        [
            'label' => 'Antrean Bayar',
            'value' => number_format($pembayaran_pending ?? 0),
            'note' => 'Butuh validasi admin',
            'url' => base_url('pelatihan/admin/verifikasi_pendaftaran'),
            'accent' => '#2563eb'
        ],
    ];
?>

<div class="dashboard-shell">
    <div class="row g-3 mb-4">
        <?php foreach ($cards as $card): ?>
            <div class="col-sm-6 col-xl-3">
                <a href="<?= $card['url'] ?>" class="metric-card text-decoration-none" style="--accent: <?= $card['accent'] ?>;">
                    <span class="metric-label"><?= esc($card['label']) ?></span>
                    <span class="metric-value"><?= esc($card['value']) ?></span>
                    <span class="metric-note"><?= esc($card['note']) ?></span>
                </a>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="row g-4">
        <div class="col-xl-8">
            <div class="panel mb-4">
                <div class="panel-head">
                    <div>
                        <h5>Pelatihan Aktif</h5>
                        <p>Progress pendaftaran, jadwal mulai, dan kelulusan peserta.</p>
                    </div>
                    <a href="<?= base_url('pelatihan/admin/pelatihan') ?>" class="btn btn-dark btn-sm rounded-pill px-3">Kelola</a>
                </div>

                <?php if (empty($pelatihan)): ?>
                    <div class="empty-state">Belum ada pelatihan aktif.</div>
                <?php else: ?>
                    <div class="training-grid">
                        <?php foreach ($pelatihan as $p): ?>
                            <?php
                                $hariMulai = $p['hari_mulai'];
                                $hariReg = $p['hari_reg_tutup'];
                                $statusJadwal = 'Berjalan';
                                if ($hariMulai === 0) {
                                    $statusJadwal = 'Dimulai hari ini';
                                } elseif ($hariMulai !== null && $hariMulai > 0) {
                                    $statusJadwal = $hariMulai . ' hari lagi mulai';
                                }
                                $regText = $hariReg === null ? 'Registrasi belum dijadwalkan' : ($hariReg < 0 ? 'Registrasi selesai' : ($hariReg === 0 ? 'Registrasi tutup hari ini' : $hariReg . ' hari lagi registrasi berakhir'));
                            ?>
                            <a href="<?= base_url('pelatihan/admin/pelatihan/kelola/' . $p['id']) ?>" class="training-card text-decoration-none">
                                <div class="d-flex justify-content-between gap-3 align-items-start mb-3">
                                    <div class="min-w-0">
                                        <div class="training-title"><?= esc($p['nama']) ?></div>
                                        <div class="training-meta"><?= tanggal_indo($p['jadwal_mulai'] ?? 'now') ?>, <?= esc(substr($p['jam_mulai'] ?? '-', 0, 5)) ?> WIB</div>
                                    </div>
                                    <span class="status-pill"><?= esc($statusJadwal) ?></span>
                                </div>
                                <div class="training-meta mb-3"><?= esc($regText) ?></div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="progress-label">
                                            <span>Daftar</span>
                                            <strong><?= (int)($p['total_daftar'] ?? 0) ?>/<?= (int)($p['kuota'] ?? 0) ?> (<?= (int)($p['persen_daftar'] ?? 0) ?>%)</strong>
                                        </div>
                                        <div class="progress thin"><div class="progress-bar bg-danger" style="width: <?= (int)($p['persen_daftar'] ?? 0) ?>%"></div></div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="progress-label">
                                            <span>Kelulusan</span>
                                            <strong><?= (int)($p['persen_lulus'] ?? 0) ?>%</strong>
                                        </div>
                                        <div class="progress thin"><div class="progress-bar bg-dark" style="width: <?= (int)($p['persen_lulus'] ?? 0) ?>%"></div></div>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="panel">
                <div class="panel-head">
                    <div>
                        <h5>Pendaftaran Terbaru</h5>
                        <p>Registrasi dan verifikasi terbaru dari peserta.</p>
                    </div>
                    <a href="<?= base_url('pelatihan/admin/verifikasi_pendaftaran') ?>" class="btn btn-outline-dark btn-sm rounded-pill px-3">Lihat Semua</a>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Peserta</th>
                                <th>Pelatihan</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recent_pendaftaran)): ?>
                                <tr><td colspan="5" class="text-center text-muted py-4">Belum ada pendaftaran baru.</td></tr>
                            <?php else: foreach ($recent_pendaftaran as $rp): ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark"><?= esc($rp['nama']) ?></div>
                                        <div class="small text-muted"><?= esc($rp['instansi'] ?? 'RSUD KOTA JOGJA') ?></div>
                                    </td>
                                    <td><span class="text-wrap fw-semibold small"><?= esc($rp['pelatihan_nama']) ?></span></td>
                                    <td class="small"><?= tanggal_indo($rp['tanggal']) ?></td>
                                    <td>
                                        <span class="badge rounded-pill text-bg-light border"><?= esc($rp['status_peserta']) ?></span>
                                        <span class="badge rounded-pill text-bg-light border"><?= esc($rp['status_pembayaran']) ?></span>
                                    </td>
                                    <td class="text-end">
                                        <a href="<?= base_url('pelatihan/admin/verifikasi_pendaftaran') ?>" class="btn btn-sm btn-danger rounded-pill px-3">Verifikasi</a>
                                    </td>
                                </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="panel mb-4">
                <div class="panel-head">
                    <div>
                        <h5>Notifikasi</h5>
                        <p>Registrasi, verifikasi, sertifikat, dan jadwal dekat.</p>
                    </div>
                </div>
                <div class="notice-list">
                    <?php if (empty($notifications)): ?>
                        <div class="empty-state">Tidak ada notifikasi penting saat ini.</div>
                    <?php else: foreach ($notifications as $n): ?>
                        <a href="<?= $n['url'] ?>" class="notice-item text-decoration-none border-<?= esc($n['type']) ?>">
                            <strong><?= esc($n['title']) ?></strong>
                            <span><?= esc($n['message']) ?></span>
                        </a>
                    <?php endforeach; endif; ?>
                </div>
            </div>

            <div class="panel mb-4">
                <div class="panel-head">
                    <div>
                        <h5>Sertifikat Terbaru</h5>
                        <p>Unggahan dan penerbitan terakhir.</p>
                    </div>
                    <a href="<?= base_url('pelatihan/admin/sertifikat') ?>" class="btn btn-outline-dark btn-sm rounded-pill px-3">Rekap</a>
                </div>
                <div class="compact-list">
                    <?php if (empty($recent_sertifikat)): ?>
                        <div class="empty-state">Belum ada sertifikat terbaru.</div>
                    <?php else: foreach ($recent_sertifikat as $s): ?>
                        <a href="<?= base_url('pelatihan/admin/sertifikat') ?>" class="compact-item text-decoration-none">
                            <span class="fw-bold text-dark"><?= esc($s['nama_peserta'] ?? $s['user_nama'] ?? 'Peserta') ?></span>
                            <small><?= esc($s['judul'] ?? $s['pelatihan_nama'] ?? 'Sertifikat') ?></small>
                            <em><?= esc($s['verifikasi'] ?? '-') ?></em>
                        </a>
                    <?php endforeach; endif; ?>
                </div>
            </div>

            <div class="panel">
                <div class="panel-head">
                    <div>
                        <h5>Distribusi Unit Kerja</h5>
                        <p>Komposisi peserta terdaftar.</p>
                    </div>
                </div>
                <?php if (empty($distribusi_unit_kerja)): ?>
                    <div class="empty-state">Belum ada data distribusi.</div>
                <?php else: foreach ($distribusi_unit_kerja as $i): ?>
                    <div class="progress-label mt-3">
                        <span><?= esc($i['nama'] ?? 'Lainnya') ?></span>
                        <strong><?= (int)$i['total'] ?> peserta (<?= (int)$i['persen'] ?>%)</strong>
                    </div>
                    <div class="progress thin"><div class="progress-bar bg-danger" style="width: <?= (int)$i['persen'] ?>%"></div></div>
                <?php endforeach; endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
    .dashboard-shell { width: 100%; }
    .metric-card, .panel, .training-card, .notice-item, .compact-item {
        display: block;
        background: #fff;
        border: 1px solid #e5e7eb;
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.06);
    }
    .metric-card {
        min-height: 150px;
        border-radius: 14px;
        padding: 22px;
        border-left: 6px solid var(--accent);
        color: #0f172a;
        transition: transform .2s ease, box-shadow .2s ease;
    }
    .metric-card:hover, .training-card:hover, .notice-item:hover, .compact-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 16px 32px rgba(15, 23, 42, 0.1);
    }
    .metric-label, .metric-note { display: block; font-size: .78rem; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: .04em; }
    .metric-value { display: block; font-size: 2.3rem; line-height: 1.1; font-weight: 900; color: #0f172a; margin: 12px 0 8px; }
    .metric-note { text-transform: none; letter-spacing: 0; color: var(--accent); }
    .panel { border-radius: 14px; padding: 22px; overflow: hidden; }
    .panel-head { display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; margin-bottom: 18px; }
    .panel-head h5 { margin: 0; color: #0f172a; font-weight: 900; }
    .panel-head p { margin: 4px 0 0; color: #64748b; font-size: .82rem; }
    .training-grid, .notice-list, .compact-list { display: grid; gap: 14px; }
    .training-card { border-radius: 12px; padding: 18px; color: #0f172a; transition: transform .2s ease, box-shadow .2s ease; }
    .training-title { font-weight: 900; color: #0f172a; line-height: 1.35; }
    .training-meta { color: #64748b; font-size: .8rem; font-weight: 700; }
    .status-pill { flex-shrink: 0; border-radius: 999px; padding: 7px 11px; background: #fff1f2; color: #ce2127; font-size: .68rem; font-weight: 900; white-space: nowrap; }
    .progress-label { display: flex; justify-content: space-between; gap: 12px; color: #64748b; font-size: .76rem; font-weight: 800; margin-bottom: 7px; }
    .progress-label strong { color: #0f172a; }
    .progress.thin { height: 8px; border-radius: 999px; background: #eef2f7; }
    .progress.thin .progress-bar { border-radius: inherit; }
    .notice-item { border-radius: 12px; border-left-width: 5px !important; padding: 14px 16px; color: #0f172a; }
    .notice-item strong, .notice-item span { display: block; }
    .notice-item strong { font-size: .86rem; margin-bottom: 4px; }
    .notice-item span, .compact-item small { color: #64748b; font-size: .78rem; }
    .compact-item { border-radius: 12px; padding: 13px 15px; color: #0f172a; }
    .compact-item span, .compact-item small, .compact-item em { display: block; }
    .compact-item em { margin-top: 6px; color: #ce2127; font-size: .72rem; font-style: normal; font-weight: 900; text-transform: uppercase; }
    .empty-state { border: 1px dashed #cbd5e1; border-radius: 12px; padding: 18px; text-align: center; color: #64748b; font-weight: 700; background: #f8fafc; }
    .min-w-0 { min-width: 0; }
    .table th { color: #64748b; font-size: .72rem; text-transform: uppercase; letter-spacing: .04em; white-space: nowrap; }
    @media (max-width: 768px) {
        .panel-head { flex-direction: column; align-items: stretch; }
        .metric-card { min-height: 130px; }
        .status-pill { white-space: normal; text-align: center; }
    }
</style>

<?= $this->endSection() ?>
