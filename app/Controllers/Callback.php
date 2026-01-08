<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Callback extends BaseController
{
    public function midtrans()
    {
        $payload = $this->request->getJSON(true);

        $signature = hash(
            'sha512',
            $payload['order_id']
                . $payload['status_code']
                . $payload['gross_amount']
                . getenv('MIDTRANS_SERVER_KEY')
        );

        if ($signature !== $payload['signature_key']) {
            return $this->response->setStatusCode(403);
        }

        $order = (new OrderModel())
            ->where('order_code', $payload['order_id'])
            ->first();

        if (in_array($payload['transaction_status'], ['settlement', 'capture'])) {
            (new OrderModel())->update($order['id'], [
                'payment_status' => 'paid'
            ]);

            // Generate BIB for all participants
            $participantModel = new \App\Models\ParticipantModel();
            $participants = $participantModel->where('order_id', $order['id'])->findAll();
            foreach ($participants as $p) {
                $participantModel->generateBib($p['id']);
            }
        }

        (new PaymentModel())->insert([
            'order_id' => $order['id'],
            'gateway' => 'midtrans',
            'gateway_ref' => $payload['transaction_id'],
            'status' => $payload['transaction_status'],
            'payload' => json_encode($payload)
        ]);
    }
}
