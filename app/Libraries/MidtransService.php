<?php

namespace App\Libraries;

class MidtransService
{
    protected $serverKey;
    protected $isProduction;
    protected $apiUrl;

    public function __construct()
    {
        // Default to Sandbox if not set.
        // You should set MIDTRANS_SERVER_KEY in .env
        $this->serverKey = getenv('MIDTRANS_SERVER_KEY') ?: 'SB-Mid-server-GwUP_Wbjn4RC4zG_ABC123';
        $this->isProduction = getenv('MIDTRANS_IS_PRODUCTION') === 'true';

        $this->apiUrl = $this->isProduction
            ? 'https://app.midtrans.com/snap/v1/transactions'
            : 'https://app.sandbox.midtrans.com/snap/v1/transactions';
    }

    public function getSnapToken($params)
    {
        $payload = json_encode($params);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Basic ' . base64_encode($this->serverKey . ':')
        ]);

        $result = curl_exec($ch);
        curl_close($ch);

        $response = json_decode($result, true);

        return $response['token'] ?? null;
    }

    public function getStatus($orderId)
    {
        $baseUrl = $this->isProduction
            ? 'https://api.midtrans.com/v2'
            : 'https://api.sandbox.midtrans.com/v2';

        $url = "$baseUrl/$orderId/status";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'Authorization: Basic ' . base64_encode($this->serverKey . ':')
        ]);

        $result = curl_exec($ch);
        curl_close($ch);

        return json_decode($result, true);
    }
}
