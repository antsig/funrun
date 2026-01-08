<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AdminModel;

class Auth extends BaseController
{
    public function login()
    {
        if (session()->get('is_admin_logged_in')) {
            return redirect()->to('/admin/dashboard');
        }
        return view('admin/auth/login');
    }

    public function processLogin()
    {
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $adminModel = new AdminModel();
        $admin = $adminModel->where('email', $email)->first();

        if ($admin && password_verify($password, $admin['password'])) {
            // Generate OTP
            $otp = rand(100000, 999999);
            $adminModel->update($admin['id'], [
                'otp' => $otp,
                'otp_expiration' => date('Y-m-d H:i:s', strtotime('+5 minutes'))
            ]);

            // Simulate Sending Email (Log it for now since SMTP might not be set)
            log_message('info', 'OTP for ' . $email . ': ' . $otp);

            // Set session for temporary Auth step
            session()->set('temp_admin_id', $admin['id']);
            session()->setFlashdata('success', 'OTP has been sent to your email ' . $email . ' (Check Logs for Dev: ' . $otp . ')');

            return redirect()->to('/admin/verify-otp');
        }

        return redirect()->back()->with('error', 'Invalid Email or Password');
    }

    public function verifyOtp()
    {
        if (!session()->has('temp_admin_id')) {
            return redirect()->to('/admin/login');
        }
        return view('admin/auth/verify_otp');
    }

    public function processOtp()
    {
        $otp = $this->request->getPost('otp');
        $adminId = session()->get('temp_admin_id');

        $adminModel = new AdminModel();
        $admin = $adminModel->find($adminId);

        if ($admin && $admin['otp'] === $otp && strtotime($admin['otp_expiration']) > time()) {
            // Clear OTP
            $adminModel->update($admin['id'], ['otp' => null, 'otp_expiration' => null]);

            // Set Real Session
            session()->set([
                'is_admin_logged_in' => true,
                'admin_id' => $admin['id'],
                'admin_name' => $admin['name'],
            ]);
            session()->remove('temp_admin_id');

            return redirect()->to('/admin/dashboard');
        }

        return redirect()->back()->with('error', 'Invalid or Expired OTP');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/admin/login');
    }
}
