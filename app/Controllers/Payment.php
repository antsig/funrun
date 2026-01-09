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
        $order = $orderModel->where('order_code', $orderCode)->first();

        if (!$order) {
            return redirect()->to('/')->with('error', 'Order not found');
        }

        // If order is already expired, redirect to restore/edit immediately
        if ($order['payment_status'] === 'expired') {
            return $this->edit($orderCode);
        }

        // Auto-check status for pending orders
        if ($order['payment_status'] === 'pending') {
            $midtrans = new \App\Libraries\MidtransService();
            // We use the same order_code as ID for Midtrans
            $status = $midtrans->getStatus($orderCode);

            if (isset($status['transaction_status'])) {
                $transactionStatus = $status['transaction_status'];
                $newStatus = null;

                if (in_array($transactionStatus, ['expire', 'cancel', 'deny'])) {
                    $newStatus = 'expired';  // Map to our 'expired' status
                } elseif ($transactionStatus == 'settlement' || $transactionStatus == 'capture') {
                    $newStatus = 'paid';
                }

                if ($newStatus && $newStatus !== $order['payment_status']) {
                    $orderModel->update($order['id'], ['payment_status' => $newStatus]);
                    $order['payment_status'] = $newStatus;

                    // IF EXPIRED, Redirect to Edit/Restore immediately!
                    if ($newStatus === 'expired') {
                        return $this->edit($orderCode);
                    }
                }
            }
        }

        return view('payment/index', [
            'order' => $order,
            'clientKey' => getenv('MIDTRANS_CLIENT_KEY') ?: 'SB-Mid-client-someKey',
        ]);
    }

    public function edit($orderCode)
    {
        $orderModel = new OrderModel();
        $order = $orderModel->where('order_code', $orderCode)->first();

        if (!$order) {
            return redirect()->to('/')->with('error', 'Order not found');
        }

        // 1. Get Participants to rebuild cart
        $participantModel = new \App\Models\ParticipantModel();
        $participants = $participantModel
            ->select('participants.*, categories.name as category_name, categories.price')
            ->join('categories', 'categories.id = participants.category_id')
            ->where('order_id', $order['id'])
            ->findAll();

        if (empty($participants)) {
            return redirect()->to('/')->with('error', 'Data peserta tidak ditemukan.');
        }

        // 2. Rebuild Cart Session
        $cart = [];
        $oldInput = [
            'buyer_name' => $order['buyer_name'],
            'buyer_email' => $order['buyer_email'],
            'buyer_phone' => $order['buyer_phone'],
            'participants' => []
        ];

        foreach ($participants as $i => $p) {
            // Cart item
            $cart[] = [
                'category_id' => $p['category_id'],
                'category_name' => $p['category_name'],
                'price' => $p['price'],
                // Store participant data in session to persist across refreshes
                'name' => $p['name'],
                'jersey_size' => $p['jersey_size'],
                'gender' => $p['gender'],
                'dob' => $p['dob']
            ];

            // Pre-fill form data
            $oldInput['participants'][$i] = [
                'category_id' => $p['category_id'],
                'name' => $p['name'],
                'jersey_size' => $p['jersey_size'],
                'gender' => $p['gender'],
                'dob' => $p['dob']  // Date format usually YYYY-MM-DD matches validation
            ];
        }

        session()->set('cart', $cart);
        // Persist buyer data in session as well
        session()->set('buyer_name', $order['buyer_name']);
        session()->set('buyer_email', $order['buyer_email']);
        session()->set('buyer_phone', $order['buyer_phone']);

        // 3. Redirect to Checkout with Input Data
        // To pass complex array like 'participants', withInput() might imply $_POST or flash old.
        // Helper `withInput()` normally takes current request inputs.
        // We can manually set the validation/old input in session if needed,
        // but cleaner to maybe just redirect and hope form uses values?
        // Actually CodeIgniter 'old()' helper looks at session '_ci_old_input'.

        return redirect()
            ->to('/checkout')
            ->withInput($oldInput)  // This effectively mocks the previous POST request
            ->with('error', 'Pembayaran sebelumnya kadaluarsa atau gagal. Silakan periksa kembali data Anda dan buat pesanan baru.');
    }

    // Confirm method removed - Logic moved to ManualPayment::upload
    // Keeping method stub if needed for redirects or deprecation warnings,
    // but best to remove or just redirect to index.
    public function confirm($orderCode = null)
    {
        return redirect()->to('/payment/' . $orderCode);
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
            ->select('participants.*, categories.name as category_name, events.name as event_name')
            ->join('categories', 'categories.id = participants.category_id')
            ->join('events', 'events.id = categories.event_id')
            ->where('order_id', $order['id'])
            ->findAll();

        // Assuming all participants in an order belong to the same event (which is true in this flow)
        $eventName = !empty($participants) ? $participants[0]['event_name'] : 'Fun Run Event';

        return view('payment/print', [
            'order' => $order,
            'participants' => $participants,
            'eventName' => $eventName
        ]);
    }
}
