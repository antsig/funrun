<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateApiTokensTable extends Migration
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
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'token' => [
                'type' => 'VARCHAR',
                'constraint' => 128,
                'unique' => true,
            ],
            'scopes' => [
                'type' => 'TEXT',  // JSON or CSV of scopes e.g. "stats:read"
                'null' => true,
            ],
            'ip_whitelist' => [
                'type' => 'TEXT',  // Optional: comma separated IPs
                'null' => true,
            ],
            'last_used_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'revoked_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('api_tokens');

        // Add initial token (migrated from env if possible, or just a default one)
        // We'll leave it empty for now and let the seeder or admin UI handle it.
    }

    public function down()
    {
        $this->forge->dropTable('api_tokens');
    }
}
