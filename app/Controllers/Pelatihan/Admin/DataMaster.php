<?php
namespace App\Controllers\Pelatihan\Admin;
use App\Controllers\BaseController;

class DataMaster extends BaseController
{
    /**
     * Menampilkan halaman Master Data Profesi.
     * Route: GET /pelatihan/admin/data_master/profesi
     */
    public function profesi()
    {
        $model = new \App\Models\Pelatihan\ProfesiPelatihanModel();
        $data = [
            'title' => 'Data Master Profesi',
            'type'  => 'profesi',
            'list'  => $model->findAll()
        ];
        return view('pelatihan/admin/data_master/index', $data);
    }

    /**
     * Menampilkan halaman Master Data Ruangan.
     * Route: GET /pelatihan/admin/data_master/ruangan
     */
    public function ruangan()
    {
        $model = new \App\Models\Pelatihan\UnitKerjaPelatihanModel();
        $data = [
            'title' => 'Data Master Ruangan',
            'type' => 'ruangan',
            'list' => $model->findAll()
        ];
        return view('pelatihan/admin/data_master/index', $data);
    }

    /**
     * Menampilkan halaman Master Data Kategori Evaluasi.
     * Route: GET /pelatihan/admin/data_master/kategori_evaluasi
     */
    public function kategori_evaluasi()
    {
        $model = new \App\Models\Pelatihan\KategoriEvaluasiPelatihanModel();
        $items = $model->findAll();
        $list = array_column($items, 'nama_kategori');
        
        $data = [
            'title' => 'Data Master Kategori Evaluasi',
            'type' => 'kategori_evaluasi',
            'list' => $list
        ];
        return view('pelatihan/admin/data_master/index', $data);
    }

    /**
     * Menampilkan halaman Master Data Kategori SKP.
     * Route: GET /pelatihan/admin/data_master/kategori_skp
     */
    public function kategori_skp()
    {
        $model = new \App\Models\Pelatihan\MasterKategoriSkpPelatihanModel();
        $all = $model->orderBy('ranah')->orderBy('nama_kategori')->findAll();

        // Group by ranah
        $grouped = [];
        $ranah_list = [];
        foreach ($all as $item) {
            $grouped[$item['ranah']][] = $item;
            if (!in_array($item['ranah'], $ranah_list)) {
                $ranah_list[] = $item['ranah'];
            }
        }

        return view('pelatihan/admin/data_master/kategori_skp', [
            'title'      => 'Ranah & Kategori SKP',
            'grouped'    => $grouped,
            'ranah_list' => $ranah_list,
        ]);
    }

    /**
     * Menyimpan data Kategori SKP baru.
     * Route: POST /pelatihan/admin/data_master/simpan_kategori_skp
     */
    public function simpan_kategori_skp()
    {
        $ranah      = trim($this->request->getPost('ranah') ?? '');
        $ranah_baru = trim($this->request->getPost('ranah_baru') ?? '');
        $kategori   = trim($this->request->getPost('nama_kategori') ?? '');

        // Allow creating new ranah
        if ($ranah === '__baru__' && !empty($ranah_baru)) {
            $ranah = $ranah_baru;
        }

        if (empty($ranah) || empty($kategori)) {
            return redirect()->back()->with('error', 'Ranah dan Nama Kategori tidak boleh kosong.');
        }

        $model = new \App\Models\Pelatihan\MasterKategoriSkpPelatihanModel();
        $existing = $model->where('ranah', $ranah)->where('nama_kategori', $kategori)->first();
        if ($existing) {
            return redirect()->back()->with('error', 'Kategori tersebut sudah ada di ranah yang sama.');
        }

        $model->insert(['ranah' => $ranah, 'nama_kategori' => $kategori]);
        return redirect()->back()->with('success', 'Kategori SKP berhasil ditambahkan.');
    }

