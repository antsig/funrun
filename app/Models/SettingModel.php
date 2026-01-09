<?php

namespace App\Models;

use CodeIgniter\Model;

class SettingModel extends Model
{
    protected $table = 'settings';
    protected $primaryKey = 'id';
    protected $allowedFields = ['key', 'value', 'group', 'type'];
    protected $useTimestamps = true;

    // Helper to get value by key
    public function getValue($key)
    {
        $row = $this->where('key', $key)->first();
        return $row ? $row['value'] : null;
    }

    // Helper to update value by key
    public function updateValue($key, $value)
    {
        return $this->where('key', $key)->set(['value' => $value])->update();
    }
}
