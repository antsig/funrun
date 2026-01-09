<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSettingsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'key' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'unique' => true,
            ],
            'value' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'group' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'default' => 'general',
            ],
            'type' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'default' => 'text',  // text, image, boolean, number
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('settings');

        // Seed initial data
        $data = [
            ['key' => 'site_title', 'value' => 'Fun Run 2024', 'group' => 'general', 'type' => 'text', 'created_at' => date('Y-m-d H:i:s')],
            ['key' => 'site_logo', 'value' => '', 'group' => 'general', 'type' => 'image', 'created_at' => date('Y-m-d H:i:s')],
            ['key' => 'site_favicon', 'value' => '', 'group' => 'general', 'type' => 'image', 'created_at' => date('Y-m-d H:i:s')],
            ['key' => 'smtp_host', 'value' => 'smtp.googlemail.com', 'group' => 'email', 'type' => 'text', 'created_at' => date('Y-m-d H:i:s')],
            ['key' => 'smtp_user', 'value' => '', 'group' => 'email', 'type' => 'text', 'created_at' => date('Y-m-d H:i:s')],
            ['key' => 'smtp_pass', 'value' => '', 'group' => 'email', 'type' => 'password', 'created_at' => date('Y-m-d H:i:s')],
            ['key' => 'contact_email', 'value' => 'admin@funrun.com', 'group' => 'contact', 'type' => 'text', 'created_at' => date('Y-m-d H:i:s')],
            ['key' => 'contact_phone', 'value' => '081234567890', 'group' => 'contact', 'type' => 'text', 'created_at' => date('Y-m-d H:i:s')],
        ];

        $this->db->table('settings')->insertBatch($data);
    }

    public function down()
    {
        $this->forge->dropTable('settings');
    }
}
