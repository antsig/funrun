<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddResetTokenToAdmins extends Migration
{
    public function up()
    {
        $fields = [
            'reset_token' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'reset_expiry' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ];
        $this->forge->addColumn('admins', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('admins', ['reset_token', 'reset_expiry']);
    }
}
