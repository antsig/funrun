<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ActivityLogModel;

class ActivityLogs extends BaseController
{
    public function index()
    {
        $model = new ActivityLogModel();

        $data = [
            'title' => 'Activity Logs',
            'logs' => $model
                ->select('activity_logs.*, admins.name as user_name')
                ->join('admins', 'admins.id = activity_logs.user_id', 'left')
                ->orderBy('activity_logs.created_at', 'DESC')
                ->paginate(20),
            'pager' => $model->pager
        ];

        return view('admin/activity_logs/index', $data);
    }
}
