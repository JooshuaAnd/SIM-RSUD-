<?= $this->extend('Pelatihan/layout/admin_layout') ?>

<?= $this->section('content') ?>



<?php if (!$is_drilldown): ?>
    <div class="card border-0 shadow-sm rounded-lg overflow-hidden bg-white">
        <div class="card-header bg-white p-4 border-0 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0 text-uppercase small" style="letter-spacing: 1px;"><i class="fas fa-list me-2 text-danger"></i> DAFTAR PELATIHAN AKTIF</h6>
            <div class="input-group input-group-sm w-25">
                <span class="input-group-text bg-white border shadow-sm"><i class="fas fa-search text-muted"></i></span>
                <input type="text" class="form-control border shadow-sm" placeholder="Cari pelatihan...">
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small fw-bold">
                    <tr>
                        <th class="ps-4">PROGRAM PELATIHAN</th>
                        <th class="text-center">METODE</th>
                        <th class="text-center">TOTAL PESERTA</th>
                        <th class="text-center">STATUS</th>
                        <th class="pe-4 text-center">AKSI MONITORING</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pelatihan as $p) : ?>
                    <tr>
                        <td class="ps-4 py-3">
                            <div class="fw-bold small text-dark"><?= $p['nama'] ?></div>
                            <div class="text-muted" style="font-size: 0.65rem;">ID: #<?= str_pad($p['id'], 4, '0', STR_PAD_LEFT) ?> | <?= $p['kategori'] ?></div>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-<?= $p['metode'] == 'Online' ? 'danger' : ($p['metode'] == 'Offline' ? 'dark' : 'warning') ?>-subtle text-<?= $p['metode'] == 'Online' ? 'danger' : ($p['metode'] == 'Offline' ? 'dark' : 'warning') ?> rounded-pill px-3 fw-bold border border-opacity-10" style="font-size: 0.6rem;">
                                <?= strtoupper($p['metode']) ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="fw-bold small text-dark"><?= $p['peserta'] ?> / <?= $p['kuota'] ?></div>
                            <div class="progress mx-auto" style="height: 6px; width: 60px; border-radius: 3px;">
                                <div class="progress-bar bg-danger" style="width: <?= ($p['peserta']/$p['kuota'])*100 ?>%"></div>
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-success text-white rounded-pill px-2 fw-bold" style="font-size: 0.55rem;">BERJALAN</span>
                        </td>
                        <td class="pe-4 text-center">
                            <a href="<?= base_url('pelatihan/admin/peserta?pelatihan_id=' . $p['id']) ?>" class="btn btn-dark btn-sm rounded-pill px-4 fw-bold shadow-sm border-0">
                                <i class="fas fa-eye me-1 text-warning"></i> MONITOR PRESENSI
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php else: ?>
    <div class="mb-4">
        <div class="bg-white p-4 rounded-lg shadow-sm border-start border-danger border-5 d-flex justify-content-between align-items-center">
            <div>
                <h4 class="fw-bold mb-1 text-dark"><?= $p['nama'] ?></h4>
                <div class="text-muted small"><i class="fas fa-calendar-alt me-2 text-danger"></i> Periode: <?= tanggal_indo($p['jadwal_mulai']) ?> - <?= tanggal_indo($p['jadwal_selesai']) ?></div>
            </div>
            <a href="<?= base_url('pelatihan/admin/peserta') ?>" class="btn btn-dark btn-sm rounded-pill px-4 fw-bold">
                <i class="fas fa-arrow-left me-2 text-warning"></i> KEMBALI
            </a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-lg overflow-hidden sticky-top" style="top: 20px;">
                <div class="card-header bg-dark text-white border-0 fw-bold small text-uppercase p-3">SESI PELATIHAN</div>
                <div class="list-group list-group-flush" id="sessionList">
                    <button class="list-group-item list-group-item-action p-3 active border-0" onclick="switchSession(1, 'PEMBUKAAN', this)">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="fw-bold small text-uppercase">SESI 1: PEMBUKAAN</div>
                            <span class="badge bg-white text-dark rounded-pill">08:00</span>
                        </div>
                    </button>
                    <button class="list-group-item list-group-item-action p-3 border-0" onclick="switchSession(2, 'MATERI INTI I', this)">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="fw-bold small text-uppercase">SESI 2: MATERI INTI I</div>
                            <span class="badge bg-light text-muted rounded-pill">10:30</span>
                        </div>
                    </button>
                    <button class="list-group-item list-group-item-action p-3 border-0" onclick="switchSession(3, 'PENUTUPAN', this)">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="fw-bold small text-uppercase">SESI 3: PENUTUPAN</div>
                            <span class="badge bg-light text-muted rounded-pill">14:00</span>
                        </div>
                    </button>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-lg overflow-hidden">
                <div class="card-header bg-white p-4 border-0 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold mb-0 small text-uppercase text-dark">PRESENSI PESERTA: <span id="currentSessionName" class="text-danger">Sesi 1</span></h5>
                        <small class="text-muted fw-bold"><?= count($peserta) ?> PESERTA TERDAFTAR</small>
                    </div>
                    <button class="btn btn-dark btn-sm rounded-pill px-4 fw-bold shadow-sm" onclick="savePresensi()">
                        <i class="fas fa-save me-2 text-warning"></i> SIMPAN DATA
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small fw-bold">
                            <tr>
                                <th class="ps-4">NAMA PESERTA</th>
                                <th class="text-center">STATUS KEHADIRAN</th>
                            </tr>
                        </thead>
                        <tbody id="participantTableBody">
                            <?php foreach ($peserta as $ps) : ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold small text-dark"><?= $ps['nama'] ?></div>
                                    <div class="text-muted" style="font-size: 0.7rem;"><?= $ps['instansi'] ?? 'Instansi Simulation' ?></div>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm rounded-pill overflow-hidden border" role="group">
                                        <input type="radio" class="btn-check" name="status_<?= $ps['id'] ?>" id="h_<?= $ps['id'] ?>" autocomplete="off" checked>
                                        <label class="btn btn-outline-success px-3 fw-bold border-0" for="h_<?= $ps['id'] ?>">HADIR</label>
                                        
                                        <input type="radio" class="btn-check" name="status_<?= $ps['id'] ?>" id="t_<?= $ps['id'] ?>" autocomplete="off">
                                        <label class="btn btn-outline-danger px-3 fw-bold border-0" for="t_<?= $ps['id'] ?>">TIDAK</label>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        function switchSession(num, name, btn) {
            // Update active state in list
            document.querySelectorAll('#sessionList .list-group-item').forEach(i => i.classList.remove('active'));
            btn.classList.add('active');
            
            // Update title
            document.getElementById('currentSessionName').innerText = 'Sesi ' + num + ' - ' + name;
            
            // Randomize presence for simulation effect
            document.querySelectorAll('#participantTableBody input[type="radio"]').forEach(radio => {
                if(Math.random() > 0.8) {
                    if(radio.id.startsWith('t_')) radio.checked = true;
                } else {
                    if(radio.id.startsWith('h_')) radio.checked = true;
                }
            });

            showToast('Memuat data presensi Sesi ' + num, 'success');
        }

        function savePresensi() {
            showToast('Data presensi sesi ini berhasil disimpan ke cloud.', 'success');
        }
    </script>
<?php endif; ?>

<?= $this->endSection() ?>
