<div class="modal fade" id="modalKelolaQuiz" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow rounded-lg">
            <div class="modal-header bg-primary-custom text-white border-0">
                <h5 class="modal-title fw-bold" id="quizModalTitle">Kelola Soal</h5>
                <div class="d-flex align-items-center gap-2">

                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
            </div>
            <div class="modal-body p-0">
                <input type="hidden" id="current_tipe_evaluasi" value="">
                <input type="hidden" id="current_evaluasi_id" value="">
                
                <div class="p-4 bg-white border-bottom">
                    <form id="formKkm" onsubmit="event.preventDefault(); saveKkm();">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold mb-1">Nilai Ambang Kelulusan (KKM)</label>
                                <div class="input-group input-group-sm w-75">
                                    <input type="number" class="form-control" id="evaluasi_kkm" required>
                                    <span class="input-group-text">/ 100</span>
                                    <button type="submit" class="btn btn-primary btn-sm">Simpan KKM</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="p-4 bg-white border-bottom d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small fw-bold text-uppercase">Daftar Pertanyaan</span>
                    </div>
                    <button class="btn btn-primary btn-sm rounded-pill px-3" onclick="tambahSoal()">
                        <i class="fas fa-plus me-1"></i> Tambah Soal
                    </button>
                </div>
                
                <div id="soalContainer" class="p-4 bg-light" style="max-height: 500px; overflow-y: auto;">
                    <div class="text-center text-muted small py-4" id="loadingSoal">
                        <i class="fas fa-spinner fa-spin me-2"></i> Memuat soal...
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 bg-light">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary-custom rounded-pill px-5 shadow-sm" onclick="confirmSaveAll()">
                    <i class="fas fa-save me-1"></i> Simpan Semua Soal
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Kelola Feedback -->
