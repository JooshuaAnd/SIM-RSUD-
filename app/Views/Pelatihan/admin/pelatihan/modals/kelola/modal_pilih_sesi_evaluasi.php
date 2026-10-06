<div class="modal fade" id="modalPilihSesiEvaluasi" tabindex="-1" aria-labelledby="pilihSesiEvaluasiTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-lg">
            <div class="modal-header bg-primary-custom text-white border-0">
                <h5 class="modal-title fw-bold" id="pilihSesiEvaluasiTitle">
                    <i class="fas fa-link me-2"></i> Hubungkan Tes ke Sesi
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <form id="formPilihSesiEvaluasi" onsubmit="lanjutkanPengaturanEvaluasi(event)">
                <div class="modal-body p-4">
                    <input type="hidden" id="jenisEvaluasiDipilih" value="">
                    <p class="text-muted small mb-4" id="pilihSesiEvaluasiDescription"></p>

                    <label for="sesiEvaluasiDipilih" class="form-label small fw-bold text-dark">SESI PELATIHAN</label>
                    <select class="form-select border shadow-sm px-3" id="sesiEvaluasiDipilih" aria-describedby="statusSesiEvaluasi" onchange="updateStatusSesiEvaluasi()" required>
                        <option value="">-- Pilih sesi pelatihan --</option>
                        <?php foreach (($sesiList ?? []) as $sesi) : ?>
                            <?php
                                $tanggalSesi = !empty($sesi['tanggal']) ? date('d M Y', strtotime($sesi['tanggal'])) : 'Tanggal belum diatur';
                                $preAda = !empty($sesi['pre_test']);
                                $postAda = !empty($sesi['post_test']);
                            ?>
                            <option
                                value="<?= (int) $sesi['id'] ?>"
                                data-pre-exists="<?= $preAda ? '1' : '0' ?>"
                                data-post-exists="<?= $postAda ? '1' : '0' ?>"
                            >
                                <?= esc($sesi['nama_sesi'] ?: 'Sesi tanpa nama') ?> — <?= esc($tanggalSesi) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <small class="text-muted d-block mt-2" id="statusSesiEvaluasi">Pilih sesi untuk membuat dan mengatur tes.</small>
                </div>
                <div class="modal-footer border-0 bg-light">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-custom rounded-pill px-4 shadow-sm">
                        Lanjut ke Pengaturan Soal <i class="fas fa-arrow-right ms-1"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
