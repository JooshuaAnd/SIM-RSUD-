<?php 
$title = $title ?? 'SIM-DIKLAT'; 
// Auto-update training status if end time has passed
try {
    $db = \Config\Database::connect();
    $db->query("UPDATE master_pelatihan SET status = 'Selesai' WHERE CONCAT(jadwal_selesai, ' ', COALESCE(jam_selesai, '23:59:59')) < NOW() AND status IN ('Publish', 'Aktif', 'Sedang Berjalan')");
} catch (\Exception $e) {}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?> - SIM-DIKLAT RSUD KOTA YOGYAKARTA</title>
    <link rel="shortcut icon" type="image/x-icon" href="<?= get_system_favicon() ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-red: #c62828;
            --primary-black: #0f141b;
            --primary-dark: #0f172a;
            --bg-light: #ffffff;
            --surface: #ffffff;
            --surface-soft: #ffffff;
            --border-muted: #ffffff;
        }

        html {
            overflow-x: hidden;
            height: 100%;
        }
        
        body {
            width: 100%;
            max-width: 100%;
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-light);
            color: var(--primary-black);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            padding-top: 80px;
        }
        
        .glass-wrapper-global {
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.95) 0%, rgba(2, 6, 23, 0.98) 100%);
            box-shadow: 0 15px 35px rgba(0,0,0,0.4), inset 0 1px 0 rgba(255,255,255,0.1);
            color: white;
            border-radius: 24px;
            padding: 2rem;
            margin-bottom: 2rem;
            border: 1px solid rgba(255,255,255,0.1);
        }
        
        .glass-card-global {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 16px;
            color: white;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        
        .glass-breadcrumb {
            background: rgba(255, 255, 255, 0.85) !important;
            backdrop-filter: blur(16px) !important;
            -webkit-backdrop-filter: blur(16px) !important;
            border: 1px solid rgba(0, 0, 0, 0.1) !important;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.05) !important;
            position: sticky;
            top: 90px;
            z-index: 1010;
        }
        
        .glass-card-global:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15), inset 0 1px 0 rgba(255,255,255,0.4);
            border-color: rgba(255,255,255,0.5);
            background: rgba(255,255,255,0.15);
        }
        
        .btn-action-global {
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) !important;
        }
        
        .btn-action-global:hover {
            transform: translateY(-5px) scale(1.05);
            box-shadow: 0 15px 25px rgba(0,0,0,0.2) !important;
            filter: brightness(1.15);
        }

        /* Visibility Fixes */
        .text-muted {
            color: #ffffff !important;
            opacity: 0.85 !important;
        }

        .text-dark {
            color: var(--primary-black) !important;
        }

        .notification-popup {
            position: fixed;
            top: 1.5rem;
            right: 1.5rem;
            z-index: 10800;
            width: min(360px, calc(100% - 3rem));
            background: rgba(15, 23, 42, 0.98);
            border: 1px solid rgba(206, 33, 39, 0.35);
            border-radius: 1.5rem;
            box-shadow: 0 30px 70px rgba(15, 23, 42, 0.25);
            color: white;
            overflow: hidden;
            backdrop-filter: blur(12px);
            transition: transform 0.25s ease, opacity 0.25s ease;
        }

        .notification-popup.d-none {
            display: none !important;
        }

        .notification-popup .popup-inner {
            display: grid;
            grid-template-columns: auto 1fr auto;
            gap: 1rem;
            align-items: center;
            padding: 1rem 1rem 1rem 1.2rem;
        }

        .notification-popup .popup-icon {
            width: 42px;
            height: 42px;
            border-radius: 14px;
            background: rgba(206, 33, 39, 0.18);
            display: grid;
            place-items: center;
            color: #fffbfb;
            font-size: 1.2rem;
        }

        .notification-popup .popup-title {
            font-size: 0.95rem;
            font-weight: 800;
            letter-spacing: 0.2px;
            margin-bottom: 0.15rem;
        }

        .notification-popup .popup-message {
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.86);
            line-height: 1.5;
        }

        .notification-popup .popup-close {
            background: transparent;
            border: none;
            color: rgba(255, 255, 255, 0.75);
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: grid;
            place-items: center;
            cursor: pointer;
            transition: color 0.2s ease, transform 0.2s ease;
        }

        .notification-popup .popup-close:hover {
            color: #ffffff;
            transform: scale(1.05);
        }

        .navbar-custom {
            background: rgba(110, 15, 25, 0.90);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            padding: 0.85rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.3);
        }

        .navbar-brand img {
            height: 42px;
            width: auto;
            background: #ffffff;
            padding: 6px;
            border-radius: 12px;
            display: block;
            box-shadow: 0 4px 15px rgba(255, 255, 255, 0.1);
            transition: transform 0.3s ease;
        }
        
        .navbar-brand:hover img {
            transform: scale(1.05) rotate(-2deg);
        }

        .logo-placeholder {
            height: 45px;
            width: 45px;
            background: white;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-red);
            font-size: 1.5rem;
            box-shadow: inset 0 0 0 2px var(--primary-red);
        }

        .nav-link {
            font-weight: 700;
            font-size: 0.95rem;
            color: rgba(255, 255, 255, 0.65) !important;
            padding: 0.5rem 1.2rem !important;
            margin: 0 0.3rem;
            border-radius: 50rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            letter-spacing: 0.3px;
        }

        .nav-link:hover,
        .nav-link.active {
            color: #ffffff !important;
            background: rgba(255, 255, 255, 0.1);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .btn-profile {
            background: rgba(255, 255, 255, 0.15);
            color: white !important;
            border-radius: 12px;
            padding: 0.6rem 1.5rem !important;
            border: 2px solid rgba(255, 255, 255, 0.3);
            font-weight: 800;
            transition: all 0.3s;
        }

        .btn-profile:hover {
            background: var(--primary-red);
            border-color: var(--primary-red);
            transform: translateY(-2px);
        }

        .card {
            border: none;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
            border-radius: 1.5rem;
        }

        .footer-custom {
            background: var(--primary-dark);
            color: white;
            padding: 2.5rem 0 1.5rem;
            margin-top: 1rem;
            border-top: 5px solid var(--primary-red);
        }

        /* Custom breadcrumb styling removed for text-only display */

        .breadcrumb-item a {
            color: #64748b;
            font-weight: 600;
        }

        .breadcrumb-item+.breadcrumb-item::before {
            color: #cbd5e1;
        }

        /* Alert & Toast Styling (Capsule Style) */
        .alert-capsule {
            border: none;
            border-radius: 20px;
            background: #ffffff;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            padding: 1rem 1.5rem;
            color: var(--primary-dark);
            display: inline-flex;
            align-items: center;
            gap: 1rem;
            font-weight: 700;
            margin-bottom: 2rem;
            border-left: 6px solid var(--primary-red);
            animation: slideInUp 0.5s ease-out;
        }

        @keyframes slideInUp {
            from {
                transform: translateY(20px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .alert-icon-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        /* Button Styling */
        .btn-primary-custom {
            background: var(--primary-red) !important;
            color: white !important;
            border: none !important;
            transition: all 0.3s ease;
            font-weight: 800;
            letter-spacing: 0.5px;
            border-radius: 50rem;
        }

        .btn-primary-custom:hover {
            background: #a51a1f !important;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(206, 33, 39, 0.4);
        }

        .btn-dark {
            background: var(--primary-black) !important;
            color: white !important;
            border: none !important;
            font-weight: 800;
            transition: all 0.3s ease;
            border-radius: 50rem;
        }

        .btn-dark:hover {
            background: #1e293b !important;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.3);
        }

        .btn-outline-custom {
            border: 2px solid var(--primary-red);
            color: var(--primary-red);
            font-weight: 800;
            transition: all 0.3s;
            border-radius: 50rem;
        }

        .btn-outline-custom:hover {
            background: var(--primary-red);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(206, 33, 39, 0.4);
        }

        .hover-text-danger {
            transition: all 0.3s ease;
            display: inline-block;
        }

        .hover-text-danger:hover {
            color: var(--primary-red) !important;
            transform: translateX(5px);
        }

        .footer-icon {
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.05);
        }

        .footer-icon:hover {
            background: var(--primary-red);
            color: white !important;
            transform: translateY(-3px) scale(1.1);
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-custom fixed-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="<?= site_url('pelatihan/peserta/beranda') ?>">
                <img src="<?= get_system_logo() ?>" alt="Logo" class="me-3" style="background: white; padding: 4px; border-radius: 12px; height: 40px;">
                <div>
                    <span class="fw-bold text-white mb-0 d-block" style="font-size: clamp(0.9rem, 3vw, 1.2rem); line-height: 1; letter-spacing: -0.5px;">SIM DIKLAT <span style="color: var(--primary-red);">RSUD KOTA YOGYAKARTA</span></span>
                </div>
            </a>
            <button class="navbar-toggler border-0 text-white" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <i class="fas fa-bars"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <?php
                $uri = service('request')->getUri()->getPath();
                $segments = explode('/', trim($uri, '/'));
                
                $isHomeActive = in_array('beranda', $segments) || in_array('dashboard', $segments) || (count($segments) > 0 && end($segments) == 'peserta');
                $isKatalogActive = in_array('pembelajaran', $segments) && !in_array('pembelajaran_saya', $segments);
                $isDiklatSayaActive = in_array('pembelajaran_saya', $segments) || in_array('belajar', $segments) || in_array('learn', $segments);
                $isSertifikatActive = in_array('sertifikat_saya', $segments) || in_array('sertifikat', $segments) || in_array('unduh_sertifikat', $segments) || in_array('upload_sertifikat', $segments) || in_array('edit_sertifikat', $segments);
                ?>
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link <?= $isHomeActive ? 'active' : '' ?>" href="<?= site_url('pelatihan/peserta/beranda') ?>">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link <?= $isKatalogActive ? 'active' : '' ?>" href="<?= site_url('pelatihan/peserta/pembelajaran') ?>">Katalog</a></li>
                    <li class="nav-item"><a class="nav-link <?= $isDiklatSayaActive ? 'active' : '' ?>" href="<?= site_url('pelatihan/peserta/pembelajaran_saya') ?>">Diklat Saya</a></li>
                    <li class="nav-item"><a class="nav-link <?= $isSertifikatActive ? 'active' : '' ?>" href="<?= site_url('pelatihan/peserta/sertifikat_saya') ?>">Sertifikat</a></li>
                    <li class="nav-item dropdown ms-lg-3">
                        <?php
                        $notifCount = 0;
                        if (session()->get('user_id')) {
                            $db = \Config\Database::connect();
                            $notifCount = $db->table('notifikasi_pelatihan')->where('user_id', session()->get('user_id'))->where('is_read', 0)->countAllResults();
                        }
                        ?>
                        <a class="nav-link dropdown-toggle btn-profile px-4 shadow-sm position-relative" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-2 text-warning"></i> <?= session()->get('nama') ?>
                            <?php if ($notifCount > 0): ?>
                                <span class="position-absolute top-0 start-100 translate-middle p-2 bg-danger border border-light rounded-circle">
                                    <span class="visually-hidden">New alerts</span>
                                </span>
                            <?php endif; ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg mt-3 p-3 rounded-lg" style="min-width: 200px;">
                            <li><a class="dropdown-item py-2 px-3 rounded-lg fw-bold small mb-1" href="<?= site_url('pelatihan/peserta/profil') ?>"><i class="fas fa-user-edit me-2 text-danger"></i> Profil Saya</a></li>
                            <li>
                                <a class="dropdown-item py-2 px-3 rounded-lg fw-bold small mb-1 d-flex justify-content-between align-items-center" href="<?= site_url('pelatihan/peserta/notifikasi') ?>">
                                    <span><i class="fas fa-bell me-2 text-warning"></i> Notifikasi</span>
                                    <?php if ($notifCount > 0): ?>
                                        <span class="badge bg-danger rounded-pill"><?= $notifCount ?></span>
                                    <?php endif; ?>
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider opacity-10">
                            </li>
                            <li><a class="dropdown-item py-2 px-3 rounded-lg fw-bold small text-danger" href="<?= site_url('pelatihan/logout') ?>"><i class="fas fa-power-off me-2"></i> Keluar</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="notification-popup d-none" id="globalNotificationPopup" role="alert" aria-live="polite" aria-atomic="true">
        <div class="popup-inner">
            <div class="popup-icon"><i class="fas fa-bell"></i></div>
            <div>
                <div class="popup-title" id="popupTitle">Notifikasi</div>
                <div class="popup-message" id="popupMessage">Pesan notifikasi akan muncul di sini.</div>
            </div>
            <button type="button" class="popup-close" onclick="hidePopupNotification()" aria-label="Tutup notifikasi">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>

    <?php 
        $isKatalogDetail = in_array('detail_pelatihan', $segments);
        $isKatalogIndex = in_array('pembelajaran', $segments) && !in_array('pembelajaran_saya', $segments);
        $isDiklatSayaDetail = in_array('belajar', $segments) || in_array('learn', $segments) || in_array('materi', $segments) || in_array('kuis', $segments) || in_array('evaluasi', $segments);
        $isSertifikatDetail = in_array('upload_sertifikat', $segments) || in_array('edit_sertifikat', $segments) || in_array('unduh_sertifikat', $segments);
        
        $isFullWidth = true;
        
        $isMainTab = $isHomeActive || ($isKatalogActive && !$isKatalogDetail) || ($isDiklatSayaActive && !$isDiklatSayaDetail) || ($isSertifikatActive && !$isSertifikatDetail) || in_array('profil', $segments) || in_array('notifikasi', $segments);
        
        // Hide breadcrumb in layout for Katalog Detail so we can render it custom in the view
        $hideBreadcrumb = $isMainTab || $isKatalogDetail;
    ?>
    <div class="<?= $isFullWidth ? 'container-fluid px-0' : 'container' ?> pt-3 pb-0" style="flex: 1 0 auto;">
        <div class="row m-0">
            <div class="col-12 <?= $isFullWidth ? 'px-4 px-lg-5' : '' ?>">
                <?php if (!$hideBreadcrumb): ?>
                <nav aria-label="breadcrumb" class="glass-breadcrumb px-4 py-2 mb-4 d-inline-block" style="border-radius: 12px;">
                    <ol class="breadcrumb mb-0 align-items-center">
                            <?php if ($isKatalogDetail): ?>
                                <li class="breadcrumb-item"><a href="<?= site_url('pelatihan/peserta/pembelajaran') ?>" class="text-decoration-none text-danger fw-bold">KATALOG</a></li>
                            <?php elseif ($isDiklatSayaDetail): ?>
                                <li class="breadcrumb-item"><a href="<?= site_url('pelatihan/peserta/pembelajaran_saya') ?>" class="text-decoration-none text-danger fw-bold">DIKLAT SAYA</a></li>
                            <?php elseif ($isSertifikatDetail): ?>
                                <li class="breadcrumb-item"><a href="<?= site_url('pelatihan/peserta/sertifikat_saya') ?>" class="text-decoration-none text-danger fw-bold">SERTIFIKAT SAYA</a></li>
                            <?php endif; ?>

                            <?php if (in_array('detail_pelatihan', $segments)): ?>
                                <li class="breadcrumb-item active fw-bold text-warning" aria-current="page">DETAIL PELATIHAN</li>
                            <?php elseif (in_array('belajar', $segments) || in_array('learn', $segments)): ?>
                                <li class="breadcrumb-item active fw-bold text-warning" aria-current="page">PROSES BELAJAR</li>
                            <?php elseif (in_array('upload_sertifikat', $segments)): ?>
                                <li class="breadcrumb-item active fw-bold text-warning" aria-current="page">UPLOAD</li>
                            <?php elseif (in_array('edit_sertifikat', $segments)): ?>
                                <li class="breadcrumb-item active fw-bold text-warning" aria-current="page">EDIT</li>
                            <?php else: ?>
                                <li class="breadcrumb-item active fw-bold text-warning" aria-current="page"><?= strtoupper($title) ?></li>
                            <?php endif; ?>
                    </ol>
                </nav>
                <?php endif; ?>
            </div>
        </div>

        <?= $this->renderSection('content') ?>
    </div>

    <footer class="text-white pt-4 pb-3 mt-auto" style="background-color: #000000; z-index: 1020; flex-shrink: 0;">
        <div class="container">
            <div class="row g-4 mb-3">
                <div class="col-md-6">
                    <div class="d-flex align-items-center mb-4">
                        <img src="<?= get_system_logo() ?>" alt="Logo" height="60" class="bg-white p-2 rounded-lg me-3">
                        <div>
                            <h4 class="fw-bold mb-0 text-white">SIM DIKLAT <span style="color: var(--primary-red);">RSUD KOTA YOGYAKARTA</span></h4>
                        </div>
                    </div>
                    <p class="small opacity-75 pe-md-5 lh-lg">Sistem Informasi Manajemen Pelatihan Terpadu untuk pengembangan kompetensi berkelanjutan bagi seluruh tenaga medis dan non-medis RSUD Kota Yogyakarta.</p>
                </div>
                <div class="col-md-3">
                    <h6 class="fw-bold mb-4 text-uppercase small letter-spacing-1">Tautan Cepat</h6>
                    <ul class="list-unstyled small opacity-75">
                        <li class="mb-3"><a href="<?= site_url('pelatihan/peserta/beranda') ?>" class="text-white text-decoration-none hover-text-danger">Beranda</a></li>
                        <li class="mb-3"><a href="<?= site_url('pelatihan/peserta/pembelajaran') ?>" class="text-white text-decoration-none hover-text-danger">Katalog</a></li>
                        <li class="mb-3"><a href="<?= site_url('pelatihan/peserta/pembelajaran_saya') ?>" class="text-white text-decoration-none hover-text-danger">Diklat Saya</a></li>
                        <li class="mb-3"><a href="<?= site_url('pelatihan/peserta/sertifikat_saya') ?>" class="text-white text-decoration-none hover-text-danger">Sertifikat</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h6 class="fw-bold mb-4 text-uppercase small letter-spacing-1">Hubungi Kami</h6>
                    <div class="d-flex gap-3 mb-3 align-items-center">
                        <div class="footer-icon"><i class="fas fa-map-marker-alt text-danger"></i></div>
                        <p class="small opacity-75 mb-0" style="transition: all 0.3s ease;">Jl. Wirosaban No.1, Sorosutan, Kota Yogyakarta</p>
                    </div>
                    <div class="d-flex gap-3 mb-3 align-items-center">
                        <div class="footer-icon"><i class="fas fa-phone text-danger"></i></div>
                        <p class="small opacity-75 mb-0" style="transition: all 0.3s ease;">(0274) 371150</p>
                    </div>
                    <div class="d-flex gap-3 align-items-center">
                        <div class="footer-icon"><i class="fas fa-envelope text-danger"></i></div>
                        <p class="small opacity-75 mb-0" style="transition: all 0.3s ease;">diklat@rsud.jogjakota.go.id</p>
                    </div>
                </div>
            </div>
            <hr class="my-4 border-white opacity-10">
            <div class="d-flex justify-content-between align-items-center small opacity-50">
                <div>&copy; 2026 RSUD Kota Yogyakarta.</div>
                <div class="d-flex gap-4">
                    <a href="#" class="text-white text-decoration-none hover-text-danger">Privacy Policy</a>
                    <a href="#" class="text-white text-decoration-none hover-text-danger">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Auto refresh every 60 seconds to keep data valid and updated
        setTimeout(() => {
            window.location.reload();
        }, 60000);

        window.notify = function(title, text, icon = 'success') {
            Swal.fire({
                title: `<span class="fw-bold fs-3 mt-3 d-block">${title === 'success' ? 'Berhasil' : title}</span>`,
                html: `<p class="text-muted fw-bold px-3">${text}</p>`,
                icon: icon,
                showConfirmButton: true,
                confirmButtonText: 'OK',
                confirmButtonColor: '#c62828',
                padding: '2.5rem',
                buttonsStyling: true,
                customClass: {
                    popup: 'rounded-4 shadow-lg border-0',
                    confirmButton: 'rounded-pill px-5 py-2 fw-bold text-uppercase'
                }
            });
        };

        window.confirmAction = function(title, text, callback) {
            Swal.fire({
                title: `<span class="fw-bold fs-3 mt-3 d-block">${title}</span>`,
                html: `<p class="text-muted fw-bold px-3">${text}</p>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#c62828',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Lanjutkan!',
                cancelButtonText: 'Batal',
                padding: '2.5rem',
                customClass: {
                    popup: 'rounded-4 shadow-lg border-0',
                    confirmButton: 'rounded-pill px-4 py-2 fw-bold',
                    cancelButton: 'rounded-pill px-4 py-2 fw-bold'
                }
            }).then((result) => {
                if (result.isConfirmed) callback();
            });
        };

        window.showPopupNotification = function(title, text, icon = 'info') {
            const popup = document.getElementById('globalNotificationPopup');
            const popupTitle = document.getElementById('popupTitle');
            const popupMessage = document.getElementById('popupMessage');

            popupTitle.innerHTML = title;
            popupMessage.innerHTML = text;
            popup.classList.remove('d-none');
            popup.style.transform = 'translateY(0)';
            popup.style.opacity = '1';

            if (window._popupTimeout) {
                clearTimeout(window._popupTimeout);
            }
            window._popupTimeout = setTimeout(() => {
                hidePopupNotification();
            }, 4500);
        };

        window.hidePopupNotification = function() {
            const popup = document.getElementById('globalNotificationPopup');
            if (!popup) return;
            popup.style.transform = 'translateY(-20px)';
            popup.style.opacity = '0';
            setTimeout(() => popup.classList.add('d-none'), 250);
        };

        // Handle Session Flashdata
        <?php if (session()->getFlashdata('success')) : ?>
            window.showPopupNotification('Berhasil!', '<?= session()->getFlashdata('success') ?>', 'success');
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')) : ?>
            window.showPopupNotification('Gagal!', '<?= session()->getFlashdata('error') ?>', 'error');
        <?php endif; ?>

        <?php if (session()->getFlashdata('warning')) : ?>
            window.showPopupNotification('Peringatan!', '<?= session()->getFlashdata('warning') ?>', 'warning');
        <?php endif; ?>

        // Prevent default for dummy links
        $(document).on('click', 'a[href="#"]', function(e) {
            e.preventDefault();
        });
    </script>
    <?= $this->renderSection('scripts') ?>
</body>

</html>