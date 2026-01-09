<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\OrderModel;

class ManualPayment extends BaseController
{
    public function upload($orderCode)
    {
        $orderModel = new OrderModel();

        log_message('info', 'ManualPayment::upload called for Order Code: ' . $orderCode);

        // Check for Post Max Size violation
        if (empty($_FILES) && empty($_POST) && isset($_SERVER['CONTENT_LENGTH']) && $_SERVER['CONTENT_LENGTH'] > 0) {
            $max = ini_get('post_max_size');
            log_message('error', 'Post Max Size Exceeded. Length: ' . $_SERVER['CONTENT_LENGTH'] . ', Max: ' . $max);
            return redirect()->to('/payment/' . ($orderCode ?? ''))->with('error', "File terlalu besar. Maksimum upload adalah $max.");
        }

        // Handle both ID and Code for backward/route compatibility
        if (is_numeric($orderCode)) {
            $order = $orderModel->find($orderCode);
        } else {
            $order = $orderModel->where('order_code', $orderCode)->first();
        }

        if (!$order) {
            log_message('error', 'Order not found for Code: ' . $orderCode);
            return redirect()->to('/')->with('error', 'Order not found');
        }

        // Validate POST request
        if (strtolower($this->request->getMethod()) !== 'post') {
            log_message('error', 'Invalid request method: ' . $this->request->getMethod());
            return redirect()->to('/payment/' . $order['order_code'])->with('error', 'Invalid request method. Please submit the form.');
        }

        $validationRule = [
            'proof_file' => [
                'label' => 'Bukti Pembayaran',
                'rules' => 'uploaded[proof_file]|mime_in[proof_file,image/jpg,image/jpeg,image/png,application/pdf]|max_size[proof_file,5120]',
            ],
            'ref_number' => [
                'label' => 'Nama Pengirim / Catatan',
                'rules' => 'required|min_length[3]|max_length[100]',
            ]
        ];

        if (!$this->validate($validationRule)) {
            log_message('error', 'Validation failed: ' . print_r($this->validator->getErrors(), true));
            return redirect()->to('/payment/' . $order['order_code'])->withInput()->with('error', $this->validator->getErrors());
        }

        $img = $this->request->getFile('proof_file');
        $fileName = '';

        if ($img->isValid() && !$img->hasMoved()) {
            $fileName = $img->getRandomName();
            // Use FCPATH to ensure correct upload directory
            try {
                $img->move(FCPATH . 'uploads/payments', $fileName);
                log_message('info', 'File uploaded successfully: ' . $fileName);
            } catch (\Exception $e) {
                log_message('error', 'File move failed: ' . $e->getMessage());
                return redirect()->to('/payment/' . $order['order_code'])->withInput()->with('error', 'Gagal menyimpan file: ' . $e->getMessage());
            }
        } else {
            log_message('error', 'File upload error: ' . $img->getErrorString() . ' (' . $img->getError() . ')');
            return redirect()->to('/payment/' . $order['order_code'])->withInput()->with('error', 'Gagal mengupload file: ' . $img->getErrorString() . ' (' . $img->getError() . ')');
        }

        // Update Order Record directly
        $data = [
            'payment_method' => 'manual',
            'payment_ref' => $this->request->getPost('ref_number'),
            'proof_file' => $fileName,
            'payment_status' => 'pending'
        ];

        if ($orderModel->update($order['id'], $data)) {
            log_message('info', 'Payment proof updated for Order ID ' . $order['id'] . ' with file: ' . $fileName);
            return redirect()->to('/payment/' . $order['order_code'])->with('success', 'Bukti pembayaran berhasil dikirim! Mohon tunggu verifikasi admin.');
        } else {
            log_message('error', 'Failed to update payment proof for Order ID ' . $order['id']);
            log_message('error', print_r($orderModel->errors(), true));
            return redirect()->to('/payment/' . $order['order_code'])->withInput()->with('error', 'Gagal menyimpan data ke database. Silakan coba lagi.');
        }
    }
}
