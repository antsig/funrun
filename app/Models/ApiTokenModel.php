<?php

namespace App\Models;

use CodeIgniter\Model;

class ApiTokenModel extends Model
{
    protected $table = 'api_tokens';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'token', 'scopes', 'ip_whitelist', 'last_used_at', 'revoked_at', 'created_at'];
    protected $useTimestamps = false;

    /**
     * Verify if a token is valid
     */
    public function isValid($token)
    {
        $record = $this
            ->where('token', $token)
            ->groupStart()
            ->where('revoked_at', null)
            ->groupEnd()
            ->first();

        return $record;
    }

    /**
     * Record usage
     */
    public function recordUsage($id)
    {
        $this->update($id, ['last_used_at' => date('Y-m-d H:i:s')]);
    }
}
