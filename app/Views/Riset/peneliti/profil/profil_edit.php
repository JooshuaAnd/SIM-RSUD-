<?php

/**
 * @var array $user
 */
?>
<?= $this->extend('Riset/peneliti/layout/template') ?>

<?= $this->section('content') ?>

<div class="d-flex align-items-center justify-content-center position-relative mb-3">
    <a href="<?= base_url('riset/peneliti/profil') ?>" class="text-decoration-none small fw-bold text-muted position-absolute start-0">
        <i class="fas fa-arrow-left me-2"></i> Kembali
    </a>
    <div class="text-center">
        <h4 class="fw-bold text-dark mb-1">Edit Profil</h4>
        <p class="text-muted small mb-0">Perbarui informasi data diri dan institusi Anda.</p>
    </div>
</div>

<style>
    .form-label-custom {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #666;
        margin-bottom: 8px;
        display: block;
    }

    .form-control-custom {
        background: #fdfdfd;
        border: 1.5px solid #eee;
        border-radius: 12px;
        padding: 12px 18px;
        color: #333;
        font-size: 14px;
        transition: 0.3s;
    }

    .form-control-custom:focus {
        background: #fff;
        border-color: var(--primary-red, #e53935);
        box-shadow: 0 0 0 4px rgba(229, 57, 53, 0.05);
        outline: none;
    }

    .profile-img-preview {
        width: 80px;
        height: 80px;
        font-size: 28px;
        background-size: cover;
        background-position: center;
    }
</style>

<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-5">
                <form action="<?= base_url('riset/peneliti/profil/update') ?>" method="POST" enctype="multipart/form-data">
                    <div class="text-center mb-5">
                        <div class="position-relative d-inline-block">
                            <div id="preview-container" class="bg-danger rounded-circle d-flex align-items-center justify-content-center text-white fw-bold mx-auto profile-img-preview"
                                <?= !empty($user['foto_profil']) ? 'style="background-image: url(' . base_url('uploads/riset/profil/' . $user['foto_profil']) . ');"' : '' ?>>
                                <?= empty($user['foto_profil']) ? substr($user['nama'], 0, 1) : '' ?>
                            </div>
                            <button type="button" onclick="document.getElementById('foto_profil').click()" class="btn btn-light position-absolute rounded-circle shadow-sm p-0 border d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; bottom: 0; right: 0; transform: translate(25%, 25%); z-index: 2;">
                                <i class="fas fa-camera text-muted" style="font-size: 14px;"></i>
                            </button>
                            <input type="file" name="foto_profil" id="foto_profil" class="d-none" accept="image/*" onchange="previewImage(this)">
                        </div>
                        <p class="text-muted small mb-0 mt-2">Klik kamera untuk mengubah foto profil</p>
                    </div>

                    <div class="mb-4">
                        <label class="form-label-custom">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control form-control-custom" value="<?= $user['nama'] ?>" required>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label-custom">Email Address</label>
                            <input type="email" name="email" class="form-control form-control-custom" value="<?= $user['email'] ?>" required readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-custom">NIM atau No. Identitas</label>
                            <input type="text" name="identitas" class="form-control form-control-custom" value="<?= $user['identitas'] ?? '' ?>" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label-custom">Institusi / Universitas</label>
                        <input type="text" name="institusi" class="form-control form-control-custom" value="<?= $user['institusi'] ?>" required>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label-custom">Program Studi / Departemen</label>
                            <input type="text" name="prodi" class="form-control form-control-custom" value="<?= $user['prodi'] ?? '' ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-custom">Nomor Telepon / WhatsApp</label>
                            <input type="tel" name="no_telp" class="form-control form-control-custom" value="<?= $user['no_telp'] ?? '' ?>" required maxlength="13" oninput="this.value = this.value.replace(/[^0-9+]/g, '')">
                        </div>
                    </div>

                    <div class="mb-5">
                        <label class="form-label-custom">Alamat Lengkap</label>
                        <textarea name="alamat" class="form-control form-control-custom" rows="3" required><?= $user['alamat'] ?? '' ?></textarea>
                    </div>

                    <div class="d-flex justify-content-center gap-3 mt-4">
                        <a href="<?= base_url('riset/peneliti/profil') ?>" class="btn btn-light px-5 py-3 rounded-3 fw-bold border" style="font-size: 14px; min-width: 120px;">Batal</a>
                        <button type="submit" class="btn btn-danger px-5 py-3 rounded-3 fw-bold shadow-sm" style="font-size: 14px; min-width: 250px;">Simpan Perubahan Profil</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                var container = document.getElementById('preview-container');
                container.style.backgroundImage = 'url(' + e.target.result + ')';
                container.innerHTML = ''; // Remove the text (initials)
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>

<?= $this->endSection() ?>
