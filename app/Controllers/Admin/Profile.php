<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AdminModel;

class Profile extends BaseController
{
    public function index()
    {
        $adminModel = new AdminModel();
        $adminId = session()->get('admin_id');
        $user = $adminModel->find($adminId);

        if (!$user) {
            return redirect()->to('/admin/login');
        }

        return view('admin/profile/index', [
            'title' => 'My Profile',
            'user' => $user
        ]);
    }

    public function update()
    {
        $adminModel = new AdminModel();
        $adminId = session()->get('admin_id');

        $rules = [
            'name' => 'required|min_length[3]',
        ];

        // Check password only if filled
        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $rules['password'] = 'min_length[6]';
            $rules['conf_password'] = 'matches[password]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'id' => $adminId,
            'name' => $this->request->getPost('name'),
            // Email is NOT updated here. It is locked.
        ];

        if (!empty($password)) {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $adminModel->save($data);

        // Update session name if changed
        session()->set('admin_name', $data['name']);

        return redirect()->to('/admin/profile')->with('success', 'Profil berhasil diperbarui.');
    }
}
