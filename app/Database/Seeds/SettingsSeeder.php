<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'key' => 'site_title',
                'value' => 'Fun Run 2026',
                'group' => 'general',
                'type' => 'text',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'key' => 'app_name',
                'value' => 'Organizer App',
                'group' => 'general',
                'type' => 'text',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'key' => 'site_logo',
                'value' => '',
                'group' => 'general',
                'type' => 'image',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'key' => 'site_favicon',
                'value' => '',
                'group' => 'general',
                'type' => 'image',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'key' => 'home_banner',
                'value' => '',
                'group' => 'general',
                'type' => 'image',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'key' => 'smtp_host',
                'value' => 'smtp.googlemail.com',
                'group' => 'email',
                'type' => 'text',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'key' => 'smtp_user',
                'value' => '',
                'group' => 'email',
                'type' => 'text',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'key' => 'smtp_pass',
                'value' => '',
                'group' => 'email',
                'type' => 'password',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'key' => 'contact_email',
                'value' => 'admin@funrun.com',
                'group' => 'contact',
                'type' => 'text',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'key' => 'contact_phone',
                'value' => '081234567890',
                'group' => 'contact',
                'type' => 'text',
                'created_at' => date('Y-m-d H:i:s')
            ],
        ];

        // Use ignore to prevent errors on duplicate keys
        $this->db->table('settings')->ignore(true)->insertBatch($data);
    }
}
