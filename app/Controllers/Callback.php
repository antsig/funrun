<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Callback extends BaseController
{
    public function midtrans()
    {
        $payload = $this->request->getJSON(true);

        try {
            $service = new \App\Services\PaymentVerificationService();
            $service->verifyMidtrans($payload);
        } catch (\Exception $e) {
            log_message('error', 'Midtrans Callback Error: ' . $e->getMessage());
            return $this->response->setStatusCode(403);  // Or 400 depending on error, but 403 for signature fail
        }
    }
}
