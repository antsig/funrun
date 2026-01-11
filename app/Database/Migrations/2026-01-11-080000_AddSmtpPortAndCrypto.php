<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSmtpPortAndCrypto extends Migration
{
    public function up()
    {
        $data = [
            [
                'key' => 'smtp_port',
                'value' => '465',
                'group' => 'email',
                'type' => 'number',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'key' => 'smtp_crypto',
                'value' => 'ssl',  // ssl or tls
                'group' => 'email',
                'type' => 'text',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        // Using ignore to avoid errors if they already exist (though unlikely in standard flow)
        $this->db->table('settings')->ignore(true)->insertBatch($data);
    }

    public function down()
    {
        $this->db->table('settings')->whereIn('key', ['smtp_port', 'smtp_crypto'])->delete();
    }
}
