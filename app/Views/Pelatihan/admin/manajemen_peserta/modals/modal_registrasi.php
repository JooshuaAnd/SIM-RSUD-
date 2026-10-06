<?php
$unit_kerja = $unit_kerja ?? [];
$profesi = $profesi ?? [];
?>
<div class="modal fade" id="modalRegistrasi" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-lg overflow-hidden">
            <div class="modal-header bg-dark text-white border-0 p-4 border-bottom border-danger border-4">
                <h5 class="modal-title fw-bold text-uppercase"><i class="fas fa-user-plus me-2 text-warning"></i> Registrasi Akun Baru</h5>
                <div class="d-flex align-items-center gap-2">

                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
            </div>
            <div class="modal-body p-4 bg-light">
                <form action="<?= base_url('pelatihan/admin/akun_peserta/tambah') ?>" method="POST" id="formRegistrasi" class="needs-validation" novalidate>
                    <?= csrf_field() ?>
                    
                    <div class="account-registration-sections">
                        <section class="account-registration-section">
                            <div class="account-registration-section-heading">
                                <span class="account-registration-section-index">01</span>
                                <div>
                                    <h6 class="account-registration-section-title">Role &amp; Jenis Peserta</h6>
                                    <p class="account-registration-section-description">Tentukan tipe akun dan hak akses peserta.</p>
                                </div>
                            </div>
                            <div class="row g-3 account-registration-section-grid">
                                <div class="col-md-12">
                                    <label class="form-label small fw-bold mb-1">PILIH ROLE</label>
                                    <select class="form-select" name="role" required>
                                        <option value="named" selected>NAMED (PEGAWAI)</option>
                                        <option value="nonnamed">NON-NAMED (UMUM)</option>
                                    </select>
                                </div>
                            </div>
                        </section>

                        <section class="account-registration-section">
                            <div class="account-registration-section-heading">
                                <span class="account-registration-section-index">02</span>
                                <div>
                                    <h6 class="account-registration-section-title">Informasi Dasar</h6>
                                    <p class="account-registration-section-description">Lengkapi identitas dan kontak utama peserta.</p>
                                </div>
                            </div>
                            <div class="row g-3 account-registration-section-grid">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold mb-1">NAMA LENGKAP</label>
                                    <input type="text" class="form-control" name="nama" placeholder="Sesuai KTP..." required pattern="[A-Za-z\s\.,']+" title="Nama hanya boleh mengandung huruf, spasi, titik, koma, atau tanda kutip tunggal.">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold mb-1">NIK (16 DIGIT)</label>
                                    <input type="text" class="form-control" name="nik" placeholder="16 digit angka..." minlength="16" maxlength="16" required pattern="\d{16}" inputmode="numeric" title="NIK harus berupa 16 digit angka.">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold mb-1">EMAIL AKTIF</label>
                                    <input type="email" class="form-control" name="email" placeholder="nama@email.com" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold mb-1">NO. WHATSAPP</label>
                                    <input type="tel" class="form-control" name="wa" placeholder="08..." required pattern="[0-9]{10,15}" maxlength="15" inputmode="numeric" title="Nomor WhatsApp harus berupa angka murni (10 s.d 15 digit).">
                                </div>
                            </div>
                        </section>

                        <section class="account-registration-section">
                            <div class="account-registration-section-heading">
                                <span class="account-registration-section-index">03</span>
                                <div>
                                    <h6 class="account-registration-section-title">Informasi Profesi</h6>
                                    <p class="account-registration-section-description">Tambahkan unit kerja dan profesi peserta.</p>
                                </div>
                            </div>
                            <div class="row g-3 account-registration-section-grid">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold mb-1">UNIT KERJA</label>
                                    <select name="id_unit_kerja" id="reg_unit_kerja" class="form-select" required>
                                        <option value="" disabled selected>Pilih Unit Kerja...</option>
                                        <?php foreach ($unit_kerja as $uk) : ?>
                                            <option value="<?= $uk['id_unit_kerja'] ?>" <?= old('id_unit_kerja') == $uk['id_unit_kerja'] ? 'selected' : '' ?>><?= esc($uk['nama_unit']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold mb-1">PROFESI</label>
                                    <select name="id_profesi" id="reg_profesi" class="form-select" required>
                                        <option value="" disabled selected>Pilih Profesi...</option>
                                        <?php foreach ($profesi as $p) : ?>
                                            <option value="<?= $p['id_profesi'] ?>" <?= old('id_profesi') == $p['id_profesi'] ? 'selected' : '' ?>><?= esc($p['nama_profesi']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </section>
                    </div>

                    <div class="account-registration-notice py-2 mb-0" role="note">
                        <i class="fas fa-info-circle me-1"></i> <strong>Pemberitahuan:</strong> Password default untuk akun yang baru dibuat adalah <strong>RSUDKotaYogyakarta2026</strong>. Pengguna hanya dapat menggunakan password ini 1x dan akan dipaksa untuk menggantinya saat login pertama kali.
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-register-submit w-100">
                            DAFTARKAN AKUN SEKARANG <i class="fas fa-arrow-right ms-2"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    #modalRegistrasi .account-registration-sections {
        display: grid;
        gap: 1rem;
    }

    #modalRegistrasi .account-registration-section {
        min-width: 0;
        padding: 1.25rem;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 1rem;
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.045);
    }

    #modalRegistrasi .account-registration-section-heading {
        display: flex;
        align-items: center;
        gap: 0.8rem;
        margin-bottom: 1.1rem;
        padding-bottom: 0.9rem;
        border-bottom: 1px solid #eef2f7;
    }

    #modalRegistrasi .account-registration-section-index {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 2.15rem;
        width: 2.15rem;
        height: 2.15rem;
        border: 1px solid #fecaca;
        border-radius: 0.7rem;
        background: #fff5f5;
        color: #ce2127;
        font-size: 0.72rem;
        font-weight: 800;
        letter-spacing: 0.04em;
    }

    #modalRegistrasi .account-registration-section-title {
        margin: 0;
        color: #1e293b;
        font-size: 0.86rem;
        font-weight: 800;
        letter-spacing: 0.06em;
        line-height: 1.35;
        text-transform: uppercase;
    }

    #modalRegistrasi .account-registration-section-description {
        margin: 0.25rem 0 0;
        color: #64748b;
        font-size: 0.74rem;
        line-height: 1.45;
    }

    #modalRegistrasi .account-registration-section-grid > [class*="col-"] {
        min-width: 0;
    }

    #modalRegistrasi .account-registration-section .form-label {
        margin-bottom: 0.45rem !important;
        color: #475569;
        letter-spacing: 0.025em;
        line-height: 1.35;
    }

    #modalRegistrasi .account-registration-section .form-control,
    #modalRegistrasi .account-registration-section .form-select {
        min-height: 44px;
    }

    #modalRegistrasi .account-registration-section .form-control::placeholder {
        color: #a8b1bd;
        opacity: 1;
    }

    #modalRegistrasi .account-registration-section .form-select:invalid {
        color: #a8b1bd;
    }

    #modalRegistrasi .account-registration-section .form-select option {
        color: #1e293b;
    }

    #modalRegistrasi .account-registration-section .form-select option[value=""] {
        color: #a8b1bd;
    }

    #modalRegistrasi .account-registration-notice {
        margin-top: 1rem;
        border: 1px solid #e2e8f0;
        border-radius: 0.75rem;
        background: #f8fafc;
        color: #475569;
        font-size: 0.82rem;
        line-height: 1.55;
    }

    #modalRegistrasi .account-registration-notice i {
        color: #94a3b8;
    }

    #modalRegistrasi .account-registration-notice strong {
        color: #334155;
    }

    @media (max-width: 767.98px) {
        #modalRegistrasi .account-registration-section {
            padding: 1rem;
            border-radius: 0.85rem;
        }

        #modalRegistrasi .account-registration-section-heading {
            align-items: flex-start;
            gap: 0.65rem;
            margin-bottom: 1rem;
        }

        #modalRegistrasi .account-registration-section-index {
            flex-basis: 1.95rem;
            width: 1.95rem;
            height: 1.95rem;
            border-radius: 0.6rem;
        }

        #modalRegistrasi .account-registration-section-title {
            font-size: 0.8rem;
        }

        #modalRegistrasi .account-registration-section-description {
            font-size: 0.7rem;
        }
    }

    @media (max-width: 575.98px) {
        #modalRegistrasi .modal-body {
            padding: 1rem !important;
        }
    }
</style>


