<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class HardeningUpdates extends Migration
{
    public function up()
    {
        // 1. Audit Log Enrichment
        $this->forge->addColumn('activity_logs', [
            'severity' => [
                'type' => 'ENUM',
                'constraint' => ['info', 'warning', 'critical'],
                'default' => 'info',
                'after' => 'action'
            ],
            'context' => [
                'type' => 'TEXT',  // For storing JSON context (before/after)
                'null' => true,
                'after' => 'details'
            ],
            'request_id' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'null' => true,
                'after' => 'id'
            ]
        ]);

        // 2. Email Queue Dead Letter
        $this->forge->addColumn('email_queue', [
            'failed_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'updated_at'
            ]
        ]);

        // Add index for request_id for faster tracing
        $this->db->query('ALTER TABLE activity_logs ADD INDEX idx_request_id (request_id)');
    }

    public function down()
    {
        $this->forge->dropColumn('activity_logs', ['severity', 'context', 'request_id']);
        $this->forge->dropColumn('email_queue', ['failed_at']);
    }
}
