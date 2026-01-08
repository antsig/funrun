<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\OrderModel;
use App\Models\ParticipantModel;

class Status extends BaseController
{
    public function index()
    {
        return view('status/form');
    }

    public function check()
    {
        $order = (new OrderModel())
            ->where('order_code', $this->request->getPost('keyword'))
            ->orWhere('buyer_email', $this->request->getPost('keyword'))
            ->first();

        if (!$order) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }

        return view('status/result', [
            'order' => $order,
            'participants' => (new ParticipantModel())
                ->where('order_id', $order['id'])
                ->findAll()
        ]);
    }
}
