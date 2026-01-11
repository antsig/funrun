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
            // 0. Cek Pengaturan
            $settingModel = new \App\Models\SettingModel();
            $bibAllowed = $settingModel->getValue('bib_config_custom_allowed') == '1';
            $bibLength = (int) ($settingModel->getValue('bib_config_length') ?? 5);

            // 1. Validasi & Kurangi Kuota
            $categoryModel = new \App\Models\CategoryModel();

            // Kelompokkan keranjang berdasarkan kategori untuk pengurangan massal jika diperlukan, atau loop sederhana.
            // Looping baik-baik saja untuk keranjang kecil.
            foreach ($cart as $item) {
                // Update Atomik: Kurangi kuota WHERE id = ? DAN quota > 0
                // Ini mencegah kondisi balapan lebih baik daripada SELECT lalu UPDATE.
                $sql = 'UPDATE categories SET quota = quota - 1 WHERE id = ? AND quota > 0';
                $db->query($sql, [$item['category_id']]);

                if ($db->affectedRows() == 0) {
                    // Gagal jika tidak ada baris yang terpengaruh (artinya kuota 0 atau ID tidak valid)
                    throw new \Exception("Tiket untuk kategori '" . $item['category_name'] . "' sudah habis terjual.");
                }
            }

            // 2. Buat Pesanan
            $orderCode = 'FR' . date('Y') . '-' . strtoupper(bin2hex(random_bytes(3)));
            $orderModel = new OrderModel();

            $orderId = $orderModel->insert([
                'order_code' => $orderCode,
                'buyer_name' => $this->request->getPost('buyer_name'),
                'buyer_email' => $this->request->getPost('buyer_email'),
                'buyer_phone' => $this->request->getPost('buyer_phone'),
                'total_amount' => array_sum(array_column($cart, 'price'))
            ]);

            // 3. Buat Peserta
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

                // Tangani Custom BIB
                if ($bibAllowed && !empty($p['bib_number'])) {
                    $requestedBib = $p['bib_number'];

                    // Validasi: Panjang
                    if (strlen($requestedBib) !== $bibLength) {
                        throw new \Exception('Nomor BIB untuk peserta ' . ($index + 1) . ' harus ' . $bibLength . ' karakter.');
                    }

                    // Validasi: Unik
                    // Cek jika ada di tabel participants
                    $exists = $participantModel->where('bib_number', $requestedBib)->first();
                    if ($exists) {
                        throw new \Exception("Nomor BIB '" . $requestedBib . "' sudah digunakan. Silakan pilih nomor lain.");
                    }

                    $data['bib_number'] = $requestedBib;
                }

                $participantModel->insert($data);
            }

            // 4. Dapatkan Snap Token
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
                // Transaksi gagal (seharusnya ditangkap oleh Exception, tapi cek ulang)
                return redirect()->back()->with('error', 'Terjadi kesalahan sistem saat memproses transaksi.');
            }

            // Hapus Keranjang hanya jika sukses
            session()->remove('cart');
            return redirect()->to('/payment/' . $orderCode);
        } catch (\Exception $e) {
            // Rollback otomatis dengan transStart/Complete di CI4 jika transStatus false?
            // Sebenarnya try-catch manual dengan transException lebih aman untuk error kustom.
            // Jika kita menggunakan transStart, kita biasanya tidak perlu rollback manual, tapi mode ketat lebih baik.
            // Mari andalkan transRollback secara manual untuk pengecualian kustom.
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }
}
