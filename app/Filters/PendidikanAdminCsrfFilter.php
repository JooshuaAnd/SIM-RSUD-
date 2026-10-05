<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Security\Exceptions\SecurityException;
use CodeIgniter\Security\Security;

class PendidikanAdminCsrfFilter implements FilterInterface
{
    public static function security(): Security
    {
        // Konfigurasi khusus admin: tidak mengaktifkan CSRF pada modul lain.
        // Token sesi tetap stabil untuk tab dan request AJAX yang berjalan bersamaan.
        $config = clone config('Security');
        $config->csrfProtection = 'session';
        $config->tokenName = 'pendidikan_admin_csrf';
        $config->regenerate = false;
        return new Security($config);
    }

    public function before(RequestInterface $request, $arguments = null)
    {
        if (!$request instanceof IncomingRequest) {
            return null;
        }
        try {
            self::security()->verify($request);
        } catch (SecurityException $e) {
            if ($request->isAJAX() || strpos($request->getUri()->getPath(), '/api/') !== false) {
                return service('response')->setStatusCode(403)->setJSON([
                    'success' => false,
                    'message' => 'Token keamanan tidak valid atau sesi berakhir. Muat ulang halaman dan coba lagi.',
                ]);
            }
            return redirect()->back()->with('error', 'Token keamanan tidak valid. Muat ulang halaman dan coba lagi.');
        }
        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return null;
    }
}
