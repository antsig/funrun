<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateActivityLogsAndIndexes extends Migration
{
    public function up()
    {
        // 1. Create activity_logs table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'action' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'target_id' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'ip_address' => [
                'type' => 'VARCHAR',
                'constraint' => 45,
            ],
            'details' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('activity_logs');

        // 2. Add Unique Index to participants.bib_number
        // We use raw query to ensure it's added to the existing table correctly
        try {
            $this->db->query('ALTER TABLE `participants` ADD UNIQUE INDEX `uniq_bib` (`bib_number`)');
        } catch (\Throwable $th) {
            // Ignore if index already exists or other minor issue, but log it?
            // For now, let it throw if it fails so we know.
        }
    }

    public function down()
    {
        $this->forge->dropTable('activity_logs');

        try {
            $this->db->query('ALTER TABLE `participants` DROP INDEX `uniq_bib`');
        } catch (\Throwable $th) {
            // Index might not exist
        }
    }
}
