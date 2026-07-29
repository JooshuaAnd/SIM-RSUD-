            <div class="tab-pane fade" id="tab-feedback" role="tabpanel">
                <div class="card border-0 shadow-sm rounded-lg p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0">Template Kuesioner Evaluasi Global</h5>
                        <button class="btn btn-secondary btn-sm rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalKelolaFeedback">
                            <i class="fas fa-edit me-2"></i> Edit Template Kuesioner
                        </button>
                    </div>

                    <div class="alert alert-info border-0 small shadow-sm mb-4">
                        <i class="fas fa-info-circle me-2"></i>
                        Template kuesioner ini bersifat <strong>global</strong> untuk seluruh pelatihan ini. Peserta akan mengevaluasi setiap <strong>Materi</strong>, <strong>Narasumber</strong>, dan <strong>Penyelenggara</strong> berdasarkan pertanyaan di setiap kategori.
                    </div>

                    <div class="row g-3" id="kuesionerTabContainer">
                        <?php if (empty($kuesioner)): ?>
                            <div class="col-12 text-center text-muted py-5">
                                <i class="fas fa-inbox fa-3x mb-3 text-light"></i>
                                <h6>Belum ada pertanyaan kuesioner.</h6>
                                <p class="small mb-0">Klik "Edit Template Kuesioner" untuk menambahkan atau menggunakan template.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($kuesioner as $kat => $pertanyaanList): ?>
                                <div class="col-md-4">
                                    <div class="p-3 border rounded-lg bg-light h-100">
                                        <h6 class="fw-bold small text-primary text-uppercase mb-3"><i class="fas fa-list-ul me-2"></i> <?= esc($kat) ?></h6>
                                        <ul class="list-unstyled small text-muted">
                                            <?php foreach ($pertanyaanList as $pq): ?>
                                                <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> <?= esc($pq['pertanyaan']) ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <?php
                    // Fetch materi, narasumber, penyelenggara for preview (per-sesi only)
                    $db_fb = \Config\Database::connect();
                    $materiCount = $db_fb->table('materi_pelatihan')->where('pelatihan_id', $p['id'])->where('sesi_id IS NOT NULL', null, false)->countAllResults();
                    $narasumberCount = $db_fb->table('narasumber_pelatihan')
                        ->join('pejabat_ttd_pelatihan', 'pejabat_ttd_pelatihan.id = narasumber_pelatihan.pejabat_ttd_id')
                        ->where('narasumber_pelatihan.pelatihan_id', $p['id'])
                        ->where('narasumber_pelatihan.sesi_id IS NOT NULL', null, false)
                        ->where('pejabat_ttd_pelatihan.status', 'Narasumber')
                        ->countAllResults();
                    $penyelenggaraCount = $db_fb->table('penyelenggara_pelatihan')
                        ->join('master_penyelenggara', 'master_penyelenggara.id = penyelenggara_pelatihan.penyelenggara_id')
                        ->where('penyelenggara_pelatihan.pelatihan_id', $p['id'])
                        ->where('penyelenggara_pelatihan.sesi_id IS NOT NULL', null, false)
                        ->countAllResults();
                    ?>
                    <hr class="mt-4">
                    <div class="row g-3 mt-1">
                        <div class="col-12">
                            <h6 class="fw-bold text-dark mb-3">Pratinjau Objek yang Akan Dievaluasi</h6>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-0 bg-primary bg-opacity-10 p-3 rounded-lg text-center">
                                <div class="fw-bold text-primary small text-uppercase mb-1"><i class="fas fa-book me-1"></i> Materi</div>
                                <div class="display-6 fw-bold text-primary"><?= $materiCount ?></div>
                                <div class="small text-muted">item akan dievaluasi</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-0 bg-success bg-opacity-10 p-3 rounded-lg text-center">
                                <div class="fw-bold text-success small text-uppercase mb-1"><i class="fas fa-chalkboard-teacher me-1"></i> Narasumber</div>
                                <div class="display-6 fw-bold text-success"><?= $narasumberCount ?></div>
                                <div class="small text-muted">narasumber akan dievaluasi</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-0 bg-warning bg-opacity-10 p-3 rounded-lg text-center">
                                <div class="fw-bold text-warning small text-uppercase mb-1"><i class="fas fa-users-cog me-1"></i> Penyelenggara</div>
                                <div class="display-6 fw-bold text-warning"><?= $penyelenggaraCount ?></div>
                                <div class="small text-muted">penyelenggara akan dievaluasi</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

