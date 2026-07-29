<?= $this->extend('Pelatihan/layout/admin_pengabdian_layout') ?>

<?= $this->section('content') ?>

<div class="card border-0 shadow-sm rounded-lg overflow-hidden bg-white mb-5">
    <div class="card-header bg-white p-4 border-bottom d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
        <div>
            <h5 class="fw-bold mb-1 text-dark small text-uppercase"><i class="fas fa-users me-2 text-danger"></i> Verifikasi Pengabdian Masyarakat</h5>
            <div class="text-muted small">Kelola verifikasi pengabdian peserta secara langsung tanpa navigasi berulang.</div>
        </div>
        <div class="badge bg-danger-subtle text-danger rounded-pill px-3 fw-bold border border-danger border-opacity-10">ACC PENGABDIAN MASYARAKAT</div>
    </div>
    <div class="tab-content" id="certTabContent">
        <!-- PENGABDIAN MASYARAKAT (Active Tab) -->
        <div class="tab-pane fade show active" id="pengabdian" role="tabpanel">
            <div class="table-responsive bg-white">
                <table id="tableSertifikat" class="table table-hover align-middle mb-0">
                    <thead class="bg-white text-muted small fw-bold">
                        <tr>
                            <th class="ps-4">PENGIRIM (DETAIL)</th>
                            <th>KEGIATAN & KATEGORI JPL</th>
                            <th class="text-center">BERKAS BUKTI</th>
                            <th class="pe-4 text-center">AKSI VERIFIKASI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($sertifikat)): ?>
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted fw-bold">Belum ada unggahan baru.</td>
                            </tr>
                            <?php else: foreach ($sertifikat as $s): ?>
                                <tr class="searchable-row">
                                    <td class="ps-4 py-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <img src="https://ui-avatars.com/api/?name=<?= urlencode($s['user_nama']) ?>&background=0f172a&color=fff" class="rounded-circle" width="40">
                                            <div>
                                                <div class="fw-bold small text-dark"><?= $s['user_nama'] ?></div>
                                                <div class="text-muted" style="font-size: 0.65rem;">
                                                    <i class="fas fa-id-card me-1"></i> <?= $s['user_profesi'] ?><br>
                                                    <i class="fas fa-building me-1"></i> RSUD KOTA YOGYAKARTA
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-start justify-content-between">
                                            <div>
                                                <div class="fw-bold small text-dark"><?= $s['judul'] ?></div>
                                                <div class="badge bg-danger-subtle text-danger small mt-1 text-uppercase" style="font-size: 0.55rem;"><?= $s['ranah'] ?? 'PENGABDIAN' ?></div>
                                                <div class="text-muted mt-1 fw-bold" style="font-size: 0.6rem;">
                                                    <i class="fas fa-tag me-1"></i> <?= $s['kategori_kegiatan'] ?? 'Kegiatan Pengabdian' ?><br>
                                                    <i class="fas fa-calendar-alt me-1"></i> <?= tanggal_indo($s['tgl_mulai'] ?? 'now') ?>
                                                </div>
                                            </div>
                                            <div class="text-end d-flex flex-column align-items-end gap-2">
                                                <span class="badge bg-danger-subtle text-danger px-3 py-1 fs-6 rounded-pill border border-danger border-opacity-25"><?= (float)($s['skp'] ?? 0) ?> JPL</span>
                                                <button class="btn btn-light btn-sm rounded-pill px-3 fw-bold text-danger text-nowrap shadow-sm border" data-item='<?= htmlspecialchars(json_encode($s), ENT_QUOTES, "UTF-8") ?>' onclick='showActivityDetail(JSON.parse(this.getAttribute("data-item")))'>
                                                    <i class="fas fa-search me-1"></i> LIHAT DETAIL
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <?php if (!empty($s['file_path'])): ?>
                                            <a href="<?= base_url($s['file_path']) ?>" target="_blank" class="btn btn-outline-dark btn-sm rounded-pill px-3 fw-bold d-inline-flex align-items-center justify-content-center">
                                                <i class="fas fa-file-pdf me-2 text-danger"></i> SERTIFIKAT
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted small">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="pe-4 text-center">
                                        <div class="d-flex gap-2 justify-content-center">
                                            <?php if (($s['verifikasi'] ?? 'pending') == 'pending'): ?>
                                                <button class="btn btn-danger btn-sm rounded-pill px-3 fw-bold shadow-sm" onclick='showRejectModal(<?= $s['id'] ?>)'>TOLAK</button>
                                                <button class="btn btn-success btn-sm rounded-pill px-3 fw-bold shadow-sm" onclick='confirmApprove(<?= $s['id'] ?>)'>TERIMA</button>
                                            <?php elseif (($s['verifikasi']) == 'approved'): ?>
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="badge bg-success py-2 px-3 rounded-pill fw-bold"><i class="fas fa-check-circle me-1"></i> TERVERIFIKASI</span>
                                                </div>
                                            <?php elseif (($s['verifikasi']) == 'rejected'): ?>
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="badge bg-danger py-2 px-3 rounded-pill fw-bold"><i class="fas fa-times-circle me-1"></i> DITOLAK</span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                        <?php endforeach;
                        endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white border-0 py-3 d-flex justify-content-end">
                <?php if ($pager ?? null): ?>
                    <style>
                        .pagination { display: flex; list-style: none; padding: 0; margin: 0; gap: 6px; align-items: center; }
                        .pagination li a, .pagination li span { 
                            color: #64748b; 
                            text-decoration: none;
                            border: none; 
                            background-color: #f1f5f9; 
                            border-radius: 8px !important; 
                            font-weight: 600; 
                            font-size: 0.85rem;
                            padding: 0.4rem 0.9rem; 
                            transition: all 0.2s ease; 
                            display: block;
                        }
                        .pagination li.active a, .pagination li.active span { 
                            background-color: #ce2127; 
                            color: white; 
                        }
                        .pagination li a:hover { 
                            background-color: #e2e8f0; 
                            color: #ce2127; 
                        }
                        .pagination li.disabled span {
                            opacity: 0.5;
                            cursor: not-allowed;
                        }
                    </style>
                    <?= isset($pager) ? $pager->links('sertifikat', 'custom_pagination') : '' ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal Approval / Verification Detail (Dynamic Fields) -->
