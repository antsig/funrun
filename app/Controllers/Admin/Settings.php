<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SettingModel;

class Settings extends BaseController
{
    public function index()
    {
        $settingModel = new SettingModel();
        $settings = $settingModel->findAll();

        // Group settings by 'group' column
        $grouped = [];
        foreach ($settings as $s) {
            $grouped[$s['group']][$s['key']] = $s['value'];
        }

        return view('admin/settings/index', ['settings' => $grouped]);
    }

    public function save()
    {
        $settingModel = new SettingModel();
        $postData = $this->request->getPost();

        // Handle File Uploads
        $files = [
            'site_logo' => 'uploads/logo',
            'site_favicon' => 'uploads/logo',
            'home_banner' => 'uploads/banner'
        ];

        foreach ($files as $key => $path) {
            if ($file = $this->request->getFile($key)) {
                if ($file->isValid() && !$file->hasMoved()) {
                    $newName = $file->getRandomName();
                    $file->move(FCPATH . $path, $newName);

                    // Get old file to delete
                    $oldFile = $settingModel->getValue($key);
                    if ($oldFile && file_exists(FCPATH . $oldFile) && is_file(FCPATH . $oldFile)) {
                        unlink(FCPATH . $oldFile);
                    }

                    // Update DB with new path
                    $settingModel->updateValue($key, $path . '/' . $newName);
                }
            }
        }

        // Handle Text Inputs
        foreach ($postData as $key => $value) {
            // Ignore CSRF token and other non-setting fields if any
            if ($key == 'csrf_test_name')
                continue;

            // Check if key exists in DB to avoid errors
            if ($settingModel->where('key', $key)->first()) {
                $settingModel->updateValue($key, $value);
            }
        }

        return redirect()->to('/admin/settings')->with('success', 'Pengaturan berhasil disimpan');
    }

    public function testEmail()
    {
        $email = \Config\Services::email();
        $settingModel = new SettingModel();

        // Load config from DB (or use what's in POST if testing before save? Better use saved DB for consistency)
        // Actually, CodeIgniter Email Config is usually in Config/Email.php.
        // We need to override it dynamically.

        $config = [
            'protocol' => 'smtp',
            'SMTPHost' => $settingModel->getValue('smtp_host'),
            'SMTPUser' => $settingModel->getValue('smtp_user'),
            'SMTPPass' => $settingModel->getValue('smtp_pass'),
            'SMTPPort' => (int) ($settingModel->getValue('smtp_port') ?? 465),
            'SMTPCrypto' => $settingModel->getValue('smtp_crypto') ?? 'ssl',
            'mailType' => 'html',
        ];

        $email->initialize($config);

        $email->setFrom($config['SMTPUser'], 'FunRun Test');
        $email->setTo(session()->get('email') ?? 'admin@funrun.com');  // Send to current admin
        $email->setSubject('Test Email Settings');
        $email->setMessage('<p>If you see this, your SMTP settings are correct.</p>');

        if ($email->send()) {
            return redirect()->to('/admin/settings')->with('success', 'Test email sent successfully! Check your inbox.');
        } else {
            return redirect()->to('/admin/settings')->with('error', 'Failed to send email. Check logs. ' . $email->printDebugger(['headers']));
        }
    }
}
