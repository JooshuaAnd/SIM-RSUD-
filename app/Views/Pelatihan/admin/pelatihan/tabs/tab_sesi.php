<div class="tab-pane fade show active" id="tab-sesi" role="tabpanel">
    <div class="card border-0 shadow-sm rounded-lg mb-4">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h5 class="fw-bold text-dark mb-1">Manajemen Sesi Pelatihan</h5>
                    <p class="text-muted small mb-0">Kelola jadwal sesi online maupun offline.</p>
                </div>
                <button class="btn btn-primary btn-sm rounded-pill px-3 fw-bold shadow-sm" onclick="openSesiModal()">
                    <i class="fas fa-plus me-1"></i> Tambah Sesi
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle border">
                    <thead class="bg-light text-dark">
                        <tr>
                            <th class="py-3 px-4">Tipe</th>
                            <th class="py-3">Nama Sesi</th>
                            <th class="py-3">Waktu</th>
                            <th class="py-3">Detail Pelaksanaan</th>
                            <th class="py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($sesiList)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Belum ada sesi yang ditambahkan.</td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($sesiList as $s): ?>
                            <tr>
                                <td class="px-4">
                                    <?php if ($s['tipe_sesi'] == 'online'): ?>
                                        <span class="badge bg-primary-subtle text-primary rounded-pill px-3"><i class="fas fa-video me-1"></i> Online</span>
                                    <?php else: ?>
                                        <span class="badge bg-info-subtle text-info rounded-pill px-3"><i class="fas fa-map-marker-alt me-1"></i> Offline</span>
                                    <?php endif; ?>
                                </td>
                                <td><span class="fw-bold"><?= $s['nama_sesi'] ?></span></td>
                                <td>
                                    <?php if ($s['tanggal']): ?>
                                    <div class="small fw-bold"><i class="far fa-calendar text-muted me-1"></i> <?= tanggal_indo($s['tanggal']) ?></div>
                                    <div class="small text-muted"><i class="far fa-clock me-1"></i> <?= $s['waktu'] ? date('H:i', strtotime($s['waktu'])) : '-' ?> s.d <?= !empty($s['jam_tutup']) ? date('H:i', strtotime($s['jam_tutup'])) . ' WIB' : '-' ?></div>
                                    <?php else: ?>
                                    <span class="text-muted small">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($s['tipe_sesi'] == 'online'): ?>
                                        <?php if ($s['meeting_link']): ?>
                                            <a href="<?= $s['meeting_link'] ?>" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill" style="font-size:0.75rem;"><i class="fas fa-external-link-alt me-1"></i> Buka Link</a>
                                            <div class="small mt-1 text-muted">Pass: <?= $s['meeting_pass'] ?: '-' ?></div>
                                        <?php else: ?>
                                            <span class="text-muted small">Link belum diatur</span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <?php if ($s['lokasi_ruang'] || $s['tempat']): ?>
                                            <div class="small fw-bold text-dark"><i class="fas fa-building text-muted me-1"></i> <?= $s['lokasi_ruang'] ? $s['lokasi_ruang'].', ' : '' ?><?= $s['tempat'] ?></div>
                                            <div class="small text-muted text-truncate" style="max-width: 200px;" title="<?= $s['alamat'] ?>"><?= $s['alamat'] ?></div>
                                            <?php if ($s['maps_url']): ?>
                                                <a href="<?= $s['maps_url'] ?>" target="_blank" class="text-primary small text-decoration-none mt-1 d-inline-block"><i class="fas fa-map-marked-alt me-1"></i> Lihat Maps</a>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="text-muted small">Lokasi belum diatur</span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    <?php if(!empty($s['narasumber'])): ?>
                                        <div class="small mt-2 text-muted"><i class="fas fa-user-tie me-1"></i> <?= $s['narasumber'] ?></div>
                                    <?php endif; ?>
                                    <?php if(!empty($s['penyelenggara'])): ?>
                                        <div class="small mt-1 text-muted"><i class="fas fa-building me-1"></i> <?= $s['penyelenggara'] ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex gap-2 justify-content-center">
                                        <?php if ($s['tipe_sesi'] == 'offline'): ?>
                                        <a href="<?= base_url('pelatihan/admin/presensi/'.$p['id'].'?sesi_id='.$s['id']) ?>" class="btn btn-sm btn-outline-success px-2 py-1" style="font-size: 0.75rem;" title="Presensi Kehadiran">
                                            <i class="fas fa-calendar-check me-1"></i> Presensi
                                        </a>
                                        <?php endif; ?>
                                        <button class="btn btn-sm btn-light text-primary" onclick='editSesi(<?= json_encode($s) ?>)' title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="<?= base_url('pelatihan/admin/pelatihan/hapus_sesi/' . $s['id']) ?>" class="btn btn-sm btn-light text-danger" onclick="return confirm('Hapus sesi ini?')" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Form Sesi -->