<div class="modal fade" id="modalApproveSKP" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-lg overflow-hidden">
            <div class="modal-header bg-dark text-white border-0 p-4">
                <h5 class="modal-title fw-bold"><i class="fas fa-check-circle me-2 text-success"></i> VERIFIKASI & PENETAPAN CAPAIAN</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formApproveSKP" method="POST">
                <div class="modal-body p-4 bg-light">
                    <div class="row g-3 mb-4">
                        <div class="col-md-12">
                            <div class="bg-white p-3 rounded-4 shadow-sm border-start border-danger border-5">
                                <div class="small text-muted fw-bold text-uppercase mb-1">Peserta & Kegiatan</div>
                                <div class="fw-bold text-dark h6 mb-1" id="app_user_name">-</div>
                                <div class="fw-bold text-danger small" id="app_judul">-</div>
                            </div>
                        </div>
                    </div>

                    <!-- Dynamic Fields Grid -->
                    <div id="app_dynamic_fields" class="row g-3">
                        <!-- Populated by JS -->
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="button" class="btn btn-white border rounded-pill px-4 fw-bold shadow-sm" data-bs-dismiss="modal">BATAL</button>
                        <button type="submit" class="btn btn-danger rounded-pill px-5 fw-bold shadow-sm">SIMPAN</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Reject Detail -->
