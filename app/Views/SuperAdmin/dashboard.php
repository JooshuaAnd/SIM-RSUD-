<?= $this->include('Pendidikan/AdminDiklat/layout/header') ?>

<div class="sidebar" id="sidebarMenu">
    <div class="d-flex flex-column">
        <div class="px-4 mb-4 mt-2">
            <p class="text-uppercase small fw-bold text-muted mb-2">Menu Super Admin</p>
        </div>
        <nav class="nav flex-column">
            <a class="nav-link active" href="#">
                <i class="fas fa-th-large"></i> Dashboard
            </a>
            <div class="px-4 mb-2 mt-4">
                <p class="text-uppercase small fw-bold text-muted mb-0">Sistem</p>
            </div>
            <a class="nav-link" href="<?= base_url('pendidikan/logout') ?>">
                <i class="fas fa-sign-out-alt"></i> Keluar
            </a>
        </nav>
    </div>
</div>
<div class="main-content">
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-bold text-dark">Dashboard Super Admin</h5>
            <small class="text-muted"><?= date('d/m/Y') ?></small>
        </div>

        <?php if (session()->getFlashdata('success')) : ?>
            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')) : ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>

        <div class="row g-4 mb-4">
            <!-- Form Pembuatan Admin -->
            <div class="col-md-5">
                <div class="card p-4">
                    <h5 class="fw-bold mb-4">Buat Akun Admin Baru</h5>
                    <form action="<?= base_url('superadmin/create_admin') ?>" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Tipe Admin</label>
                            <select name="tipe_admin" id="tipe_admin" class="form-select" required>
                                <option value="" disabled selected>-- Pilih Tipe Admin --</option>
                                <option value="riset">Admin Riset</option>
                                <option value="pelatihan">Admin Pelatihan</option>
                                <option value="admin_pengabdian">Admin Pengabdian (Pelatihan)</option>
                                <option value="pendidikan">Admin Pendidikan (Diklat)</option>
                            </select>
                        </div>
                        <div class="mb-3" id="nik_container" style="display: none;">
                            <label class="form-label">NIK / Identitas</label>
                            <input type="text" name="nik" id="nik" class="form-control" placeholder="Masukkan NIK" minlength="16" maxlength="16" inputmode="numeric" pattern="[0-9]{16}" title="NIK harus tepat 16 digit angka">
                        </div>
                        <div class="mb-3" id="nama_container" style="display: none;">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" id="nama_lengkap" class="form-control" placeholder="Masukkan Nama Lengkap" pattern="[A-Za-zÀ-ÖØ-öø-ÿ ]+" title="Nama hanya boleh berisi huruf dan spasi">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required placeholder="Masukkan Email">
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required placeholder="Masukkan Password">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-plus-circle me-2"></i> Buat Admin
                        </button>
                    </form>
                </div>
            </div>

            <!-- Daftar Admin -->
            <div class="col-md-7">
                <div class="card p-4">
                    <h5 class="fw-bold mb-4">Daftar Admin Sistem</h5>
                    
                    <ul class="nav nav-tabs" id="adminTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="riset-tab" data-bs-toggle="tab" data-bs-target="#riset" type="button" role="tab">Riset</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pelatihan-tab" data-bs-toggle="tab" data-bs-target="#pelatihan" type="button" role="tab">Pelatihan</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pendidikan-tab" data-bs-toggle="tab" data-bs-target="#pendidikan" type="button" role="tab">Pendidikan</button>
                        </li>
                    </ul>
                    
                    <div class="tab-content pt-3" id="adminTabsContent">
                        <!-- Tab Riset -->
                        <div class="tab-pane fade show active" id="riset" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Nama</th>
                                            <th>Email</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($adminRiset)): ?>
                                            <?php foreach ($adminRiset as $admin): ?>
                                            <tr>
                                                <td><?= $admin['nama'] ?></td>
                                                <td><?= $admin['email'] ?></td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-info ms-1" onclick="kirimResetEmail('<?= $admin['id'] ?>', 'riset', '<?= $admin['nama'] ?>')">
                                                        <i class="fas fa-envelope"></i> Kirim Reset Email
                                                    </button>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr><td colspan="3" class="text-center">Belum ada admin Riset</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Tab Pelatihan -->
                        <div class="tab-pane fade" id="pelatihan" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Nama</th>
                                            <th>NIK</th>
                                            <th>Email</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($adminPelatihan)): ?>
                                            <?php foreach ($adminPelatihan as $admin): ?>
                                            <tr>
                                                <td>
                                                    <?= $admin['nama_lengkap'] ?>
                                                    <?php if($admin['role'] == 'admin_pengabdian'): ?>
                                                        <span class="badge bg-info ms-2">Pengabdian</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-primary ms-2">Admin</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= $admin['nik'] ?></td>
                                                <td><?= $admin['email'] ?></td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-info ms-1" onclick="kirimResetEmail('<?= $admin['nik'] ?>', 'pelatihan', '<?= $admin['nama_lengkap'] ?>')">
                                                        <i class="fas fa-envelope"></i> Kirim Reset Email
                                                    </button>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr><td colspan="4" class="text-center">Belum ada admin Pelatihan</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Tab Pendidikan -->
                        <div class="tab-pane fade" id="pendidikan" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Email</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($adminPendidikan)): ?>
                                            <?php foreach ($adminPendidikan as $admin): ?>
                                            <tr>
                                                <td><?= $admin['email'] ?></td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-info ms-1" onclick="kirimResetEmail('<?= $admin['id'] ?>', 'pendidikan', '<?= $admin['email'] ?>')">
                                                        <i class="fas fa-envelope"></i> Kirim Reset Email
                                                    </button>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr><td colspan="2" class="text-center">Belum ada admin Pendidikan</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>



