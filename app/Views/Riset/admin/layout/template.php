<?= $this->include('Riset/admin/layout/header') ?>

<?= $this->include('Riset/admin/layout/sidebar') ?>

<div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom border-light">
    <div class="d-flex align-items-center">
        <div>
            <h3 class="fw-bold text-dark mb-0" style="letter-spacing: -0.5px;"><?= $title ?? 'Dashboard' ?></h3>
        </div>
    </div>
    <nav aria-label="breadcrumb" class="d-none d-sm-block">
        <ol class="breadcrumb mb-0 px-3 py-1 bg-white rounded-pill shadow-sm" style="font-size: 10px; font-weight: 600;">
            <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-muted">Admin</a></li>
            <li class="breadcrumb-item active text-danger" aria-current="page"><?= $title ?? 'Dashboard' ?></li>
        </ol>
    </nav>
</div>

<!-- Flash Messages -->
<?php if (session()->getFlashdata('success')) : ?>
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
        <div class="d-flex align-items-center">
            <i class="fas fa-check-circle fs-4 me-3"></i>
            <div>
                <strong>Berhasil!</strong><br>
                <span class="fs-6"><?= session()->getFlashdata('success') ?></span>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')) : ?>
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
        <div class="d-flex align-items-center">
            <i class="fas fa-exclamation-circle fs-4 me-3"></i>
            <div>
                <strong>Gagal!</strong><br>
                <span class="fs-6"><?= session()->getFlashdata('error') ?></span>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('warning')) : ?>
    <div class="alert alert-warning alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
        <div class="d-flex align-items-center">
            <i class="fas fa-exclamation-triangle fs-4 me-3"></i>
            <div>
                <strong>Perhatian!</strong><br>
                <span class="fs-6"><?= session()->getFlashdata('warning') ?></span>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?= $this->renderSection('content') ?>

<?= $this->include('Riset/admin/layout/footer') ?>
