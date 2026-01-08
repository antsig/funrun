<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSlugToEvents extends Migration
{
    public function up()
    {
        $fields = [
            'slug' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'unique' => true,
                'after' => 'name',
            ],
        ];
        $this->forge->addColumn('events', $fields);

        // Populate existing events with slugs
        $db = \Config\Database::connect();
        $events = $db->table('events')->get()->getResultArray();
        foreach ($events as $event) {
            $slug = url_title($event['name'], '-', true);
            // Ensure unique
            if ($db->table('events')->where('slug', $slug)->countAllResults() > 0) {
                $slug .= '-' . $event['id'];
            }
            $db->table('events')->where('id', $event['id'])->update(['slug' => $slug]);
        }
    }

    public function down()
    {
        $this->forge->dropColumn('events', 'slug');
    }
}
