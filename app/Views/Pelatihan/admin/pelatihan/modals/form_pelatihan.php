<div class="modal fade" id="modalPelatihan" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-lg">
            <div class="modal-header bg-dark text-white border-0 p-4">
                <h5 class="modal-title fw-bold" id="modalTitle"><i class="fas fa-plus-circle me-2 text-warning"></i> INPUT MASTER DIKLAT BARU</h5>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-warning btn-sm rounded-pill fw-bold" onclick="autofillTesting()" title="Auto-fill form untuk testing">
                        <i class="fas fa-bolt me-1"></i> TESTING AUTOFILL
                    </button>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
            </div>
            <div class="modal-body p-4 p-md-5 bg-light">
                <form id="formPelatihan" method="POST" action="<?= base_url('pelatihan/admin/pelatihan/simpan') ?>" class="needs-validation" enctype="multipart/form-data" novalidate>
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" id="f_id">
                    <div class="row g-4">
                        <div class="col-md-12"><h6 class="fw-bold text-danger border-bottom pb-2 text-uppercase small" style="letter-spacing: 1px;">Informasi Dasar Program</h6></div>
                        
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">NAMA PELATIHAN (JUDUL)</label>
                            <input type="text" name="nama" id="f_nama" class="form-control rounded-pill border shadow-sm px-4" placeholder="Contoh: Pelatihan K3 Rumah Sakit" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">TEMA PELATIHAN</label>
                            <input type="text" name="tema" id="f_tema" class="form-control rounded-pill border shadow-sm px-4" placeholder="Contoh: Keselamatan dan Kesehatan Kerja">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-dark">PROGRAM</label>
                            <select name="program" id="f_program" class="form-select rounded-pill border shadow-sm px-4">
                                <option value="Webinar">Webinar</option>
                                <option value="Seminar">Seminar</option>
                                <option value="Workshop">Workshop</option>
                                <option value="Bimtek">Bimtek</option>
                                <option value="Pelatihan">Pelatihan</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-dark">KATEGORI ILMU</label>
                            <input type="text" name="kategori" id="f_kategori" class="form-control rounded-pill border shadow-sm px-4" placeholder="Kesehatan, IT, dll" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-dark">RANAH PEMENUHAN SKP</label>
                            <select name="ranah_skp" id="f_ranah_skp" class="form-select rounded-pill border shadow-sm px-4" onchange="updateTrainingCategories(this.value)">
                                <?php foreach ($ranah_skp_list ?? [] as $r): ?>
                                <option value="<?= esc($r) ?>"><?= esc($r) ?></option>
                                <?php endforeach; ?>
                                <?php if (empty($ranah_skp_list ?? [])): ?>
                                <option value="Pembelajaran">Pembelajaran (Learning)</option>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">KATEGORI KEGIATAN SKP</label>
                            <select name="kategori_kegiatan" id="f_kategori_kegiatan" class="form-select rounded-pill border shadow-sm px-4" required>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-dark">BOBOT SKP</label>
                            <input type="number" step="0.1" name="skp" id="f_skp" class="form-control rounded-pill border shadow-sm px-4" placeholder="Contoh: 2.5" oninput="this.value = this.value.replace(/[^0-9.]/g, '')" required>
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-dark">BIAYA</label>
                            <select name="biaya" id="f_biaya" class="form-select rounded-pill border shadow-sm px-4" onchange="togglePaidFields(this.value)">
                                <option value="Gratis">Gratis</option>
                                <option value="Berbayar">Berbayar</option>
                            </select>
                        </div>
                        <div id="paid_fields" class="col-md-9 d-none">
                            <div class="row g-2">
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold text-dark">BANK</label>
                                    <input type="text" name="nama_bank" id="f_nama_bank" class="form-control rounded-pill border shadow-sm px-4" placeholder="Mandiri/BCA/dll">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold text-dark">NO. REKENING</label>
                                    <input type="text" name="no_rekening" id="f_rekening" class="form-control rounded-pill border shadow-sm px-4" placeholder="123-45-678">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold text-dark">A.N REKENING</label>
                                    <input type="text" name="atas_nama" id="f_atas_nama" class="form-control rounded-pill border shadow-sm px-4" placeholder="Nama Pemilik">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold text-dark">NOMINAL (RP)</label>
                                    <input type="number" name="biaya_nominal" id="f_biaya_nominal" class="form-control rounded-pill border shadow-sm px-4" placeholder="150000" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-dark">LEVEL</label>
                            <select name="level" id="f_level" class="form-select rounded-pill border shadow-sm px-4">
                                <option value="Pemula">Pemula</option>
                                <option value="Menengah">Menengah</option>
                                <option value="Lanjut">Lanjut</option>
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-dark">JPL</label>
                            <input type="number" name="jpl" id="f_jpl" class="form-control rounded-pill border shadow-sm px-4" placeholder="Contoh: 10" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-dark">CAKUPAN</label>
                            <select name="cakupan" id="f_cakupan" class="form-select rounded-pill border shadow-sm px-4">
                                <option value="Lokal">Lokal</option>
                                <option value="Nasional">Nasional</option>
                                <option value="Internasional">Internasional</option>
                            </select>
                        </div>

                        <div class="col-md-12 mt-4"><h6 class="fw-bold text-danger border-bottom pb-2 text-uppercase small">Metode & Penyelenggaraan</h6></div>
                        
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-dark">MEKANISME</label>
                            <select name="mekanisme" id="f_mekanisme" class="form-select rounded-pill border shadow-sm px-4" onchange="toggleClosedFields(this.value)">
                                <option value="Terbuka">Terbuka (Umum)</option>
                                <option value="Tertutup">Tertutup (Khusus)</option>
                            </select>
                        </div>
                        <div id="closed_fields" class="col-md-6 d-none">
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-dark">TARGET KHUSUS (PROFESI)</label>
                                    <select name="target_khusus_profesi[]" id="f_target_khusus_profesi" class="form-select border shadow-sm" multiple="multiple">
                                        <option value="Semua Profesi">Semua Profesi</option>
                                        <?php if(isset($profesi)): foreach($profesi as $prf): ?>
                                            <option value="<?= $prf['nama_profesi'] ?>"><?= $prf['nama_profesi'] ?></option>
                                        <?php endforeach; endif; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-dark">TARGET KHUSUS (UNIT KERJA)</label>
                                    <select name="target_khusus_unit[]" id="f_target_khusus_unit" class="form-select border shadow-sm" multiple="multiple">
                                        <option value="Semua Unit Kerja">Semua Unit Kerja</option>
                                        <?php if(isset($unit_kerja)): foreach($unit_kerja as $uk): ?>
                                            <option value="<?= $uk['nama_unit'] ?>"><?= $uk['nama_unit'] ?></option>
                                        <?php endforeach; endif; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-dark">METODE</label>
                            <select name="metode" id="f_metode" class="form-select rounded-pill border shadow-sm px-4">
                                <option value="Online">Online</option>
                                <option value="Offline / Clasical">Offline / Clasical</option>
                                <option value="Blended / Hybrid">Blended / Hybrid</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-dark">NARASUMBER</label>
                            <select name="narasumber[]" id="f_narasumber" class="form-select border shadow-sm" multiple="multiple" required></select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-dark">PENYELENGGARA</label>
                            <select name="penyelenggara[]" id="f_penyelenggara" class="form-select border shadow-sm" multiple="multiple" required></select>
                        </div>
                        <div class="col-md-9">
                            <label class="form-label small fw-bold text-dark">KONTAK PENYEDIA</label>
                            <input type="text" name="kontak" id="f_kontak" class="form-control rounded-pill border shadow-sm px-4" placeholder="No Telp / WA" required>
                        </div>
                        
                        <div class="col-md-12 mt-4"><h6 class="fw-bold text-danger border-bottom pb-2 text-uppercase small">Jadwal & Kuota</h6></div>
                        
                        <div class="col-md-12">
                            <div class="p-3 bg-white border rounded-lg shadow-sm mb-3">
                                <div class="small fw-bold text-muted mb-3 text-uppercase"><i class="fas fa-calendar-check me-2 text-danger"></i> Masa Pendaftaran (Registrasi)</div>
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold">Pendaftaran Buka</label>
                                        <input type="date" name="reg_buka_tgl" id="f_reg_buka_tgl" class="form-control rounded-pill border shadow-sm px-3">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold">Waktu Buka</label>
                                        <input type="time" name="reg_buka_jam" id="f_reg_buka_jam" class="form-control rounded-pill border shadow-sm px-3">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold">Pendaftaran Tutup</label>
                                        <input type="date" name="reg_tutup_tgl" id="f_reg_tutup_tgl" class="form-control rounded-pill border shadow-sm px-3">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold">Waktu Tutup</label>
                                        <input type="time" name="reg_tutup_jam" id="f_reg_tutup_jam" class="form-control rounded-pill border shadow-sm px-3">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-dark">TGL MULAI PELAKSANAAN</label>
                            <input type="date" name="jadwal_mulai" id="f_jadwal_mulai" class="form-control rounded-pill border shadow-sm px-3" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-dark">JAM MULAI</label>
                            <input type="time" name="jam_mulai" id="f_jam_mulai" class="form-control rounded-pill border shadow-sm px-3" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-dark">TGL BERAKHIR</label>
                            <input type="date" name="jadwal_selesai" id="f_jadwal_selesai" class="form-control rounded-pill border shadow-sm px-3" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-dark">JAM BERAKHIR</label>
                            <input type="time" name="jam_selesai" id="f_jam_selesai" class="form-control rounded-pill border shadow-sm px-3" required>
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-dark">KUOTA</label>
                            <input type="number" name="kuota" id="f_kuota" class="form-control rounded-pill border shadow-sm px-4 text-center" placeholder="Jumlah" min="1" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                        </div>
                        <div class="col-md-9">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label small fw-bold text-dark mb-0">TARGET PROFESI</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="checkAllProfesi">
                                    <label class="form-check-label small fw-bold text-secondary" for="checkAllProfesi">Pilih Semua</label>
                                </div>
                            </div>
                            <div class="border rounded shadow-sm p-3 bg-white" style="max-height: 200px; overflow-y: auto;">
                                <div class="row">
                                <?php if(isset($profesi)): foreach($profesi as $index => $prf): ?>
                                    <div class="col-md-4 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input target-profesi-checkbox" type="checkbox" name="target_profesi[]" value="<?= $prf['nama_profesi'] ?>" id="profesi_<?= $index ?>">
                                            <label class="form-check-label small" for="profesi_<?= $index ?>">
                                                <?= $prf['nama_profesi'] ?>
                                            </label>
                                        </div>
                                    </div>
                                <?php endforeach; endif; ?>
                                </div>
                            </div>
                            <!-- Input hidden to enforce requirement visually if needed, though checkboxes can be validated manually -->
                            <div class="invalid-feedback d-block" id="profesi-error" style="display: none;">Harap pilih minimal satu target profesi.</div>
                        </div>

                        <div class="col-md-12 mt-4"><h6 class="fw-bold text-danger border-bottom pb-2 text-uppercase small">Deskripsi & Konten</h6></div>
                        
                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-dark">PENGUMUMAN (OPSIONAL)</label>
                            <input type="text" name="pengumuman" id="f_pengumuman" class="form-control rounded-pill border shadow-sm px-4" placeholder="Informasi penting">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-dark">TUJUAN PELATIHAN</label>
                            <textarea name="tujuan" id="f_tujuan" class="form-control border shadow-sm p-3" rows="2" style="border-radius: 15px;" required></textarea>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-dark">DESKRIPSI LENGKAP</label>
                            <textarea name="deskripsi" id="f_deskripsi" class="form-control border shadow-sm p-3" rows="3" style="border-radius: 15px;" required></textarea>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-dark">KOMPETENSI (PISAHKAN DENGAN KOMA)</label>
                            <input type="text" name="kompetensi" id="f_kompetensi" class="form-control rounded-pill border shadow-sm px-4" placeholder="Leadership, Komunikasi Medis" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-dark">GAMBAR PELATIHAN (OPSIONAL)</label>
                            <input type="file" name="gambar_pelatihan" id="f_gambar_pelatihan" class="form-control form-control-file rounded-pill border shadow-sm px-4 py-2" accept="image/*">
                            <small class="text-muted d-block mt-1 ms-3">Format yang didukung: JPG, JPEG, PNG, WEBP. Maksimal 2MB. Biarkan kosong jika tidak ingin mengubah gambar.</small>
                            <small id="gambar_pelatihan_info" class="text-danger fw-bold d-none mt-1 ms-3"></small>
                        </div>
                    </div>
                    <div class="mt-5 text-center">
                        <button type="button" class="btn btn-dark rounded-pill px-5 py-2 fw-bold me-2" data-bs-dismiss="modal">BATAL</button>
                        <button type="submit" class="btn btn-danger rounded-pill px-5 py-2 fw-bold shadow">SIMPAN DATA PELATIHAN</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
