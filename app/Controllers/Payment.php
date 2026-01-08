<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\OrderModel;
use App\Models\PaymentModel;

class Payment extends BaseController
{
    public function index($orderCode = null)
    {
        if (!$orderCode) {
            return redirect()->to('/');
        }

        $orderModel = new OrderModel();
        // Look up by order_code instead of ID
        $order = $orderModel->where('order_code', $orderCode)->first();

        if (!$order) {
            return redirect()->to('/')->with('error', 'Order not found');
        }

        return view('payment/index', [
            'order' => $order,
            'clientKey' => getenv('MIDTRANS_CLIENT_KEY') ?: 'SB-Mid-client-someKey',  // Sandbox default if not set
        ]);
    }

    public function confirm($orderId = null)
    {
        // NOTE: For confirmation form action, we can keep ID or switch to Code.
        // But since the View posts to /payment/confirm/{id}, we need to verify.
        // To be consistent with "Masking", we should probably change the route to use Code too.
        // Let's assume the argument is now $orderCode to fully implement the request.

        $orderCode = $orderId;  // Renaming argument for clarity if we change route pattern

        // If the ID is numeric, it might be the old way. But we want to enforce Code.
        // Let's check if it looks like an order code (starts with FR)

        $orderModel = new OrderModel();
        if (is_numeric($orderCode)) {
            $order = $orderModel->find($orderCode);
        } else {
            $order = $orderModel->where('order_code', $orderCode)->first();
        }

        if (!$order) {
            return redirect()->to('/')->with('error', 'Order not found');
        }

        if ($this->request->getMethod() === 'post') {
            $paymentModel = new PaymentModel();
            $paymentModel->insert([
                'order_id' => $order['id'],
                'gateway' => 'manual',
                'gateway_ref' => $this->request->getPost('ref_number'),
                'status' => 'pending',
                'payload' => json_encode($this->request->getPost()),
            ]);

            // Redirect back to payment page using Code
            return redirect()->to('/payment/' . $order['order_code'])->with('success', 'Payment confirmation submitted!');
        }

        return redirect()->to('/payment/' . $order['order_code']);
    }

    public function print($orderCode)
    {
        $orderModel = new OrderModel();
        $order = $orderModel->where('order_code', $orderCode)->first();

        if (!$order || $order['payment_status'] !== 'paid') {
            return redirect()->to('/payment/' . $orderCode)->with('error', 'Pesanan belum lunas atau tidak ditemukan.');
        }

        // We need participants for the invoice/proof
        $participantModel = new \App\Models\ParticipantModel();
        $participants = $participantModel
            ->select('participants.*, categories.name as category_name')
            ->join('categories', 'categories.id = participants.category_id')
            ->where('order_id', $order['id'])
            ->findAll();

        return view('payment/print', [
            'order' => $order,
            'participants' => $participants
        ]);
    }
}
