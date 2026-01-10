<?php

namespace App\Models;

use CodeIgniter\Model;

class SocialMediaModel extends Model
{
    protected $table = 'social_media_links';
    protected $primaryKey = 'id';
    protected $allowedFields = ['platform', 'account_name', 'url', 'icon', 'is_active'];
    protected $useTimestamps = true;
}
