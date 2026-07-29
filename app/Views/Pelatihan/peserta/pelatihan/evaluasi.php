<?= $this->extend('Pelatihan/layout/peserta_layout') ?>

<?= $this->section('content') ?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm overflow-hidden rounded-lg mb-4">
            <div class="card-header bg-white p-4 border-bottom text-center">
                <h4 class="fw-bold mb-1">Evaluasi Penyelenggaraan Pelatihan</h4>
                <p class="text-muted small mb-0">Umpan balik Anda sangat berarti untuk perbaikan mutu pelatihan kami.</p>
            </div>
            <div class="card-body p-5">
                <form action="<?= base_url('pelatihan/peserta/evaluasi/submit/'.$id) ?>" method="POST">
                    
                    <h5 class="fw-bold mb-4 text-primary-custom border-bottom pb-2">I. KUALITAS MATERI</h5>
                    <div class="mb-5">
                        <p class="fw-bold small mb-3">1. Seberapa baik dan relevan Kualitas Materi yang diberikan?</p>
                        <div class="d-flex justify-content-between px-3 mb-2">
                            <?php for($i=1; $i<=5; $i++): ?>
                            <div class="text-center">
                                <input type="radio" name="materi_relevansi" value="<?= $i ?>" class="form-check-input d-block mx-auto mb-1" required>
                                <label class="small text-muted"><?= $i ?></label>
                            </div>
                            <?php endfor; ?>
                        </div>
                        <div class="d-flex justify-content-between small text-muted">
                            <span>Sangat Kurang</span>
                            <span>Sangat Baik</span>
                        </div>
                    </div>

                    <h5 class="fw-bold mb-4 text-primary-custom border-bottom pb-2">II. KUALITAS NARASUMBER</h5>
                    <div class="mb-5">
                        <p class="fw-bold small mb-3">2. Bagaimana Kualitas Narasumber selama sesi pembelajaran?</p>
                        <select class="form-select border-0 bg-light" name="fasilitator_score" required>
                            <option value="">Pilih Penilaian...</option>
                            <option value="5">Sangat Baik</option>
                            <option value="4">Baik</option>
                            <option value="3">Cukup</option>
                            <option value="2">Kurang</option>
                            <option value="1">Sangat Kurang</option>
                        </select>
                    </div>

                    <h5 class="fw-bold mb-4 text-primary-custom border-bottom pb-2">III. KUALITAS KELOLA</h5>
                    <div class="mb-5">
                        <p class="fw-bold small mb-3">3. Bagaimana Kualitas Kelola layanan administrasi dan teknis?</p>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="fasilitas[]" value="Akses Materi Lancar" id="f1">
                            <label class="form-check-label small" for="f1">Akses Materi Lancar</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="fasilitas[]" value="Komunikasi Panitia Baik" id="f2">
                            <label class="form-check-label small" for="f2">Komunikasi Panitia Baik</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="fasilitas[]" value="Sistem LMS Stabil" id="f3">
                            <label class="form-check-label small" for="f3">Sistem LMS Stabil</label>
                        </div>
                    </div>

                    <div class="mb-5">
                        <p class="fw-bold small mb-3">4. Saran dan masukan untuk pelatihan ini di masa mendatang (Opsional).</p>
                        <textarea class="form-control border-0 bg-light" rows="4" placeholder="Tulis masukan Anda di sini..."></textarea>
                    </div>

                    <div class="alert alert-warning border-0 small rounded-lg p-3 mb-5">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="anonim">
                            <label class="form-check-label fw-bold" for="anonim">Kirim sebagai Anonim (Sembunyikan identitas saya)</label>
                        </div>
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-primary-custom px-5 py-3 rounded-pill fw-bold shadow">SUBMIT EVALUASI & SELESAI <i class="fas fa-check-circle ms-2"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
