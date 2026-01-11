<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEmailQueueTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'to_email' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'subject' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'message' => [
                'type' => 'TEXT',
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'processing', 'sent', 'failed'],
                'default' => 'pending',
            ],
            'error_message' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'attempts' => [
                'type' => 'INT',
                'constraint' => 3,
                'default' => 0,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('status');
        $this->forge->createTable('email_queue');
    }

    public function down()
    {
        $this->forge->dropTable('email_queue');
    }
}
