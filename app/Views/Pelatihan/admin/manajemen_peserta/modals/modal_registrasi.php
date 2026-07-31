<?php
$unit_kerja = $unit_kerja ?? [];
$profesi = $profesi ?? [];
?>
<div class="modal fade" id="modalRegistrasi" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
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
                    
                    <div class="section-title">Role & Jenis Peserta</div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-12">
                            <label class="form-label small fw-bold mb-1">PILIH ROLE</label>
                            <select class="form-select" name="role" required>
                                <option value="named" selected>NAMED (PEGAWAI)</option>
                                <option value="nonnamed">NON-NAMED (UMUM)</option>
                            </select>
                        </div>
                    </div>

                    <div class="section-title">Informasi Dasar</div>
                    <div class="row g-3 mb-4">
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
                            <input type="email" class="form-control" name="email" placeholder="nama@email.com" required pattern="[a-zA-Z0-9._%+-]+@(gmail\.com|students\.ukcw\.ac\.id|[a-zA-Z0-9.-]+\.go\.id)" title="Email harus menggunakan domain @gmail.com, @students.ukcw.ac.id, atau instansi pemerintah (.go.id).">
                            <div class="form-text" style="font-size: 0.65rem;">Hanya @gmail.com, @students.ukcw.ac.id, atau .go.id</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold mb-1">NO. WHATSAPP</label>
                            <input type="tel" class="form-control" name="wa" placeholder="08..." required pattern="[0-9]{10,15}" maxlength="15" inputmode="numeric" title="Nomor WhatsApp harus berupa angka murni (10 s.d 15 digit).">
                        </div>
                    </div>

                    <div class="section-title">Informasi Profesi</div>
                    <div class="row g-3 mb-4">
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

                    <div class="alert alert-info py-2 mt-2 mb-0" style="font-size: 0.85rem; border-left: 4px solid #0dcaf0;">
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


