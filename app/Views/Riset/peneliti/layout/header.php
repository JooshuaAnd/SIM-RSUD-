<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'SIM Diklat RSUD' ?> - Peneliti</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Favicon -->
    <link rel="icon" type="image/jpeg" href="<?= base_url('assets/img/logo_rs.jpg') ?>">
    
    <style>
        :root {
            --primary-red: #c62828;
            --dark-bg: #1a1a1a;
            --sidebar-width: 260px;
        }
        
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8f9fa;
        }
        
        .navbar-dark {
            background-color: var(--dark-bg) !important;
            border-bottom: 3px solid var(--primary-red);
        }
        
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background-color: white;
            box-shadow: 2px 0 10px rgba(0,0,0,0.05);
            padding-top: 70px;
            z-index: 100;
        }
        
        .main-content {
            margin-left: var(--sidebar-width);
            padding-top: 90px;
            padding-bottom: 20px;
            min-height: calc(100vh - 71px);
            padding-right: 25px;
            padding-left: 25px;
        }
        
        .footer-peneliti {
            margin-left: var(--sidebar-width);
            transition: all 0.3s ease;
        }

        .nav-link {
            padding: 12px 20px;
            color: #555;
            font-weight: 500;
            border-left: 4px solid transparent;
            transition: all 0.3s;
        }
        
        .nav-link:hover, .nav-link.active {
            color: var(--primary-red);
            background-color: rgba(198, 40, 40, 0.05);
            border-left-color: var(--primary-red);
        }
        
        .nav-link i {
            width: 25px;
            margin-right: 10px;
        }
        
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            margin-bottom: 24px;
        }
        
        .btn-primary {
            background-color: var(--primary-red);
            border-color: var(--primary-red);
        }
        
        .btn-primary:hover {
            background-color: #b71c1c;
            border-color: #b71c1c;
        }
        
        .badge-active { background-color: #e8f5e9; color: #2e7d32; }
        
        .navbar-brand img {
            height: 40px;
            margin-right: 10px;
        }

        @media (max-width: 991.98px) {
            .sidebar { left: -260px; transition: all 0.3s ease; }
            .main-content { margin-left: 0; padding-left: 15px; padding-right: 15px; transition: all 0.3s ease; }
            .footer-peneliti { margin-left: 0; }
            .sidebar.show { left: 0; }
        }

        @media (max-width: 575.98px) {
            .navbar-brand { font-size: 14px; }
            .navbar-brand img { width: 30px; height: 30px; margin-right: 5px !important; }
            .nav-link.dropdown-toggle { font-size: 13px; padding-right: 0; }
            .main-content { padding-top: 80px; padding-left: 10px; padding-right: 10px; }
            .navbar .container-fluid { padding-left: 10px; padding-right: 10px; }
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container-fluid">
            <button class="navbar-toggler me-2 border-0 d-lg-none" type="button" id="sidebarToggleBtn">
                <span class="navbar-toggler-icon"></span>
            </button>
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="<?= base_url('assets/img/logo_rs.jpg') ?>" alt="Logo" width="40" class="me-2 d-none d-sm-block">
                <span class="fw-bold">SIM DIKLAT <span style="color: var(--primary-red);">RSUD</span></span>
            </a>
            <div class="ms-auto">
                <ul class="navbar-nav mb-2 mb-lg-0">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-white d-flex align-items-center" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <?php if (session()->get('riset_user_foto')): ?>
                                <img src="<?= base_url('uploads/riset/profil/' . session()->get('riset_user_foto')) ?>" class="rounded-circle me-2 object-fit-cover" width="24" height="24" alt="Profile">
                            <?php else: ?>
                                <i class="fas fa-user-circle me-2"></i>
                            <?php endif; ?>
                            <?= session()->get('riset_user_nama') ?? 'Peneliti' ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="<?= base_url('riset/peneliti/profil') ?>"><i class="fas fa-user fa-sm fa-fw me-2 text-gray-400"></i> Profil Saya</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="<?= base_url('riset/logout') ?>"><i class="fas fa-sign-out-alt fa-sm fa-fw me-2"></i> Keluar</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
