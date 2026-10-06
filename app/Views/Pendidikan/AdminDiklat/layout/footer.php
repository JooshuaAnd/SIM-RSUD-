    </div>
</div>

<!-- Modal Ganti Password Admin -->
<div class="modal fade" id="modalGantiPasswordAdmin" tabindex="-1" aria-labelledby="modalGantiPasswordAdminLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalGantiPasswordAdminLabel">Ganti Password Admin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url(session()->get('role') === 'superadmin' ? 'superadmin/update_password' : 'pendidikan/admin/diklat/update_password') ?>" method="post">
                <?php $adminCsrf = \App\Filters\PendidikanAdminCsrfFilter::security(); ?>
                <input type="hidden" name="<?= esc($adminCsrf->getTokenName(), 'attr') ?>" value="<?= esc($adminCsrf->getHash(), 'attr') ?>">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="old_password" class="form-label">Password Lama</label>
                        <input type="password" class="form-control" id="old_password" name="old_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_password" class="form-label">Password Baru</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required minlength="6">
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required minlength="6">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Password</button>
                </div>
            </form>
        </div>
    </div>
</div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function showAdminDiklatNotification(icon, title, text) {
            return Swal.fire({
                icon: icon,
                title: title,
                text: text,
                confirmButtonColor: '#c62828'
            });
        }

        function confirmAdminDiklat(title, text) {
            return Swal.fire({
                icon: 'question',
                title: title,
                text: text,
                showCancelButton: true,
                confirmButtonText: 'Ya, lanjutkan',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#c62828',
                cancelButtonColor: '#6c757d'
            });
        }

        function confirmDeleteAdminDiklat(title, text) {
            return Swal.fire({
                icon: 'warning',
                title: title,
                text: text,
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#c62828',
                cancelButtonColor: '#6c757d'
            });
        }

        function reloadAdminDiklatAfterModal(selector, message) {
            $(selector).modal('hide');
            showAdminDiklatNotification('success', 'Berhasil', message || 'Data berhasil diperbarui.').then(function() {
                location.reload();
            });
        }
    </script>
    
    <script>
        $(document).ready(function() {
            $('.navbar-toggler').on('click', function() {
                $('#sidebarMenu').toggleClass('show');
            });

            <?php if (session()->getFlashdata('success')) : ?>
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: <?= json_encode(session()->getFlashdata('success'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>,
                });
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')) : ?>
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: <?= json_encode(session()->getFlashdata('error'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>,
                });
            <?php endif; ?>
        });
    </script>
</body>
</html>
