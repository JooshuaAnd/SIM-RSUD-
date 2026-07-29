<?= $this->extend('Riset/peneliti/layout/template') ?>

<?= $this->section('content') ?>
<?php
/**
 * @var array $user
 */
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1">Profil Peneliti</h4>
        <p class="text-muted small mb-0">Kelola informasi data diri dan institusi Anda.</p>
    </div>
    <a href="<?= base_url('riset/peneliti/profil/edit') ?>" class="btn btn-outline-danger btn-sm px-3 rounded-pill border fw-bold" style="font-size: 11px;">
        <i class="fas fa-edit me-2"></i> Edit Profil
    </a>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4 py-3 d-flex align-items-center" style="background: rgba(40, 167, 69, 0.05); color: #28a745;">
        <i class="fas fa-check-circle me-3" style="font-size: 20px;"></i>
        <div class="fw-bold small"><?= session()->getFlashdata('success') ?></div>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4 py-3 d-flex align-items-center" style="background: rgba(220, 53, 69, 0.05); color: #dc3545;">
        <i class="fas fa-exclamation-circle me-3" style="font-size: 20px;"></i>
        <div class="fw-bold small"><?= session()->getFlashdata('error') ?></div>
    </div>
<?php endif; ?>

<div class="row g-4">
    <!-- Profile Card -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 text-center p-4">
            <div class="position-relative d-inline-block mx-auto mb-4">
                <?php if (!empty($user['foto_profil'])): ?>
                    <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 100px; height: 100px; background-size: cover; background-position: center; background-image: url('<?= base_url('uploads/riset/profil/' . $user['foto_profil']) ?>');">
                    </div>
                <?php else: ?>
                    <div class="bg-danger rounded-circle d-flex align-items-center justify-content-center text-white fw-bold" style="width: 100px; height: 100px; font-size: 36px;">
                        <?= substr($user['nama'], 0, 1) ?>
                    </div>
                <?php endif; ?>
                <a href="<?= base_url('riset/peneliti/profil/edit') ?>" class="position-absolute bottom-0 end-0 bg-white rounded-circle shadow-sm p-2 d-flex align-items-center justify-content-center text-decoration-none" style="width: 32px; height: 32px; border: 1px solid #eee;">
                    <i class="fas fa-camera text-muted" style="font-size: 12px;"></i>
                </a>
            </div>
            <h5 class="fw-bold text-dark mb-1"><?= $user['nama'] ?></h5>
            <p class="text-muted small mb-3"><?= $user['institusi'] ?></p>
            <div class="d-flex justify-content-center gap-2">
                <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill" style="font-size: 10px;">Peneliti Utama</span>
                <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill" style="font-size: 10px;">Terverifikasi</span>
            </div>

            <hr class="my-4" style="opacity: 0.1;">

            <div class="text-start">
                <div class="mb-3">
                    <label class="text-uppercase fw-bold text-muted d-block mb-1" style="font-size: 9px; letter-spacing: 1px;">Email Address</label>
                    <div class="text-dark fw-medium" style="font-size: 13px;"><?= $user['email'] ?></div>
                </div>
                <div class="mb-0">
                    <label class="text-uppercase fw-bold text-muted d-block mb-1" style="font-size: 9px; letter-spacing: 1px;">Phone Number</label>
                    <div class="text-dark fw-medium" style="font-size: 13px;"><?= $user['no_telp'] ?? '-' ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Biodata Details -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white py-4 px-4 border-0">
                <h6 class="fw-bold mb-0 text-danger" style="font-size: 15px;">Informasi Biodata Lengkap</h6>
            </div>
            <hr class="m-0" style="opacity: 0.1;">
            <div class="card-body p-4">
                <div class="row mb-4">
                    <div class="col-md-4">
                        <label class="text-uppercase fw-bold text-muted" style="font-size: 10px; letter-spacing: 0.5px;">NIM atau No. Identitas</label>
                        <div class="d-flex align-items-center mt-1">
                            <i class="fas fa-id-card text-danger me-2 opacity-75"></i>
                            <p class="text-dark fw-bold mb-0" style="font-size: 13px;"><?= $user['identitas'] ?? '-' ?></p>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-4">
                        <label class="text-uppercase fw-bold text-muted" style="font-size: 10px; letter-spacing: 0.5px;">Institusi</label>
                    </div>
                    <div class="col-md-8">
                        <p class="text-dark fw-bold" style="font-size: 13px;"><?= $user['institusi'] ?? '-' ?></p>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-4">
                        <label class="text-uppercase fw-bold text-muted" style="font-size: 10px; letter-spacing: 0.5px;">Program Studi / Departemen</label>
                    </div>
                    <div class="col-md-8">
                        <p class="text-dark fw-bold" style="font-size: 13px;"><?= $user['prodi'] ?? '-' ?></p>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-4">
                        <label class="text-uppercase fw-bold text-muted" style="font-size: 10px; letter-spacing: 0.5px;">Alamat Lengkap</label>
                    </div>
                    <div class="col-md-8">
                        <p class="text-dark" style="font-size: 13px; line-height: 1.6;"><?= $user['alamat'] ?? '-' ?></p>
                    </div>
                </div>

                <div class="mt-4 p-4 rounded-4 bg-light border border-dashed border-secondary border-opacity-25">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="bg-dark bg-opacity-10 p-2 rounded-3 me-3">
                                <i class="fas fa-shield-halved text-dark" style="font-size: 18px;"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-1" style="font-size: 13px;">Keamanan Akun</h6>
                                <p class="text-muted mb-0" style="font-size: 11px;">Pastikan data Anda selalu terbaru untuk kelancaran proses verifikasi berkas riset.</p>
                            </div>
                        </div>
                        <button class="btn btn-dark btn-sm px-4 rounded-pill fw-bold" style="font-size: 11px;" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                            Ganti Password
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Change Password Modal -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="fw-bold mb-0">Ganti Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form action="<?= base_url('riset/peneliti/profil/update_password') ?>" method="POST" id="formGantiPassword">
                    <div class="mb-3">
                        <label class="form-label text-uppercase fw-bold text-muted" style="font-size: 10px; letter-spacing: 1px;">Password Saat Ini</label>
                        <input type="password" name="old_password" class="form-control rounded-3" placeholder="••••••••" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-uppercase fw-bold text-muted" style="font-size: 10px; letter-spacing: 1px;">Password Baru</label>
                        <input type="password" name="new_password" id="new_password" class="form-control rounded-3" placeholder="Minimal 8 karakter" required minlength="8">
                    </div>
                    <div class="mb-4">
                        <label class="form-label text-uppercase fw-bold text-muted" style="font-size: 10px; letter-spacing: 1px;">Konfirmasi Password Baru</label>
                        <input type="password" name="confirm_password" id="confirm_password" class="form-control rounded-3" placeholder="Ulangi password baru" required minlength="8">
                    </div>
                    <button type="submit" class="btn btn-danger w-100 py-3 rounded-3 fw-bold shadow-sm" style="font-size: 13px;">Simpan Perubahan Password</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Reset form when modal is closed
        const changePasswordModal = document.getElementById('changePasswordModal');
        if (changePasswordModal) {
            changePasswordModal.addEventListener('hidden.bs.modal', function () {
                const form = document.getElementById('formGantiPassword');
                if (form) {
                    form.reset();
                    // Clear custom validation messages
                    const newPass = document.getElementById('new_password');
                    const confPass = document.getElementById('confirm_password');
                    if (newPass) newPass.setCustomValidity('');
                    if (confPass) confPass.setCustomValidity('');
                    const oldPass = document.querySelector('input[name="old_password"]');
                    if (oldPass) oldPass.setCustomValidity('');
                }
            });
        }

        // Validation for change password form
        const newPass = document.getElementById('new_password');
        const confPass = document.getElementById('confirm_password');

        if (newPass && confPass) {
            const validatePassword = () => {
                if (newPass.value !== confPass.value) {
                    confPass.setCustomValidity('Konfirmasi password tidak cocok dengan password baru.');
                } else {
                    confPass.setCustomValidity('');
                }
            };
            newPass.addEventListener('input', validatePassword);
            confPass.addEventListener('input', validatePassword);

            // Custom messages for HTML5 validation
            [newPass, confPass].forEach(input => {
                input.addEventListener('invalid', function(e) {
                    if (e.target.validity.valueMissing) {
                        e.target.setCustomValidity('Kolom ini wajib diisi.');
                    } else if (e.target.validity.tooShort) {
                        e.target.setCustomValidity('Password minimal harus ' + e.target.minLength + ' karakter.');
                    }
                });
                input.addEventListener('input', function(e) {
                    if (e.target.id === 'confirm_password' && newPass.value !== confPass.value) {
                        e.target.setCustomValidity('Konfirmasi password tidak cocok dengan password baru.');
                    } else {
                        e.target.setCustomValidity('');
                    }
                });
            });

            const oldPass = document.querySelector('input[name="old_password"]');
            if (oldPass) {
                oldPass.addEventListener('invalid', function(e) {
                    if (e.target.validity.valueMissing) {
                        e.target.setCustomValidity('Kolom password saat ini wajib diisi.');
                    }
                });
                oldPass.addEventListener('input', function(e) {
                    e.target.setCustomValidity('');
                });
            }
        }
    });
</script>

<?= $this->endSection() ?>
