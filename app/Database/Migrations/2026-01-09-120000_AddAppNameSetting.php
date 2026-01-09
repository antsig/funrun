<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAppNameSetting extends Migration
{
    public function up()
    {
        $this->db->table('settings')->insert([
            'key' => 'app_name',
            'value' => 'FunRun App',
            'group' => 'general',
            'type' => 'text',
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function down()
    {
        $this->db->table('settings')->where('key', 'app_name')->delete();
    }
}
