<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Registration extends BaseController
{
    public function index()
    {
        return view('registration/index', [
            'cart' => session()->get('cart') ?? []
        ]);
    }

    public function add()
    {
        $cart = session()->get('cart') ?? [];
        $data = $this->request->getPost();

        // Check if this category is already in the cart (for notification type)
        $categoryId = $data['category_id'] ?? null;
        $exists = false;
        foreach ($cart as $item) {
            if (isset($item['category_id']) && $item['category_id'] == $categoryId) {
                $exists = true;
                break;
            }
        }

        $qty = $data['quantity'] ?? 1;
        unset($data['quantity']);

        for ($i = 0; $i < $qty; $i++) {
            $cart[] = $data;
        }

        session()->set('cart', $cart);

        // If it was already in the cart, use a subtle toast. Otherwise, use the main success popup.
        if ($exists) {
            return redirect()->to('/#register')->with('toast_success', $qty . ' tiket ditambahkan (+)');
        } else {
            return redirect()->to('/#register')->with('success', $qty . ' tiket berhasil ditambahkan ke keranjang.');
        }
    }

    public function remove($index)
    {
        $cart = session()->get('cart');
        unset($cart[$index]);
        session()->set('cart', array_values($cart));

        if ($this->request->getGet('redirect') === 'checkout') {
            return redirect()->to('/checkout');
        }

        return redirect()->back();
    }

    public function decrease($categoryId)
    {
        $cart = session()->get('cart') ?? [];

        // Find the index of the first item with this category_id
        foreach ($cart as $index => $item) {
            if (isset($item['category_id']) && $item['category_id'] == $categoryId) {
                unset($cart[$index]);
                break;  // Remove only one
            }
        }

        session()->set('cart', array_values($cart));
        return redirect()->to('/#register');
    }
}
