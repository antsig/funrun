<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRegistrationDeadlineToEvents extends Migration
{
    public function up()
    {
        $this->forge->addColumn('events', [
            'registration_deadline' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'event_date'
            ]
        ]);

        // Fix: Set default deadline to event_date for existing data to avoid immediate closure
        $this->db->query('UPDATE events SET registration_deadline = event_date WHERE registration_deadline IS NULL');
    }

    public function down()
    {
        $this->forge->dropColumn('events', 'registration_deadline');
    }
}
