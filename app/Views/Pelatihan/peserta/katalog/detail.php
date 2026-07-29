<?php
$p = $p ?? [];
$reg = $reg ?? [];
$reg_status = $reg_status ?? 'belum_daftar';
$is_reg_open = $is_reg_open ?? false;
$is_learning_open = $is_learning_open ?? false;
$is_learning_finished = $is_learning_finished ?? false;
$konten = $konten ?? [];
$reg_buka = $reg_buka ?? null;
$reg_tutup = $reg_tutup ?? null;
$jadwal_mulai = $jadwal_mulai ?? null;
$jadwal_selesai = $jadwal_selesai ?? null;
$has_progress = $has_progress ?? false;
?>

<?= $this->extend('pelatihan/layout/peserta_layout') ?>

<?= $this->section('content') ?>

<style>
    :root {
        --primary-red: #ce2127;
        --primary-dark: #111111;
        --primary-black: #000000;
        --soft-shadow: 0 10px 40px rgba(0, 0, 0, 0.05);
    }

    .detail-header {
        background: var(--primary-black);
        color: white;
        padding: 60px 0 80px;
        border-radius: 0 0 40px 40px;
        margin-bottom: 50px;
        position: relative;
        overflow: hidden;
    }

    .detail-header::after {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, rgba(206, 33, 39, 0.2) 0%, transparent 60%);
        pointer-events: none;
    }

    .info-card {
        background: white;
        border-radius: 24px;
        padding: 30px;
        box-shadow: var(--soft-shadow);
        border: 1px solid rgba(0,0,0,0.05);
        position: sticky;
        top: 20px;
    }

    .section-title {
        position: relative;
        padding-left: 18px;
        margin-bottom: 25px;
        font-weight: 800;
        color: var(--primary-dark);
        font-size: 1.25rem;
    }

    .section-title::before {
        content: '';
        position: absolute;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        height: 80%;
        width: 5px;
        background: var(--primary-red);
        border-radius: 10px;
    }

    .badge-premium {
        background: rgba(255,255,255,0.15);
        border: 1px solid rgba(255,255,255,0.3);
        backdrop-filter: blur(5px);
        padding: 8px 16px;
        font-weight: 700;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
    }

    .feature-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }
    
    @media (max-width: 768px) {
        .feature-grid {
            grid-template-columns: 1fr;
        }
    }

    .feature-item {
        background: #f8fafc;
        border-radius: 16px;
        padding: 20px;
        display: flex;
        align-items: flex-start;
        gap: 15px;
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
    }

    .feature-item:hover {
        background: white;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        border-color: #cbd5e1;
    }

    .feature-icon {
        background: white;
        width: 45px;
        height: 45px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary-red);
        font-size: 1.25rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.02);
        flex-shrink: 0;
    }

    .registration-alert {
        background: #6c757d; /* Abu-abu untuk belum dibuka */
        color: white;
        border-radius: 16px;
        padding: 20px;
        border-left: 5px solid #495057;
    }
    
    .target-profesi-box {
        /* max-height removed to show all */
    }
    
    /* Scrollbar styling for target profesi removed */
</style>

<?php
    $gambarPelatihan = !empty($p['gambar_pelatihan']) ? base_url($p['gambar_pelatihan']) : null;
?>