<div class="modal fade" id="modalRejectSKP" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-lg overflow-hidden">
            <div class="modal-header bg-dark text-white border-0 p-4">
                <h5 class="modal-title fw-bold"><i class="fas fa-times-circle me-2 text-danger"></i> PENOLAKAN PENGAJUAN</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formRejectSKP" method="POST">
                <div class="modal-body p-4 bg-light">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small">Alasan Penolakan</label>
                        <textarea class="form-control rounded-3 border-0 shadow-sm" name="alasan_penolakan" rows="4" placeholder="Masukkan alasan mengapa kegiatan ini ditolak..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 bg-white">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">BATAL</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-5 fw-bold shadow">TOLAK PENGAJUAN</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    let modalApprove;

    const fieldLabels = {
        'institusi': 'Institusi Kerja',
        'jenis_kegiatan': 'Jenis Kegiatan',
        'rincian': 'Rincian Kegiatan',
        'tempat': 'Tempat Kegiatan',
        'periode': 'Periode Kunjungan',
        'jumlah_kunjungan': 'Jumlah Kunjungan',
        'temuan_kasus': 'Temuan Kasus',
        'jumlah_pasien': 'Jumlah Pasien',
        'jumlah_tindakan': 'Jumlah Tindakan',
        'jenis_tindakan': 'Jenis Tindakan',
        'status_peneliti': 'Status Peneliti',
        'area': 'Area Publikasi',
        'tahun': 'Tahun Terbit',
        'edisi': 'Edisi/Vol',
        'penerbit': 'Penerbit/Jurnal',
        'keterangan': 'Catatan Tambahan',
        'waktu_jam': 'Durasi (Jam)',
        'tgl_mulai': 'Tgl Mulai',
        'tgl_selesai': 'Tgl Selesai',
        'ranah': 'Ranah Kegiatan',
        'kategori_kegiatan': 'Kategori Kegiatan',
        'tgl_upload': 'Tgl Upload',
        'no_sertifikat': 'No Sertifikat',
        'jam_pelajaran': 'Jam Pelajaran',
        'peran': 'Peran',
        'materi': 'Materi',
        'topik': 'Topik',
        'tujuan': 'Tujuan'
    };

    function showApproveModal(s) {
        if (!modalApprove) {
            modalApprove = new bootstrap.Modal(document.getElementById('modalApproveSKP'));
        }

        document.getElementById('app_user_name').innerText = s.user_nama;
        document.getElementById('app_judul').innerText = s.judul;
        document.getElementById('formApproveSKP').action = "<?= base_url('pelatihan/admin_pengabdian/certificate/updateskp/') ?>" + s.id;

        const container = document.getElementById('app_dynamic_fields');
        container.innerHTML = '';

        // Exclude internal keys
        const exclude = ['id', 'user_id', 'user_nama', 'user_profesi', 'verifikasi', 'tgl_verifikasi', 'dokumen', 'dokumen_st', 'jenis_dokumen', 'judul', 'skp', 'created_at', 'updated_at', 'alasan_penolakan', 'file_path'];

        for (const [key, val] of Object.entries(s)) {
            if (!exclude.includes(key) && val) {
                const label = fieldLabels[key] || key.replace(/_/g, ' ').toUpperCase();
                const displayVal = key === 'file_path' ? val.split('/').pop() : val;
                const col = document.createElement('div');
                col.className = 'col-md-6';
                col.innerHTML = `
                    <div class="p-3 bg-white border rounded-lg h-100 shadow-sm">
                        <label class="small text-muted fw-bold text-uppercase mb-1 d-block">${label}</label>
                        <input type="text" name="${key}" class="form-control form-control-sm border-0 fw-bold p-0 text-dark" value="${displayVal}" readonly>
                    </div>
                `;
                container.appendChild(col);
            }
        }

        const skpCol = document.createElement('div');
        skpCol.className = 'col-md-6';
        skpCol.innerHTML = `
            <div class="p-2 bg-white rounded-4 border border-danger border-opacity-25 h-100 d-flex flex-column justify-content-center">
                <label class="form-label small fw-bold text-danger text-uppercase mb-1 px-1">VALIDASI NILAI JPL AKHIR</label>
                <div class="input-group">
                    <input type="number" step="any" name="skp" id="app_skp_value" class="form-control border-danger fw-bold text-center form-control-lg" placeholder="0" required style="height: 48px; font-size: 1.1rem;">
                    <span class="input-group-text bg-danger text-white border-danger fw-bold px-3" style="height: 48px;">JPL</span>
                </div>
            </div>
        `;
        container.appendChild(skpCol);
        
        document.getElementById('app_skp_value').value = parseFloat(s.skp || 0);

        modalApprove.show();
    }

    function showActivityDetail(s) {
        showApproveModal(s);
    }

    let modalReject;

    function showRejectModal(id) {
        if (!modalReject) {
            modalReject = new bootstrap.Modal(document.getElementById('modalRejectSKP'));
        }
        document.getElementById('formRejectSKP').action = "<?= base_url('pelatihan/admin_pengabdian/certificate/reject/') ?>" + id;
        modalReject.show();
    }

    function confirmApprove(id) {
        confirmAction('Setujui Pengajuan?', 'Pastikan JPL akhir sudah Anda simpan. Data akan disetujui dan JPL peserta akan dihitung ulang.', () => {
            let form = document.createElement('form');
            form.method = 'POST';
            form.action = "<?= base_url('pelatihan/admin_pengabdian/certificate/approve/') ?>" + id;
            document.body.appendChild(form);
            form.submit();
        });
    }


</script>
<?= $this->endSection() ?>
