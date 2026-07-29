<?php
namespace App\Controllers\Pelatihan\Peserta;
use App\Controllers\BaseController;

class Finance extends BaseController
{
    public function index($id)
    {
        return view('Pelatihan/peserta/pembayaran/index', ['title' => 'Pembayaran Pelatihan', 'id' => $id]);
    }

    public function upload($id)
    {
        $userId = $this->session->get('user_id');
        $pembayaran = $this->session->get('pembayaran') ?? [];
        $pembayaran[] = ['user_id' => $userId, 'pelatihan_id' => $id, 'bukti' => 'bukti.jpg', 'status' => 'pending'];
        $this->session->set('pembayaran', $pembayaran);

        $pendaftaran = $this->session->get('pendaftaran') ?? [];
        foreach ($pendaftaran as &$pd) {
            if ($pd['user_id'] == $userId && $pd['pelatihan_id'] == $id) { $pd['status'] = 'pending'; break; }
        }
        $this->session->set('pendaftaran', $pendaftaran);

        return redirect()->to('/pelatihan/peserta/detail_pelatihan/'.$id)->with('success', 'Bukti terunggah!');
    }
}
