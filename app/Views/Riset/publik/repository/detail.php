<?php

/** @var array $data */ ?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Detail Publikasi' ?> - SIM DIKLAT RSUD Yogyakarta</title>
    <!-- Favicon -->
    <link rel="icon" type="image/jpeg" href="<?= base_url('assets/img/logo_rs.jpg') ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f0f2f5;
        }

        .bg-dark-header {
            background-color: #1a1a1a;
        }

        .publication-header {
            background-color: #222;
            color: white;
            padding: 30px 0 80px;
        }

        .category-badge {
            background: rgba(229, 57, 53, 0.2);
            color: #ff5252;
            font-size: 11px;
            font-weight: 700;
            border-radius: 4px;
            padding: 4px 12px;
        }

        .active-menu {
            color: #e53935 !important;
            border-bottom: 2px solid #e53935;
            padding-bottom: 5px;
        }

        .abstract-card {
            border-radius: 16px;
            margin-top: -60px;
            z-index: 10;
            position: relative;
            border: none;
        }

        .btn-request {
            background: #e53935;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 30px;
            font-weight: 700;
            transition: transform 0.2s;
        }

        .btn-request:hover {
            background: #c62828;
            color: white;
            transform: scale(1.05);
        }
    </style>
</head>

<body>

    <!-- Header -->
    <nav class="navbar navbar-dark bg-dark-header py-3 px-4">
        <div class="container-fluid d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 gap-md-0">
            <a class="navbar-brand fw-bold mb-0" href="<?= base_url('repository/catalog') ?>" style="font-size: 18px; letter-spacing: 1px;">
                SIM <span style="color: #e53935;">DIKLAT</span> RSUD Yogyakarta
            </a>
            <div class="d-flex align-items-center gap-4">
                <a href="<?= base_url('repository/catalog') ?>" class="text-white text-decoration-none fw-bold <?= (strpos(uri_string(), 'repository') !== false) ? 'active-menu' : '' ?>" style="font-size: 12px; letter-spacing: 1px;">KATALOG</a>
                <a href="<?= base_url('riset/login') ?>" class="btn btn-outline-light rounded-pill px-4" style="font-size: 11px; font-weight: 600;">LOGIN AKSES</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="publication-header px-4">
        <div class="container">
            <div class="d-flex align-items-center gap-3 mb-4">
                <a href="<?= base_url('repository/catalog') ?>" class="btn btn-outline-light btn-sm rounded-pill" style="font-size: 12px; font-weight: 600; padding: 6px 18px; border-color: rgba(255,255,255,0.2); transition: all 0.2s;">
                    <i class="fas fa-arrow-left me-2"></i> Kembali ke Katalog
                </a>
                <span class="category-badge d-inline-block text-uppercase m-0"><?= esc($data['kategori_jurnal'] ?? 'Penelitian') ?></span>
            </div>
            <h1 class="fw-bold mb-4" style="line-height: 1.3; font-size: 32px; max-width: 900px;"><?= esc($data['judul']) ?></h1>
            <div class="d-flex flex-wrap gap-4 text-white opacity-75" style="font-size: 13px;">
                <div><i class="fas fa-users me-2"></i> <?= esc($data['nama']) ?></div>
                <div><i class="fas fa-university me-2"></i> <?= esc($data['institusi']) ?></div>
                <div><i class="fas fa-calendar-alt me-2"></i> <?= !empty($data['waktu_selesai']) ? date('Y', strtotime($data['waktu_selesai'])) : date('Y', strtotime($data['created_at'])) ?></div>
            </div>
        </div>
    </section>

    <!-- Content -->
    <div class="container pb-5 px-4">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card abstract-card shadow p-5">
                    <h5 class="fw-bold text-dark mb-4">Abstrak</h5>
                    <p class="text-muted" style="line-height: 1.8; font-size: 15px; text-align: justify;"><?= nl2br(esc($data['abstrak'])) ?></p>

                    <!-- Informasi Publikasi Jurnal / Artikel -->
                    <div class="row mt-5 pt-4 border-top">
                        <div class="col-md-12">
                            <h5 class="fw-bold text-dark mb-4"><i class="fas fa-info-circle text-danger me-2"></i>Informasi Publikasi Jurnal / Artikel</h5>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="p-3 bg-light rounded-3 border-start border-danger border-3">
                                <span class="text-muted small d-block">JENIS JURNAL</span>
                                <span class="fw-bold text-dark" style="font-size: 14px;"><?= esc($data['jenis_jurnal'] ?? 'Internasional') ?></span>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="p-3 bg-light rounded-3 border-start border-danger border-3">
                                <span class="text-muted small d-block">NAMA PUBLIKASI</span>
                                <span class="fw-bold text-dark" style="font-size: 14px;"><?= esc($data['nama_publikasi'] ?? '-') ?></span>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="p-3 bg-light rounded-3 border-start border-danger border-3">
                                <span class="text-muted small d-block">ISSN / E-ISSN</span>
                                <span class="fw-bold text-dark" style="font-size: 14px;"><?= esc($data['issn'] ?? '-') ?></span>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="p-3 bg-light rounded-3 border-start border-danger border-3">
                                <span class="text-muted small d-block">SCOPE / BIDANG</span>
                                <span class="fw-bold text-dark" style="font-size: 14px;"><?= esc($data['scope'] ?? '-') ?></span>
                            </div>
                        </div>
                        <?php if (!empty($data['alamat_web'])): ?>
                            <div class="col-md-12">
                                <div class="p-3 bg-light rounded-3 border-start border-danger border-3 d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="text-muted small d-block">ALAMAT WEB (URL) JURNAL</span>
                                        <a href="<?= esc($data['alamat_web']) ?>" target="_blank" class="fw-bold text-danger text-break text-decoration-none" style="font-size: 14px;">
                                            <?= esc($data['alamat_web']) ?> <i class="fas fa-external-link-alt ms-1 small"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Informasi Akses File Fisik -->
                    <div class="mt-5 p-4 bg-light rounded-4 text-center border-start border-danger border-4">
                        <div class="mb-3 text-danger">
                            <i class="fas fa-hospital-user" style="font-size: 40px;"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-2" style="font-size: 16px;">Akses Laporan & File Lengkap Penelitian</h6>
                        <p class="text-muted small mx-auto mb-0" style="max-width: 700px; line-height: 1.6; font-size: 13.5px;">
                            Demi menjaga kerahasiaan data pasien, etika penelitian, dan hak cipta penerbitan, dokumen lengkap laporan hasil penelitian, naskah publikasi, atau draf jurnal <strong>tidak dipublikasikan secara online secara bebas</strong>.
                            <br><br>
                            Bagi akademisi, praktisi, atau peneliti yang membutuhkan akses lengkap untuk referensi ilmiah, silakan <strong>datang langsung ke Unit Pendidikan & Pelatihan (Diklat) dan Perpustakaan RSUD Kota Yogyakarta</strong>.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="py-5 text-center text-muted small">
        <p>© 2026 RSUD Kota Yogyakarta. All rights reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>