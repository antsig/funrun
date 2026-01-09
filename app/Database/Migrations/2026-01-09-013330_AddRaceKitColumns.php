<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRaceKitColumns extends Migration
{
    public function up()
    {
        // Add columns to participants table
        $this->forge->addColumn('participants', [
            'is_collected' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'after' => 'bib_number'
            ],
            'collected_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'is_collected'
            ],
            'collected_by' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => true,
                'after' => 'collected_at'
            ]
        ]);

        // Add confirmed_by to orders table
        $this->forge->addColumn('orders', [
            'confirmed_by' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => true,
                'after' => 'payment_status'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('participants', ['is_collected', 'collected_at', 'collected_by']);
        $this->forge->dropColumn('orders', ['confirmed_by']);
    }
}
