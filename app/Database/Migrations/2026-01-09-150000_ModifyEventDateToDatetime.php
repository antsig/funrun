<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifyEventDateToDatetime extends Migration
{
    public function up()
    {
        $this->forge->modifyColumn('events', [
            'event_date' => [
                'type' => 'DATETIME',
                'null' => false,
            ]
        ]);
    }

    public function down()
    {
        $this->forge->modifyColumn('events', [
            'event_date' => [
                'type' => 'DATE',
                'null' => false,
            ]
        ]);
    }
}