<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tipeAdminSelect = document.getElementById('tipe_admin');
        const nikContainer = document.getElementById('nik_container');
        const nikInput = document.getElementById('nik');
        const namaContainer = document.getElementById('nama_container');
        const namaInput = document.getElementById('nama_lengkap');

        nikInput.addEventListener('input', function() {
            this.value = this.value.replace(/\D/g, '').slice(0, 16);
        });

        namaInput.addEventListener('input', function() {
            this.value = this.value.replace(/[^\p{L} ]/gu, '');
        });

        tipeAdminSelect.addEventListener('change', function() {
            const val = this.value;
            if (val === 'pelatihan' || val === 'admin_pengabdian') {
                nikContainer.style.display = 'block';
                nikInput.setAttribute('required', 'required');
                nikInput.setAttribute('minlength', '16');
                nikInput.setAttribute('maxlength', '16');
                nikInput.setAttribute('pattern', '[0-9]{16}');
                namaContainer.style.display = 'block';
                namaInput.setAttribute('required', 'required');
            } else if (val === 'riset') {
                nikContainer.style.display = 'none';
                nikInput.removeAttribute('required');
                nikInput.removeAttribute('minlength');
                nikInput.removeAttribute('maxlength');
                nikInput.removeAttribute('pattern');
                nikInput.value = '';
                namaContainer.style.display = 'block';
                namaInput.setAttribute('required', 'required');
            } else if (val === 'pendidikan') {
                nikContainer.style.display = 'none';
                nikInput.removeAttribute('required');
                nikInput.removeAttribute('minlength');
                nikInput.removeAttribute('maxlength');
                nikInput.removeAttribute('pattern');
                nikInput.value = '';
                namaContainer.style.display = 'none';
                namaInput.removeAttribute('required');
                namaInput.value = '';
            }
        });
    });



    function kirimResetEmail(id, modul, name) {
        if(confirm('Apakah Anda yakin ingin mengirimkan reset password ke email admin ' + name + '?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '<?= base_url('superadmin/reset_password_email') ?>';
            
            const idInput = document.createElement('input');
            idInput.type = 'hidden';
            idInput.name = 'id';
            idInput.value = id;
            form.appendChild(idInput);
            
            const modulInput = document.createElement('input');
            modulInput.type = 'hidden';
            modulInput.name = 'modul';
            modulInput.value = modul;
            form.appendChild(modulInput);
            
            document.body.appendChild(form);
            form.submit();
        }
    }
</script>

<?= $this->include('Pendidikan/AdminDiklat/layout/footer') ?>
