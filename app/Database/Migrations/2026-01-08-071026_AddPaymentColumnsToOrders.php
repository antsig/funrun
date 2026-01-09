<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPaymentColumnsToOrders extends Migration
{
    public function up()
    {
        $fields = [
            'payment_method' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'after' => 'payment_status'
            ],
            'payment_ref' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'payment_method'
            ],
            'proof_file' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'payment_ref'
            ],
        ];
        $this->forge->addColumn('orders', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('orders', ['payment_method', 'payment_ref', 'proof_file']);
    }
}