<div class="modal fade" id="modalSesi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-light border-0 px-4 py-3">
                <h5 class="modal-title fw-bold text-dark" id="modalSesiTitle">Tambah Sesi Baru</h5>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-warning btn-sm rounded-pill fw-bold" onclick="autofillSesi()" title="Auto-fill sesi untuk testing">
                        <i class="fas fa-bolt me-1"></i> TESTING
                    </button>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <form action="<?= base_url('pelatihan/admin/pelatihan/simpan_sesi') ?>" method="POST">
                <div class="modal-body px-4 py-4" style="max-height: 70vh; overflow-y: auto;">
                    <input type="hidden" name="pelatihan_id" value="<?= $p['id'] ?>">
                    <input type="hidden" name="id_sesi" id="id_sesi" value="">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small">Tipe Sesi</label>
                        <select class="form-select border-light-subtle bg-light" name="tipe_sesi" id="tipe_sesi" onchange="toggleSesiFields()" required>
                            <option value="">-- Pilih Tipe --</option>
                            <?php if ($p['metode'] == 'Online' || strpos(strtolower($p['metode']), 'blended') !== false): ?>
                                <option value="online">Online Meeting (Zoom/GMeet dll)</option>
                            <?php endif; ?>
                            <?php if ($p['metode'] == 'Offline' || strpos(strtolower($p['metode']), 'clasical') !== false || strpos(strtolower($p['metode']), 'blended') !== false): ?>
                                <option value="offline">Tatap Muka (Offline)</option>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small">Nama/Topik Sesi</label>
                        <input type="text" class="form-control border-light-subtle bg-light" name="nama_sesi" id="nama_sesi" required placeholder="Contoh: Sesi Pembukaan">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small">Narasumber</label>
                        <select class="form-select border-light-subtle bg-light" name="narasumber" id="narasumber" style="width: 100%;">
                            <option value="">-- Pilih Narasumber --</option>
                            <?php if(!empty($master_narasumber)): foreach($master_narasumber as $mn): ?>
                                <option value="<?= htmlspecialchars($mn['nama_narasumber']) ?>"><?= htmlspecialchars($mn['nama_narasumber']) ?></option>
                            <?php endforeach; endif; ?>
                        </select>
                        <small class="text-muted">Pilih dari daftar narasumber yang terdaftar pada pelatihan ini.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small">Penyelenggara</label>
                        <select class="form-select border-light-subtle bg-light" name="penyelenggara" id="penyelenggara" style="width: 100%;">
                            <option value="">-- Pilih Penyelenggara --</option>
                            <?php if(!empty($master_penyelenggara)): foreach($master_penyelenggara as $mp): ?>
                                <option value="<?= htmlspecialchars($mp['nama_penyelenggara']) ?>"><?= htmlspecialchars($mp['nama_penyelenggara']) ?></option>
                            <?php endforeach; endif; ?>
                        </select>
                        <small class="text-muted">Pilih dari daftar penyelenggara yang terdaftar pada pelatihan ini.</small>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-dark small">Tanggal</label>
                            <input type="date" class="form-control border-light-subtle bg-light" name="tanggal" id="tanggal" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-dark small">Jam Buka (Mulai)</label>
                            <input type="time" class="form-control border-light-subtle bg-light" name="waktu" id="waktu" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-dark small">Jam Tutup (Selesai)</label>
                            <input type="time" class="form-control border-light-subtle bg-light" name="jam_tutup" id="jam_tutup">
                        </div>
                    </div>

                    <!-- Online Fields -->
                    <div id="online_fields" style="display:none;">
                        <hr class="text-muted opacity-25">
                        <h6 class="fw-bold mb-3 text-primary"><i class="fas fa-video me-2"></i> Pengaturan Online</h6>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark small">Meeting Link (URL)</label>
                            <input type="url" class="form-control border-light-subtle bg-light" name="meeting_link" id="meeting_link" placeholder="https://zoom.us/j/123...">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark small">Meeting Password/Passcode</label>
                            <input type="text" class="form-control border-light-subtle bg-light" name="meeting_pass" id="meeting_pass" placeholder="Biarkan kosong jika tidak ada">
                        </div>
                    </div>

                    <!-- Offline Fields -->
                    <div id="offline_fields" style="display:none;">
                        <hr class="text-muted opacity-25">
                        <h6 class="fw-bold mb-3 text-info"><i class="fas fa-map-marker-alt me-2"></i> Pengaturan Lokasi Offline</h6>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark small">Ruang / Aula</label>
                            <input type="text" class="form-control border-light-subtle bg-light" name="lokasi_ruang" id="lokasi_ruang" placeholder="Contoh: Aula Serbaguna Lt. 4">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark small">Nama Tempat/Gedung</label>
                            <input type="text" class="form-control border-light-subtle bg-light" name="tempat" id="tempat" placeholder="Contoh: Balai Diklat">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark small">Alamat Lengkap</label>
                            <textarea class="form-control border-light-subtle bg-light" name="alamat" id="alamat" rows="2" placeholder="Jalan xyz..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark small">Google Maps URL</label>
                            <input type="url" class="form-control border-light-subtle bg-light" name="maps_url" id="maps_url" placeholder="https://goo.gl/maps/...">
                        </div>
                    </div>

                </div>
                <div class="modal-footer bg-light border-0 px-4 py-3">
                    <button type="button" class="btn btn-light border fw-bold rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary fw-bold rounded-pill px-4 shadow-sm">Simpan Sesi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function toggleSesiFields() {
        const tipe = document.getElementById('tipe_sesi').value;
        const onlineF = document.getElementById('online_fields');
        const offlineF = document.getElementById('offline_fields');

        if (tipe === 'online') {
            onlineF.style.display = 'block';
            offlineF.style.display = 'none';
        } else if (tipe === 'offline') {
            onlineF.style.display = 'none';
            offlineF.style.display = 'block';
        } else {
            onlineF.style.display = 'none';
            offlineF.style.display = 'none';
        }
    }

    function openSesiModal() {
        document.getElementById('modalSesiTitle').innerText = 'Tambah Sesi Baru';
        document.getElementById('id_sesi').value = '';
        document.getElementById('tipe_sesi').value = '';
        document.getElementById('nama_sesi').value = '';
        document.getElementById('tanggal').value = '';
        document.getElementById('waktu').value = '';
        document.getElementById('jam_tutup').value = '';
        
        document.getElementById('meeting_link').value = '';
        document.getElementById('meeting_pass').value = '';
        
        document.getElementById('lokasi_ruang').value = '';
        document.getElementById('tempat').value = '';
        document.getElementById('alamat').value = '';
        document.getElementById('maps_url').value = '';

        $('#narasumber').val('');
        $('#penyelenggara').val('');
        
        toggleSesiFields();
        new bootstrap.Modal(document.getElementById('modalSesi')).show();
    }

    function editSesi(sesi) {
        document.getElementById('modalSesiTitle').innerText = 'Edit Sesi';
        document.getElementById('id_sesi').value = sesi.id;
        document.getElementById('tipe_sesi').value = sesi.tipe_sesi;
        document.getElementById('nama_sesi').value = sesi.nama_sesi;
        document.getElementById('tanggal').value = sesi.tanggal;
        document.getElementById('waktu').value = sesi.waktu;
        document.getElementById('jam_tutup').value = sesi.jam_tutup || '';
        
        document.getElementById('meeting_link').value = sesi.meeting_link;
        document.getElementById('meeting_pass').value = sesi.meeting_pass;
        
        document.getElementById('lokasi_ruang').value = sesi.lokasi_ruang;
        document.getElementById('tempat').value = sesi.tempat;
        document.getElementById('alamat').value = sesi.alamat;
        document.getElementById('maps_url').value = sesi.maps_url;
        
        var selectNarasumber = $('#narasumber');
        selectNarasumber.val('');
        if (sesi.narasumber) {
            var nList = sesi.narasumber.split(',');
            if (nList.length > 0) {
                selectNarasumber.val(nList[0].trim());
            }
        }

        var selectPenyelenggara = $('#penyelenggara');
        selectPenyelenggara.val('');
        if (sesi.penyelenggara) {
            var pList = sesi.penyelenggara.split(',');
            if (pList.length > 0) {
                selectPenyelenggara.val(pList[0].trim());
            }
        }

        toggleSesiFields();
        new bootstrap.Modal(document.getElementById('modalSesi')).show();
    }

    function autofillSesi() {
        var now = new Date();
        var nextWeek = new Date(now.getTime() + 7 * 86400000).toISOString().split('T')[0];

        var tipeSesi = document.getElementById('tipe_sesi');
        var options = tipeSesi.options;
        for (var i = 0; i < options.length; i++) {
            if (options[i].value === 'offline') {
                tipeSesi.value = 'offline';
                break;
            }
        }
        toggleSesiFields();

        document.getElementById('nama_sesi').value = 'Sesi Pembukaan & Pengenalan Materi';
        document.getElementById('tanggal').value = nextWeek;
        document.getElementById('waktu').value = '08:00';
        document.getElementById('jam_tutup').value = '10:00';

        document.getElementById('lokasi_ruang').value = 'Aula Serbaguna Lt. 4';
        document.getElementById('tempat').value = 'RSUD Kota Yogyakarta';
        document.getElementById('alamat').value = 'Jl. Jend. Sudirman No. 50, Yogyakarta';
        document.getElementById('maps_url').value = 'https://goo.gl/maps/example';

        showToast('Form sesi berhasil diisi dengan data testing!', 'success');
    }
</script>
