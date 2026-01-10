<?php

namespace App\Controllers;

use App\Models\OrderModel;
use App\Models\ParticipantModel;

class Ticket extends BaseController
{
    public function index()
    {
        return view('ticket/index', [
            'title' => 'Cek Status Tiket'
        ]);
    }

    public function check()
    {
        $orderCode = $this->request->getPost('order_code');

        if (!$orderCode) {
            return redirect()->to('/cek-tiket')->with('error', 'Silakan masukkan kode pesanan.');
        }

        $orderModel = new OrderModel();
        $participantModel = new ParticipantModel();

        $order = $orderModel->where('order_code', $orderCode)->first();

        if (!$order) {
            return redirect()->to('/cek-tiket')->with('error', 'Kode pesanan tidak ditemukan.');
        }

        $participants = $participantModel->getParticipantsByOrder($order['id']);

        return view('ticket/index', [
            'title' => 'Status Tiket: ' . $orderCode,
            'order' => $order,
            'participants' => $participants
        ]);
    }
}
