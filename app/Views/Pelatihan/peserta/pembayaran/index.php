<?= $this->extend('Pelatihan/layout/peserta_layout') ?>

<?= $this->section('content') ?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card border-0 shadow-lg rounded-lg overflow-hidden">
            <div class="bg-primary-custom p-4 text-center text-white">
                <h5 class="fw-bold mb-0 text-uppercase">PEMBAYARAN PELATIHAN</h5>
            </div>
            <div class="card-body p-5">
                <div class="alert alert-warning border-0 small rounded-lg p-3 mb-4">
                    <i class="fas fa-exclamation-triangle me-2"></i> Pendaftaran Anda hampir selesai. Silakan lakukan pembayaran untuk mendapatkan akses materi.
                </div>

                <div class="text-center mb-5">
                    <p class="text-muted small fw-bold mb-1">TOTAL TAGIHAN</p>
                    <h2 class="fw-bold text-primary-custom">Rp 500.000</h2>
                </div>

                <div class="p-4 bg-light rounded-lg mb-5 border-start border-primary-custom border-4">
                    <h6 class="fw-bold mb-3 small">METODE PEMBAYARAN</h6>
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/5c/Bank_Central_Asia.svg/1200px-Bank_Central_Asia.svg.png" height="20" alt="BCA">
                        <div>
                            <div class="small fw-bold">Bank BCA</div>
                            <div class="fw-bold text-dark h5 mb-0">1234567890</div>
                            <small class="text-muted">a.n NusaLMS Academy</small>
                        </div>
                    </div>
                </div>

                <form action="<?= base_url('pelatihan/peserta/pembayaran/upload/'.$id) ?>" method="POST" enctype="multipart/form-data">
                    <div class="mb-5">
                        <label class="form-label small fw-bold text-muted text-uppercase">UPLOAD BUKTI PEMBAYARAN</label>
                        <div class="border-2 border-dashed rounded-lg p-4 text-center bg-light cursor-pointer" onclick="document.getElementById('file_bukti').click()">
                            <i class="fas fa-cloud-upload-alt text-muted fa-2x mb-2"></i>
                            <p class="small text-muted mb-0">Klik untuk memilih file bukti bayar (JPG/PNG)</p>
                            <input type="file" name="bukti" id="file_bukti" class="d-none" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary-custom w-100 py-3 rounded-pill fw-bold shadow">UNGGAH SEKARANG <i class="fas fa-check-circle ms-2"></i></button>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
