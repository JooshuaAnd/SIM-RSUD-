<?= $this->include('Pendidikan/Institusi/layout/header') ?>
<?= $this->include('Pendidikan/Institusi/layout/sidebar') ?>

<div class="row mt-4">
    <div class="col-12 text-center">
        <div class="card border-0 shadow-sm py-5 px-3">
            <div class="card-body">
                <div class="mb-4 text-danger">
                    <i class="fas fa-times-circle fa-6x"></i>
                </div>
                <h3 class="fw-bold text-dark">Registrasi Institusi Ditolak</h3>
                <p class="text-muted mb-4 mx-auto" style="max-width: 600px;">
                    Mohon maaf, pengajuan pendaftaran institusi Anda tidak dapat disetujui oleh Admin Diklat RSUD. Anda dapat melihat alasan penolakan secara detail melalui tombol di bawah ini.
                </p>
                <button type="button" class="btn btn-danger btn-lg fw-bold px-5 rounded-pill" data-bs-toggle="modal" data-bs-target="#alasanModal">
                    <i class="fas fa-envelope-open-text me-2"></i> Lihat Alasan Penolakan
                </button>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4 justify-content-center">
    <div class="col-md-8 text-center">
        <p class="small text-muted">
            Jika Anda merasa terjadi kesalahan atau memiliki pertanyaan lebih lanjut, silakan hubungi bagian tata usaha Diklat RSUD via Telepon atau WhatsApp.
        </p>
        <a href="<?= base_url('pendidikan/logout') ?>" class="btn btn-outline-secondary mt-2">
            <i class="fas fa-sign-out-alt me-1"></i> Keluar
        </a>
    </div>
</div>

<!-- Modal Alasan Penolakan -->
<div class="modal fade" id="alasanModal" tabindex="-1" aria-labelledby="alasanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-danger text-white border-0">
                <h5 class="modal-title fw-bold" id="alasanModalLabel"><i class="fas fa-info-circle me-2"></i> Detail Penolakan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-light text-center">
                <h6 class="text-muted text-uppercase small fw-bold mb-3">Catatan dari Admin Diklat:</h6>
                <div class="bg-white p-4 rounded shadow-sm border">
                    <p class="mb-0 fs-5 text-dark italic">
                        "<?= esc($alasan_penolakan) ?>"
                    </p>
                </div>
            </div>
            <div class="modal-footer border-0 justify-content-center">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<?= $this->include('Pendidikan/Institusi/layout/footer') ?>