<div class="detail-header" <?= $gambarPelatihan ? 'style="background-image: linear-gradient(90deg, rgba(0,0,0,0.85), rgba(0,0,0,0.7)), url(' . esc($gambarPelatihan) . '); background-size: cover; background-position: center;"' : '' ?>>
    <div class="container-fluid px-4 px-lg-5 position-relative" style="z-index: 2;">
        <div class="row align-items-center">
            <div class="col-lg-10 mx-auto animate__animated animate__fadeInUp">
                <div class="d-flex gap-2 mb-4 flex-wrap">
                    <span class="badge badge-premium rounded-pill text-white"><?= strtoupper($p['metode'] ?? 'ONLINE') ?></span>
                    <span class="badge badge-premium rounded-pill text-white"><?= strtoupper($p['biaya'] ?? 'GRATIS') ?></span>
                    <span class="badge badge-premium rounded-pill text-white"><i class="fas fa-tag me-1"></i> <?= strtoupper($p['kategori'] ?? 'UMUM') ?></span>
                </div>
                <h1 class="display-5 fw-extrabold mb-4 lh-sm text-white" style="letter-spacing: -0.5px;"><?= $p['nama'] ?? 'Pelatihan Kesehatan' ?></h1>
                <div class="d-flex align-items-center gap-3 bg-white bg-opacity-10 p-3 rounded-4 d-inline-flex border border-white border-opacity-10">
                    <?php 
                        $penyList = explode(',', $p['penyelenggara'] ?? 'Dinas Kesehatan');
                        $firstPeny = trim($penyList[0]);
                    ?>
                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($firstPeny) ?>&background=ce2127&color=fff" class="rounded-circle shadow-sm" width="45">
                    <div>
                        <div class="fw-bold text-white mb-1"><?= strtoupper($p['penyelenggara'] ?? 'Dinas Kesehatan') ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-4 px-lg-5 mb-5" style="margin-top: -60px; position: relative; z-index: 10;">
    <div class="row g-4 justify-content-center">
        
        <!-- Sidebar Info -->
        <div class="col-lg-3 col-xl-3 order-lg-2 animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
            <div class="info-card">
                <h5 class="fw-bold mb-4 text-dark d-flex align-items-center gap-2">
                    <i class="fas fa-clipboard-check text-danger"></i> Registrasi
                </h5>
                
                <div class="bg-light rounded-4 p-3 mb-4">
                    <div class="d-flex justify-content-between mb-3 align-items-center">
                        <span class="text-muted fw-bold small">KUOTA</span>
                        <span class="h6 fw-bold mb-0 text-dark"><?= $p['kuota'] ?? 0 ?> <small class="text-muted">Slot</small></span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 align-items-center">
                        <span class="text-muted fw-bold small">TERISI</span>
                        <span class="h6 fw-bold mb-0 text-dark"><?= $p['peserta'] ?? 0 ?> <small class="text-muted">Peserta</small></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                        <span class="text-muted fw-bold small">TOTAL BOBOT</span>
                        <span class="h5 fw-bold mb-0 text-danger"><?= $p['jpl'] ?? 0 ?> <small class="text-muted fs-6">JPL</small></span>
                    </div>
                </div>

                <?php if (($p['biaya'] ?? '') == 'Berbayar'): ?>
                <div class="mb-4 border border-danger border-opacity-25 p-3 rounded-4 bg-danger bg-opacity-10">
                    <h6 class="fw-bold mb-3 text-danger"><i class="fas fa-wallet me-2"></i> Biaya Pendaftaran</h6>
                    <div class="fs-4 fw-bold text-dark mb-3">Rp <?= number_format($p['biaya_nominal'] ?? 0, 0, ',', '.') ?></div>
                    <div class="small text-muted mb-1">Transfer Rekening:</div>
                    <div class="bg-white p-2 rounded border fw-bold text-dark mb-1"><?= $p['nama_bank'] ?? '-' ?></div>
                    <div class="bg-white p-2 rounded border font-monospace text-danger fw-bold mb-1"><?= $p['no_rekening'] ?? '-' ?></div>
                    <div class="bg-white p-2 rounded border small text-muted">a.n <?= $p['atas_nama'] ?? '-' ?></div>
                </div>
                <?php endif; ?>

                <div class="mb-0">
                    <?php
                        $nowTs = time();
                        $regBukaTs = strtotime($reg_buka ?? '');
                        $regTutupTs = strtotime($reg_tutup ?? '');
                        $regBukaText = $regBukaTs ? date('d M Y H:i', $regBukaTs) . ' WIB' : '-';
                        $regTutupText = $regTutupTs ? date('d M Y H:i', $regTutupTs) . ' WIB' : '-';
                    ?>
                    <?php if ($reg_status == 'pending') : ?>
                        <?php if (($p['biaya'] ?? '') == 'Berbayar') : ?>
                            <?php if (!empty($reg['bukti_bayar'])): ?>
                                <div class="alert alert-warning border-0 text-center py-3 mb-0 rounded-3 shadow-sm">
                                    <i class="fas fa-hourglass-half mb-2 fs-4"></i><br>
                                    <span class="fw-bold d-block text-dark">MENUNGGU VERIFIKASI</span>
                                    <small class="text-dark opacity-75">Bukti bayar sedang ditinjau.</small>
                                </div>
                            <?php else: ?>
                                <button type="button" onclick="showUploadForm()" class="btn w-100 py-3 fs-6 rounded-pill text-white fw-bold shadow" style="background-color: var(--primary-red);">
                                    UNGGAH BUKTI BAYAR <i class="fas fa-upload ms-2"></i>
                                </button>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="alert alert-warning border-0 text-center py-3 mb-0 rounded-3 shadow-sm">
                                <i class="fas fa-hourglass-half mb-2 fs-4"></i><br>
                                <span class="fw-bold d-block text-dark">MENUNGGU VERIFIKASI</span>
                                <small class="text-dark opacity-75">Admin sedang meninjau profil Anda.</small>
                            </div>
                        <?php endif; ?>
                    <?php elseif ($reg_status == 'disetujui') : ?>
                        <?php if ($is_learning_finished): ?>
                            <div class="alert alert-secondary border-0 text-center py-3 mb-0 rounded-3 shadow-sm fw-bold">
                                PELATIHAN BERAKHIR
                            </div>
                        <?php elseif ($is_learning_open): ?>
                            <a href="<?= base_url('pelatihan/peserta/belajar/'.$p['id']) ?>" class="btn w-100 py-3 rounded-pill fw-bold text-white shadow-lg" style="background-color: #0f766e; transition: all 0.3s;">
                                <?= !empty($has_progress) ? 'LANJUTKAN BELAJAR' : 'MULAI BELAJAR' ?> <i class="fas fa-play-circle ms-2"></i>
                            </a>
                        <?php else: ?>
                            <div class="alert alert-success border-0 text-center py-3 mb-0 rounded-3 shadow-sm">
                                <i class="fas fa-check-circle mb-2 fs-4"></i><br>
                                <span class="fw-bold d-block text-dark">TERDAFTAR</span>
                                <small class="text-dark opacity-75">Tunggu jadwal dimulai.</small>
                            </div>
                        <?php endif; ?>
                    <?php else : ?>
                        <?php if (!$is_reg_open): ?>
                            <div class="registration-alert text-center mb-0">
                                <?php if ($regBukaTs && $nowTs < $regBukaTs): ?>
                                    <i class="fas fa-lock mb-2 fs-4 text-white-50"></i>
                                    <strong class="d-block mb-1">PENDAFTARAN BELUM DIBUKA</strong>
                                    <small class="d-block">Buka: <?= esc($regBukaText) ?></small>
                                <?php else: ?>
                                    <i class="fas fa-lock mb-2 fs-4 text-white-50"></i>
                                    <strong class="d-block mb-1">PENDAFTARAN DITUTUP</strong>
                                    <small class="d-block">Tutup: <?= esc($regTutupText) ?></small>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <form action="<?= base_url('pelatihan/peserta/daftar/'.$p['id']) ?>" method="POST">
                                <button type="submit" class="btn w-100 py-3 rounded-pill text-white fw-bold shadow-lg" style="background-color: var(--primary-red); transition: all 0.3s;">
                                    DAFTAR SEKARANG <i class="fas fa-arrow-right ms-2"></i>
                                </button>
                            </form>
                            <div class="text-center mt-3 small text-muted">
                                Ditutup pada: <?= esc($regTutupText) ?>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
                
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-lg-8 col-xl-8 order-lg-1 animate__animated animate__fadeInUp" style="animation-delay: 0.1s;">
            
            <div class="bg-white p-4 p-md-5 rounded-4 shadow-sm border mb-4" style="border-color: #f1f5f9 !important;">
                
                <!-- Pengumuman -->
                <?php if (!empty($p['pengumuman'])): ?>
                <div class="alert border d-flex gap-3 align-items-center mb-5 rounded-3" style="background-color: var(--primary-black); color: white; border-color: var(--primary-red) !important; border-left: 5px solid var(--primary-red) !important;">
                    <div class="bg-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 40px; color: var(--primary-red);">
                        <i class="fas fa-bullhorn"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1 text-white">Pengumuman</h6>
                        <p class="mb-0 small text-white-50"><?= $p['pengumuman'] ?></p>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Tentang -->
                <div class="mb-5">
                    <h4 class="section-title">Tentang Pelatihan</h4>
                    <p class="text-muted" style="line-height: 1.8;">
                        <?= $p['deskripsi'] ?? 'Deskripsi pelatihan tidak tersedia.' ?>
                    </p>
                </div>

                <!-- Informasi Grid -->
                <div class="mb-5">
                    <h4 class="section-title">Informasi Lengkap</h4>
                    <div class="feature-grid">
                        <div class="feature-item">
                            <div class="feature-icon"><i class="fas fa-shapes"></i></div>
                            <div>
                                <div class="small text-muted fw-bold mb-1">Tema / Program</div>
                                <div class="text-dark fw-bold small"><?= $p['tema'] ?? '-' ?></div>
                                <div class="text-danger small fw-semibold"><?= $p['program'] ?? '-' ?></div>
                            </div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon"><i class="fas fa-layer-group"></i></div>
                            <div>
                                <div class="small text-muted fw-bold mb-1">Level & Cakupan</div>
                                <div class="text-dark fw-bold small"><?= $p['level'] ?? '-' ?></div>
                                <div class="text-danger small fw-semibold"><?= $p['cakupan'] ?? '-' ?></div>
                            </div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon"><i class="fas fa-user-md"></i></div>
                            <div class="w-100">
                                <div class="small text-muted fw-bold mb-1">Target Profesi</div>
                                <div class="text-dark fw-bold small target-profesi-box">
                                    <?php 
                                        $profesiList = explode(',', $p['target_profesi'] ?? 'Semua Profesi');
                                        foreach($profesiList as $prof) {
                                            echo '<span class="d-inline-block bg-light border rounded px-2 py-1 mb-1 me-1" style="font-size:0.7rem;">'.trim($prof).'</span>';
                                        }
                                    ?>
                                </div>
                                <?php if(!empty($p['target_khusus_profesi'])): ?>
                                    <div class="text-danger small fw-semibold mt-1 border-top pt-1">Khusus: <?= $p['target_khusus_profesi'] ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon"><i class="fas fa-calendar-alt"></i></div>
                            <div>
                                <div class="small text-muted fw-bold mb-1">Jadwal Pelaksanaan</div>
                                <div class="text-dark fw-bold small"><?= tanggal_indo($p['jadwal_mulai'] ?? 'now') ?> - <?= tanggal_indo($p['jadwal_selesai'] ?? 'now') ?></div>
                                <div class="text-danger small fw-semibold"><?= $p['jam_mulai'] ?? '08:00' ?> s.d <?= $p['jam_selesai'] ?? '16:00' ?> WIB</div>
                            </div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon"><i class="fas fa-map-marker-alt"></i></div>
                            <div>
                                <div class="small text-muted fw-bold mb-1">Metode & Lokasi</div>
                                <div class="text-dark fw-bold small"><?= $p['metode'] ?? 'Online' ?></div>
                                <?php
                                    $metodeText = $p['metode'] ?? 'Online';
                                    $lokasiText = 'Zoom / LMS';
                                    if ($metodeText == 'Offline' || $metodeText == 'Offline / Clasical') {
                                        $lokasiText = empty($p['lokasi']) ? 'LOKASI OFFLINE menyesuaikan sesi.' : $p['lokasi'];
                                    } elseif (strpos(strtolower($metodeText), 'blended') !== false || strpos(strtolower($metodeText), 'hybrid') !== false) {
                                        $lokasiText = 'Zoom / LMS & ' . (empty($p['lokasi']) ? 'LOKASI OFFLINE menyesuaikan sesi.' : $p['lokasi']);
                                    }
                                ?>
                                <div class="text-danger small fw-semibold"><?= $lokasiText ?></div>
                            </div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon"><i class="fas fa-chalkboard-teacher"></i></div>
                            <div>
                                <div class="small text-muted fw-bold mb-1">Narasumber</div>
                                <div class="text-dark fw-bold small"><?= $p['narasumber'] ?? '-' ?></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tujuan & Kompetensi -->
                <div class="row g-4 mb-5">
                    <div class="col-md-6">
                        <div class="bg-light p-4 rounded-4 h-100 border" style="border-color: #e2e8f0 !important;">
                            <h6 class="fw-bold mb-3 text-dark d-flex align-items-center gap-2"><i class="fas fa-bullseye text-danger"></i> Tujuan</h6>
                            <p class="text-muted small mb-0 lh-lg"><?= $p['tujuan'] ?? 'Belum ada informasi tujuan.' ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="bg-light p-4 rounded-4 h-100 border" style="border-color: #e2e8f0 !important;">
                            <h6 class="fw-bold mb-3 text-dark d-flex align-items-center gap-2"><i class="fas fa-star text-danger"></i> Kompetensi</h6>
                            <p class="text-muted small mb-0 lh-lg"><?= $p['kompetensi'] ?? 'Belum ada informasi kompetensi.' ?></p>
                        </div>
                    </div>
                </div>

                <!-- Kurikulum -->
                <div class="mb-2">
                    <h4 class="section-title">Kurikulum Materi</h4>
                    <?php if(empty($konten)): ?>
                        <div class="text-center py-5 text-muted bg-light rounded-4 border">
                            <i class="fas fa-folder-open mb-3 fs-3 opacity-50"></i>
                            <div class="small fw-bold">Belum ada materi terdaftar</div>
                        </div>
                    <?php else: ?>
                        <div class="list-group list-group-flush rounded-3 border">
                            <?php foreach($konten as $index => $k): ?>
                                <div class="list-group-item p-3 border-bottom <?= $index % 2 == 0 ? 'bg-light' : 'bg-white' ?>">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="bg-white rounded-circle shadow-sm d-flex align-items-center justify-content-center fw-bold text-danger border" style="width: 35px; height: 35px; flex-shrink: 0; font-size: 0.8rem;">
                                            <?= $index + 1 ?>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="fw-bold mb-1 text-dark" style="font-size: 0.9rem;"><?= $k['judul'] ?></h6>
                                                    <?php if(!empty($k['deskripsi'])): ?>
                                                        <p class="text-muted mb-0" style="font-size: 0.75rem;"><?= $k['deskripsi'] ?></p>
                                                    <?php endif; ?>
                                                </div>
                                                <span class="badge rounded-pill bg-dark px-2 py-1 text-white-50" style="font-size: 0.65rem;">
                                                    <i class="fas fa-<?= $k['tipe'] == 'video' ? 'play-circle' : ($k['tipe'] == 'materi' ? 'file-alt' : 'tasks') ?> me-1"></i>
                                                    <?= strtoupper($k['tipe']) ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function showUploadForm() {
        Swal.fire({
            title: 'Unggah Bukti Pembayaran',
            html: `
                <div class="text-start mb-3">
                    <p class="text-muted small">Format yang didukung: JPG, PNG, PDF (Maks. 2MB)</p>
                </div>
                <form id="uploadForm" action="<?= base_url('pelatihan/peserta/upload_bukti_bayar/'.$p['id']) ?>" method="POST" enctype="multipart/form-data">
                    <input type="file" name="bukti_bayar" id="bukti_bayar" class="form-control" accept=".jpg,.jpeg,.png,.pdf" required>
                </form>
            `,
            showCancelButton: true,
            confirmButtonText: 'Kirim',
            cancelButtonText: 'Batal',
            confirmButtonColor: 'var(--primary-red)',
            cancelButtonColor: '#64748b',
            preConfirm: () => {
                const fileInput = document.getElementById('bukti_bayar');
                if (!fileInput.files.length) {
                    Swal.showValidationMessage('File bukti bayar harus dipilih');
                    return false;
                }
                document.getElementById('uploadForm').submit();
            }
        });
    }

    <?php if (session()->getFlashdata('show_upload_popup')): ?>
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            showUploadForm();
        }, 500);
    });
    <?php endif; ?>
</script>
<?= $this->endSection() ?>
