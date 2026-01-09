<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AdminModel;

class Users extends BaseController
{
    public function index()
    {
        $adminModel = new AdminModel();
        $data['users'] = $adminModel->findAll();

        return view('admin/users/index', $data);
    }

    public function create()
    {
        return view('admin/users/form', ['title' => 'Tambah Admin']);
    }

    public function store()
    {
        $adminModel = new AdminModel();

        $rules = [
            'name' => 'required',
            'email' => 'required|valid_email|is_unique[admins.email]',
            'password' => 'required|min_length[6]',
            'conf_password' => 'matches[password]',
            'role' => 'required|in_list[administrator,admin]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('/admin/users/create')->withInput()->with('errors', $this->validator->getErrors());
        }

        $adminModel->save([
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role' => $this->request->getPost('role'),
        ]);

        return redirect()->to('/admin/users')->with('success', 'Admin berhasil ditambahkan');
    }

    public function edit($id)
    {
        $adminModel = new AdminModel();
        $user = $adminModel->find($id);

        if (!$user) {
            return redirect()->to('/admin/users')->with('error', 'User not found');
        }

        return view('admin/users/form', [
            'title' => 'Edit Admin',
            'user' => $user
        ]);
    }

    public function update($id)
    {
        $adminModel = new AdminModel();

        $rules = [
            'name' => 'required',
            'email' => "required|valid_email|is_unique[admins.email,id,{$id}]",
            'role' => 'required|in_list[administrator,admin]'
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
            'id' => $id,
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'role' => $this->request->getPost('role'),
        ];

        if (!empty($password)) {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $adminModel->save($data);

        return redirect()->to('/admin/users')->with('success', 'Admin berhasil diperbarui');
    }

    public function delete($id)
    {
        $adminModel = new AdminModel();

        // Prevent deleting self (optional safety)
        if ($id == session()->get('admin_id')) {
            return redirect()->to('/admin/users')->with('error', 'Tidak dapat menghapus akun sendiri');
        }

        $adminModel->delete($id);
        return redirect()->to('/admin/users')->with('success', 'Admin berhasil dihapus');
    }
}
