<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $role = session()->get('role');

        // If no role argument is passed, default to 'administrator' check
        $requiredRole = $arguments[0] ?? 'administrator';

        if ($role !== $requiredRole) {
            return redirect()->to('/admin/dashboard')->with('error', 'Akses ditolak. Anda tidak memiliki izin untuk mengakses halaman ini.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing here
    }
}
