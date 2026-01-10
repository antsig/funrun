<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSocialMediaLinks extends Migration
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
            'platform' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
            ],
            'url' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'icon' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,  // Path to uploaded icon or class name
            ],
            'is_active' => [
                'type' => 'BOOLEAN',
                'default' => true,
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
        $this->forge->createTable('social_media_links');
    }

    public function down()
    {
        $this->forge->dropTable('social_media_links');
    }
}
