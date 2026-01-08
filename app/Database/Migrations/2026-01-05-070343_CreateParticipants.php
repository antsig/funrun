<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateParticipants extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'auto_increment' => true],
            'order_id' => ['type' => 'INT'],
            'name' => ['type' => 'VARCHAR', 'constraint' => 100],
            'gender' => ['type' => 'VARCHAR', 'constraint' => 10],
            'dob' => ['type' => 'DATE'],
            'category_id' => ['type' => 'INT'],
            'jersey_size' => ['type' => 'VARCHAR', 'constraint' => 5],
            'jersey_status' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'pending'],
            'bib_number' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true]
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('participants', true);
    }

    public function down()
    {
        $this->forge->dropTable('participants', true);
    }
}
