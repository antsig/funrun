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
        $db = \Config\Database::connect();
        $builder = $db->table('orders');
        $builder->select('orders.*');

        // Filter: Status
        $status = $this->request->getGet('status');
        if ($status) {
            $builder->where('payment_status', $status);
        }

        // Filter: Category
        // To filter by category, we need to join participants -> orders.
        // NOTE: An order may have multiple participants with different categories.
        // If we filter category X, show orders containing AT LEAST one participant of category X.
        $categoryId = $this->request->getGet('category_id');
        if ($categoryId) {
            $builder->join('participants', 'participants.order_id = orders.id');
            $builder->where('participants.category_id', $categoryId);
            $builder->groupBy('orders.id');  // Avoid duplicates
        }

        $builder->orderBy('orders.created_at', 'DESC');

        $data['orders'] = $builder->get()->getResultArray();

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

        $paymentModel = new PaymentModel();
        $data['payment'] = $paymentModel->where('order_id', $id)->first();

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

        // Also update payment record if exists
        $paymentModel = new PaymentModel();
        $payment = $paymentModel->where('order_id', $id)->first();
        if ($payment) {
            $paymentModel->update($payment['id'], ['status' => $this->request->getPost('status')]);
        }

        // Generate BIB if Paid manual
        if ($this->request->getPost('status') == 'paid') {
            $participantModel = new ParticipantModel();
            $participants = $participantModel->where('order_id', $id)->findAll();
            foreach ($participants as $p) {
                $participantModel->generateBib($p['id']);
            }
        }

        return redirect()->back()->with('success', 'Order status updated');
    }
}
