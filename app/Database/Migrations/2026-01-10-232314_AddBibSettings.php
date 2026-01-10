<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddBibSettings extends Migration
{
    public function up()
    {
        // Insert default settings
        $data = [
            [
                'key' => 'bib_config_length',
                'value' => '5',
                'group' => 'event',
                'type' => 'number',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'key' => 'bib_config_custom_allowed',
                'value' => '0',  // 0 = False, 1 = True
                'group' => 'event',
                'type' => 'boolean',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('settings')->insertBatch($data);
    }

    public function down()
    {
        $this->db->table('settings')->whereIn('key', ['bib_config_length', 'bib_config_custom_allowed'])->delete();
    }
}
