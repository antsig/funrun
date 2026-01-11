<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\EmailQueueModel;

class EmailQueue extends BaseController
{
    public function index()
    {
        $model = new EmailQueueModel();

        $data = [
            'title' => 'Email Queue Monitor',
            'emails' => $model->orderBy('created_at', 'DESC')->paginate(20),
            'pager' => $model->pager,
            'stats' => [
                'pending' => $model->where('status', 'pending')->countAllResults(),
                'sent' => $model->where('status', 'sent')->countAllResults(),
                'failed' => $model->where('status', 'failed')->countAllResults(),
            ]
        ];

        return view('admin/email_queue/index', $data);
    }

    public function retry($id)
    {
        $model = new EmailQueueModel();
        $email = $model->find($id);

        if ($email && $email['status'] === 'failed') {
            $model->update($id, [
                'status' => 'pending',
                'attempts' => 0,
                'error_message' => null
            ]);
            return redirect()->back()->with('success', 'Email requeued for retry.');
        }

        return redirect()->back()->with('error', 'Cannot retry this email.');
    }

    public function delete($id)
    {
        $model = new EmailQueueModel();
        $model->delete($id);
        return redirect()->back()->with('success', 'Email removed from queue.');
    }
}
