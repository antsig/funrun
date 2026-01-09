<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTakerDetailsToParticipants extends Migration
{
    public function up()
    {
        $fields = [
            'taker_name' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
                'after' => 'collected_by',
            ],
            'taker_phone' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
                'after' => 'taker_name',
            ],
        ];

        $this->forge->addColumn('participants', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('participants', ['taker_name', 'taker_phone']);
    }
}
