<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Nilai Mahasiswa</title>
    <style>
        @page { margin: 36pt 36pt 48pt; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10pt; color: #333; }
        h1 { font-size: 18pt; color: #c62828; margin: 0 0 5pt; }
        h2 { font-size: 12pt; margin: 20pt 0 5pt; page-break-after: avoid; }
        p { margin: 4pt 0; }
        .subtitle, .muted { color: #666; }
        .identity { width: 100%; margin: 16pt 0; border-top: 2pt solid #c62828; padding-top: 8pt; }
        .identity td { padding: 3pt 0; vertical-align: top; }
        .label { width: 100pt; color: #666; }
        .summary { padding: 10pt; background: #fff0f0; border: 1pt solid #f3d5d5; }
        .grades { width: 100%; border-collapse: collapse; margin-top: 8pt; table-layout: fixed; font-size: 9pt; }
        .grades th, .grades td { border: 0.5pt solid #ddd; padding: 6pt; vertical-align: top; overflow-wrap: break-word; }
        .grades th { background: #f4f4f4; text-align: left; }
        .grades tr { page-break-inside: avoid; }
        .grades thead { display: table-header-group; }
        .score { text-align: center; }
        .note { font-size: 8pt; margin: 8pt 0; }
        .empty-stase { page-break-inside: avoid; }
    </style>
</head>
<body>
    <h1>Rekap Nilai Mahasiswa</h1>
    <p class="subtitle">Portal Akademik - Pendidikan RSUD</p>
    <table class="identity">
        <tr><td class="label">Nama mahasiswa</td><td>: <?= esc($mahasiswa['nama_lengkap']) ?></td></tr>
        <tr><td class="label">NIM</td><td>: <?= esc($mahasiswa['nim'] ?? '-') ?></td></tr>
        <tr><td class="label">Institusi</td><td>: <?= esc($mahasiswa['nama_institusi'] ?? '-') ?></td></tr>
        <tr><td class="label">Program studi</td><td>: <?= esc($mahasiswa['program_studi'] ?? '-') ?></td></tr>
        <tr><td class="label">Tanggal unduh</td><td>: <?= date('d-m-Y H:i') ?></td></tr>
    </table>
    <div class="summary">
        Nilai akhir dari institusi:
        <strong><?= isset($mahasiswa['nilai_akhir']) && $mahasiswa['nilai_akhir'] !== '' ? esc($mahasiswa['nilai_akhir']) : 'Belum dinilai' ?></strong>
    </div>
    <p class="note muted">Rekap ini menampilkan nilai yang tercatat saat diunduh. Nilai tugas diberikan oleh CI, sedangkan nilai akhir dicatat oleh institusi.</p>

    <?php if (empty($staseList)): ?>
        <p class="muted" style="margin-top: 20pt;">Belum ada data stase untuk mahasiswa ini.</p>
    <?php endif; ?>
    <?php foreach ($staseList as $index => $stase): ?>
        <div class="<?= empty($stase['tasks']) ? 'empty-stase' : '' ?>">
        <h2><?= $index + 1 ?>. <?= esc($stase['nama_stase']) ?></h2>
        <p>Ruangan: <?= esc($stase['nama_ruangan'] ?: 'Ruangan Utama') ?></p>
        <p class="muted">Pembimbing (CI): <?= esc($stase['ci_name'] ?: 'Belum Ditugaskan') ?></p>
        <table class="grades">
            <thead><tr><th style="width: 55%;">Tugas</th><th style="width: 25%;">Status</th><th style="width: 20%;">Nilai</th></tr></thead>
            <tbody>
                <?php if (empty($stase['tasks'])): ?>
                    <tr><td colspan="3" class="muted">Belum ada tugas pada stase ini.</td></tr>
                <?php endif; ?>
                <?php foreach ($stase['tasks'] as $tugas): ?>
                    <tr>
                        <td><?= esc($tugas['nama_tugas']) ?></td>
                        <td><?= esc($tugas['status'] ?? 'Belum Dikerjakan') ?></td>
                        <td class="score"><?= $tugas['nilai'] !== null ? esc($tugas['nilai']) : 'Belum dinilai' ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    <?php endforeach; ?>
</body>
</html>
