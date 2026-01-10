<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAccountNameToSocialMedia extends Migration
{
    public function up()
    {
        $this->forge->addColumn('social_media_links', [
            'account_name' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'after' => 'url',
                'null' => true,
            ],
        ]);

        // Also ensure platform constraint is long enough if not already
        $this->forge->modifyColumn('social_media_links', [
            'platform' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('social_media_links', 'account_name');
    }
}
