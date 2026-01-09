<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\OrderModel;
use App\Models\ParticipantModel;
use App\Models\PaymentModel;

class Orders extends BaseController
{
    public function index()
    {
        $orderModel = new OrderModel();

        // Select logic (if needed explicit select, otherwise findAll/paginate selects *)
        $orderModel->select('orders.*');

        // Filter: Status
        $status = $this->request->getGet('status');
        if ($status) {
            $orderModel->where('orders.payment_status', $status);
        }

        // Filter: Category
        $categoryId = $this->request->getGet('category_id');
        if ($categoryId) {
            $orderModel->join('participants', 'participants.order_id = orders.id');
            $orderModel->where('participants.category_id', $categoryId);
            $orderModel->groupBy('orders.id');  // Avoid duplicates
        }

        $orderModel->orderBy('orders.created_at', 'DESC');

        // Pagination
        $data['orders'] = $orderModel->paginate(10);
        $data['pager'] = $orderModel->pager;

        // Current page for numbering
        $data['currentPage'] = $this->request->getVar('page') ? $this->request->getVar('page') : 1;

        // Pass Categories for Filter Dropdown
        $data['categories'] = (new \App\Models\CategoryModel())->findAll();
        $data['filters'] = [
            'status' => $status,
            'category_id' => $categoryId
        ];

        return view('admin/orders/index', $data);
    }

    public function show($id)
    {
        $orderModel = new OrderModel();
        $data['order'] = $orderModel->find($id);

        // Payment info is now in order record, so no separate model needed
        // But the view might expect $payment variable or we update view.
        // Let's pass $order as $payment equivalent or just update view.
        // I will update the view, so no $data['payment'] needed here.

        $participantModel = new ParticipantModel();
        $data['participants'] = $participantModel
            ->select('participants.*, categories.name as category_name')
            ->join('categories', 'categories.id = participants.category_id', 'left')
            ->where('order_id', $id)
            ->findAll();

        return view('admin/orders/detail', $data);
    }

    public function updateStatus($id)
    {
        $model = new OrderModel();
        $model->update($id, ['payment_status' => $this->request->getPost('status')]);

        // Generate BIB if Paid manual (or changed to paid)
        if ($this->request->getPost('status') == 'paid') {
            $participantModel = new ParticipantModel();
            $participants = $participantModel->where('order_id', $id)->findAll();
            foreach ($participants as $p) {
                // Check if BIB exists to prevent regeneration?
                // generateBib checks internally usually or overwrites.
                $participantModel->generateBib($p['id']);
            }
        }

        return redirect()->back()->with('success', 'Order status updated');
    }

    public function approvePayment($orderId)
    {
        $orderModel = new OrderModel();
        $order = $orderModel->find($orderId);

        if (!$order) {
            return redirect()->back()->with('error', 'Order not found');
        }

        // Update Order to Paid
        $orderModel->update($orderId, ['payment_status' => 'paid']);

        // Generate BIBs
        $participantModel = new ParticipantModel();
        $participants = $participantModel->where('order_id', $orderId)->findAll();
        foreach ($participants as $p) {
            $participantModel->generateBib($p['id']);
        }

        return redirect()->back()->with('success', 'Payment approved manually.');
    }

    public function rejectPayment($orderId)
    {
        $orderModel = new OrderModel();
        $orderModel->update($orderId, ['payment_status' => 'failed']);

        return redirect()->back()->with('success', 'Payment rejected.');
    }
}
