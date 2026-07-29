<?= $this->extend('Pelatihan/layout/peserta_layout') ?>

<?= $this->section('content') ?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-0 shadow-lg rounded-lg overflow-hidden">
            <div class="card-header bg-primary-custom text-white p-4 border-0 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-bold mb-0">Kuis Evaluasi: <?= $p['nama'] ?></h5>
                    <p class="small mb-0 opacity-75">Jawablah pertanyaan berikut dengan benar (Minimal kelulusan: 70)</p>
                </div>
                <div class="bg-white text-primary-custom rounded-pill px-3 py-1 fw-bold small shadow-sm">
                    <i class="fas fa-clock me-1"></i> Waktu: 15:00
                </div>
            </div>
            <div class="card-body p-5">
                <form action="<?= base_url('pelatihan/peserta/kuis/submit/'.$id) ?>" method="POST">
                    
                    <?php if(empty($kuis)): ?>
                        <!-- Simulasi jika data kuis kosong -->
                        <div class="mb-5">
                            <h6 class="fw-bold">1. Menurut Anda, apa manfaat utama dari pelatihan ini?</h6>
                            <div class="mt-3">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="q1" id="q1a" value="A" required>
                                    <label class="form-check-label" for="q1a">A. Meningkatkan kompetensi teknis</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="q1" id="q1b" value="B">
                                    <label class="form-check-label" for="q1b">B. Sekedar mendapatkan sertifikat</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="q1" id="q1c" value="C">
                                    <label class="form-check-label" for="q1c">C. Tidak ada manfaat</label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-5">
                            <h6 class="fw-bold">2. Manakah prosedur yang benar dalam pendaftaran online?</h6>
                            <div class="mt-3">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="q2" id="q2a" value="A" required>
                                    <label class="form-check-label" for="q2a">A. Isi data diri - Upload dokumen - Selesai</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="q2" id="q2b" value="B">
                                    <label class="form-check-label" for="q2b">B. Selesai - Upload - Isi data</label>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php $no=1; foreach($kuis as $k): ?>
                            <div class="mb-5">
                                <h6 class="fw-bold"><?= $no++ ?>. <?= $k['soal'] ?></h6>
                                <div class="mt-3">
                                    <?php 
                                    $labels = ['A', 'B', 'C', 'D'];
                                    foreach($k['opsi'] as $idx => $opsi): 
                                    ?>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="q<?= $no ?>" id="q<?= $no ?>_<?= $idx ?>" value="<?= $labels[$idx] ?? 'X' ?>" required>
                                        <label class="form-check-label" for="q<?= $no ?>_<?= $idx ?>">
                                            <?= $labels[$idx] ?? '' ?>. <?= $opsi ?>
                                        </label>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <div class="alert alert-warning border-0 small mt-5">
                        <i class="fas fa-exclamation-triangle me-2"></i> Periksa kembali jawaban Anda. Anda memiliki kesempatan maksimal 2 kali untuk mencoba kuis ini.
                    </div>

                    <div class="text-end mt-4 border-top pt-4">
                        <button type="submit" class="btn btn-primary-custom px-5 py-2 rounded-pill fw-bold shadow-sm">Kumpulkan Jawaban <i class="fas fa-paper-plane ms-2"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
