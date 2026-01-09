<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class InitialSeeder extends Seeder
{
    public function run()
    {
        $eventData = [
            'name' => 'FunRun Gorontalo Utaras 2026',
            'description' => 'Join the biggest running event in Gorontalo! Experience the thrill of running through the city streets with thousands of other participants. Medals and jerseys included for all finishers.',
            'event_date' => date('Y-m-d', strtotime('+3 months')),
            'location' => 'Blok Plan Gorontalo Utara',
            'created_at' => date('Y-m-d H:i:s'),
        ];

        // Insert Event
        $this->db->table('events')->insert($eventData);
        $eventId = $this->db->insertID();

        $categories = [
            [
                'event_id' => $eventId,
                'name' => '5K Fun Run',
                'price' => 150000,
                'quota' => 1000,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'event_id' => $eventId,
                'name' => '10K Race',
                'price' => 250000,
                'quota' => 500,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'event_id' => $eventId,
                'name' => 'Half Marathon (21K)',
                'price' => 400000,
                'quota' => 200,
                'created_at' => date('Y-m-d H:i:s'),
            ]
        ];

        // Insert Categories
        $this->db->table('categories')->insertBatch($categories);
    }
}
