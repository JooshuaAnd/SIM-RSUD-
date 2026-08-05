<?php
$unit_kerja = $unit_kerja ?? [];
$profesi = $profesi ?? [];
?>
<div class="modal fade" id="modalEditUser" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-lg overflow-hidden">
            <div class="modal-header bg-dark text-white border-0 p-4 border-bottom border-danger border-4">
                <h5 class="modal-title fw-bold text-uppercase"><i class="fas fa-edit me-2 text-warning"></i> Edit Akun Peserta</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <form action="<?= base_url('pelatihan/admin/akun_peserta/edit') ?>" method="POST" id="formEditUser" class="needs-validation" novalidate>
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" id="edit_id">
                    
                    <div class="section-title">Role & Jenis Peserta</div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6 d-none">
                            <label class="form-label small fw-bold mb-1">PILIH ROLE</label>
                            <select name="role" id="edit_role" class="form-select" required>
                                <option value="peserta" selected>PESERTA</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small fw-bold mb-1">JENIS PESERTA</label>
                            <select name="jenis_peserta" id="edit_jenis_peserta" class="form-select" required>
                                <option value="" disabled>Pilih Jenis...</option>
                                <option value="named">NAMED (PEGAWAI)</option>
                                <option value="non_named">NON-NAMED (UMUM/MAHASISWA)</option>
                            </select>
                        </div>
                    </div>

                    <div class="section-title">Informasi Dasar</div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold mb-1">NAMA LENGKAP</label>
                            <input type="text" class="form-control" name="nama" id="edit_nama" required pattern="[A-Za-z\s\.,']+" title="Nama hanya boleh mengandung huruf, spasi, titik, koma, atau tanda kutip tunggal.">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold mb-1">NIK (16 DIGIT)</label>
                            <input type="text" class="form-control" name="nik" id="edit_nik" minlength="16" maxlength="16" required pattern="\d{16}" inputmode="numeric" title="NIK harus berupa 16 digit angka.">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold mb-1">EMAIL AKTIF</label>
                            <input type="email" class="form-control" name="email" id="edit_email" required>
                            
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold mb-1">NO. WHATSAPP</label>
                            <input type="tel" class="form-control" name="wa" id="edit_wa" required pattern="[0-9]{10,15}" maxlength="15" inputmode="numeric" title="Nomor WhatsApp harus berupa angka murni (10 s.d 15 digit).">
                        </div>
                    </div>

                    <div class="section-title">Informasi Profesi</div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold mb-1">UNIT KERJA (KHUSUS NAMED)</label>
                            <select name="id_unit_kerja" id="edit_unit_kerja" class="form-select" required>
                                <option value="" disabled selected>Pilih Unit Kerja...</option>
                                <?php foreach ($unit_kerja as $uk) : ?>
                                    <option value="<?= $uk['id_unit_kerja'] ?>"><?= esc($uk['nama_unit']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold mb-1">PROFESI (KHUSUS NAMED)</label>
                            <select name="id_profesi" id="edit_profesi" class="form-select" required>
                                <option value="" disabled selected>Pilih Profesi...</option>
                                <?php foreach ($profesi as $p) : ?>
                                    <option value="<?= $p['id_profesi'] ?>"><?= esc($p['nama_profesi']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small fw-bold mb-1">TARGET JPL TAHUNAN</label>
                            <input type="number" class="form-control" name="target_jpl" id="edit_target_jpl" min="0" required placeholder="Contoh: 20">
                            <div class="form-text" style="font-size: 0.65rem;">Target JPL tahunan khusus untuk peserta ini (dapat dikustomisasi per orang).</div>
                        </div>
                    </div>



                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-dark w-100 btn-register-submit" style="background:#1e293b; box-shadow: 0 8px 16px rgba(30, 41, 59, 0.2);">
                            SIMPAN PERUBAHAN DATA <i class="fas fa-save ms-2"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

