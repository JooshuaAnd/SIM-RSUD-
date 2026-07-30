<div class="sidebar shadow-sm" id="sidebarMenu">
    <div class="d-flex flex-column h-100">
        <div class="p-3 mb-2">
            <div class="d-flex align-items-center p-2 bg-light rounded-3">
                <img src="https://ui-avatars.com/api/?name=Admin+Diklat&background=c62828&color=fff" class="rounded-circle me-2" width="40">
                <div class="overflow-hidden">
                    <h6 class="mb-0 text-dark fw-bold small text-truncate">Admin Diklat</h6>
                    <span class="text-muted" style="font-size: 10px;">Admin (Riset)</span>
                </div>
            </div>
        </div>

        <nav class="nav flex-column flex-grow-1">
            <div class="px-3 mb-2 mt-2">
                <label class="text-uppercase fw-bold text-muted" style="font-size: 10px; letter-spacing: 1px;">Menu Utama</label>
            </div>
            
            <a class="nav-link <?= (!isset($active_menu) || $active_menu == 'dashboard') ? 'active' : '' ?>" href="<?= base_url('riset/admin/dashboard') ?>">
                <i class="fas fa-th-large"></i> Dashboard
            </a>

            <div class="px-3 mb-2 mt-4">
                <label class="text-uppercase fw-bold text-muted" style="font-size: 10px; letter-spacing: 1px;">Operasional</label>
            </div>

            <a class="nav-link <?= (isset($active_menu) && $active_menu == 'review') ? 'active' : '' ?>" href="<?= base_url('riset/admin/review') ?>">
                <i class="fas fa-microscope"></i> Studi Pendahuluan
            </a>

            <a class="nav-link <?= (isset($active_menu) && $active_menu == 'izin') ? 'active' : '' ?>" href="<?= base_url('riset/admin/izin') ?>">
                <i class="fas fa-file-signature"></i> Izin Penelitian
            </a>

            <a class="nav-link <?= (isset($active_menu) && $active_menu == 'publikasi') ? 'active' : '' ?>" href="<?= base_url('riset/admin/publikasi') ?>">
                <i class="fas fa-file-export"></i> Review Publikasi
            </a>

            <div class="px-3 mb-2 mt-4">
                <label class="text-uppercase fw-bold text-muted" style="font-size: 10px; letter-spacing: 1px;">Pengaturan</label>
            </div>

            <a class="nav-link <?= (isset($active_menu) && $active_menu == 'pengaturan_surat') ? 'active' : '' ?>" href="<?= base_url('riset/admin/pengaturan-surat') ?>">
                <i class="fas fa-cog"></i> Pengaturan Riset
            </a>
        </nav>

        <div class="p-3 mt-auto border-top">
            <a href="<?= base_url('riset/logout') ?>" class="btn btn-outline-danger w-100 btn-sm fw-bold">
                <i class="fas fa-sign-out-alt me-1"></i> Keluar
            </a>
        </div>
    </div>
</div>

<div class="main-content">