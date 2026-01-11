<?php

namespace App\Models;

use CodeIgniter\Model;

class ActivityLogModel extends Model
{
    protected $table = 'activity_logs';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'action', 'target_id', 'ip_address', 'details', 'severity', 'context', 'request_id', 'created_at'];
    protected $useTimestamps = false;  // handling created_at manually for simplicity or use true

    // Static request ID per lifecycle
    protected static $requestId = null;

    public function __construct()
    {
        parent::__construct();
        if (self::$requestId === null) {
            self::$requestId = uniqid('req_', true);
        }
    }

    /**
     * Helper to log activity
     *
     * @param string $action The action name (e.g. 'login', 'create_user')
     * @param string|int|null $targetId The ID of the object being affected
     * @param mixed $details Additional details (string or array)
     * @param string $severity 'info', 'warning', 'critical'
     * @param array|null $context Context data (before/after state)
     * @return int|string|bool Insert ID
     */
    public function log($action, $targetId = null, $details = null, $severity = 'info', $context = null)
    {
        $userId = session()->get('admin_id') ?? session()->get('id') ?? null;

        $data = [
            'user_id' => $userId,
            'action' => $action,
            'target_id' => $targetId,
            'ip_address' => service('request')->getIPAddress(),
            'details' => is_array($details) || is_object($details) ? json_encode($details) : $details,
            'severity' => $severity,
            'context' => $context ? json_encode($context) : null,
            'request_id' => self::$requestId,
            'created_at' => date('Y-m-d H:i:s'),
        ];

        return $this->insert($data);
    }
}
