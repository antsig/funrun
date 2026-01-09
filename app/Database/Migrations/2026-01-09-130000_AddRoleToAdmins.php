<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRoleToAdmins extends Migration
{
    public function up()
    {
        $this->forge->addColumn('admins', [
            'role' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'default' => 'admin',  // Default to lower role for safety
                'after' => 'password'
            ]
        ]);

        // Upgrade existing admins to 'administrator' to ensure access continuity
        $this->db->table('admins')->update(['role' => 'administrator']);
    }

    public function down()
    {
        $this->forge->dropColumn('admins', 'role');
    }
}
