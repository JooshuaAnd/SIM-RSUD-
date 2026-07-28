<?php
/**
 * Shared partial: Form fields for pejabat_ttd_pelatihan (Narasumber / Pejabat TTD).
 *
 * Expected variables:
 *   $pejabat   - existing data row (for edit), or [] for new
 *   $prefix    - string prefix for input IDs (default: 'pj')
 *   $showStatus - bool, whether to show status dropdown (default: true)
 *   $showFoto  - bool, whether to show foto upload (default: true)
 *   $showTtd   - bool, whether to show ttd_image upload (default: true)
 *   $showRiwayat - bool, whether to show riwayat textarea (default: true)
 */
$pejabat = $pejabat ?? [];
$prefix  = $prefix ?? 'pj';
$showStatus = $showStatus ?? true;
$showFoto   = $showFoto ?? true;
$showTtd    = $showTtd ?? true;
$showRiwayat = $showRiwayat ?? true;

$v = function($key) use ($pejabat) { return esc($pejabat[$key] ?? ''); };
?>

<div class="mb-3">
    <label class="form-label small fw-bold">Status</label>
    <?php if ($showStatus): ?>
        <select name="status" id="<?= $prefix ?>_status" class="form-select rounded-pill border">
            <option value="Narasumber" <?= ($pejabat['status'] ?? '') === 'Narasumber' ? 'selected' : '' ?>>Narasumber</option>
            <option value="Pejabat" <?= ($pejabat['status'] ?? '') === 'Pejabat' ? 'selected' : '' ?>>Pejabat TTD</option>
        </select>
    <?php else: ?>
        <input type="hidden" name="status" value="Narasumber">
        <div class="form-control-plaintext small fw-bold text-success">Narasumber</div>
    <?php endif; ?>
</div>

<div class="mb-3">
    <label class="form-label small fw-bold">Nama Lengkap <span class="text-danger">*</span></label>
    <input type="text" name="nama_pejabat" id="<?= $prefix ?>_nama" class="form-control rounded-pill border" placeholder="Contoh: Dr. H. Ariyudi Yunantoro" required value="<?= $v('nama_pejabat') ?>">
</div>

<div class="row g-2 mb-3">
    <div class="col-md-6">
        <label class="form-label small fw-bold">Gelar Depan</label>
        <input type="text" name="gelar_depan" id="<?= $prefix ?>_gelar_depan" class="form-control rounded-pill border" placeholder="Dr." value="<?= $v('gelar_depan') ?>">
    </div>
    <div class="col-md-6">
        <label class="form-label small fw-bold">Gelar Belakang</label>
        <input type="text" name="gelar_belakang" id="<?= $prefix ?>_gelar_belakang" class="form-control rounded-pill border" placeholder="M.Kes" value="<?= $v('gelar_belakang') ?>">
    </div>
</div>

<div class="mb-3">
    <label class="form-label small fw-bold">Pendidikan</label>
    <input type="text" name="pendidikan" id="<?= $prefix ?>_pendidikan" class="form-control rounded-pill border" placeholder="S.Ked, Sp.PD" value="<?= $v('pendidikan') ?>">
</div>

<div class="mb-3">
    <label class="form-label small fw-bold">Keahlian</label>
    <input type="text" name="keahlian" id="<?= $prefix ?>_keahlian" class="form-control rounded-pill border" placeholder="Penyakit Dalam, Kardiologi" value="<?= $v('keahlian') ?>">
</div>

<div class="mb-3">
    <label class="form-label small fw-bold">Atas Nama Pejabat (a.n)</label>
    <input type="text" name="an_pejabat" id="<?= $prefix ?>_an" class="form-control rounded-pill border" placeholder="Contoh: a.n Direktur" value="<?= $v('an_pejabat') ?>">
</div>

<div class="mb-3">
    <label class="form-label small fw-bold">Jabatan / Kedudukan</label>
    <input type="text" name="jabatan" id="<?= $prefix ?>_jabatan" class="form-control rounded-pill border" placeholder="Contoh: Direktur RSUD" value="<?= $v('jabatan') ?>">
</div>

<div class="mb-3">
    <label class="form-label small fw-bold">NIP Pejabat</label>
    <input type="text" name="nip_pejabat" id="<?= $prefix ?>_nip" class="form-control rounded-pill border" placeholder="Contoh: 19690124XXXXXX" value="<?= $v('nip_pejabat') ?>">
</div>

<div class="row g-2 mb-3">
    <div class="col-md-6">
        <label class="form-label small fw-bold">Kontak / No. Telp</label>
        <input type="text" name="kontak" id="<?= $prefix ?>_kontak" class="form-control rounded-pill border" placeholder="08xxx" value="<?= $v('kontak') ?>">
    </div>
    <div class="col-md-6">
        <label class="form-label small fw-bold">Email</label>
        <input type="email" name="email" id="<?= $prefix ?>_email" class="form-control rounded-pill border" placeholder="email@contoh.com" value="<?= $v('email') ?>">
    </div>
</div>

<?php if ($showRiwayat): ?>
<div class="mb-3">
    <label class="form-label small fw-bold">Riwayat / Bio</label>
    <textarea name="riwayat" id="<?= $prefix ?>_riwayat" class="form-control border p-3" rows="3" placeholder="Riwayat singkat, pengalaman, publikasi, dll." style="border-radius: 15px;"><?= $v('riwayat') ?></textarea>
</div>
<?php endif; ?>

<?php if ($showFoto): ?>
<div class="mb-3">
    <label class="form-label small fw-bold">Foto Profil</label>
    <input type="file" name="foto" id="<?= $prefix ?>_foto" class="form-control rounded-pill border" accept="image/*">
    <?php if (!empty($pejabat['foto'])): ?>
        <div class="mt-1"><small class="text-muted">File saat ini: <?= basename($pejabat['foto']) ?></small></div>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php if ($showTtd): ?>
<div class="mb-3">
    <label class="form-label small fw-bold">Upload TTD (PNG Transparan)</label>
    <input type="file" name="ttd_image" id="<?= $prefix ?>_ttd" class="form-control rounded-pill border" accept="image/png">
    <?php if (!empty($pejabat['ttd_image'])): ?>
        <div class="mt-1"><small class="text-muted">File saat ini: <?= basename($pejabat['ttd_image']) ?></small></div>
    <?php endif; ?>
</div>
<?php endif; ?>
