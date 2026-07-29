<?= $this->extend('Riset/admin/layout/template') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-2 text-gray-800">Pengaturan Riset</h1>
            <p class="mb-0 text-muted">Atur gambar kop surat untuk keperluan cetak surat di Riset (Surat Stupen, Izin Penelitian, Publikasi).</p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <h6 class="m-0 font-weight-bold text-primary">Form Pengaturan Riset</h6>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('success')) : ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?= session()->getFlashdata('success') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('errors')) : ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                <?php foreach (session()->getFlashdata('errors') as $error) : ?>
                                    <li><?= $error ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('riset/admin/pengaturan-surat/save') ?>" method="post" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= isset($pengaturan['id']) ? $pengaturan['id'] : '' ?>">

                        <div class="mb-3">
                            <label for="nama_pejabat" class="form-label fw-bold">Nama Pejabat</label>
                            <input type="text" class="form-control" id="nama_pejabat" name="nama_pejabat" value="<?= isset($pengaturan['nama_pejabat']) ? $pengaturan['nama_pejabat'] : old('nama_pejabat') ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="nip_pejabat" class="form-label fw-bold">NIP Pejabat</label>
                            <input type="text" class="form-control" id="nip_pejabat" name="nip_pejabat" value="<?= isset($pengaturan['nip_pejabat']) ? $pengaturan['nip_pejabat'] : old('nip_pejabat') ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="jabatan" class="form-label fw-bold">Jabatan</label>
                            <input type="text" class="form-control" id="jabatan" name="jabatan" value="<?= isset($pengaturan['jabatan']) ? $pengaturan['jabatan'] : old('jabatan') ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="pangkat" class="form-label fw-bold">Pangkat / Golongan</label>
                            <input type="text" class="form-control" id="pangkat" name="pangkat" value="<?= isset($pengaturan['pangkat']) ? $pengaturan['pangkat'] : old('pangkat') ?>" placeholder="Pembina Utama Muda (IV/c)">
                        </div>

                        <hr class="my-4">
                        <h6 class="font-weight-bold text-primary mb-3">Pengaturan Rekening Bank</h6>

                        <div class="mb-3">
                            <label for="nama_bank" class="form-label fw-bold">Nama Bank</label>
                            <input type="text" class="form-control" id="nama_bank" name="nama_bank" value="<?= isset($pengaturan['nama_bank']) ? $pengaturan['nama_bank'] : old('nama_bank') ?>" placeholder="Misal: Bank BPD DIY">
                        </div>

                        <div class="mb-3">
                            <label for="nomor_rekening" class="form-label fw-bold">Nomor Rekening</label>
                            <input type="text" class="form-control" id="nomor_rekening" name="nomor_rekening" value="<?= isset($pengaturan['nomor_rekening']) ? $pengaturan['nomor_rekening'] : old('nomor_rekening') ?>" placeholder="006.XXX.XXXXX">
                        </div>

                        <div class="mb-3">
                            <label for="nama_rekening" class="form-label fw-bold">Nama Rekening</label>
                            <input type="text" class="form-control" id="nama_rekening" name="nama_rekening" value="<?= isset($pengaturan['nama_rekening']) ? $pengaturan['nama_rekening'] : old('nama_rekening') ?>" placeholder="a.n. RSUD Kota Yogyakarta">
                        </div>

                        <hr class="my-4">
                        <h6 class="font-weight-bold text-primary mb-3">Pengaturan Kop Surat</h6>

                        <div class="mb-4">
                            <label for="logo_kop" class="form-label fw-bold">Gambar Kop Surat</label>
                            <?php if(isset($pengaturan['logo_kop']) && !empty($pengaturan['logo_kop'])): ?>
                                <div class="mb-2">
                                    <img src="<?= base_url('uploads/riset/pengaturan/' . $pengaturan['logo_kop']) ?>" alt="Logo Kop" class="img-thumbnail" style="max-height: 100px;">
                                </div>
                            <?php endif; ?>
                            <input class="form-control" type="file" id="logo_kop" name="logo_kop" accept="image/png, image/jpeg, image/jpg">
                            <div class="form-text">Format: JPG, JPEG, PNG. Maksimal ukuran: 2MB. Biarkan kosong jika tidak ingin mengubah.</div>
                        </div>

                        </div>

                        <button type="submit" class="btn btn-primary w-100">Simpan Pengaturan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
