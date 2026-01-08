<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\EventModel;
use App\Models\OrderModel;
use App\Models\ParticipantModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $eventModel = new EventModel();
        $orderModel = new OrderModel();
        $participantModel = new ParticipantModel();

        $data = [
            'total_events' => $eventModel->countAllResults(),
            'total_orders' => $orderModel->countAllResults(),
            'total_participants' => $participantModel->countAllResults(),
            'recent_orders' => $orderModel->orderBy('created_at', 'DESC')->limit(5)->find()
        ];

        return view('admin/dashboard', $data);
    }
}
