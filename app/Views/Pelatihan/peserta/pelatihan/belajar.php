<?php
$p = $p ?? [];
$konten = $konten ?? [];
$completed_steps = $completed_steps ?? [];
$active_step = $active_step ?? [];
$active_id = $active_id ?? 1;
$pg = $pg ?? [];
$user = $user ?? [];
$evalQuestions = $evalQuestions ?? [];
$sesiList = $sesiList ?? [];
$sertifikat = $sertifikat ?? [];
$preTestQuestions = $preTestQuestions ?? [];
$postTestQuestions = $postTestQuestions ?? [];
$evalIndex = $evalIndex ?? null;
$certIndex = $certIndex ?? null;
$postTestIndex = $postTestIndex ?? null;
$materiList = $materiList ?? [];
$narasumberList = $narasumberList ?? [];
$penyelenggaraList = $penyelenggaraList ?? [];
$ratingAlreadySubmitted = $ratingAlreadySubmitted ?? false;
$submittedSesiEvaluations = $submittedSesiEvaluations ?? [];

if (!function_exists('renderPelatihanFilePreview')) {
    function renderPelatihanFilePreview($filePath, $title = '', $fallbackUrl = '') {
        $filePath = $filePath ?: '';
        $title = $title ?: basename($filePath);
        $fileUrl = $fallbackUrl ?: base_url($filePath);
        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true)) {
            return '<div class="text-center"><img src="' . esc($fileUrl, 'attr') . '" class="img-fluid rounded shadow" style="max-height: 600px;" alt="' . esc($title, 'attr') . '"></div>';
        }

        if (in_array($ext, ['mp4', 'webm', 'ogg'], true)) {
            $mime = $ext === 'mp4' ? 'video/mp4' : ($ext === 'webm' ? 'video/webm' : 'video/ogg');
            return '<video controls class="w-100 rounded shadow" style="max-height: 500px;"><source src="' . esc($fileUrl, 'attr') . '" type="' . esc($mime, 'attr') . '">Browser Anda tidak mendukung pemutaran video.</video>';
        }

        if ($ext === 'pdf') {
            return '<div class="document-preview-shell"><div class="document-preview-head"><span>' . esc($title) . '</span></div><iframe src="' . esc($fileUrl, 'attr') . '" class="inline-document-preview" title="' . esc($title, 'attr') . '"></iframe></div>';
        }

        if (in_array($ext, ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'], true)) {
            $label = in_array($ext, ['xls', 'xlsx'], true) ? 'Excel' : (in_array($ext, ['ppt', 'pptx'], true) ? 'PowerPoint' : 'Word');
            return '<div class="document-preview-shell"><div class="document-preview-head"><span>' . esc($title) . '</span></div><div class="p-4 text-center bg-light" style="min-height: 220px;"><i class="fas fa-file-alt fa-3x text-muted mb-3"></i><h6 class="fw-bold text-dark">Preview ' . esc($label) . ' tidak tersedia di halaman ini</h6><p class="text-muted small mb-0">File materi ini tidak dapat dibuka langsung dari ruang belajar.</p></div></div>';
        }

        if (in_array($ext, ['txt', 'csv'], true)) {
            return '<div class="document-preview-shell"><div class="document-preview-head"><span>' . esc($title) . '</span></div><div class="p-4 text-center bg-light" style="min-height: 220px;"><i class="fas fa-file-alt fa-3x text-muted mb-3"></i><h6 class="fw-bold text-dark">Preview teks tidak tersedia</h6><p class="text-muted small mb-0">File materi ini tidak dapat dibuka langsung dari ruang belajar.</p></div></div>';
        }

        return '<div class="py-4 text-center"><i class="fas fa-file-alt fa-4x text-muted mb-3"></i><h6 class="text-white">File ini tidak punya preview bawaan browser.</h6><div class="text-warning fw-bold">' . esc($title) . '</div></div>';
    }
}
?>

<?= $this->extend('pelatihan/layout/peserta_layout') ?>

<?= $this->section('content') ?>

