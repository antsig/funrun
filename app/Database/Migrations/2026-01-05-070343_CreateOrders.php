<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOrders extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'auto_increment' => true],
            'order_code' => ['type' => 'VARCHAR', 'constraint' => 50, 'unique' => true],
            'buyer_name' => ['type' => 'VARCHAR', 'constraint' => 100],
            'buyer_email' => ['type' => 'VARCHAR', 'constraint' => 100],
            'buyer_phone' => ['type' => 'VARCHAR', 'constraint' => 20],
            'total_amount' => ['type' => 'DECIMAL', 'constraint' => '10,2'],
            'snap_token' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'payment_status' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'pending'],
            'created_at' => ['type' => 'DATETIME', 'null' => true]
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('orders', true);
    }

    public function down()
    {
        $this->forge->dropTable('orders', true);
    }
}
