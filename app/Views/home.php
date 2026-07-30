<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIM DIKLAT RSUD Kota Yogyakarta</title>
    <link rel="icon" type="image/jpeg" href="<?= base_url('assets/img/logo_rs.jpg') ?>">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-color: #c62828;
            --primary-dark: #b71c1c;
            --secondary-color: #000000;
            --light-bg: #f8f9fa;
            --white: #ffffff;
            --font-main: 'Inter', sans-serif;
            --font-heading: 'Outfit', sans-serif;
        }

        body {
            font-family: var(--font-main);
            background-color: var(--light-bg);
            color: #333;
            overflow-x: hidden;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        main {
            flex: 1;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-family: var(--font-heading);
            font-weight: 700;
        }

        /* Navbar */
        .navbar {
            background-color: var(--secondary-color) !important;
            padding: 1rem 0;
            transition: all 0.3s ease;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            letter-spacing: 1px;
            color: var(--white) !important;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.8) !important;
            font-weight: 500;
            margin: 0 10px;
            transition: color 0.3s ease;
        }

        .nav-link:hover,
        .nav-link.active {
            color: var(--primary-color) !important;
        }

        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, var(--primary-color) 0%, #8e0000 100%);
            padding: 70px 0 110px;
            color: var(--white);
            position: relative;
            clip-path: polygon(0 0, 100% 0, 100% 90%, 0% 100%);
        }

        .hero-section h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        }

        .hero-section p {
            font-size: 1.15rem;
            opacity: 0.9;
            max-width: 700px;
            margin: 0 auto;
        }

        /* Module Cards */
        .section-modules {
            margin-top: -90px;
            padding-bottom: 80px;
            position: relative;
            z-index: 10;
        }

        .module-card {
            background: var(--white);
            border-radius: 20px;
            border: none;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            height: 100%;
            text-align: center;
            padding: 40px 30px;
        }

        .module-card:hover {
            transform: translateY(-15px);
            box-shadow: 0 20px 40px rgba(198, 40, 40, 0.15);
        }

        .icon-box {
            width: 80px;
            height: 80px;
            background: rgba(198, 40, 40, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            transition: all 0.3s ease;
        }

        .module-card:hover .icon-box {
            background: var(--primary-color);
            transform: rotateY(360deg);
        }

        .icon-box i {
            font-size: 32px;
            color: var(--primary-color);
            transition: all 0.3s ease;
        }

        .module-card:hover .icon-box i {
            color: var(--white);
        }

        .module-card h3 {
            margin-bottom: 15px;
            color: var(--secondary-color);
        }

        .module-card p {
            color: #666;
            margin-bottom: 25px;
            font-size: 0.95rem;
            line-height: 1.6;
        }

        .btn-module {
            background-color: var(--primary-color);
            color: var(--white);
            border: none;
            padding: 12px 25px;
            border-radius: 30px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            width: 100%;
        }

        .btn-module:hover {
            background-color: var(--primary-dark);
            color: var(--white);
            box-shadow: 0 5px 15px rgba(198, 40, 40, 0.3);
            transform: scale(1.05);
        }

        /* Footer */
        footer {
            background-color: #1a1a1a;
            color: rgba(255, 255, 255, 0.6);
            padding: 40px 0;
            text-align: center;
        }

        footer strong {
            color: var(--white);
        }

        /* Animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fadeIn 0.8s ease forwards;
        }

        .delay-1 {
            animation-delay: 0.2s;
        }

        .delay-2 {
            animation-delay: 0.4s;
        }

        .delay-3 {
            animation-delay: 0.6s;
        }

        @media (max-width: 768px) {
            .navbar-brand {
                font-size: 1.1rem !important;
                white-space: normal;
                line-height: 1.2;
            }
            
            .navbar-brand img {
                height: 40px !important;
                width: auto;
            }

            .hero-section {
                padding: 40px 0 100px;
                clip-path: polygon(0 0, 100% 0, 100% 95%, 0% 100%);
            }

            .hero-section img.rounded-circle {
                height: 90px !important;
                width: auto;
            }

            .hero-section h1 {
                font-size: 1.75rem;
                margin-top: 10px;
            }

            .hero-section p {
                font-size: 0.95rem;
                padding: 0 10px;
            }

            .section-modules {
                margin-top: -60px;
                padding-bottom: 40px;
            }

            .module-card {
                padding: 25px 20px;
            }
            
            .icon-box {
                width: 60px;
                height: 60px;
                margin-bottom: 15px;
            }
            
            .icon-box i {
                font-size: 24px;
            }
        }
    </style>
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="/">
                <img src="<?= base_url('assets/img/logo_rs_jogja.jpg') ?>" alt="Logo RSUD Yogyakarta" height="50" class="me-3">
                SIM DIKLAT RSUD Yogyakarta
            </a>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="hero-section text-center">
        <div class="container">
            <div class="mb-4 animate-fade-in">
                <img src="<?= base_url('assets/img/logo_rs_jogja.jpg') ?>" alt="Logo RSUD Yogyakarta" height="120" class="rounded-circle bg-white p-2 shadow">
            </div>
            <h1 class="animate-fade-in">Sistem Informasi Diklat RSUD Yogyakarta</h1>
            <p class="animate-fade-in delay-1">Mendukung pengelolaan pelatihan, pendidikan, dan riset secara terintegrasi untuk mewujudkan sumber daya manusia kesehatan yang kompeten dan profesional.</p>
        </div>
    </header>

    <!-- Content / Modules Section -->
    <main class="section-modules">
        <div class="container">
            <div class="row g-4">
                <!-- Modul Pelatihan -->
                <div class="col-md-4 animate-fade-in delay-1">
                    <div class="module-card">
                        <div class="icon-box">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                        <h3>Pelatihan</h3>
                        <p>Kelola pelatihan berkelanjutan untuk tenaga medis dan non-medis guna meningkatkan kompetensi dan kualitas layanan kesehatan.</p>
                        <a href="/pelatihan/login" class="btn-module">Masuk Pelatihan</a>
                    </div>
                </div>

                <!-- Modul Pendidikan -->
                <div class="col-md-4 animate-fade-in delay-2">
                    <div class="module-card">
                        <div class="icon-box">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <h3>Pendidikan</h3>
                        <p>Fasilitasi program pendidikan klinis, praktik kerja lapangan (PKL), dan pemagangan bagi mahasiswa dan institusi pendidikan.</p>
                        <a href="/pendidikan" class="btn-module">Masuk Pendidikan</a>
                    </div>
                </div>

                <!-- Modul Riset -->
                <div class="col-md-4 animate-fade-in delay-3">
                    <div class="module-card">
                        <div class="icon-box">
                            <i class="fas fa-microscope"></i>
                        </div>
                        <h3>Riset</h3>
                        <p>Kelola perizinan penelitian, studi dokumentasi, dan pengembangan ilmu pengetahuan kesehatan secara komprehensif.</p>
                        <a href="<?= base_url('repository/catalog') ?>" class="btn-module">Masuk Riset</a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <p class="mb-2"><strong>RSUD Kota Yogyakarta</strong></p>
                    <p class="mb-0">&copy; <?= date('Y') ?> SIM DIKLAT. All Rights Reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>