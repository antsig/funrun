<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIndexesToTables extends Migration
{
    public function up()
    {
        // Add indexes to orders table
        // Check if index exists before adding to avoid errors is hard in pure CI migration without raw SQL
        // So we use try-catch or just addSql for specific db driver, but for now simple addKey is cleaner if we assume fresh state or no index.
        // However, addKey() only works with createTable(). For existing tables, we need query.

        // $this->db->query("ALTER TABLE `orders` ADD INDEX `idx_orders_user_id` (`user_id`)");
        $this->db->query('ALTER TABLE `orders` ADD INDEX `idx_orders_payment_status` (`payment_status`)');
        $this->db->query('ALTER TABLE `orders` ADD INDEX `idx_orders_created_at` (`created_at`)');

        // Add indexes to participants table
        $this->db->query('ALTER TABLE `participants` ADD INDEX `idx_participants_order_id` (`order_id`)');
        $this->db->query('ALTER TABLE `participants` ADD INDEX `idx_participants_category_id` (`category_id`)');
        $this->db->query('ALTER TABLE `participants` ADD INDEX `idx_participants_bib_number` (`bib_number`)');

        // Add indexes to categories table
        $this->db->query('ALTER TABLE `categories` ADD INDEX `idx_categories_event_id` (`event_id`)');

        // Add indexes to events table if needed, e.g. slug is already unique
    }

    public function down()
    {
        // Drop indexes
        // $this->db->query('ALTER TABLE `orders` DROP INDEX `idx_orders_user_id`');
        $this->db->query('ALTER TABLE `orders` DROP INDEX `idx_orders_payment_status`');
        $this->db->query('ALTER TABLE `orders` DROP INDEX `idx_orders_created_at`');

        $this->db->query('ALTER TABLE `participants` DROP INDEX `idx_participants_order_id`');
        $this->db->query('ALTER TABLE `participants` DROP INDEX `idx_participants_category_id`');
        $this->db->query('ALTER TABLE `participants` DROP INDEX `idx_participants_bib_number`');

        $this->db->query('ALTER TABLE `categories` DROP INDEX `idx_categories_event_id`');
    }
}
