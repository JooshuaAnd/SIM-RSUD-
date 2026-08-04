<?= $this->extend('Riset/admin/layout/template') ?>

<?= $this->section('content') ?>

<?php /** @var array $user */ ?>
<style>
    /* Hide native password reveal icon in Edge */
    input[type="password"]::-ms-reveal,
    input[type="password"]::-ms-clear {
        display: none;
    }
</style>

<?php if (session()->getFlashdata('success')) : ?>
    <div class="alert alert-success alert-dismissible fade show border-0 rounded-4 shadow-sm" role="alert">
        <i class="fas fa-check-circle me-2"></i> <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')) : ?>
    <div class="alert alert-danger alert-dismissible fade show border-0 rounded-4 shadow-sm" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i> <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="row g-4">
    <div class="col-lg-4">
        <!-- Profile Card -->
        <div class="card border-0 shadow-sm rounded-4 mb-4 text-center">
            <div class="card-body p-4 pt-5">
                <div class="position-relative d-inline-block mb-3">
                    <?php if (!empty($user['foto_profil'])): ?>
                        <img src="<?= base_url($user['foto_profil']) ?>" alt="Profile" class="rounded-circle shadow-sm" style="width: 120px; height: 120px; object-fit: cover; border: 3px solid #fff;">
                    <?php else: ?>
                        <div class="rounded-circle shadow-sm bg-danger text-white d-flex align-items-center justify-content-center mx-auto" style="width: 120px; height: 120px; border: 3px solid #fff; font-size: 40px; font-weight: bold;">
                            <?= strtoupper(substr($user['nama'], 0, 2)) ?>
                        </div>
                    <?php endif; ?>
                    <label for="foto_profil" class="position-absolute bottom-0 end-0 bg-white text-danger rounded-circle shadow p-2" style="cursor: pointer; transform: translate(-10%, -10%);">
                        <i class="fas fa-camera"></i>
                    </label>
                </div>
                <h5 class="fw-bold mb-1"><?= esc($user['nama']) ?></h5>
                <p class="text-muted mb-3" style="font-size: 13px;">Administrator Riset & Publikasi</p>
                <div class="d-flex justify-content-center gap-2 mb-3">
                    <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill"><i class="fas fa-shield-alt me-1"></i> Admin</span>
                </div>
                <hr class="text-muted opacity-25 my-4">
                <div class="d-flex justify-content-between align-items-center text-start mb-2">
                    <span class="text-muted" style="font-size: 12px;"><i class="fas fa-envelope text-danger me-2"></i> Email</span>
                    <span class="fw-bold" style="font-size: 12px;"><?= esc($user['email']) ?></span>
                </div>
                <div class="d-flex justify-content-between align-items-center text-start">
                    <span class="text-muted" style="font-size: 12px;"><i class="fas fa-phone text-danger me-2"></i> Telepon</span>
                    <span class="fw-bold" style="font-size: 12px;"><?= esc($user['no_telp']) ?></span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white py-3 px-4 border-0 rounded-top-4 border-bottom">
                <h5 class="fw-bold mb-0" style="font-size: 16px;">Edit Informasi Profil</h5>
            </div>
            <div class="card-body p-4">
                <form action="<?= base_url('riset/admin/profil/update') ?>" method="post" enctype="multipart/form-data">
                    <input type="file" id="foto_profil" name="foto_profil" class="d-none" accept="image/*" onchange="previewImage(this)">
                    
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label class="form-label text-muted fw-bold" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm py-2 px-3 rounded-3" name="nama" value="<?= esc($user['nama']) ?>" required style="font-size: 13px; border-color: #e0e0e0; background-color: #fcfcfc;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted fw-bold" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control form-control-sm py-2 px-3 rounded-3" name="email" value="<?= esc($user['email']) ?>" required style="font-size: 13px; border-color: #e0e0e0; background-color: #fcfcfc;">
                        </div>
                    </div>

                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label class="form-label text-muted fw-bold" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">No. Telepon / WhatsApp <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm py-2 px-3 rounded-3" name="no_telp" value="<?= esc($user['no_telp']) ?>" required style="font-size: 13px; border-color: #e0e0e0; background-color: #fcfcfc;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted fw-bold" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Institusi <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm py-2 px-3 rounded-3" name="institusi" value="<?= esc($user['institusi']) ?>" required style="font-size: 13px; border-color: #e0e0e0; background-color: #fcfcfc;">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-muted fw-bold" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Alamat Kantor (Opsional)</label>
                        <textarea class="form-control py-2 px-3 rounded-3" name="alamat" rows="3" style="font-size: 13px; border-color: #e0e0e0; background-color: #fcfcfc;"><?= esc($user['alamat'] ?? '') ?></textarea>
                    </div>

                    <hr class="text-muted opacity-25 my-4">

                    <h6 class="fw-bold mb-3" style="font-size: 14px;">Ubah Password</h6>
                    <p class="text-muted mb-4" style="font-size: 12px;">Biarkan kosong jika Anda tidak ingin mengubah password.</p>

                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label class="form-label text-muted fw-bold" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Password Baru</label>
                            <div class="input-group">
                                <input type="password" class="form-control form-control-sm py-2 px-3 rounded-start-3" id="password" name="password" style="font-size: 13px; border-color: #e0e0e0; background-color: #fcfcfc;">
                                <button class="btn btn-outline-secondary rounded-end-3" type="button" onclick="togglePassword()" style="border-color: #e0e0e0;"><i class="fas fa-eye" id="toggleIcon"></i></button>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                        <button type="submit" class="btn btn-danger px-4 py-2 rounded-pill fw-bold shadow-sm d-flex align-items-center" style="font-size: 13px;">
                            <i class="fas fa-save me-2"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword() {
    var x = document.getElementById("password");
    var icon = document.getElementById("toggleIcon");
    if (x.type === "password") {
        x.type = "text";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
    } else {
        x.type = "password";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
    }
}

function previewImage(input) {
    if (input.files && input.files[0]) {
        // You could add dynamic preview here using FileReader if needed
        // For now we just let the user submit the form to see changes
    }
}
</script>

<?= $this->endSection() ?>
