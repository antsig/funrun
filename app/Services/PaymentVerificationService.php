<?php

namespace App\Services;

use App\Models\ActivityLogModel;
use App\Models\EmailQueueModel;
use App\Models\OrderModel;
use App\Models\PaymentModel;

class PaymentVerificationService
{
    // Define State Machine constants
    const STATUS_PENDING = 'pending';
    const STATUS_REVIEWED = 'reviewed';
    const STATUS_APPROVED = 'approved';  // aka 'paid'
    const STATUS_REJECTED = 'rejected';  // aka 'failed'
    const STATUS_EXPIRED = 'expired';

    // Map legacy status to new state machine strictness if needed
    // For now assuming 'paid' maps to APPROVED

    protected $orderModel;
    protected $bibService;
    protected $activityLog;
    protected $emailQueue;

    public function __construct()
    {
        $this->orderModel = new OrderModel();
        $this->bibService = new BIBGeneratorService();
        $this->activityLog = new ActivityLogModel();
        $this->emailQueue = new EmailQueueModel();
    }

    /**
     * Process a manual approval by Admin
     */
    public function approve($orderId, $adminId)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $order = $this->orderModel->find($orderId);
            if (!$order) {
                throw new \Exception('Order not found');
            }

            // State transition check
            if ($order['payment_status'] === 'paid') {
                $db->transRollback();  // Or just complete if idempotent
                return true;
            }

            $this->orderModel->update($orderId, [
                'payment_status' => 'paid',
                'confirmed_by' => $adminId,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            // Generate BIBs
            $this->bibService->generateForOrder($orderId);

            // Audit Log
            $this->activityLog->log('payment_approved', $orderId, 'Manual approval by admin', 'info');

            // Email Trigger
            $this->emailQueue->enqueue(
                $order['buyer_email'],
                'Pembayaran Diterima - FunRun',
                $this->buildEmailContent($order)
            );

            $db->transComplete();
            return true;
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Approve Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Reject a payment
     */
    public function reject($orderId, $reason = null)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $this->orderModel->update($orderId, [
                'payment_status' => 'failed',
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            $this->activityLog->log('payment_rejected', $orderId, ['reason' => $reason], 'warning');

            // Retrieve order for email
            $order = $this->orderModel->find($orderId);
            if ($order) {
                $this->emailQueue->enqueue(
                    $order['buyer_email'],
                    'Pembayaran Ditolak - FunRun',
                    "<p>Mohon maaf, pembayaran pesanan <strong>#{$order['order_code']}</strong> ditolak. Alasan: $reason</p>"
                );
            }

            $db->transComplete();
            return true;
        } catch (\Exception $e) {
            $db->transRollback();
            throw $e;
        }
    }

    /**
     * Verify Midtrans callback
     */
    public function verifyMidtrans($payload)
    {
        // ... (Logic extracted from Callback controller)
        // Check signature...

        $serverKey = getenv('MIDTRANS_SERVER_KEY');
        $signature = hash('sha512', $payload['order_id'] . $payload['status_code'] . $payload['gross_amount'] . $serverKey);

        if ($signature !== $payload['signature_key']) {
            throw new \Exception('Invalid Signature');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $order = $this->orderModel->where('order_code', $payload['order_id'])->first();
            if (!$order) {
                throw new \Exception('Order not found');
            }

            $transactionStatus = $payload['transaction_status'];

            if (in_array($transactionStatus, ['settlement', 'capture'])) {
                // Idempotency check could go here
                if ($order['payment_status'] !== 'paid') {
                    $this->orderModel->update($order['id'], ['payment_status' => 'paid']);
                    $this->bibService->generateForOrder($order['id']);
                    $this->activityLog->log('payment_gateway_success', $order['id'], 'Midtrans settlement', 'info');

                    $this->emailQueue->enqueue(
                        $order['buyer_email'],
                        'Pembayaran Berhasil - FunRun',
                        $this->buildEmailContent($order)
                    );
                }
            } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
                $this->orderModel->update($order['id'], ['payment_status' => 'failed']);
                $this->activityLog->log('payment_gateway_failed', $order['id'], "Midtrans $transactionStatus", 'warning');
            }

            // Record raw transaction log
            (new PaymentModel())->insert([
                'order_id' => $order['id'],
                'gateway' => 'midtrans',
                'gateway_ref' => $payload['transaction_id'],
                'status' => $transactionStatus,
                'payload' => json_encode($payload)
            ]);

            $db->transComplete();
            return true;
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Midtrans Error: ' . $e->getMessage());
            throw $e;
        }
    }

    private function buildEmailContent($order)
    {
        return "<p>Halo {$order['buyer_name']},</p>
                <p>Pembayaran untuk pesanan <strong>#{$order['order_code']}</strong> telah berhasil dikonfirmasi.</p>
                <p>Terima kasih telah mendaftar!</p>";
    }
}
