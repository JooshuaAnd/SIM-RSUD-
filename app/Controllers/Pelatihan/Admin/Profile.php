<?php
namespace App\Controllers\Pelatihan\Admin;
use App\Controllers\BaseController;

class Profile extends BaseController
{
    public function index()
    {
        $userId = $this->session->get('user_id');
        if (!$userId) return redirect()->to('/login');

        $db = \Config\Database::connect();
        
        $user = $db->table('users_pelatihan')
                   ->select('users_pelatihan.*, unit_kerja_pelatihan.nama_unit, profesi_pelatihan.nama_profesi')
                   ->join('unit_kerja_pelatihan', 'unit_kerja_pelatihan.id_unit_kerja = users_pelatihan.id_unit_kerja', 'left')
                   ->join('profesi_pelatihan', 'profesi_pelatihan.id_profesi = users_pelatihan.id_profesi', 'left')
                   ->where('nik', $userId)
                   ->get()->getRowArray();
        
        if (!$user) {
            return redirect()->to('/login');
        }

        $unit_kerja = $db->table('unit_kerja_pelatihan')->get()->getResultArray();
        $profesi = $db->table('profesi_pelatihan')->get()->getResultArray();

        return view('Pelatihan/admin/profil/index', [
            'title' => 'Profil Admin', 
            'user' => $user,
            'unit_kerja' => $unit_kerja,
            'profesi' => $profesi
        ]);
    }

    public function update()
    {
        $userId = $this->session->get('user_id');
        if (!$userId) return redirect()->to('/login');

        $db = \Config\Database::connect();
        $data = $this->request->getPost();

        $updateData = [
            'nama_lengkap' => $data['nama_lengkap'],
            'email' => $data['email'],
            'no_wa' => $data['no_wa'],
        ];

        if (isset($data['id_unit_kerja']) && !empty($data['id_unit_kerja'])) {
            $updateData['id_unit_kerja'] = $data['id_unit_kerja'];
        }

        if (isset($data['id_profesi']) && !empty($data['id_profesi'])) {
            $updateData['id_profesi'] = $data['id_profesi'];
        }

        if (!empty($data['password'])) {
            $updateData['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        $db->table('users_pelatihan')->where('nik', $userId)->update($updateData);

        return redirect()->to('/pelatihan/admin/profil')->with('success', 'Profil berhasil diperbarui!');
    }
}
