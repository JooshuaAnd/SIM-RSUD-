<?php
$p = $p ?? [];
$sesiList = $sesiList ?? [];
?>
<div class="modal fade" id="modalTambahMateri" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-lg overflow-hidden">
            <div class="modal-header bg-primary-custom text-white border-0">
                <h5 class="modal-title fw-bold small"><i class="fas fa-plus-circle me-2"></i> Tambah Materi Pembelajaran</h5>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-warning btn-sm rounded-pill fw-bold" onclick="autofillMateri()" title="Auto-fill materi untuk testing">
                        <i class="fas fa-bolt me-1"></i> TESTING
                    </button>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
            </div>
            <div class="modal-body p-4 bg-light">
                <form action="<?= base_url('pelatihan/admin/pelatihan/materi/simpan') ?>" method="POST" id="formMateri" enctype="multipart/form-data">
                    <input type="hidden" name="pelatihan_id" value="<?= $p['id'] ?>">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Judul Materi</label>
                        <input type="text" name="judul" class="form-control rounded-pill" placeholder="Contoh: Modul 1 - Pendahuluan" required>
                    </div>
                        <div class="row g-2 mb-3">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-muted">Segmen ke-</label>
                                <input type="number" name="segmen" class="form-control rounded-pill" placeholder="Otomatis" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            </div>
                            <div class="col-md-8">
                                <label class="form-label small fw-bold text-muted">Terkait Sesi (Opsional)</label>
                                <select name="sesi_id" class="form-select rounded-pill">
                                    <option value="">-- Tidak Terkait Sesi --</option>
                                    <?php if(!empty($sesiList)): foreach($sesiList as $s): ?>
                                    <option value="<?= $s['id'] ?>"><?= ($s['tipe_sesi'] == 'online' ? '[Online] ' : '[Offline] ') . $s['nama_sesi'] ?></option>
                                    <?php endforeach; endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Tipe Materi</label>
                            <select name="tipe" id="tipeMateriInput" class="form-select rounded-pill">
                                <option value="video">Video Pembelajaran</option>
                                <option value="foto">Foto / Gambar</option>
                                <option value="pdf">Dokumen PDF</option>
                                <option value="docs">Dokumen (Word)</option>
                                <option value="excel">Dokumen (Excel)</option>
                                <option value="audio">Rekaman Suara (Audio)</option>
                                <option value="link">Link Eksternal</option>
                                <option value="lainnya">Lainnya</option>
                            </select>
                        </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Deskripsi Singkat</label>
                        <textarea name="deskripsi" class="form-control" rows="3" style="border-radius: 15px;" placeholder="Jelaskan isi materi ini..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">File / Link Materi</label>
                        
                        <div id="fileUploadContainer">
                            <div class="border rounded-pill bg-white p-2 px-3 d-flex justify-content-between align-items-center">
                                <span class="small text-muted" id="fileName">Pilih file materi...</span>
                                <button type="button" class="btn btn-light btn-sm rounded-pill" onclick="document.getElementById('fileInput').click()">Browse</button>
                            </div>
                            <input type="file" name="file_materi" id="fileInput" style="display:none" accept=".mp4,.webm,.ogg,.mov,.avi,.mkv,.wmv" onchange="document.getElementById('fileName').innerText = this.files[0].name">
                            <div id="formatHint" class="small text-muted mt-2">Format yang didukung: Video (.mp4, .webm, .ogg, .mov, .avi, .mkv, .wmv)</div>
                        </div>

                        <div id="linkInputContainer" style="display:none;">
                            <input type="url" name="link_materi" id="linkMateriInput" class="form-control rounded-pill" placeholder="https://...">
                            <div class="small text-muted mt-2">Masukkan link eksternal materi.</div>
                        </div>
                    </div>
                    
                    <script>
                        document.getElementById('tipeMateriInput').addEventListener('change', function() {
                            const fileInput = document.getElementById('fileInput');
                            const hint = document.getElementById('formatHint');
                            const fileContainer = document.getElementById('fileUploadContainer');
                            const linkContainer = document.getElementById('linkInputContainer');
                            
                            if (this.value === 'link') {
                                fileContainer.style.display = 'none';
                                linkContainer.style.display = 'block';
                            } else {
                                fileContainer.style.display = 'block';
                                linkContainer.style.display = 'none';
                                
                                switch(this.value) {
                                    case 'video':
                                        fileInput.accept = '.mp4,.webm,.ogg,.mov,.avi,.mkv,.wmv';
                                        hint.innerText = 'Format yang didukung: Video (.mp4, .webm, .ogg, .mov, .avi, .mkv, .wmv)';
                                        break;
                                    case 'audio':
                                        fileInput.accept = '.mp3,.wav,.m4a';
                                        hint.innerText = 'Format yang didukung: Audio (.mp3, .wav, .m4a)';
                                        break;
                                    case 'foto':
                                        fileInput.accept = '.jpg,.jpeg,.png,.webp';
                                        hint.innerText = 'Format yang didukung: Foto/Gambar (.jpg, .jpeg, .png, .webp)';
                                        break;
                                    case 'pdf':
                                        fileInput.accept = '.pdf';
                                        hint.innerText = 'Format yang didukung: PDF (.pdf)';
                                        break;
                                    case 'docs':
                                        fileInput.accept = '.doc,.docx';
                                        hint.innerText = 'Format yang didukung: Word Document (.doc, .docx)';
                                        break;
                                    case 'excel':
                                        fileInput.accept = '.xls,.xlsx';
                                        hint.innerText = 'Format yang didukung: Excel (.xls, .xlsx)';
                                        break;
                                    default:
                                        fileInput.accept = '*';
                                        hint.innerText = 'Format yang didukung: Semua file';
                                        break;
                                }
                                
                                // Reset file input when type changes
                                fileInput.value = '';
                                document.getElementById('fileName').innerText = 'Pilih file materi...';
                            }
                        });
                    </script>
                    <div class="mt-4 pt-3 border-top text-end">
                        <button type="button" class="btn btn-light btn-lg rounded-pill px-5 fw-bold" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary-custom btn-lg rounded-pill px-5 fw-bold shadow"><i class="fas fa-save me-1"></i> Simpan Materi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Materi -->
