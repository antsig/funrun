<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePayments extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'auto_increment' => true],
            'order_id' => ['type' => 'INT'],
            'gateway' => ['type' => 'VARCHAR', 'constraint' => 50],
            'gateway_ref' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'status' => ['type' => 'VARCHAR', 'constraint' => 20],
            'payload' => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true]
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('payments', true);
    }

    public function down()
    {
        $this->forge->dropTable('payments', true);
    }
}