<style>
    :root {
        --primary-red: #ce2127;
        --accent-yellow: #ffc107;
        --primary-dark: #0f172a;
        --bg-light: #f8fafc;
        --active-item: #f1f5f9;
    }

    .learning-layout {
        display: flex;
        min-height: calc(100vh - 100px);
        margin: 0;
        background: var(--bg-light);
        border-radius: 24px;
        overflow: hidden;
        border: 1px solid #e5e7eb;
    }

    .learning-sidebar {
        width: 320px;
        background: white;
        border-right: 1px solid #e2e8f0;
        overflow-y: auto;
        flex-shrink: 0;
        display: flex;
        flex-direction: column;
    }

    .learning-content {
        flex-grow: 1;
        padding: 40px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
    }

    .sidebar-header {
        padding: 25px;
        border-bottom: 2px solid var(--primary-red);
        background: var(--primary-dark);
        color: white;
    }

    .sidebar-nav {
        flex-grow: 1;
        padding: 20px 0;
    }

    .nav-step {
        padding: 15px 25px;
        display: flex;
        align-items: center;
        gap: 15px;
        text-decoration: none;
        color: #475569;
        transition: all 0.2s;
        border-left: 5px solid transparent;
        font-size: 0.9rem;
        font-weight: 600;
    }

    .nav-step:hover {
        background: #f1f5f9;
        color: var(--primary-red);
    }

    .nav-step.active {
        background: #fff1f2;
        border-left-color: var(--primary-red);
        color: var(--primary-red);
        font-weight: 800;
    }

    .nav-step.completed i {
        color: #10b981;
    }

    .nav-step.locked {
        color: #94a3b8;
    }

    .nav-step i {
        font-size: 1.1rem;
        width: 24px;
        text-align: center;
    }

    .content-card {
        background: white;
        border-radius: 24px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
        padding: 50px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        border: 1px solid #f1f5f9;
    }

    .material-viewer {
        flex-grow: 1;
        background: var(--primary-dark);
        border-radius: 20px;
        margin-bottom: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        position: relative;
        border: 8px solid #e2e8f0;
    }

    .inline-document-preview {
        width: 100%;
        min-height: 620px;
        border: 0;
        border-radius: 14px;
        background: #ffffff;
    }

    .document-preview-shell {
        background: #ffffff;
        border-radius: 16px;
        overflow: hidden;
        border: 1px solid rgba(255,255,255,.15);
    }

    .document-preview-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        color: #0f172a;
        background: #f8fafc;
        border-bottom: 1px solid #e5e7eb;
        font-weight: 800;
        font-size: .82rem;
    }

    .document-preview-head a {
        color: var(--primary-red);
        text-decoration: none;
        white-space: nowrap;
    }

    .viewer-placeholder {
        text-align: center;
        color: #fff;
    }

    /* Multi-step Evaluation Stepper */
    .evaluasi-stepper {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 50px;
        position: relative;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
    }

    .evaluasi-stepper::before {
        content: '';
        position: absolute;
        top: 25px;
        left: 50px;
        right: 50px;
        height: 2px;
        background: #edf2f7;
        z-index: 1;
    }

    .stepper-progress {
        position: absolute;
        top: 25px;
        left: 50px;
        height: 2px;
        background: var(--primary-red);
        z-index: 2;
        transition: width 0.3s ease;
    }

    .step-item {
        position: relative;
        z-index: 3;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
        width: 100px;
    }

    .step-circle {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: white;
        border: 2px solid #edf2f7;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        color: #718096;
        transition: all 0.3s;
    }

    .step-item.active .step-circle {
        border-color: var(--primary-red);
        background: var(--primary-red);
        color: white;
        box-shadow: 0 4px 12px rgba(206, 33, 39, 0.3);
    }

    .step-item.completed .step-circle {
        border-color: #10b981;
        background: #10b981;
        color: white;
    }

    .step-label {
        font-size: 0.85rem;
        font-weight: 600;
        color: #a0aec0;
    }

    .step-item.active .step-label {
        color: var(--primary-dark);
    }

    /* Rating Scale 1-5 Custom */
    .rating-row {
        margin-bottom: 30px;
    }

    .rating-options {
        display: flex;
        gap: 10px;
        margin-top: 15px;
    }

    .rating-btn {
        flex: 1;
        text-align: center;
    }

    .rating-btn input {
        display: none;
    }

    .rating-btn label {
        display: block;
        padding: 12px;
        background: #f8fafc;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.2s;
        border: 2px solid #edf2f7;
        font-weight: 800;
    }

    .rating-btn input:checked + label {
        background: #fff1f2;
        border-color: var(--primary-red);
        color: var(--primary-red);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(206, 33, 39, 0.1);
    }

    /* Quiz Interactivity */
    .quiz-option {
        padding: 20px;
        background: white;
        border: 2px solid #edf2f7;
        border-radius: 16px;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 15px;
        position: relative;
    }

    .quiz-option:hover {
        border-color: var(--primary-red);
        background: #fff1f2;
    }

    .quiz-option input {
        display: none;
    }

    .quiz-option.selected {
        border-color: var(--primary-red);
        background: #fff1f2;
        box-shadow: 0 4px 12px rgba(206, 33, 39, 0.1);
    }

    .quiz-option-circle {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        border: 2px solid #cbd5e0;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .quiz-option.selected .quiz-option-circle {
        border-color: var(--primary-red);
        background: var(--primary-red);
    }

    .quiz-option.selected .quiz-option-circle::after {
        content: '';
        width: 8px;
        height: 8px;
        background: white;
        border-radius: 50%;
    }

    .btn-selanjutnya {
        background-color: var(--primary-red);
        color: white;
        padding: 15px 40px;
        border-radius: 12px;
        font-weight: 800;
        border: none;
        transition: all 0.3s;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .btn-selanjutnya:hover {
        background-color: #a51a1f;
        transform: translateY(-2px);
        box-shadow: 0 8px 15px rgba(206, 33, 39, 0.2);
    }

    .btn-selanjutnya:disabled {
        background-color: #cbd5e0;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    @media (max-width: 992px) {
        .learning-layout { flex-direction: column; }
        .learning-sidebar { width: 100%; max-height: none; }
        .learning-content { padding: 18px; }
        .content-card { padding: 24px; }
        .inline-document-preview { min-height: 420px; }
    }

</style>

<div class="learning-layout">
    
    <!-- Sidebar -->
    <div class="learning-sidebar">
        <div class="sidebar-header">
            <div class="small text-muted mb-1 text-uppercase fw-bold letter-spacing-1">Progress Belajar</div>
            <h6 class="fw-bold mb-3"><?= $p['nama'] ?></h6>
            <div class="progress" style="height: 8px; background: #e2e8f0; border-radius: 10px;">
                <div class="progress-bar bg-danger shadow-sm" style="width: <?= (count($completed_steps) / count($konten)) * 100 ?>%"></div>
            </div>
            <div class="d-flex justify-content-between mt-2">
                <small class="text-light fw-bold opacity-75"><?= count($completed_steps) ?>/<?= count($konten) ?> SELESAI</small>
                <small class="text-light fw-bold"><?= round((count($completed_steps) / count($konten)) * 100) ?>%</small>
            </div>
        </div>

        <div class="sidebar-nav">
            <?php 
                $max_completed = !empty($completed_steps) ? max($completed_steps) : 0;
                foreach ($konten as $index => $k) : 
                    $is_completed = in_array($k['id'], $completed_steps);
                    $is_active = $active_id == $k['id'];
                    $is_locked = $k['id'] > ($max_completed + 1);
                    $is_materi = $k['tipe'] == 'materi_segmen';
                    $is_evaluasi_sesi = $k['tipe'] == 'evaluasi_sesi';
                    $is_sub_item = $is_materi || $is_evaluasi_sesi;
                    $sesiStatus = isset($k['sesi_id']) ? ($presensiStatusList[$k['sesi_id']] ?? null) : null;
                    // Hanya lock materi_segmen & evaluasi_sesi jika alfa, BUKAN step presensi/sesi itu sendiri
                    $isAlfaBlocked = $is_sub_item && $sesiStatus === 'Alfa';
                    if ($isAlfaBlocked) $is_locked = true;
            ?>
                <a href="<?= $is_locked ? 'javascript:void(0)' : base_url('pelatihan/peserta/belajar/'.$p['id'].'?step='.$k['id']) ?>" 
                   class="nav-step <?= $is_active ? 'active' : '' ?> <?= $is_completed ? 'completed' : '' ?> <?= $is_locked ? 'locked opacity-50' : '' ?> <?= $is_sub_item ? 'ms-4' : '' ?>"
                   <?= $is_locked ? 'onclick="Swal.fire({icon:\'lock\', title:\'Materi Terkunci\', text:\''.($isAlfaBlocked ? 'Anda tidak dapat mengakses materi ini karena status kehadiran tercatat ALFA.' : 'Selesaikan materi sebelumnya untuk membuka bagian ini.').'\', confirmButtonColor:\'#1a202c\'})"' : '' ?>>
                    <?php if ($is_locked) : ?>
                        <i class="fas fa-lock"></i>
                    <?php elseif ($is_completed) : ?>
                        <i class="fas fa-check-circle"></i>
                    <?php elseif ($is_active) : ?>
                        <i class="fas fa-play-circle"></i>
                    <?php elseif ($is_evaluasi_sesi) : ?>
                        <i class="fas fa-clipboard-check"></i>
                    <?php else : ?>
                        <i class="fas fa-circle-notch"></i>
                    <?php endif; ?>
                    <span class="fw-bold"><?= strtoupper($k['judul']) ?></span>
                    <?php if ($k['tipe'] == 'pre_test' || $k['tipe'] == 'post_test') : ?>
                        <span class="badge bg-dark text-white shadow-sm px-3 py-2 fw-bold border border-white"><?= strtoupper($p['mekanisme']) ?></span>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Content Area -->
    <div class="learning-content">
        
        <div class="content-card">
            <div class="d-flex justify-content-between align-items-center mb-5 border-bottom pb-4">
                <div>
                    <h2 class="fw-bold mb-0 text-dark"><?= strtoupper($active_step['judul']) ?></h2>
                    <?php if ($active_step['tipe'] == 'presensi') : ?>
                        <p class="text-muted small mb-0 mt-2 fw-bold"><i class="fas fa-map-marker-alt me-1 text-dark"></i> LOKASI: RSUD KOTA YOGYAKARTA</p>
                    <?php endif; ?>
                </div>
                <div class="badge bg-dark text-white px-4 py-2 rounded-pill fw-bold" style="font-size: 0.7rem; letter-spacing: 1px;"><?= strtoupper(str_replace('_', ' ', $active_step['tipe'])) ?></div>
            </div>

            <?php if ($active_step['tipe'] == 'presensi') : ?>
                <?php
                    $presensiOpen = isset($active_step['available']) ? (bool)$active_step['available'] : true;
                    $presensiOpenAt = !empty($active_step['open_at']) ? date('d M Y H:i', strtotime($active_step['open_at'])) : null;
                    $presensiCloseAt = !empty($active_step['close_at']) ? date('d M Y H:i', strtotime($active_step['close_at'])) : null;
                    $nowLabel = date('d M Y | H:i:s');
                ?>
                <div class="text-center py-4 animate__animated animate__fadeIn">
                    <div class="mb-5">
                        <div class="d-flex align-items-center justify-content-center gap-2 mb-4 flex-wrap">
                            <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill fw-bold border border-danger border-opacity-25">
                                <i class="fas fa-users me-1"></i> SESI TATAP MUKA
                            </span>
                            <span class="badge bg-dark px-3 py-2 rounded-pill fw-bold border border-secondary border-opacity-25">
                                <i class="fas fa-clock me-1"></i> Waktu Sekarang: <?= $nowLabel ?> WIB
                            </span>
                            <span class="badge <?= $presensiOpen ? 'bg-success' : 'bg-danger' ?> px-3 py-2 rounded-pill fw-bold border border-opacity-25">
                                <i class="fas <?= $presensiOpen ? 'fa-door-open' : 'fa-lock' ?> me-1"></i> <?= $presensiOpen ? 'SESI DIBUKA' : 'SESI DITUTUP' ?>
                            </span>
                        </div>

                        <div class="card bg-light border-0 p-4 rounded-4 shadow-sm mx-auto mb-4" style="max-width: 500px; text-align: left;">
                            <div class="fw-bold text-secondary small mb-3 border-bottom pb-2">DATA PRESENSI YANG AKAN DICATAT:</div>
                            <div class="row g-2 small">
                                <div class="col-4 text-muted fw-bold">Nama Peserta</div>
                                <div class="col-8 text-dark fw-bold">: <?= esc($user['nama_lengkap'] ?? '') ?></div>

                                <div class="col-4 text-muted fw-bold">NIK / NIP</div>
                                <div class="col-8 text-dark fw-bold">: <?= esc($user['nik'] ?? '') ?></div>

                                <div class="col-4 text-muted fw-bold">Waktu Presensi</div>
                                <div class="col-8 text-dark fw-bold">: <?= date('d M Y H:i:s') ?> WIB</div>

                                <div class="col-4 text-muted fw-bold">Lokasi Sesi</div>
                                <div class="col-8 text-dark fw-bold">: <?= esc($active_step['tempat'] ?? '-') ?></div>
                            </div>
                        </div>

                        <p class="text-muted small mx-auto" style="max-width: 600px;">
                            <?php if ($presensiOpen): ?>
                                Sesi presensi tatap muka telah dibuka. Silakan klik tombol presensi di bawah ini untuk mencatatkan kehadiran Anda.
                            <?php else: ?>
                                Presensi offline belum dapat dilakukan. Tombol presensi hanya akan aktif jika waktu pelaksanaan sesi sudah dimulai oleh administrator.
                            <?php endif; ?>
                        </p>
                        
                        <div class="text-muted small fw-bold mt-3">
                            <i class="fas fa-calendar-alt me-1"></i> Jadwal Sesi: 
                            <span class="text-danger">
                                <?= !empty($active_step['tanggal']) ? tanggal_indo($active_step['tanggal']) : '' ?>
                                (<?= !empty($active_step['waktu']) ? date('H:i', strtotime($active_step['waktu'])) : '00:00' ?> - <?= !empty($active_step['jam_tutup']) ? date('H:i', strtotime($active_step['jam_tutup'])) : 'Selesai' ?> WIB)
                            </span>
                        </div>
                    </div>
                    
                    <?php
                        $statusHadirPresensi = $active_step['status_hadir'] ?? null;
                        $isAlfaPresensi = ($statusHadirPresensi === 'Alfa');
                    ?>
                    <?php if (isset($active_step['is_attended']) && $active_step['is_attended'] && $isAlfaPresensi && $presensiOpen): ?>
                        <!-- Alfa tapi sesi masih buka: bisa override presensi sendiri -->
                        <div class="text-center">
                            <div class="mb-3"><span class="badge bg-danger px-4 py-2 rounded-pill fs-6 fw-bold"><i class="fas fa-times-circle me-1"></i> STATUS: ALFA</span></div>
                            <p class="text-danger small fw-bold">Status Anda saat ini ALFA. Sesi masih buka — Anda dapat presensi sekarang untuk mengubah status.</p>
                            <a href="<?= base_url('pelatihan/peserta/tandai_selesai/'.$p['id'].'/'.$active_id) ?><?= isset($active_step['sesi_id']) ? '?sesi_id='.$active_step['sesi_id'] : '' ?>" class="btn px-5 py-4 rounded-4 shadow-lg fw-bold fs-5 border-0 hover-scale" style="background: #0f766e !important; color: white !important; min-width: 300px;">
                                PRESENSI SEKARANG <i class="fas fa-check-circle ms-2 text-warning"></i>
                            </a>
                        </div>
                    <?php elseif (isset($active_step['is_attended']) && $active_step['is_attended']): ?>
                        <div class="text-center">
                            <?php if ($statusHadirPresensi === 'Hadir'): ?>
                                <div class="mb-3"><span class="badge bg-success px-4 py-2 rounded-pill fs-6 fw-bold"><i class="fas fa-check-circle me-1"></i> STATUS: HADIR</span></div>
                            <?php elseif ($statusHadirPresensi === 'Izin'): ?>
                                <div class="mb-3"><span class="badge bg-warning text-dark px-4 py-2 rounded-pill fs-6 fw-bold"><i class="fas fa-exclamation-circle me-1"></i> STATUS: IZIN</span></div>
                            <?php elseif ($isAlfaPresensi): ?>
                                <div class="mb-2"><span class="badge bg-danger px-4 py-2 rounded-pill fs-6 fw-bold"><i class="fas fa-times-circle me-1"></i> STATUS: ALFA</span></div>
                                <p class="text-danger small fw-bold">Status kehadiran Anda tercatat ALFA. Materi pada sesi ini tidak dapat diakses.</p>
                            <?php elseif ($statusHadirPresensi !== null): ?>
                                <div class="mb-3"><span class="badge bg-secondary px-4 py-2 rounded-pill fs-6 fw-bold"><?= esc($statusHadirPresensi) ?></span></div>
                            <?php endif; ?>
                            <a href="<?= base_url('pelatihan/peserta/tandai_selesai/'.$p['id'].'/'.$active_id.(isset($active_step['sesi_id']) ? '?sesi_id='.$active_step['sesi_id'] : '')) ?>" class="btn px-5 py-3 rounded-pill fw-bold shadow-lg hover-scale fs-5 mt-2 animate__animated animate__pulse animate__infinite" style="background: var(--primary-red); color: white; border: none; min-width: 300px;">
                                SELESAI &amp; LANJUT <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                        </div>
                    <?php elseif ($presensiOpen): ?>
                        <a href="<?= base_url('pelatihan/peserta/tandai_selesai/'.$p['id'].'/'.$active_id) ?><?= isset($active_step['sesi_id']) ? '?sesi_id='.$active_step['sesi_id'] : '' ?>" class="btn px-5 py-4 rounded-4 shadow-lg fw-bold fs-5 border-0 hover-scale" style="background: #0f766e !important; color: white !important; min-width: 300px;">
                            PRESENSI SEKARANG <i class="fas fa-check-circle ms-2 text-warning"></i>
                        </a>
                    <?php else: ?>
                        <?php if ($statusHadirPresensi === 'Alfa'): ?>
                            <div class="mb-2"><span class="badge bg-danger px-4 py-2 rounded-pill fs-6 fw-bold"><i class="fas fa-times-circle me-1"></i> STATUS: ALFA (dipresensi admin)</span></div>
                            <p class="text-danger small fw-bold">Status kehadiran Anda tercatat ALFA. Materi pada sesi ini tidak dapat diakses.</p>
                            <a href="<?= base_url('pelatihan/peserta/tandai_selesai/'.$p['id'].'/'.$active_id.(isset($active_step['sesi_id']) ? '?sesi_id='.$active_step['sesi_id'] : '')) ?>" class="btn px-5 py-3 rounded-pill fw-bold shadow-lg hover-scale fs-5 mt-2" style="background: var(--primary-red); color: white; border: none; min-width: 300px;">
                                LANJUT KE SESI BERIKUTNYA <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                        <?php elseif ($statusHadirPresensi === 'Izin'): ?>
                            <div class="mb-2"><span class="badge bg-warning text-dark px-4 py-2 rounded-pill fs-6 fw-bold"><i class="fas fa-exclamation-circle me-1"></i> STATUS: IZIN (dipresensi admin)</span></div>
                            <a href="<?= base_url('pelatihan/peserta/tandai_selesai/'.$p['id'].'/'.$active_id.(isset($active_step['sesi_id']) ? '?sesi_id='.$active_step['sesi_id'] : '')) ?>" class="btn px-5 py-3 rounded-pill fw-bold shadow-lg hover-scale fs-5 mt-2 animate__animated animate__pulse animate__infinite" style="background: var(--primary-red); color: white; border: none; min-width: 300px;">
                                SELESAI &amp; LANJUT KE MATERI <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                        <?php elseif ($statusHadirPresensi === 'Hadir'): ?>
                            <div class="mb-2"><span class="badge bg-success px-4 py-2 rounded-pill fs-6 fw-bold"><i class="fas fa-check-circle me-1"></i> STATUS: HADIR (dipresensi admin)</span></div>
                            <a href="<?= base_url('pelatihan/peserta/tandai_selesai/'.$p['id'].'/'.$active_id.(isset($active_step['sesi_id']) ? '?sesi_id='.$active_step['sesi_id'] : '')) ?>" class="btn px-5 py-3 rounded-pill fw-bold shadow-lg hover-scale fs-5 mt-2 animate__animated animate__pulse animate__infinite" style="background: var(--primary-red); color: white; border: none; min-width: 300px;">
                                SELESAI &amp; LANJUT KE MATERI <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                        <?php else: ?>
                            <button class="btn px-5 py-4 rounded-4 shadow-sm fw-bold fs-5 border-0" style="background: #cbd5e1 !important; color: #64748b !important; min-width: 300px; cursor: not-allowed;" disabled>
                                PRESENSI SEKARANG <i class="fas fa-lock ms-2"></i>
                            </button>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

            <?php elseif ($active_step['tipe'] == 'materi' || $active_step['tipe'] == 'sesi' || $active_step['tipe'] == 'materi_segmen') : ?>
                <div class="material-viewer shadow-lg border-0" style="background: #0f172a; border-radius: 30px; position: relative;">
                    <!-- Decorative Controls -->
                    <div class="position-absolute top-0 start-0 w-100 p-3 d-flex gap-2" style="z-index: 10; background: linear-gradient(to bottom, rgba(0,0,0,0.5), transparent);">
                        <div style="width: 12px; height: 12px; background: #ff5f56; border-radius: 50%;"></div>
                        <div style="width: 12px; height: 12px; background: #ffbd2e; border-radius: 50%;"></div>
                        <div style="width: 12px; height: 12px; background: #27c93f; border-radius: 50%;"></div>
                    </div>
                    
                    <div class="viewer-placeholder p-5 text-center w-100">
                        <?php 
                        $sessionOpen = isset($active_step['available']) ? (bool)$active_step['available'] : true;
                        if (!$sessionOpen): ?>
                            <div class="mb-4">
                                <i class="fas fa-lock fa-6x text-white opacity-25"></i>
                            </div>
                            <h3 class="fw-bold text-white mb-3">AKSES DITUTUP / BELUM DIBUKA</h3>
                            <p class="text-white-50 fs-5 mb-4" style="max-width: 500px; margin: 0 auto;">Akses terkunci. Sesi ini hanya dapat diakses sesuai dengan waktu pelaksanaan yang telah diatur.</p>
                            <div class="p-3 bg-white bg-opacity-10 border border-white border-opacity-25 rounded-4 mx-auto mb-4" style="max-width: 480px;">
                                <div class="text-white-50 small fw-bold mb-1">WAKTU PELAKSANAAN</div>
                                <div class="h6 text-warning fw-bold mb-0">
                                    <?= !empty($active_step['tanggal']) ? tanggal_indo($active_step['tanggal']) : date('d M Y') ?> | 
                                    <?= !empty($active_step['waktu']) ? date('H:i:s', strtotime($active_step['waktu'])) : '00:00:00' ?> s.d 
                                    <?= !empty($active_step['jam_tutup']) ? date('H:i:s', strtotime($active_step['jam_tutup'])) : 'Selesai' ?> WIB
                                </div>
                            </div>
                            <?php
                                $closedSesiStatus = isset($active_step['sesi_id']) ? ($presensiStatusList[$active_step['sesi_id']] ?? null) : ($active_step['status_hadir'] ?? null);
                                // Hanya tampilkan status+tombol di sini untuk tipe sesi/presensi
                                // Untuk materi_segmen, bottom bar yang handle
                                $showStatusInContent = in_array($active_step['tipe'], ['sesi', 'presensi']);
                            ?>
                            <?php if ($showStatusInContent): ?>
                                <?php if ($closedSesiStatus === 'Alfa'): ?>
                                    <div class="mb-3"><span class="badge bg-danger px-4 py-2 rounded-pill fw-bold fs-6"><i class="fas fa-times-circle me-1"></i> STATUS PRESENSI: ALFA</span></div>
                                    <p class="text-white-50 small fw-bold mb-3">Status kehadiran Anda tercatat ALFA. Materi sesi ini tidak dapat diakses.</p>
                                    <a href="<?= base_url('pelatihan/peserta/tandai_selesai/'.$p['id'].'/'.$active_id.'?sesi_id='.$active_step['sesi_id']) ?>" class="btn px-5 py-3 rounded-pill fw-bold shadow-lg fs-5" style="background: var(--primary-red); color: white; border: none;">
                                        LANJUT KE SESI BERIKUTNYA <i class="fas fa-arrow-right ms-2"></i>
                                    </a>
                                <?php elseif ($closedSesiStatus === 'Izin'): ?>
                                    <div class="mb-3"><span class="badge bg-warning text-dark px-4 py-2 rounded-pill fw-bold fs-6"><i class="fas fa-exclamation-circle me-1"></i> STATUS PRESENSI: IZIN</span></div>
                                    <a href="<?= base_url('pelatihan/peserta/tandai_selesai/'.$p['id'].'/'.$active_id.'?sesi_id='.$active_step['sesi_id']) ?>" class="btn px-5 py-3 rounded-pill fw-bold shadow-lg hover-scale fs-5 animate__animated animate__pulse animate__infinite" style="background: var(--primary-red); color: white; border: none;">
                                        SELESAI &amp; LANJUT KE MATERI <i class="fas fa-arrow-right ms-2"></i>
                                    </a>
                                <?php elseif ($closedSesiStatus === 'Hadir'): ?>
                                    <div class="mb-3"><span class="badge bg-success px-4 py-2 rounded-pill fw-bold fs-6"><i class="fas fa-check-circle me-1"></i> STATUS PRESENSI: HADIR</span></div>
                                    <a href="<?= base_url('pelatihan/peserta/tandai_selesai/'.$p['id'].'/'.$active_id.'?sesi_id='.$active_step['sesi_id']) ?>" class="btn px-5 py-3 rounded-pill fw-bold shadow-lg hover-scale fs-5 animate__animated animate__pulse animate__infinite" style="background: var(--primary-red); color: white; border: none;">
                                        SELESAI &amp; LANJUT KE MATERI <i class="fas fa-arrow-right ms-2"></i>
                                    </a>
                                <?php endif; ?>
                            <?php elseif ($closedSesiStatus !== null): ?>
                                <!-- materi_segmen: hanya tampilkan badge, bottom bar handle navigasi -->
                                <?php if ($closedSesiStatus === 'Alfa'): ?>
                                    <div class="mt-3"><span class="badge bg-danger px-4 py-2 rounded-pill fw-bold"><i class="fas fa-times-circle me-1"></i> STATUS: ALFA — Materi tidak dapat diakses</span></div>
                                <?php elseif ($closedSesiStatus === 'Izin'): ?>
                                    <div class="mt-3"><span class="badge bg-warning text-dark px-4 py-2 rounded-pill fw-bold"><i class="fas fa-exclamation-circle me-1"></i> STATUS: IZIN</span></div>
                                <?php elseif ($closedSesiStatus === 'Hadir'): ?>
                                    <div class="mt-3"><span class="badge bg-success px-4 py-2 rounded-pill fw-bold"><i class="fas fa-check-circle me-1"></i> STATUS: HADIR</span></div>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php elseif ($active_step['tipe'] == 'sesi') : ?>
                            <div class="card bg-white bg-opacity-10 border border-white border-opacity-25 p-4 rounded-4 shadow-sm mx-auto mb-4" style="max-width: 500px; text-align: left;">
                                <div class="text-white-50 small fw-bold mb-2 border-bottom border-white border-opacity-10 pb-2">JADWAL PELAKSANAAN:</div>
                                <div class="row g-2 small text-white">
                                    <div class="col-4 text-white-50 fw-bold">Tanggal</div>
                                    <div class="col-8 fw-bold">: <?= !empty($active_step['tanggal']) ? tanggal_indo($active_step['tanggal']) : '' ?></div>

                                    <div class="col-4 text-white-50 fw-bold">Jam Sesi</div>
                                    <div class="col-8 fw-bold">: 
                                        <?= !empty($active_step['waktu']) ? date('H:i', strtotime($active_step['waktu'])) : '00:00' ?> s.d 
                                        <?= !empty($active_step['jam_tutup']) ? date('H:i', strtotime($active_step['jam_tutup'])) : 'Selesai' ?> WIB
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex flex-column align-items-center gap-3">
                                <?php
                                    $statusHadirSesi = $active_step['status_hadir'] ?? null;
                                    $isAlfaSesi = ($statusHadirSesi === 'Alfa');
                                ?>
                                <?php if (isset($active_step['is_attended']) && $active_step['is_attended'] && $isAlfaSesi && $sessionOpen): ?>
                                    <!-- Alfa tapi sesi masih buka: bisa override presensi sendiri -->
                                    <?php if (!empty($active_step['meeting_link'])): ?>
                                        <a href="<?= $active_step['meeting_link'] ?>" target="_blank" class="btn btn-info w-100 py-3 fw-bold rounded-4 text-white shadow-lg hover-scale fs-5">
                                            GABUNG MEETING <i class="fas fa-video ms-2"></i>
                                        </a>
                                    <?php endif; ?>
                                    <div class="text-center w-100">
                                        <div class="mb-2"><span class="badge bg-danger px-4 py-2 rounded-pill fw-bold"><i class="fas fa-times-circle me-1"></i> STATUS: ALFA</span></div>
                                        <p class="text-white-50 small fw-bold">Status Anda saat ini ALFA. Sesi masih buka — presensi sekarang untuk mengubah status.</p>
                                        <a href="<?= base_url('pelatihan/peserta/tandai_selesai/'.$p['id'].'/'.$active_id) ?><?= isset($active_step['sesi_id']) ? '?sesi_id='.$active_step['sesi_id'] : '' ?>" class="btn w-100 py-3 rounded-4 fw-bold shadow-lg hover-scale fs-5 border-0" style="background: #0f766e !important; color: white !important;">
                                            PRESENSI SEKARANG <i class="fas fa-user-check ms-2"></i>
                                        </a>
                                    </div>
                                <?php elseif (isset($active_step['is_attended']) && $active_step['is_attended']): ?>
                                    <?php if (!empty($active_step['meeting_link'])): ?>
                                        <a href="<?= $active_step['meeting_link'] ?>" target="_blank" class="btn btn-info w-100 py-3 fw-bold rounded-4 text-white shadow-lg hover-scale fs-5">
                                            GABUNG MEETING <i class="fas fa-video ms-2"></i>
                                        </a>
                                    <?php endif; ?>
                                    <div class="text-center w-100">
                                        <?php if ($statusHadirSesi === 'Hadir'): ?>
                                            <div class="mb-2"><span class="badge bg-success px-4 py-2 rounded-pill fw-bold"><i class="fas fa-check-circle me-1"></i> STATUS: HADIR</span></div>
                                        <?php elseif ($statusHadirSesi === 'Izin'): ?>
                                            <div class="mb-2"><span class="badge bg-warning text-dark px-4 py-2 rounded-pill fw-bold"><i class="fas fa-exclamation-circle me-1"></i> STATUS: IZIN</span></div>
                                        <?php elseif ($isAlfaSesi): ?>
                                            <div class="mb-2"><span class="badge bg-danger px-4 py-2 rounded-pill fw-bold"><i class="fas fa-times-circle me-1"></i> STATUS: ALFA</span></div>
                                            <p class="text-danger small fw-bold mt-1">Status kehadiran Anda tercatat ALFA. Materi sesi ini tidak dapat diakses.</p>
                                        <?php endif; ?>
                                        <a href="<?= base_url('pelatihan/peserta/belajar/'.$p['id'].'?step='.($active_id + 1)) ?>" class="btn w-100 py-3 rounded-pill fw-bold shadow-lg hover-scale fs-5 mt-3 animate__animated animate__pulse animate__infinite" style="background: var(--primary-red); color: white; border: none;">
                                            SELESAI &amp; LANJUT <i class="fas fa-arrow-right ms-2"></i>
                                        </a>
                                    </div>
                                <?php elseif ($sessionOpen): ?>
                                    <?php if (!empty($active_step['meeting_link'])): ?>
                                        <a href="<?= $active_step['meeting_link'] ?>" target="_blank" class="btn btn-info w-100 py-3 fw-bold rounded-4 text-white shadow-lg hover-scale fs-5">
                                            GABUNG MEETING <i class="fas fa-video ms-2"></i>
                                        </a>
                                    <?php endif; ?>
                                    <a href="<?= base_url('pelatihan/peserta/tandai_selesai/'.$p['id'].'/'.$active_id) ?><?= isset($active_step['sesi_id']) ? '?sesi_id='.$active_step['sesi_id'] : '' ?>" class="btn w-100 py-3 rounded-4 fw-bold shadow-lg hover-scale fs-5 border-0" style="background: #0f766e !important; color: white !important;">
                                        PRESENSI SEKARANG <i class="fas fa-user-check ms-2"></i>
                                    </a>
                                <?php else: ?>
                                    <?php if ($statusHadirSesi === 'Alfa'): ?>
                                        <div class="mb-2"><span class="badge bg-danger px-4 py-2 rounded-pill fw-bold"><i class="fas fa-times-circle me-1"></i> STATUS: ALFA (dipresensi admin)</span></div>
                                        <p class="text-white-50 small fw-bold">Materi pada sesi ini tidak dapat diakses.</p>
                                        <a href="<?= base_url('pelatihan/peserta/belajar/'.$p['id'].'?step='.($active_id + 1)) ?>" class="btn w-100 py-3 rounded-pill fw-bold shadow-lg hover-scale fs-5 mt-2" style="background: var(--primary-red); color: white; border: none;">
                                            LANJUT KE SESI BERIKUTNYA <i class="fas fa-arrow-right ms-2"></i>
                                        </a>
                                    <?php elseif ($statusHadirSesi === 'Izin'): ?>
                                        <div class="mb-2"><span class="badge bg-warning text-dark px-4 py-2 rounded-pill fw-bold"><i class="fas fa-exclamation-circle me-1"></i> STATUS: IZIN (dipresensi admin)</span></div>
                                        <a href="<?= base_url('pelatihan/peserta/belajar/'.$p['id'].'?step='.($active_id + 1)) ?>" class="btn w-100 py-3 rounded-pill fw-bold shadow-lg hover-scale fs-5 mt-2 animate__animated animate__pulse animate__infinite" style="background: var(--primary-red); color: white; border: none;">
                                            SELESAI &amp; LANJUT <i class="fas fa-arrow-right ms-2"></i>
                                        </a>
                                    <?php elseif ($statusHadirSesi === 'Hadir'): ?>
                                        <div class="mb-2"><span class="badge bg-success px-4 py-2 rounded-pill fw-bold"><i class="fas fa-check-circle me-1"></i> STATUS: HADIR (dipresensi admin)</span></div>
                                        <a href="<?= base_url('pelatihan/peserta/belajar/'.$p['id'].'?step='.($active_id + 1)) ?>" class="btn w-100 py-3 rounded-pill fw-bold shadow-lg hover-scale fs-5 mt-2 animate__animated animate__pulse animate__infinite" style="background: var(--primary-red); color: white; border: none;">
                                            SELESAI &amp; LANJUT <i class="fas fa-arrow-right ms-2"></i>
                                        </a>
                                    <?php else: ?>
                                        <?php if (!empty($active_step['meeting_link'])): ?>
                                            <button class="btn btn-secondary w-100 py-3 fw-bold rounded-4 shadow-sm fs-5 border-0" style="cursor: not-allowed;" disabled>
                                                GABUNG MEETING <i class="fas fa-lock ms-2"></i>
                                            </button>
                                        <?php endif; ?>
                                        <button class="btn btn-secondary w-100 py-3 rounded-4 fw-bold shadow-sm fs-5 border-0" style="cursor: not-allowed;" disabled>
                                            PRESENSI SEKARANG <i class="fas fa-lock ms-2"></i>
                                        </button>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        <?php elseif ($active_step['tipe'] == 'materi_segmen') : ?>
                            <?php
                                $sesiPresensiStatus = $presensiStatusList[$active_step['sesi_id']] ?? null;
                                $isAlfaLocked = ($sesiPresensiStatus === 'Alfa');
                            ?>
                            <?php if ($isAlfaLocked): ?>
                                <div class="text-center py-5">
                                    <i class="fas fa-lock fa-5x text-white opacity-25 mb-4"></i>
                                    <h3 class="fw-bold text-white mb-3">MATERI DITUTUP</h3>
                                    <p class="text-white-50 fs-5 mb-0" style="max-width: 500px; margin: 0 auto;">Anda tidak dapat mengakses materi ini karena status kehadiran Anda pada sesi terkait tercatat <strong class="text-danger">ALFA</strong>. Silakan hubungi admin untuk informasi lebih lanjut.</p>
                                </div>
                            <?php else: ?>
                            <div class="text-start p-4">
                                <?php foreach($active_step['materi_list'] as $m): ?>
                                    <div class="bg-white bg-opacity-10 rounded-4 p-4 mb-4 border border-white border-opacity-25">
                                        <h4 class="fw-bold text-warning mb-2"><?= esc($m['judul']) ?></h4>

                                        <?php if(!empty($m['file_path'])): ?>
                                            <div class="mt-3 text-center bg-dark p-3 rounded-3 overflow-hidden">
                                                <?php
                                                    $fileUrl = base_url($m['file_path']);
                                                    echo renderPelatihanFilePreview($m['file_path'], $m['judul'], $fileUrl);
                                                ?>
                                            </div>
                                        <?php endif; ?>

                                        <?php if (!empty($m['deskripsi'])): ?>
                                            <div class="mt-3 text-white-50 lh-lg" style="font-size: 0.9rem;"><?= $m['deskripsi'] ?></div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if ($active_step['tipe'] != 'sesi') : ?>
                <?php
                    $currentSesiPresensiStatus = isset($active_step['sesi_id']) ? ($presensiStatusList[$active_step['sesi_id']] ?? null) : null;
                    $isCurrentAlfaLocked = ($currentSesiPresensiStatus === 'Alfa');
                ?>
                <div class="mt-4 p-4 rounded-4 d-flex justify-content-center align-items-center" style="background: #0f172a;">
                    <?php if ($isCurrentAlfaLocked || $sessionOpen): ?>
                        <a href="<?= base_url('pelatihan/peserta/tandai_selesai/'.$p['id'].'/'.$active_id) ?><?= isset($active_step['sesi_id']) ? '?sesi_id='.$active_step['sesi_id'] : '' ?>" class="btn px-5 py-3 rounded-pill fw-bold shadow-lg hover-scale fs-5 animate__animated animate__pulse animate__infinite" style="background: var(--primary-red); color: white; border: none; flex-shrink: 0;">
                            SELESAI &amp; LANJUT <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                    <?php else: ?>
                        <button class="btn px-5 py-3 rounded-pill fw-bold shadow-sm fs-5" style="background: #475569 !important; color: #94a3b8 !important; cursor: not-allowed; border: none; flex-shrink: 0;" disabled>
                            SELESAI &amp; LANJUT <i class="fas fa-lock ms-2"></i>
                        </button>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

            <?php elseif ($active_step['tipe'] == 'evaluasi_sesi') : ?>
                <?php
                    $sesiId = $active_step['sesi_id'];
                    $isSesiEvalSubmitted = in_array($sesiId, $submittedSesiEvaluations);
                    $materiForSesi = array_values(array_filter($materiList ?? [], fn($m) => (int)($m['sesi_id'] ?? 0) === (int)$sesiId));
                    $narasumberForSesi = array_values(array_filter($narasumberList ?? [], fn($n) => (int)($n['sesi_id'] ?? 0) === (int)$sesiId));
                    $penyelenggaraForSesi = array_values(array_filter($penyelenggaraList ?? [], fn($p) => (int)($p['sesi_id'] ?? 0) === (int)$sesiId));
                    $globalQuestions = $evalQuestions ?? [];
                ?>

                <div class="evaluasi-area py-2">
                    <?php if ($isSesiEvalSubmitted) : ?>
                        <div class="text-center py-5">
                            <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                            <h5 class="fw-bold">Evaluasi Sesi Ini Sudah Dikirim</h5>
                            <p class="text-muted">Anda telah menyelesaikan evaluasi untuk sesi ini. Terima kasih atas penilaian Anda.</p>
                            <a href="<?= base_url('pelatihan/peserta/belajar/'.$p['id'].'?step='.($active_id + 1)) ?>" class="btn btn-selanjutnya mt-3">LANJUT <i class="fas fa-arrow-right ms-2"></i></a>
                        </div>
                    <?php else : ?>
                        <form id="evalSesiForm" action="<?= base_url('pelatihan/peserta/submit_evaluasi_sesi/'.$p['id']) ?>" method="POST">
                            <input type="hidden" name="sesi_id" value="<?= $sesiId ?>">
                            <input type="hidden" name="step_id" value="<?= $active_id ?>">

                            <div class="mb-5">
                                <div class="d-flex align-items-center gap-4 mb-4 p-4 bg-light rounded-4 border-start border-primary border-5">
                                    <div class="bg-primary p-3 rounded-circle text-white shadow-sm">
                                        <i class="fas fa-book fa-2x"></i>
                                    </div>
                                    <div>
                                        <div class="text-muted small fw-bold text-uppercase">Evaluasi Materi Sesi Ini</div>
                                        <h4 class="fw-bold mb-0">Penilaian Materi Pembelajaran</h4>
                                    </div>
                                </div>

                                <?php
                                    $materiQs = [];
                                    foreach ($globalQuestions as $kat => $qs) {
                                        if (strtolower($kat) === 'materi') { $materiQs = $qs; break; }
                                    }
                                ?>
                                <?php if (empty($materiForSesi)): ?>
                                    <div class="alert alert-info">Belum ada materi yang terdaftar di sesi ini.</div>
                                <?php elseif (empty($materiQs)): ?>
                                    <div class="alert alert-warning">Belum ada pertanyaan evaluasi untuk kategori Materi.</div>
                                <?php else: ?>
                                    <?php foreach ($materiForSesi as $materi): ?>
                                        <div class="mb-4 bg-white p-4 rounded-4 shadow-sm border">
                                            <h5 class="fw-bold text-dark mb-3"><i class="fas fa-file-alt me-2 text-primary"></i> <?= esc($materi['judul']) ?></h5>
                                            <?php foreach ($materiQs as $q): ?>
                                                <div class="rating-row mb-4">
                                                    <label class="fw-bold mb-3 d-block"><?= esc($q['pertanyaan']) ?></label>
                                                    <div class="rating-options">
                                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                                            <div class="rating-btn">
                                                                <input type="radio" name="rating_materi[<?= $materi['id'] ?>][<?= $q['id'] ?>]" id="rmateri_<?= $materi['id'] ?>_<?= $q['id'] ?>_<?= $i ?>" value="<?= $i ?>" required>
                                                                <label for="rmateri_<?= $materi['id'] ?>_<?= $q['id'] ?>_<?= $i ?>">
                                                                    <div class="fw-bold"><?= $i ?></div>
                                                                    <?php if ($i == 1): ?><div style="font-size:0.65rem;margin-top:4px;line-height:1;">Sangat Kurang</div><?php endif; ?>
                                                                    <?php if ($i == 5): ?><div style="font-size:0.65rem;margin-top:4px;line-height:1;">Sangat Baik</div><?php endif; ?>
                                                                </label>
                                                            </div>
                                                        <?php endfor; ?>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>

                            <div class="mb-5">
                                <div class="d-flex align-items-center gap-4 mb-4 p-4 bg-light rounded-4 border-start border-success border-5">
                                    <div class="bg-success p-3 rounded-circle text-white shadow-sm">
                                        <i class="fas fa-chalkboard-teacher fa-2x"></i>
                                    </div>
                                    <div>
                                        <div class="text-muted small fw-bold text-uppercase">Evaluasi Narasumber</div>
                                        <h4 class="fw-bold mb-0">Penilaian Narasumber</h4>
                                    </div>
                                </div>
                                <?php
                                    $narasumberQs = [];
                                    foreach ($globalQuestions as $kat => $qs) {
                                        if (strtolower($kat) === 'narasumber') { $narasumberQs = $qs; break; }
                                    }
                                ?>
                                <?php if (empty($narasumberForSesi)): ?>
                                    <div class="alert alert-info">Belum ada narasumber yang terdaftar di sesi ini.</div>
                                <?php elseif (empty($narasumberQs)): ?>
                                    <div class="alert alert-warning">Belum ada pertanyaan evaluasi untuk kategori Narasumber.</div>
                                <?php else: ?>
                                    <?php foreach ($narasumberForSesi as $narasumber): ?>
                                        <div class="mb-4 bg-white p-4 rounded-4 shadow-sm border">
                                            <h5 class="fw-bold text-dark mb-3"><i class="fas fa-user me-2 text-success"></i> <?= esc($narasumber['nama'] ?? '') ?></h5>
                                            <?php foreach ($narasumberQs as $q): ?>
                                                <div class="rating-row mb-4">
                                                    <label class="fw-bold mb-3 d-block"><?= esc($q['pertanyaan']) ?></label>
                                                    <div class="rating-options">
                                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                                            <div class="rating-btn">
                                                                <input type="radio" name="rating_narasumber[<?= $narasumber['id'] ?>][<?= $q['id'] ?>]" id="rnar_sesi_<?= $narasumber['id'] ?>_<?= $q['id'] ?>_<?= $i ?>" value="<?= $i ?>" required>
                                                                <label for="rnar_sesi_<?= $narasumber['id'] ?>_<?= $q['id'] ?>_<?= $i ?>">
                                                                    <div class="fw-bold"><?= $i ?></div>
                                                                    <?php if ($i == 1): ?><div style="font-size:0.65rem;margin-top:4px;line-height:1;">Sangat Kurang</div><?php endif; ?>
                                                                    <?php if ($i == 5): ?><div style="font-size:0.65rem;margin-top:4px;line-height:1;">Sangat Baik</div><?php endif; ?>
                                                                </label>
                                                            </div>
                                                        <?php endfor; ?>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>

                            <div class="mb-5">
                                <div class="d-flex align-items-center gap-4 mb-4 p-4 bg-light rounded-4 border-start border-warning border-5">
                                    <div class="bg-warning p-3 rounded-circle text-dark shadow-sm">
                                        <i class="fas fa-users-cog fa-2x"></i>
                                    </div>
                                    <div>
                                        <div class="text-muted small fw-bold text-uppercase">Evaluasi Penyelenggara</div>
                                        <h4 class="fw-bold mb-0">Penilaian Penyelenggara</h4>
                                    </div>
                                </div>
                                <?php
                                    $penyelenggaraQs = [];
                                    foreach ($globalQuestions as $kat => $qs) {
                                        if (strtolower($kat) === 'penyelenggara') { $penyelenggaraQs = $qs; break; }
                                    }
                                ?>
                                <?php if (empty($penyelenggaraForSesi)): ?>
                                    <div class="alert alert-info">Belum ada penyelenggara yang terdaftar di sesi ini.</div>
                                <?php elseif (empty($penyelenggaraQs)): ?>
                                    <div class="alert alert-warning">Belum ada pertanyaan evaluasi untuk kategori Penyelenggara.</div>
                                <?php else: ?>
                                    <?php foreach ($penyelenggaraForSesi as $penyelenggara): ?>
                                        <div class="mb-4 bg-white p-4 rounded-4 shadow-sm border">
                                            <h5 class="fw-bold text-dark mb-3"><i class="fas fa-building me-2 text-warning"></i> <?= esc($penyelenggara['nama'] ?? '') ?></h5>
                                            <?php foreach ($penyelenggaraQs as $q): ?>
                                                <div class="rating-row mb-4">
                                                    <label class="fw-bold mb-3 d-block"><?= esc($q['pertanyaan']) ?></label>
                                                    <div class="rating-options">
                                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                                            <div class="rating-btn">
                                                                <input type="radio" name="rating_penyelenggara[<?= $penyelenggara['id'] ?>][<?= $q['id'] ?>]" id="rpen_sesi_<?= $penyelenggara['id'] ?>_<?= $q['id'] ?>_<?= $i ?>" value="<?= $i ?>" required>
                                                                <label for="rpen_sesi_<?= $penyelenggara['id'] ?>_<?= $q['id'] ?>_<?= $i ?>">
                                                                    <div class="fw-bold"><?= $i ?></div>
                                                                    <?php if ($i == 1): ?><div style="font-size:0.65rem;margin-top:4px;line-height:1;">Sangat Kurang</div><?php endif; ?>
                                                                    <?php if ($i == 5): ?><div style="font-size:0.65rem;margin-top:4px;line-height:1;">Sangat Baik</div><?php endif; ?>
                                                                </label>
                                                            </div>
                                                        <?php endfor; ?>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>

                            <?php
                                $fasilQs = [];
                                foreach ($globalQuestions as $kat => $qs) {
                                    if (strtolower($kat) === 'fasilitator') { $fasilQs = $qs; break; }
                                }
                            ?>
                            <div class="mb-5">
                                <div class="d-flex align-items-center gap-4 mb-4 p-4 bg-light rounded-4 border-start border-dark border-5">
                                    <div class="bg-dark p-3 rounded-circle text-white shadow-sm">
                                        <i class="fas fa-user-tie fa-2x"></i>
                                    </div>
                                    <div>
                                        <div class="text-muted small fw-bold text-uppercase">Evaluasi Fasilitator</div>
                                        <h4 class="fw-bold mb-0">Fasilitator Sesi: <?= esc($active_step['judul']) ?></h4>
                                    </div>
                                </div>
                                <?php if (empty($fasilQs)): ?>
                                    <div class="alert alert-warning">Belum ada pertanyaan evaluasi untuk kategori Fasilitator.</div>
                                <?php else: ?>
                                    <div class="mb-4 bg-white p-4 rounded-4 shadow-sm border">
                                        <?php foreach ($fasilQs as $q): ?>
                                            <div class="rating-row mb-4">
                                                <label class="fw-bold mb-3 d-block"><?= esc($q['pertanyaan']) ?></label>
                                                <div class="rating-options">
                                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                                        <div class="rating-btn">
                                                            <input type="radio" name="rating_fasilitator[<?= $sesiId ?>][<?= $q['id'] ?>]" id="q_fasil_sesi_<?= $sesiId ?>_<?= $q['id'] ?>_<?= $i ?>" value="<?= $i ?>" required>
                                                            <label for="q_fasil_sesi_<?= $sesiId ?>_<?= $q['id'] ?>_<?= $i ?>">
                                                                <div class="fw-bold"><?= $i ?></div>
                                                                <?php if ($i == 1): ?><div style="font-size:0.65rem;margin-top:4px;line-height:1;">Sangat Kurang</div><?php endif; ?>
                                                                <?php if ($i == 5): ?><div style="font-size:0.65rem;margin-top:4px;line-height:1;">Sangat Baik</div><?php endif; ?>
                                                            </label>
                                                        </div>
                                                    <?php endfor; ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="d-flex gap-3 mt-4">
                                <button type="submit" class="btn py-3 flex-grow-2 rounded-pill fw-bold shadow-lg" style="background: #0f172a !important; color: white !important;">KIRIM EVALUASI SESI <i class="fas fa-paper-plane ms-2"></i></button>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>

            <?php elseif ($active_step['tipe'] == 'pre_test' || $active_step['tipe'] == 'post_test') : ?>
                <?php if ($active_step['tipe'] == 'pre_test' && !empty($pre_test_attempted)) : ?>
                    <div class="alert alert-secondary bg-opacity-10 rounded-4 border-0 p-4">
                        <h5 class="fw-bold mb-2"><i class="fas fa-check-circle me-2 text-success"></i> Pre-Test Sudah Dikerjakan</h5>
                        <p class="mb-4 text-muted">Pre-Test hanya dapat dikerjakan 1 kali. Nilai Anda: <strong class="fs-5"><?= $pre_test_score ?? 0 ?></strong></p>
                        <a href="<?= base_url('pelatihan/peserta/belajar/'.$p['id'].'?step='.($active_id + 1)) ?>" class="btn px-4 py-2 rounded-pill fw-bold shadow-sm" style="background: var(--primary-red); color: white; border: none;">
                            LANJUT KE TAHAP BERIKUTNYA <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                <?php elseif ($active_step['tipe'] == 'post_test' && $post_test_status == 'Lulus' && !isset($_GET['retake'])) : ?>
                    <div class="alert alert-success bg-opacity-10 rounded-4 border-0 p-4">
                        <h5 class="fw-bold mb-3 text-success"><i class="fas fa-check-circle me-2"></i> Post-Test Berhasil Diselesaikan</h5>
                        <div class="d-flex gap-5">
                            <div>
                                <div class="small text-muted fw-bold mb-1">Skor Pre-Test</div>
                                <div class="fs-3 fw-bold text-dark"><?= $pre_test_score ?? 0 ?></div>
                            </div>
                            <div>
                                <div class="small text-muted fw-bold mb-1">Skor Post-Test</div>
                                <div class="fs-3 fw-bold text-success"><?= $post_test_score ?? 0 ?></div>
                            </div>
                        </div>
                        <div class="mt-4 pt-3 border-top border-success border-opacity-25 d-flex gap-3 flex-wrap">
                            <a href="<?= base_url('pelatihan/peserta/belajar/'.$p['id'].'?step='.($active_id + 1)) ?>" class="btn px-5 py-3 rounded-pill fw-bold shadow-lg hover-scale fs-5 animate__animated animate__pulse animate__infinite" style="background: var(--primary-red); color: white; border: none;">
                                SELESAI & LANJUT KE EVALUASI <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                            <?php $sisa = 3 - ($post_test_attempts ?? 0); if ($sisa > 0): ?>
                                <a href="<?= base_url('pelatihan/peserta/belajar/'.$p['id'].'?step='.$active_id.'&retake=1') ?>" class="btn btn-outline-success px-4 py-3 rounded-pill fw-bold border-2">
                                    <i class="fas fa-redo-alt me-2"></i> KERJAKAN ULANG (Sisa <?= $sisa ?>x)
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php elseif ($active_step['tipe'] == 'post_test' && !empty($post_test_attempts) && $post_test_attempts >= 3) : ?>
                    <div class="alert alert-danger rounded-4 border-0 p-4">
                        <h5 class="fw-bold mb-2"><i class="fas fa-lock me-2"></i> Batas Post-Test Telah Tercapai</h5>
                        <p class="mb-0 text-muted">Post-Test hanya dapat dikerjakan maksimal 3 kali. Anda tidak dapat mengulanginya lagi.</p>
                    </div>
                <?php elseif (isset($_GET['error']) && $_GET['error'] == 'score_low') :
                    $lastScore = $_GET['last_score'] ?? 0;
                    $attempts = $_GET['attempts'] ?? 1;
                    $sisa_percobaan = 3 - $attempts;
                ?>
                    <div class="alert alert-danger bg-danger bg-opacity-10 border-danger border-2 text-center p-5 rounded-4 mb-4 mx-auto" style="max-width: 800px; margin-top: 50px;">
                        <h4 class="fw-bold text-danger mb-4"><i class="fas fa-times-circle me-2"></i> Nilai Tidak Memenuhi Standar</h4>
                        
                        <div class="d-flex justify-content-center gap-5 mb-4">
                            <div>
                                <div class="small text-muted fw-bold mb-1">Skor Pre-Test</div>
                                <div class="fs-2 fw-bold text-dark"><?= $pre_test_score ?? 0 ?></div>
                            </div>
                            <div>
                                <div class="small text-muted fw-bold mb-1">Skor Post-Test Saat Ini</div>
                                <div class="fs-2 fw-bold text-danger"><?= $lastScore ?></div>
                            </div>
                        </div>
                        
                        <p class="mb-0 text-muted fs-5">Skor minimal kelulusan adalah <strong><?= $post_test_kkm ?? 70 ?></strong>.</p>
                        <?php if ($sisa_percobaan > 0): ?>
                            <p class="mb-0 fw-bold mt-4 text-danger fs-5">Apakah Anda ingin mengerjakan ulang Post-Test?</p>
                            <p class="mb-4 fw-bold text-danger">Sisa kuota pengerjaan Anda: <?= $sisa_percobaan ?> kali</p>
                            <a href="<?= base_url('pelatihan/peserta/belajar/'.$p['id'].'?step='.$active_id.'&retake=1') ?>" class="btn btn-danger px-5 py-3 rounded-pill fw-bold shadow-sm fs-5">
                                <i class="fas fa-redo-alt me-2"></i> KERJAKAN ULANG SEKARANG
                            </a>
                        <?php else: ?>
                            <p class="mb-0 fw-bold mt-4 text-danger fs-5">Kesempatan Anda telah habis.</p>
                        <?php endif; ?>
                    </div>
                <?php else : ?>
                <div class="test-area py-4" id="quizContainer">
                    <div class="row g-4">
                        <div class="col-lg-9">
                            <div class="d-flex justify-content-between align-items-center mb-5 bg-light p-3 rounded-pill border">
                                <div class="flex-grow-1 mx-4">
                                    <div class="progress" style="height: 12px; border-radius: 20px; background: #e2e8f0;">
                                        <div class="progress-bar bg-dark shadow-sm" id="quizProgress" style="width: 20%"></div>
                                    </div>
                                </div>
                                <span class="fw-bold text-dark me-3" id="quizCounter">SOAL 1/5</span>
                            </div>
                            
                            <div id="questionArea">
                                <!-- Questions will be injected here via JS -->
                            </div>
                            
                            <div class="d-flex justify-content-between mt-5">
                                <button onclick="prevQuestion()" id="btnPrev" class="btn btn-outline-dark px-5 py-3 rounded-pill fw-bold invisible" style="min-width: 220px;">
                                    <i class="fas fa-arrow-left me-2"></i> SEBELUMNYA
                                </button>
                                <button onclick="nextQuestionQuiz()" id="btnNext" class="btn btn-primary shadow-lg px-5 py-3 rounded-pill fw-bold" style="background-color: #3b82f6 !important; border: none !important; min-width: 220px; transition: all 0.3s ease;">
                                    BERIKUTNYA <i class="fas fa-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="card border-0 shadow-sm rounded-4 sticky-top" style="top: 100px; background: #fff;">
                                <div class="card-body p-4">
                                    <h6 class="fw-bold mb-4 text-center">Navigasi Soal</h6>
                                    <div class="d-flex flex-wrap gap-2 justify-content-center" id="quizNavGrid">
                                        <!-- Boxes injected via JS -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    <?php
                        $quizDataJson = "[]";
                        if ($active_step['tipe'] == 'pre_test') {
                            $mapped = array_map(function($q) {
                                $kunci = strtolower(trim($q['jawaban_benar']));
                                return [
                                    'id' => $q['id'],
                                    'q' => $q['pertanyaan'],
                                    'a' => [$q['opsi_a'], $q['opsi_b'], $q['opsi_c'], $q['opsi_d']],
                                    'c' => $kunci == 'a' ? 0 : ($kunci == 'b' ? 1 : ($kunci == 'c' ? 2 : 3)),
                                    'f' => $q['file_path'] ?? null
                                ];
                            }, $preTestQuestions ?? []);
                            $quizDataJson = json_encode($mapped);
                        } elseif ($active_step['tipe'] == 'post_test') {
                            $mapped = array_map(function($q) {
                                $kunci = strtolower(trim($q['jawaban_benar']));
                                return [
                                    'id' => $q['id'],
                                    'q' => $q['pertanyaan'],
                                    'a' => [$q['opsi_a'], $q['opsi_b'], $q['opsi_c'], $q['opsi_d']],
                                    'c' => $kunci == 'a' ? 0 : ($kunci == 'b' ? 1 : ($kunci == 'c' ? 2 : 3)),
                                    'f' => $q['file_path'] ?? null
                                ];
                            }, $postTestQuestions ?? []);
                            $quizDataJson = json_encode($mapped);
                        }
                    ?>
                    const quizData = <?= $quizDataJson ?>;
                    const quizStorageKey = 'quiz_<?= $p['id'] ?>_<?= $active_step['tipe'] ?? '' ?>';

                    let currentQ = 0;
                    let savedIdx = sessionStorage.getItem(quizStorageKey + '_idx');
                    if (savedIdx) {
                        let parsedIdx = parseInt(savedIdx);
                        if (!isNaN(parsedIdx) && parsedIdx >= 0 && parsedIdx < quizData.length) {
                            currentQ = parsedIdx;
                        }
                    }

                    let answers = new Array(quizData.length).fill(null);
                    
                    let savedQuiz = sessionStorage.getItem(quizStorageKey);
                    if (savedQuiz) {
                        let parsedQuiz = JSON.parse(savedQuiz);
                        if (parsedQuiz.length === answers.length) {
                            answers = parsedQuiz;
                        }
                    }

                    function buildAttachmentPreview(filePath, title) {
                        const ext = (filePath || '').split('.').pop().toLowerCase();
                        const url = '<?= base_url() ?>' + filePath;
                        const fileName = (filePath || '').split('/').pop();
                        const displayTitle = title || fileName || 'Lampiran Soal';

                        if (['jpg','jpeg','png','gif','webp'].includes(ext)) {
                            return `<div class="mb-4 text-center"><img src="${url}" class="img-fluid rounded shadow" style="max-height: 400px;" alt="${displayTitle}"></div>`;
                        }

                        if (['mp4','webm','ogg'].includes(ext)) {
                            return `<div class="mb-4 text-center"><video controls class="w-100 rounded shadow" style="max-height: 400px;"><source src="${url}" type="video/${ext}">Browser Anda tidak mendukung pemutaran video.</video></div>`;
                        }

                        if (ext === 'pdf') {
                            return `
                                <div class="mb-4 document-preview-shell shadow-sm">
                                    <div class="document-preview-head">
                                        <span>${displayTitle}</span>
                                    </div>
                                    <iframe src="${url}" class="inline-document-preview" style="min-height: 500px;" title="${displayTitle}"></iframe>
                                </div>`;
                        }

                        if (['doc','docx','xls','xlsx','ppt','pptx'].includes(ext)) {
                            const label = ['xls','xlsx'].includes(ext) ? 'Excel' : (['ppt','pptx'].includes(ext) ? 'PowerPoint' : 'Word');
                            return `
                                <div class="mb-4 document-preview-shell shadow-sm">
                                    <div class="document-preview-head">
                                        <span>${displayTitle}</span>
                                        <a href="${url}" target="_blank" rel="noopener">Unduh file</a>
                                    </div>
                                    <div class="p-4 text-center bg-light" style="min-height: 220px;">
                                        <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                                        <h6 class="fw-bold text-dark">Preview ${label} tidak tersedia di halaman ini</h6>
                                        <p class="text-muted small mb-0">File materi ini tidak dapat dibuka langsung dari ruang belajar.</p>
                                    </div>
                                </div>`;
                        }

                        if (['txt','csv'].includes(ext)) {
                            return `
                                <div class="mb-4 document-preview-shell shadow-sm">
                                    <div class="document-preview-head">
                                        <span>${displayTitle}</span>
                                        <a href="${url}" target="_blank" rel="noopener">Buka tab baru</a>
                                    </div>
                                    <iframe src="${url}" class="inline-document-preview" style="min-height: 500px;" title="${displayTitle}"></iframe>
                                </div>`;
                        }

                        return `<div class="mb-4 text-center p-3 bg-light rounded fw-bold">File pendukung: <a href="${url}" target="_blank" class="text-danger text-decoration-none">${fileName}</a></div>`;
                    }

                    function renderNavGrid() {
                        const navGrid = document.getElementById('quizNavGrid');
                        if (navGrid) {
                            navGrid.innerHTML = quizData.map((_, i) => `
                                <button onclick="gotoQuestion(${i})" class="btn fw-bold p-0 ${currentQ === i ? 'btn-primary text-white border-primary' : (answers[i] !== null ? 'btn-success text-white border-success' : 'btn-outline-secondary')} d-flex align-items-center justify-content-center shadow-sm" style="width: 45px; height: 45px; border-width: 2px;">
                                    ${i + 1}
                                </button>
                            `).join('');
                        }
                    }

                    function gotoQuestion(idx) {
                        currentQ = idx;
                        renderQuestion();
                    }

                    function renderQuestion() {
                        const q = quizData[currentQ];
                        const area = document.getElementById('questionArea');
                        sessionStorage.setItem(quizStorageKey + '_idx', currentQ);
                        
                        let mediaHtml = '';
                        if (q.f) {
                            mediaHtml = buildAttachmentPreview(q.f, q.q || 'Lampiran Soal');
                        }

                        area.innerHTML = `
                            <div class="question-card">
                                ${mediaHtml}
                                <h5 class="fw-bold mb-4">${q.q}</h5>
                                <div class="d-flex flex-column gap-3">
                                    ${q.a.map((opt, i) => `
                                        <div class="quiz-option ${answers[currentQ] === i ? 'selected' : ''}" onclick="selectOption(${i})">
                                            <div class="quiz-option-circle"></div>
                                            <span class="fw-medium">${opt}</span>
                                        </div>
                                    `).join('')}
                                </div>
                            </div>
                        `;

                        document.getElementById('quizProgress').style.width = ((currentQ + 1) / quizData.length * 100) + '%';
                        document.getElementById('quizCounter').innerText = `Soal ${currentQ + 1}/${quizData.length}`;
                        document.getElementById('btnPrev').classList.toggle('invisible', currentQ === 0);
                        document.getElementById('btnNext').innerHTML = currentQ === quizData.length - 1 ? 
                            'Selesai & Kirim <i class="fas fa-paper-plane ms-2"></i>' : 
                            'Berikutnya <i class="fas fa-arrow-right ms-2"></i>';
                        
                        // Apply light blue style if not finished
                        if (currentQ < quizData.length - 1) {
                            document.getElementById('btnNext').style.backgroundColor = '#3b82f6';
                            document.getElementById('btnNext').classList.remove('btn-dark');
                            document.getElementById('btnNext').classList.add('btn-primary');
                        } else {
                            document.getElementById('btnNext').style.backgroundColor = '#0f172a';
                        }
                        renderNavGrid();
                    }

                    function selectOption(idx) {
                        answers[currentQ] = idx;
                        sessionStorage.setItem(quizStorageKey, JSON.stringify(answers));
                        const options = document.querySelectorAll('#questionArea .quiz-option');
                        options.forEach((opt, i) => {
                            if (i === idx) {
                                opt.classList.add('selected');
                            } else {
                                opt.classList.remove('selected');
                            }
                        });
                        renderNavGrid();
                    }

                    function prevQuestion() {
                        if (currentQ > 0) {
                            currentQ--;
                            renderQuestion();
                        }
                    }

                    function nextQuestionQuiz() {
                        if (answers[currentQ] === null) {
                            Swal.fire({ icon: 'warning', title: 'Belum Menjawab', text: 'Silakan pilih salah satu jawaban terlebih dahulu!', confirmButtonColor: '#1a202c' });
                            return;
                        }

                        if (currentQ < quizData.length - 1) {
                            currentQ++;
                            renderQuestion();
                        } else {
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = '<?= base_url('pelatihan/peserta/submit_kuis/' . $p['id']) ?>';
                            
                            const stepInput = document.createElement('input');
                            stepInput.type = 'hidden';
                            stepInput.name = 'step_id';
                            stepInput.value = '<?= $active_id ?>';
                            form.appendChild(stepInput);
                            
                            const tipeInput = document.createElement('input');
                            tipeInput.type = 'hidden';
                            tipeInput.name = 'tipe_ujian';
                            tipeInput.value = '<?= $active_step['tipe'] ?>';
                            form.appendChild(tipeInput);
                            
                            const letters = ['A', 'B', 'C', 'D'];
                            const answerData = answers.map((ansIdx, i) => {
                                return {
                                    soal_id: quizData[i].id,
                                    jawaban: letters[ansIdx]
                                };
                            });
                            
                            const answersInput = document.createElement('input');
                            answersInput.type = 'hidden';
                            answersInput.name = 'answers';
                            answersInput.value = JSON.stringify(answerData);
                            form.appendChild(answersInput);
                            
                            sessionStorage.removeItem(quizStorageKey);
                            sessionStorage.removeItem(quizStorageKey + '_idx');
                            
                            document.body.appendChild(form);
                            form.submit();
                        }
                    }

                    renderQuestion();
                </script>
                <?php endif; ?>
            <?php elseif ($active_step['tipe'] == 'evaluasi') : ?>
                <?php 
                    $post_test_completed = $postTestIndex ? in_array($postTestIndex, $completed_steps) : true;
                ?>

                <div class="evaluasi-area py-2">
                    <?php if (!$post_test_completed) : ?>
                        <div class="text-center py-5">
                            <i class="fas fa-lock fa-4x text-muted mb-3"></i>
                            <h5 class="fw-bold">Evaluasi Belum Terbuka</h5>
                            <p class="text-muted">Anda harus menyelesaikan <strong>Post-Test</strong> terlebih dahulu untuk dapat mengisi evaluasi ini.</p>
                            <a href="<?= base_url('pelatihan/peserta/belajar/'.$p['id'].'?step='.$postTestIndex) ?>" class="btn btn-selanjutnya mt-3">Pergi ke Post-Test</a>
                        </div>
                    <?php elseif ($ratingAlreadySubmitted) : ?>
                        <div class="text-center py-5">
                            <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                            <h5 class="fw-bold">Evaluasi Sudah Dikirim</h5>
                            <p class="text-muted">Anda telah menyelesaikan evaluasi pelatihan ini. Terima kasih atas penilaian Anda.</p>
                            <a href="<?= base_url('pelatihan/peserta/belajar/'.$p['id'].'?step='.$certIndex) ?>" class="btn btn-selanjutnya mt-3">Lihat Sertifikat <i class="fas fa-arrow-right ms-2"></i></a>
                        </div>
                    <?php else : ?>
                        <form id="evaluationForm" action="<?= base_url('pelatihan/peserta/submit_evaluasi/'.$p['id']) ?>" method="POST">

                            <div class="d-flex align-items-center gap-4 mb-5 p-4 bg-light rounded-4 border-start border-danger border-5">
                                <div class="p-3 rounded-circle text-white shadow-sm" style="background: var(--primary-red);">
                                    <i class="fas fa-star fa-2x"></i>
                                </div>
                                <div>
                                    <h4 class="fw-bold mb-1 text-uppercase">Rating &amp; Saran Keseluruhan</h4>
                                    <p class="text-muted small mb-0 fw-bold">Berikan penilaian akhir dan masukan Anda</p>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="fw-bold mb-3 d-block">Rating Keseluruhan Pelatihan (1-5):</label>
                                <div class="rating-options" style="justify-content: flex-start;">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <div class="rating-btn">
                                            <input type="radio" name="rating_umum" id="rumum_<?= $i ?>" value="<?= $i ?>" required>
                                            <label for="rumum_<?= $i ?>">
                                                <div class="fw-bold"><?= $i ?></div>
                                                <?php if ($i == 1): ?><div style="font-size:0.65rem;margin-top:4px;line-height:1;">Sangat Kurang</div><?php endif; ?>
                                                <?php if ($i == 5): ?><div style="font-size:0.65rem;margin-top:4px;line-height:1;">Sangat Baik</div><?php endif; ?>
                                            </label>
                                        </div>
                                    <?php endfor; ?>
                                </div>
                            </div>

                            <div class="mb-5">
                                <label class="fw-bold mb-2">Saran &amp; Masukan Tambahan:</label>
                                <textarea name="saran" class="form-control border-0 bg-light p-3 rounded-4" rows="5" placeholder="Ketik saran atau kritik konstruktif Anda di sini..."></textarea>
                            </div>

                            <div class="d-flex gap-3 mt-4">
                                <button type="submit" class="btn py-3 flex-grow-2 rounded-pill fw-bold shadow-lg" style="background: #0f172a !important; color: white !important;">KIRIM &amp; SELESAIKAN <i class="fas fa-paper-plane ms-2"></i></button>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>

            <?php elseif ($active_step['tipe'] == 'sertifikat') : ?>
                <div class="text-center py-4">
                    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
                    <?php if (empty($sertifikat)) : ?>
                        <div class="success-portal-card p-5 mb-5 mx-auto animate__animated animate__fadeInUp" style="max-width: 800px; background: white; border-radius: 40px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.08); position: relative; overflow: hidden; border: 1px solid #f1f5f9;">
                            <!-- Decorative Background Elements -->
                            <div style="position: absolute; top: -50px; right: -50px; width: 200px; height: 200px; background: rgba(255, 193, 7, 0.05); border-radius: 50%; z-index: 0;"></div>
                            <div style="position: absolute; bottom: -30px; left: -30px; width: 150px; height: 150px; background: rgba(15, 23, 42, 0.05); border-radius: 50%; z-index: 0;"></div>

                            <div class="position-relative" style="z-index: 1;">
                                <div class="mb-5 text-center">
                                    <div class="bg-warning bg-opacity-10 d-inline-block p-4 rounded-circle mb-4 animate__animated animate__bounceIn">
                                        <i class="fas fa-hourglass-half fa-4x text-warning"></i>
                                    </div>
                                    <h2 class="display-6 fw-bold text-dark mb-2 letter-spacing-1">SERTIFIKAT DALAM PROSES</h2>
                                    <div class="badge bg-warning bg-opacity-25 text-dark rounded-pill px-4 py-2 mt-2 fw-bold border border-warning border-opacity-50" style="font-size: 0.9rem;"><i class="fas fa-spinner fa-spin me-2"></i> Sedang Menerbitkan...</div>
                                </div>

                                <div class="p-5 mb-5 rounded-4 text-center shadow-sm" style="background: #f8fafc; border: 1px solid #e2e8f0; position: relative;">
                                    <div style="position: absolute; top: 0; left: 0; right: 0; height: 5px; background: linear-gradient(90deg, var(--primary-red), #f59e0b); border-radius: 4px 4px 0 0;"></div>
                                    <i class="fas fa-quote-left fa-2x text-muted opacity-25 mb-3"></i>
                                    <p class="text-dark fw-bold fs-5 mb-3" style="line-height: 1.6;">Selamat! Anda telah berhasil menyelesaikan pelatihan <br><span class="text-danger"><?= $p['nama'] ?></span></p>
                                    <p class="text-muted mb-0" style="font-size: 1.05rem;">Sertifikat Anda sedang dalam tahap verifikasi akhir dan proses penandatanganan oleh penyelenggara. Anda akan menerima notifikasi segera setelah sertifikat digital resmi Anda siap untuk diunduh.</p>
                                </div>
                                
                                <div class="d-flex justify-content-center">
                                    <a href="<?= base_url('pelatihan/peserta/beranda') ?>" class="btn px-5 py-3 fw-bold rounded-pill fs-5 d-flex align-items-center justify-content-center gap-3 shadow-sm hover-scale" style="border: 2px solid #0f172a; color: #0f172a; background: transparent; transition: all 0.3s ease;">
                                        <i class="fas fa-arrow-left fs-5"></i> KEMBALI KE BERANDA
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                    <script>
                        window.onload = function() {
                            // Trigger Confetti
                            var count = 200;
                            var defaults = { origin: { y: 0.7 } };

                            function fire(particleRatio, opts) {
                                confetti(Object.assign({}, defaults, opts, {
                                    particleCount: Math.floor(count * particleRatio)
                                }));
                            }

                            fire(0.25, { spread: 26, startVelocity: 55 });
                            fire(0.2, { spread: 60 });
                            fire(0.35, { spread: 100, decay: 0.91, scalar: 0.8 });
                            fire(0.1, { spread: 120, startVelocity: 25, decay: 0.92, scalar: 1.2 });
                            fire(0.1, { spread: 120, startVelocity: 45 });

                            // Trigger SweetAlert2
                            Swal.fire({
                                title: '<span class="text-success fw-bold">PELATIHAN SELESAI!</span>',
                                html: 'Selamat! Anda telah menyelesaikan <b><?= $p['nama'] ?></b> dengan hasil yang memuaskan.',
                                icon: 'success',
                                iconColor: '#10b981',
                                confirmButtonText: 'LIHAT HASIL AKHIR',
                                confirmButtonColor: '#1e293b',
                                background: '#fff',
                                backdrop: `rgba(15, 23, 42, 0.4)`,
                                showClass: { popup: 'animate__animated animate__zoomIn' },
                                hideClass: { popup: 'animate__animated animate__fadeOut' }
                            });
                        };
                    </script>

                    <div class="success-portal-card p-5 mb-5 mx-auto animate__animated animate__fadeInUp" style="max-width: 800px; background: white; border-radius: 40px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.08); position: relative; overflow: hidden; border: 1px solid #f1f5f9;">
                        <!-- Decorative Background Elements -->
                        <div style="position: absolute; top: -50px; right: -50px; width: 200px; height: 200px; background: rgba(16, 185, 129, 0.05); border-radius: 50%; z-index: 0;"></div>
                        <div style="position: absolute; bottom: -30px; left: -30px; width: 150px; height: 150px; background: rgba(206, 33, 39, 0.05); border-radius: 50%; z-index: 0;"></div>

                        <div class="position-relative" style="z-index: 1;">
                            <div class="mb-4">
                                <div class="bg-success bg-opacity-10 d-inline-block p-4 rounded-circle mb-4 animate__animated animate__bounceIn animate__delay-1s">
                                    <i class="fas fa-award fa-5x text-success"></i>
                                </div>
                                <h1 class="display-5 fw-bold text-dark mb-2">SELAMAT! ANDA LULUS</h1>
                                <p class="text-muted fw-bold fs-5 mb-5 opacity-75">Sertifikat Digital Anda telah diterbitkan dan siap diunduh.</p>
                            </div>

                            <div class="row g-4 mb-5">
                                <div class="col-md-4">
                                    <div class="p-4 rounded-4 h-100" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                        <small class="text-muted fw-bold d-block mb-2 text-uppercase letter-spacing-1">HASIL AKHIR</small>
                                        <div class="display-6 fw-bold text-dark"><?= $post_test_score ?? 0 ?></div>
                                        <div class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1 mt-2 fw-bold">KOMPETEN</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-4 rounded-4 h-100" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                        <small class="text-muted fw-bold d-block mb-2 text-uppercase letter-spacing-1">TOTAL DURASI</small>
                                        <div class="h3 fw-bold text-dark mb-0"><?= $p['jpl'] ?> JPL</div>
                                        <div class="small text-muted mt-2 fw-bold">Jam Pelajaran</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-4 rounded-4 h-100" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                        <small class="text-muted fw-bold d-block mb-2 text-uppercase letter-spacing-1">PENYELENGGARA</small>
                                        <div class="h6 fw-bold text-dark mb-0 text-truncate" title="<?= $p['penyelenggara'] ?>"><?= strtoupper($p['penyelenggara']) ?></div>
                                        <div class="small text-muted mt-2 fw-bold">Institusi Terdaftar</div>
                                    </div>
                                </div>
                            </div>

                            <div class="p-4 mb-5 rounded-4 d-flex align-items-center gap-4 text-start" style="background: linear-gradient(135deg, #1e293b 0%, #334155 100%); color: white;">
                                <div class="bg-white bg-opacity-10 p-3 rounded-3">
                                    <i class="fas fa-id-badge fa-2x"></i>
                                </div>
                                <div>
                                    <div class="opacity-75 small fw-bold">DITERBITKAN UNTUK</div>
                                    <h4 class="fw-bold mb-0"><?= strtoupper($user['nama_lengkap'] ?? 'Peserta') ?></h4>
                                </div>
                                <div class="ms-auto text-end">
                                    <div class="opacity-50 small fw-bold">TANGGAL TERBIT</div>
                                    <div class="fw-bold"><?= date('d F Y') ?></div>
                                </div>
                            </div>

                            <div class="d-flex flex-column flex-md-row gap-3 justify-content-center">
                                <a href="<?= base_url('pelatihan/peserta/sertifikat_saya') ?>" class="btn px-5 py-3 shadow-lg fw-bold rounded-pill fs-5 d-flex align-items-center justify-content-center gap-2" style="background: var(--primary-red) !important; color: white !important;">
                                    <i class="fas fa-file-download fs-4"></i> UNDUH SERTIFIKAT
                                </a>
                                <a href="<?= base_url('pelatihan/peserta/beranda') ?>" class="btn px-5 py-3 fw-bold rounded-pill fs-5 d-flex align-items-center justify-content-center gap-2 shadow-sm" style="border: 2px solid #0f172a; color: #0f172a; background: transparent;">
                                    <i class="fas fa-home fs-4"></i> KEMBALI KE BERANDA
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>?>
