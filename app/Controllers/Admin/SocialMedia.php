<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SocialMediaModel;

class SocialMedia extends BaseController
{
    protected $socialMediaModel;

    public function __construct()
    {
        $this->socialMediaModel = new SocialMediaModel();
    }

    public function index()
    {
        $data = [
            'social_media' => $this->socialMediaModel->findAll(),
            'title' => 'Social Media Management'
        ];
        return view('admin/social_media/index', $data);
    }

    public function store()
    {
        $validation = \Config\Services::validation();
        $validation->setRules([
            'url' => 'required|valid_url',
            'account_name' => 'required',  // Now acting as display text
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $url = $this->request->getPost('url');
        $platform = $this->detectPlatform($url);
        $icon = $this->getIconClass($platform);

        $this->socialMediaModel->save([
            'platform' => $platform,
            'account_name' => $this->request->getPost('account_name'),
            'url' => $url,
            'icon' => $icon,
            'is_active' => 1
        ]);

        return redirect()->to('/admin/settings/social-media')->with('success', 'Social media added successfully.');
    }

    public function delete($id)
    {
        // No file deletion needed as we store CSS classes now
        $this->socialMediaModel->delete($id);
        return redirect()->to('/admin/settings/social-media')->with('success', 'Social media deleted successfully.');
    }

    private function detectPlatform($url)
    {
        $url = strtolower($url);
        if (strpos($url, 'facebook.com') !== false)
            return 'Facebook';
        if (strpos($url, 'instagram.com') !== false)
            return 'Instagram';
        if (strpos($url, 'twitter.com') !== false || strpos($url, 'x.com') !== false)
            return 'Twitter';
        if (strpos($url, 'youtube.com') !== false)
            return 'YouTube';
        if (strpos($url, 'tiktok.com') !== false)
            return 'TikTok';
        if (strpos($url, 'linkedin.com') !== false)
            return 'LinkedIn';
        if (strpos($url, 'whatsapp.com') !== false || strpos($url, 'wa.me') !== false)
            return 'WhatsApp';
        return 'Website';
    }

    private function getIconClass($platform)
    {
        switch ($platform) {
            case 'Facebook':
                return 'fab fa-facebook';
            case 'Instagram':
                return 'fab fa-instagram';
            case 'Twitter':
                return 'fab fa-twitter';  // or fa-x-twitter if updated
            case 'YouTube':
                return 'fab fa-youtube';
            case 'TikTok':
                return 'fab fa-tiktok';
            case 'LinkedIn':
                return 'fab fa-linkedin';
            case 'WhatsApp':
                return 'fab fa-whatsapp';
            default:
                return 'fas fa-globe';
        }
    }
}
