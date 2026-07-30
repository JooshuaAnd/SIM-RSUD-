<div class="sidebar shadow-sm" id="sidebarMenu">
    <div class="d-flex flex-column h-100">
        <div class="p-3 mb-2">
            <div class="d-flex align-items-center p-2 bg-light rounded-3">
                <?php if (session()->get('riset_user_foto')): ?>
                    <img src="<?= base_url('uploads/riset/profil/' . session()->get('riset_user_foto')) ?>" class="rounded-circle me-2 object-fit-cover" width="40" height="40" alt="Profile">
                <?php else: ?>
                    <img src="https://ui-avatars.com/api/?name=<?= urlencode(session()->get('riset_user_nama') ?? 'P') ?>&background=c62828&color=fff" class="rounded-circle me-2" width="40" height="40">
                <?php endif; ?>
                <div class="overflow-hidden">
                    <h6 class="mb-0 text-dark fw-bold small text-truncate"><?= session()->get('riset_user_nama') ?? 'Peneliti' ?></h6>
                    <span class="text-muted" style="font-size: 10px;">Peneliti (Riset)</span>
                </div>
            </div>
        </div>

        <nav class="nav flex-column flex-grow-1">
            <div class="px-3 mb-2 mt-2">
                <label class="text-uppercase fw-bold text-muted" style="font-size: 10px; letter-spacing: 1px;">Menu Utama</label>
            </div>
            
            <a class="nav-link <?= ($active_menu ?? '') == 'dashboard' ? 'active' : '' ?>" href="<?= base_url('riset/peneliti/dashboard') ?>">
                <i class="fas fa-home"></i> Dashboard
            </a>

            <div class="px-3 mb-2 mt-4">
                <label class="text-uppercase fw-bold text-muted" style="font-size: 10px; letter-spacing: 1px;">Pengajuan Riset</label>
            </div>

            <a class="nav-link <?= ($active_menu ?? '') == 'riset' ? 'active' : '' ?>" href="<?= base_url('riset/peneliti/pengajuan/stupen/baru') ?>">
                <i class="fas fa-microscope"></i> Studi Pendahuluan
            </a>

            <a class="nav-link <?= ($active_menu ?? '') == 'izin' ? 'active' : '' ?>" href="<?= base_url('riset/peneliti/pengajuan/izin/baru') ?>">
                <i class="fas fa-file-signature"></i> Izin Penelitian
            </a>

            <a class="nav-link <?= ($active_menu ?? '') == 'publikasi' ? 'active' : '' ?>" href="<?= base_url('riset/peneliti/publikasi') ?>">
                <i class="fas fa-file-export"></i> Publikasi
            </a>

            <div class="px-3 mb-2 mt-4">
                <label class="text-uppercase fw-bold text-muted" style="font-size: 10px; letter-spacing: 1px;">Monitoring</label>
            </div>

            <a class="nav-link <?= ($active_menu ?? '') == 'status' ? 'active' : '' ?>" href="<?= base_url('riset/peneliti/status') ?>">
                <i class="fas fa-list-check"></i> Status & Download
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
