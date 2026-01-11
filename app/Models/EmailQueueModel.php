<?php

namespace App\Models;

use CodeIgniter\Model;

class EmailQueueModel extends Model
{
    protected $table = 'email_queue';
    protected $primaryKey = 'id';
    protected $allowedFields = ['to_email', 'subject', 'message', 'status', 'error_message', 'attempts', 'created_at', 'updated_at'];
    protected $useTimestamps = true;

    // Helper to queue an email
    public function enqueue($to, $subject, $message)
    {
        return $this->insert([
            'to_email' => $to,
            'subject' => $subject,
            'message' => $message,
            'status' => 'pending'
        ]);
    }
}
