<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCategories extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'auto_increment' => true],
            'event_id' => ['type' => 'INT'],
            'name' => ['type' => 'VARCHAR', 'constraint' => 100],
            'price' => ['type' => 'DECIMAL', 'constraint' => '10,2'],
            'quota' => ['type' => 'INT', 'default' => 0],
            'bib_prefix' => ['type' => 'VARCHAR', 'constraint' => 10, 'null' => true],
            'last_bib' => ['type' => 'INT', 'default' => 0],
            'created_at' => ['type' => 'DATETIME', 'null' => true]
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('categories', true);
    }

    public function down()
    {
        $this->forge->dropTable('categories', true);
    }
}
