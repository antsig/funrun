<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ActivityLogModel;  // Added
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
        $newStatus = $this->request->getPost('status');

        // If paid, go through service to ensure BIBs and logs
        if ($newStatus == 'paid') {
            try {
                (new \App\Services\PaymentVerificationService())->approve($id, session()->get('admin_id'));
            } catch (\Exception $e) {
                return redirect()->back()->with('error', $e->getMessage());
            }
        } else {
            // For other status manual updates (e.g. pending/failed)
            // We can use the service reject or just update directly if it's a simple status change
            // But if we want state machine enforcement, we should put it in service.
            // For now, let's keep simple manual update for non-paid via model but adding log.
            $model = new OrderModel();
            $model->update($id, ['payment_status' => $newStatus]);
            (new ActivityLogModel())->log('update_order_status', $id, "Status changed to $newStatus");
        }

        return redirect()->back()->with('success', 'Order status updated');
    }

    public function approvePayment($orderId)
    {
        try {
            (new \App\Services\PaymentVerificationService())->approve($orderId, session()->get('admin_id'));
            return redirect()->back()->with('success', 'Payment approved manually.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function rejectPayment($orderId)
    {
        try {
            (new \App\Services\PaymentVerificationService())->reject($orderId, 'Manual Rejection by Admin');
            return redirect()->back()->with('success', 'Payment rejected.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
