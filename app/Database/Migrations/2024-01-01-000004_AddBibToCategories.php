<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddBibToCategories extends Migration
{
    public function up()
    {
        $fields = [
            'bib_prefix' => [
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => true,
                'after' => 'quota',
            ],
            'last_bib' => [
                'type' => 'INT',
                'default' => 0,
                'after' => 'bib_prefix',
            ],
        ];
        $this->forge->addColumn('categories', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('categories', 'bib_prefix');
        $this->forge->dropColumn('categories', 'last_bib');
    }
}
