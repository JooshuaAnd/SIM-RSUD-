<?php
namespace App\Controllers\Pelatihan\Admin;
use App\Controllers\BaseController;

class User extends BaseController
{
    public function index()
    {
        $users = array_filter($this->session->get('users'), fn($u) => $u['role'] == 'peserta');
        $data = [
            'title' => 'Manajemen Akun Peserta',
            'users' => $users
        ];
        return view('Pelatihan/admin/manajemen_peserta/index', $data);
    }
}
