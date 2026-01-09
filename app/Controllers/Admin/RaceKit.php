<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\OrderModel;
use App\Models\ParticipantModel;

class RaceKit extends BaseController
{
    public function index()
    {
        // Fetch recent collections
        // Fetch recent collections
        $participantModel = new ParticipantModel();
        $recentCollections = $participantModel
            ->select('participants.*, orders.order_code, orders.buyer_name, admins.name as admin_name')
            ->join('orders', 'orders.id = participants.order_id')
            ->join('admins', 'admins.id = participants.collected_by', 'left')
            ->where('is_collected', 1)
            ->orderBy('collected_at', 'DESC')
            ->limit(20)
            ->findAll();

        return view('admin/racekit/index', ['recentCollections' => $recentCollections]);
    }

    public function search()
    {
        $orderCode = trim($this->request->getVar('order_code'));
        if (!$orderCode) {
            return redirect()->back()->with('error', 'Please enter Order Code');
        }

        return redirect()->to('/admin/racekit/detail/' . $orderCode);
    }

    public function detail($orderCode)
    {
        $orderModel = new OrderModel();
        $order = $orderModel->where('order_code', $orderCode)->first();

        if (!$order) {
            return redirect()->to('/admin/racekit')->with('error', 'Order not found.');
        }

        // Fetch participants with category info
        $participantModel = new ParticipantModel();
        $participants = $participantModel
            ->select('participants.*, categories.name as category_name')
            ->join('categories', 'categories.id = participants.category_id')
            ->where('order_id', $order['id'])
            ->findAll();

        return view('admin/racekit/detail', [
            'order' => $order,
            'participants' => $participants
        ]);
    }

    public function markCollected($participantId)
    {
        $participantModel = new ParticipantModel();

        $takerName = $this->request->getPost('taker_name');
        $takerPhone = $this->request->getPost('taker_phone');

        // Optional validation if you want to enforce it backend side
        if (empty($takerName) || empty($takerPhone)) {
            return redirect()->back()->with('error', 'Nama Pengambil dan No. HP wajib diisi.');
        }

        $data = [
            'is_collected' => 1,
            'jersey_status' => 'taken',
            'collected_at' => date('Y-m-d H:i:s'),
            'collected_by' => session()->get('admin_id'),
            'taker_name' => $takerName,
            'taker_phone' => $takerPhone,
        ];

        $participantModel->update($participantId, $data);

        return redirect()->to('/admin/racekit')->with('success', 'Participant marked as Collected');
    }

    public function markAllCollected($orderId)
    {
        $participantModel = new ParticipantModel();

        $data = [
            'is_collected' => 1,
            'jersey_status' => 'taken',  // Update jersey status
            'collected_at' => date('Y-m-d H:i:s'),
            'collected_by' => session()->get('admin_id')
        ];

        $participantModel->where('order_id', $orderId)->set($data)->update();

        return redirect()->back()->with('success', 'All participants marked as Collected');
    }
}
