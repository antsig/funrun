<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\OrderModel;
use App\Models\ParticipantModel;

class Checkout extends BaseController
{
    public function index()
    {
        if (!session()->has('cart')) {
            return redirect()->to('/');
        }

        // Get Settings
        $settingModel = new \App\Models\SettingModel();
        $bibAllowed = $settingModel->getValue('bib_config_custom_allowed') == '1';
        $bibLength = (int) ($settingModel->getValue('bib_config_length') ?? 5);

        return view('checkout/form', [
            'cart' => session()->get('cart'),
            'bibAllowed' => $bibAllowed,
            'bibLength' => $bibLength
        ]);
    }

    public function process()
    {
        $cart = session()->get('cart');
        if (empty($cart)) {
            return redirect()->to('/');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // 0. Settings Check
            $settingModel = new \App\Models\SettingModel();
            $bibAllowed = $settingModel->getValue('bib_config_custom_allowed') == '1';
            $bibLength = (int) ($settingModel->getValue('bib_config_length') ?? 5);

            // 1. Validate & Deduct Quota
            $categoryModel = new \App\Models\CategoryModel();

            // Group cart by category to deduct in bulk if needed, or simply loop.
            // Looping is fine for small carts.
            foreach ($cart as $item) {
                // Atomic Update: Decrement quota WHERE id = ? AND quota > 0
                // This prevents race conditions better than SELECT then UPDATE.
                $sql = 'UPDATE categories SET quota = quota - 1 WHERE id = ? AND quota > 0';
                $db->query($sql, [$item['category_id']]);

                if ($db->affectedRows() == 0) {
                    // Fail if no rows affected (meaning quota was 0 or ID invalid)
                    throw new \Exception("Tiket untuk kategori '" . $item['category_name'] . "' sudah habis terjual.");
                }
            }

            // 2. Create Order
            $orderCode = 'FR' . date('Y') . '-' . strtoupper(bin2hex(random_bytes(3)));
            $orderModel = new OrderModel();

            $orderId = $orderModel->insert([
                'order_code' => $orderCode,
                'buyer_name' => $this->request->getPost('buyer_name'),
                'buyer_email' => $this->request->getPost('buyer_email'),
                'buyer_phone' => $this->request->getPost('buyer_phone'),
                'total_amount' => array_sum(array_column($cart, 'price'))
            ]);

            // 3. Create Participants
            $participantModel = new ParticipantModel();
            foreach ($this->request->getPost('participants') as $index => $p) {
                $data = [
                    'order_id' => $orderId,
                    'name' => $p['name'],
                    'gender' => $p['gender'],
                    'dob' => $p['dob'],
                    'category_id' => $p['category_id'],
                    'jersey_size' => $p['jersey_size']
                ];

                // Handle Custom BIB
                if ($bibAllowed && !empty($p['bib_number'])) {
                    $requestedBib = $p['bib_number'];

                    // Validation: Length
                    if (strlen($requestedBib) !== $bibLength) {
                        throw new \Exception('Nomor BIB untuk peserta ' . ($index + 1) . ' harus ' . $bibLength . ' karakter.');
                    }

                    // Validation: Unique
                    // Check if exists in participants table
                    $exists = $participantModel->where('bib_number', $requestedBib)->first();
                    if ($exists) {
                        throw new \Exception("Nomor BIB '" . $requestedBib . "' sudah digunakan. Silakan pilih nomor lain.");
                    }

                    $data['bib_number'] = $requestedBib;
                }

                $participantModel->insert($data);
            }

            // 4. Get Snap Token
            $midtrans = new \App\Libraries\MidtransService();
            $params = [
                'transaction_details' => [
                    'order_id' => $orderCode,
                    'gross_amount' => (int) array_sum(array_column($cart, 'price')),
                ],
                'customer_details' => [
                    'first_name' => $this->request->getPost('buyer_name'),
                    'email' => $this->request->getPost('buyer_email'),
                    'phone' => $this->request->getPost('buyer_phone'),
                ],
            ];

            $snapToken = $midtrans->getSnapToken($params);

            if ($snapToken) {
                $orderModel->update($orderId, ['snap_token' => $snapToken]);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                // Transaction failed (should be caught by Exception, but double check)
                return redirect()->back()->with('error', 'Terjadi kesalahan sistem saat memproses transaksi.');
            }

            // Clear Cart only on success
            session()->remove('cart');
            return redirect()->to('/payment/' . $orderCode);
        } catch (\Exception $e) {
            // Rollback is automatic with transStart/Complete in CI4 if transStatus is false?
            // Actually manual try-catch with transException is safer for custom errors.
            // If we use transStart, we don't need manual rollback usually, but strict mode is better.
            // Let's rely on transRollback manually for custom exceptions.
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }
}