    /**
     * Menghapus data Kategori SKP berdasarkan ID.
     * Route: GET /pelatihan/admin/data_master/hapus_kategori_skp/(:num)
     * @param int $id ID kategori yang akan dihapus
     */
    public function hapus_kategori_skp(int $id)
    {
        $model = new \App\Models\Pelatihan\MasterKategoriSkpPelatihanModel();
        $item  = $model->find($id);
        if (!$item) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }
        $model->delete($id);
        return redirect()->back()->with('success', 'Kategori SKP berhasil dihapus.');
    }

    /**
     * Mengubah nama ranah SKP (menggabungkan atau merubah nama).
     * Route: POST /pelatihan/admin/data_master/rename_ranah
     */
    public function rename_ranah()
    {
        $ranah_lama = trim($this->request->getPost('ranah_lama') ?? '');
        $ranah_baru = trim($this->request->getPost('ranah_baru') ?? '');

        if (empty($ranah_lama) || empty($ranah_baru)) {
            return redirect()->back()->with('error', 'Nama ranah tidak boleh kosong.');
        }
        if ($ranah_lama === $ranah_baru) {
            return redirect()->back()->with('error', 'Nama ranah baru sama dengan yang lama.');
        }

        $db = \Config\Database::connect();
        // Check if target ranah name already exists (to avoid collision)
        $existing = $db->table('master_kategori_skp_pelatihan')->where('ranah', $ranah_baru)->countAllResults();
        if ($existing > 0) {
            // Merge: update ranah_lama rows to ranah_baru, duplicates will be deduplicated
            $db->table('master_kategori_skp_pelatihan')
               ->where('ranah', $ranah_lama)
               ->update(['ranah' => $ranah_baru]);
            return redirect()->back()->with('success', 'Ranah berhasil digabungkan ke "' . $ranah_baru . '".');
        }

        $db->table('master_kategori_skp_pelatihan')
           ->where('ranah', $ranah_lama)
           ->update(['ranah' => $ranah_baru]);

        return redirect()->back()->with('success', 'Ranah berhasil diubah dari "' . $ranah_lama . '" menjadi "' . $ranah_baru . '".');
    }

    /**
     * Menghapus ranah beserta seluruh kategorinya.
     * Route: GET /pelatihan/admin/data_master/hapus_ranah/(.*)
     * @param string $ranah Nama ranah yang akan dihapus
     */
    public function hapus_ranah(string $ranah)
    {
        $ranah = urldecode($ranah);
        $db = \Config\Database::connect();
        $count = $db->table('master_kategori_skp_pelatihan')->where('ranah', $ranah)->countAllResults();
        if ($count === 0) {
            return redirect()->back()->with('error', 'Ranah tidak ditemukan.');
        }
        $db->table('master_kategori_skp_pelatihan')->where('ranah', $ranah)->delete();
        return redirect()->back()->with('success', 'Ranah "' . $ranah . '" beserta ' . $count . ' kategori berhasil dihapus.');
    }

    /**
     * Menyimpan data master lainnya (kategori evaluasi, profesi, ruangan).
     * Route: POST /pelatihan/admin/data_master/simpan/(:any)
     * @param string $type Jenis data master
     */
    public function simpan(string $type)
    {
        $name = $this->request->getPost('name') ?? $this->request->getPost('nama');
        
        if ($type === 'kategori_evaluasi') {
            $model = new \App\Models\Pelatihan\KategoriEvaluasiPelatihanModel();
            $existing = $model->where('nama_kategori', $name)->first();
            if (!$existing) {
                $model->insert(['nama_kategori' => $name]);
                return redirect()->back()->with('success', 'Data Kategori Evaluasi berhasil ditambahkan.');
            }
            return redirect()->back()->with('error', 'Kategori tersebut sudah ada.');
        }

        if ($type === 'profesi') {
            $model = new \App\Models\Pelatihan\ProfesiPelatihanModel();
            $existing = $model->where('nama_profesi', $name)->first();
            if (!$existing) {
                $model->insert([
                    'nama_profesi'    => $name,
                    'kategori_target' => $this->request->getPost('kategori_target') ?? 'Non-Named',
                    'target_jpl'      => $this->request->getPost('target_jpl') ?? 20
                ]);
                return redirect()->back()->with('success', 'Data Profesi berhasil ditambahkan.');
            }
            return redirect()->back()->with('error', 'Profesi tersebut sudah ada.');
        }

        if ($type === 'ruangan') {
            $model = new \App\Models\Pelatihan\UnitKerjaPelatihanModel();
            $existing = $model->where('nama_unit', $name)->first();
            if (!$existing) {
                $model->insert(['nama_unit' => $name]);
                return redirect()->back()->with('success', 'Data Ruangan berhasil ditambahkan.');
            }
            return redirect()->back()->with('error', 'Ruangan tersebut sudah ada.');
        }

        $sessionKey = 'master_' . $type;
        $list = $this->session->get($sessionKey) ?? [];

        if (!in_array($name, $list)) {
            $list[] = $name;
            $this->session->set($sessionKey, $list);
            return redirect()->back()->with('success', 'Data ' . $type . ' berhasil ditambahkan.');
        }

        return redirect()->back()->with('error', 'Data sudah ada.');
    }

    /**
     * Menghapus data master lainnya (kategori evaluasi, profesi, ruangan).
     * Route: GET /pelatihan/admin/data_master/hapus/(:any)/(:any)
     * @param string $type Jenis data master
     * @param string $indexOrName ID atau nama item yang dihapus
     */
    public function hapus(string $type, string $indexOrName)
    {
        if ($type === 'kategori_evaluasi') {
            $model = new \App\Models\Pelatihan\KategoriEvaluasiPelatihanModel();
            $items = $model->findAll();
            $list = array_column($items, 'nama_kategori');
            
            if (isset($list[$indexOrName])) {
                $nameToDelete = $list[$indexOrName];
                $model->where('nama_kategori', $nameToDelete)->delete();
                return redirect()->back()->with('success', 'Data Kategori Evaluasi berhasil dihapus.');
            }
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        if ($type === 'profesi') {
            $model = new \App\Models\Pelatihan\ProfesiPelatihanModel();
            $model->delete($indexOrName);
            return redirect()->back()->with('success', 'Data Profesi berhasil dihapus.');
        }

        if ($type === 'ruangan') {
            $model = new \App\Models\Pelatihan\UnitKerjaPelatihanModel();
            $model->delete($indexOrName);
            return redirect()->back()->with('success', 'Data Ruangan berhasil dihapus.');
        }

        $sessionKey = 'master_' . $type;
        $list = $this->session->get($sessionKey) ?? [];
        
        if (isset($list[$indexOrName])) {
            unset($list[$indexOrName]);
            $list = array_values($list);
            $this->session->set($sessionKey, $list);
            return redirect()->back()->with('success', 'Data ' . $type . ' berhasil dihapus.');
        }

        return redirect()->back()->with('error', 'Data tidak ditemukan.');
    }

    public function narasumber()
    {
        $model = new \App\Models\Pelatihan\PejabatTtdPelatihanModel();
        $data = [
            'title' => 'Data Master Narasumber',
            'list'  => $model->where('status', 'Narasumber')->orderBy('nama_pejabat', 'ASC')->findAll()
        ];
        return view('pelatihan/admin/data_master/narasumber', $data);
    }

    public function simpan_narasumber()
    {
        $model = new \App\Models\Pelatihan\PejabatTtdPelatihanModel();
        $id = $this->request->getPost('id');

        $data = [
            'status'         => $this->request->getPost('status') ?? 'Narasumber',
            'nama_pejabat'   => $this->request->getPost('nama_pejabat'),
            'gelar_depan'    => $this->request->getPost('gelar_depan') ?? null,
            'gelar_belakang' => $this->request->getPost('gelar_belakang') ?? null,
            'pendidikan'     => $this->request->getPost('pendidikan') ?? null,
            'keahlian'       => $this->request->getPost('keahlian') ?? null,
            'an_pejabat'     => $this->request->getPost('an_pejabat') ?? null,
            'jabatan'        => $this->request->getPost('jabatan') ?? null,
            'nip_pejabat'    => $this->request->getPost('nip_pejabat') ?? null,
            'kontak'         => $this->request->getPost('kontak') ?? null,
            'email'          => $this->request->getPost('email') ?? null,
            'riwayat'        => $this->request->getPost('riwayat') ?? null,
            'updated_at'     => date('Y-m-d H:i:s'),
        ];

        // Handle foto upload
        $file = $this->request->getFile('foto');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
            $extension = strtolower($file->getExtension());
            if (in_array($extension, $allowedExtensions, true) && $file->getSize() <= 2 * 1024 * 1024) {
                if (!is_dir(ROOTPATH . 'public/uploads/pelatihan/foto')) {
                    mkdir(ROOTPATH . 'public/uploads/pelatihan/foto', 0777, true);
                }
                $namaPejabat = preg_replace('/[^A-Za-z0-9]/', '_', $this->request->getPost('nama_pejabat') ?: 'Narasumber');
                $newName = "Foto_{$namaPejabat}_" . date('Ymd_His') . "." . $extension;
                $file->move(ROOTPATH . 'public/uploads/pelatihan/foto', $newName);
                $data['foto'] = 'uploads/pelatihan/foto/' . $newName;
            }
        }

        // Handle ttd_image upload
        $ttdFile = $this->request->getFile('ttd_image');
        if ($ttdFile && $ttdFile->isValid() && !$ttdFile->hasMoved()) {
            $extension = strtolower($ttdFile->getExtension());
            if ($extension === 'png' && $ttdFile->getSize() <= 2 * 1024 * 1024) {
                if (!is_dir(ROOTPATH . 'public/uploads/pelatihan/ttd')) {
                    mkdir(ROOTPATH . 'public/uploads/pelatihan/ttd', 0777, true);
                }
                $namaPejabat = preg_replace('/[^A-Za-z0-9]/', '_', $this->request->getPost('nama_pejabat') ?: 'Pejabat');
                $newName = "TTD_{$namaPejabat}_" . date('Ymd_His') . ".png";
                $ttdFile->move(ROOTPATH . 'public/uploads/pelatihan/ttd', $newName);
                $data['ttd_image'] = 'ttd/' . $newName;
            }
        }

        if ($id) {
            $model->update($id, $data);
            return redirect()->back()->with('success', 'Data narasumber berhasil diperbarui.');
        }

        $data['created_at'] = date('Y-m-d H:i:s');
        $model->insert($data);
        return redirect()->back()->with('success', 'Data narasumber berhasil ditambahkan.');
    }

    public function hapus_narasumber(int $id)
    {
        $model = new \App\Models\Pelatihan\PejabatTtdPelatihanModel();
        $model->delete($id);
        return redirect()->back()->with('success', 'Data narasumber berhasil dihapus.');
    }

    public function simpan_narasumber_ajax()
    {
        $model = new \App\Models\Pelatihan\PejabatTtdPelatihanModel();
        $data = [
            'status'         => 'Narasumber',
            'nama_pejabat'   => $this->request->getPost('nama_pejabat'),
            'gelar_depan'    => $this->request->getPost('gelar_depan') ?? null,
            'gelar_belakang' => $this->request->getPost('gelar_belakang') ?? null,
            'pendidikan'     => $this->request->getPost('pendidikan') ?? null,
            'keahlian'       => $this->request->getPost('keahlian') ?? null,
            'an_pejabat'     => $this->request->getPost('an_pejabat') ?? null,
            'jabatan'        => $this->request->getPost('jabatan') ?? null,
            'nip_pejabat'    => $this->request->getPost('nip_pejabat') ?? null,
            'kontak'         => $this->request->getPost('kontak') ?? null,
            'email'          => $this->request->getPost('email') ?? null,
            'riwayat'        => $this->request->getPost('riwayat') ?? null,
            'created_at'     => date('Y-m-d H:i:s'),
        ];
        $model->insert($data);
        $newId = $model->getInsertID();

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => ['id' => $newId, 'nama_pejabat' => $data['nama_pejabat'], 'gelar_depan' => $data['gelar_depan'], 'gelar_belakang' => $data['gelar_belakang']]
        ]);
    }

    public function penyelenggara()
    {
        $model = new \App\Models\Pelatihan\MasterPenyelenggaraModel();
        $data = [
            'title' => 'Data Master Penyelenggara',
            'list'  => $model->orderBy('nama', 'ASC')->findAll()
        ];
        return view('pelatihan/admin/data_master/penyelenggara', $data);
    }

    public function simpan_penyelenggara()
    {
        $model = new \App\Models\Pelatihan\MasterPenyelenggaraModel();
        $id = $this->request->getPost('id');

        $data = [
            'nama'         => $this->request->getPost('nama'),
            'alamat'       => $this->request->getPost('alamat') ?? null,
            'fokus_bidang' => $this->request->getPost('fokus_bidang') ?? null,
            'kontak'       => $this->request->getPost('kontak') ?? null,
            'email'        => $this->request->getPost('email') ?? null,
            'status'       => $this->request->getPost('status') ?? 'Aktif',
            'updated_at'   => date('Y-m-d H:i:s'),
        ];

        if ($id) {
            $model->update($id, $data);
            return redirect()->back()->with('success', 'Data penyelenggara berhasil diperbarui.');
        }

        $data['created_at'] = date('Y-m-d H:i:s');
        $model->insert($data);
        return redirect()->back()->with('success', 'Data penyelenggara berhasil ditambahkan.');
    }

    public function hapus_penyelenggara(int $id)
    {
        $model = new \App\Models\Pelatihan\MasterPenyelenggaraModel();
        $model->delete($id);
        return redirect()->back()->with('success', 'Data penyelenggara berhasil dihapus.');
    }

    public function simpan_penyelenggara_ajax()
    {
        $model = new \App\Models\Pelatihan\MasterPenyelenggaraModel();
        $data = [
            'nama'         => $this->request->getPost('nama'),
            'alamat'       => $this->request->getPost('alamat') ?? null,
            'fokus_bidang' => $this->request->getPost('fokus_bidang') ?? null,
            'kontak'       => $this->request->getPost('kontak') ?? null,
            'email'        => $this->request->getPost('email') ?? null,
            'status'       => 'Aktif',
            'created_at'   => date('Y-m-d H:i:s'),
        ];
        $model->insert($data);
        $newId = $model->getInsertID();

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => ['id' => $newId, 'nama' => $data['nama']]
        ]);
    }
}
