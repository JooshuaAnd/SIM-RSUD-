<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'SIM Diklat RSUD') ?> - Admin Diklat</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- Favicon -->
    <link rel="icon" type="image/jpeg" href="<?= base_url('assets/img/logo_rs.jpg') ?>">
    <!-- JQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <?php $adminCsrf = \App\Filters\PendidikanAdminCsrfFilter::security(); ?>
    <meta name="admin-csrf-header" content="<?= esc($adminCsrf->getHeaderName(), 'attr') ?>">
    <meta name="admin-csrf-token" content="<?= esc($adminCsrf->getHash(), 'attr') ?>">
    <script>
        function escapeAdminDiklatHtml(value) {
            const element = document.createElement('span');
            element.textContent = String(value == null ? '' : value);
            return element.innerHTML;
        }

        (function() {
            const adminBase = new URL(<?= json_encode(base_url('pendidikan/admin/diklat/'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>, location.href);
            const header = document.querySelector('meta[name="admin-csrf-header"]').content;
            const token = document.querySelector('meta[name="admin-csrf-token"]').content;
            function needsToken(url, method) {
                const target = new URL(url, location.href);
                return target.origin === adminBase.origin
                    && target.pathname.startsWith(adminBase.pathname)
                    && /^(POST|PUT|PATCH|DELETE)$/i.test(method);
            }
            $.ajaxPrefilter(function(options, original, xhr) {
                if (needsToken(options.url, options.type || 'GET')) {
                    xhr.setRequestHeader(header, token);
                }
            });
            const originalFetch = window.fetch;
            window.fetch = function(input, options) {
                const init = Object.assign({}, options);
                const isRequest = input instanceof Request;
                if (needsToken(isRequest ? input.url : input, init.method || (isRequest ? input.method : 'GET'))) {
                    const headers = new Headers(init.headers || (isRequest ? input.headers : undefined));
                    headers.set(header, token);
                    init.headers = headers;
                }
                return originalFetch.call(this, input, init);
            };
        })();
    </script>

    <style>
        :root {
            --primary-red: #c62828;
            --dark-bg: #1a1a1a;
            --sidebar-width: 260px;
        }

        body {
            font-family: 'Inter', sans-serif;
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
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.05);
            padding-top: 70px;
            z-index: 100;
            transition: left 0.3s ease;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            padding-top: 90px;
            padding-bottom: 40px;
            min-height: 100vh;
        }

        .nav-link {
            padding: 12px 20px;
            color: #555;
            font-weight: 500;
            border-left: 4px solid transparent;
            transition: all 0.3s;
        }

        .sidebar .nav-link:hover {
            color: #343a40;
            background-color: #fff0f0;
            border-left-color: transparent;
        }

        .sidebar .nav-link.active,
        .sidebar .nav-link.active:hover {
            color: var(--primary-red);
            background-color: #fde2e2;
            border-left-color: var(--primary-red);
        }

        .nav-link i {
            width: 25px;
            margin-right: 10px;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
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

        .badge-menunggu {
            background-color: #ff9800;
        }

        .badge-disetujui {
            background-color: #4caf50;
        }

        .badge-ditolak {
            background-color: #f44336;
        }

        .badge-revisi {
            background-color: #2196f3;
        }

        .navbar-brand img {
            height: 40px;
            margin-right: 10px;
        }

        .table>thead {
            background-color: #f8f9fa;
        }

        .table>thead th {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #6c757d;
            border-bottom: 2px solid #dee2e6;
        }

        @media (max-width: 991.98px) {
            .sidebar {
                left: calc(var(--sidebar-width) * -1);
            }

            .main-content {
                margin-left: 0;
            }

            .sidebar.show {
                left: 0;
            }
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="<?= base_url('assets/img/logo_rs.jpg') ?>" alt="Logo" width="40" class="me-2">
                <span class="fw-bold">SIM DIKLAT <span style="color: var(--primary-red);">RSUD</span></span>
            </a>
            <button class="navbar-toggler" type="button" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end">
                <ul class="navbar-nav mb-2 mb-lg-0">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-white" href="#" id="navbarDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle me-1"></i> <?= esc(session()->get('name') ?? 'Admin Diklat') ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <?php if (session()->get('role') !== 'superadmin') : ?>
                                <li><a class="dropdown-item" href="<?= base_url('pendidikan/admin/diklat') ?>"><i class="fas fa-tachometer-alt fa-sm fa-fw me-2 text-gray-400"></i> Dashboard</a></li>
                            <?php endif; ?>
                            <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#modalGantiPasswordAdmin"><i class="fas fa-key fa-sm fa-fw me-2 text-gray-400"></i> Ganti Password</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="<?= base_url('pendidikan/logout') ?>"><i class="fas fa-sign-out-alt fa-sm fa-fw me-2 text-gray-400"></i> Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
