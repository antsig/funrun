<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CategoryModel;
use App\Models\OrderModel;
use App\Models\ParticipantModel;

class Reports extends BaseController
{
    public function index()
    {
        return view('admin/reports/index', [
            'title' => 'Laporan Event'
        ]);
    }

    public function orders()
    {
        $orderModel = new OrderModel();

        // Filters
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        $status = $this->request->getGet('status');

        // Query Builder
        $builder = $orderModel->orderBy('created_at', 'DESC');

        if ($startDate) {
            $builder->where('created_at >=', $startDate . ' 00:00:00');
        }
        if ($endDate) {
            $builder->where('created_at <=', $endDate . ' 23:59:59');
        }
        if ($status) {
            $builder->where('payment_status', $status);
        }

        $orders = $builder->findAll();

        return view('admin/reports/orders', [
            'title' => 'Laporan Pesanan',
            'orders' => $orders,
            'filters' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'status' => $status
            ]
        ]);
    }

    public function participants()
    {
        $participantModel = new ParticipantModel();
        $categoryModel = new CategoryModel();

        // Filters
        $categoryId = $this->request->getGet('category_id');
        $gender = $this->request->getGet('gender');

        $builder = $participantModel
            ->select('participants.*, categories.name as category_name, orders.order_code, orders.payment_status')
            ->join('categories', 'categories.id = participants.category_id')
            ->join('orders', 'orders.id = participants.order_id')
            ->orderBy('participants.created_at', 'DESC');

        if ($categoryId) {
            $builder->where('participants.category_id', $categoryId);
        }
        if ($gender) {
            $builder->where('participants.gender', $gender);
        }

        $participants = $builder->findAll();

        return view('admin/reports/participants', [
            'title' => 'Laporan Peserta',
            'participants' => $participants,
            'categories' => $categoryModel->findAll(),
            'filters' => [
                'category_id' => $categoryId,
                'gender' => $gender
            ]
        ]);
    }

    public function export_orders()
    {
        $orderModel = new OrderModel();

        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        $status = $this->request->getGet('status');

        $builder = $orderModel->orderBy('created_at', 'DESC');

        if ($startDate)
            $builder->where('created_at >=', $startDate . ' 00:00:00');
        if ($endDate)
            $builder->where('created_at <=', $endDate . ' 23:59:59');
        if ($status)
            $builder->where('payment_status', $status);

        $orders = $builder->findAll();

        $filename = 'Laporan_Pesanan_' . date('Ymd_His') . '.csv';

        header('Content-Description: File Transfer');
        header("Content-Disposition: attachment; filename=$filename");
        header('Content-Type: application/csv; ');

        $file = fopen('php://output', 'w');

        // Header
        fputcsv($file, ['No', 'Kode Pesanan', 'Nama Pemesan', 'Email', 'No HP', 'Total Bayar', 'Status', 'Tanggal']);

        foreach ($orders as $key => $row) {
            fputcsv($file, [
                $key + 1,
                $row['order_code'],
                $row['buyer_name'],
                $row['buyer_email'],
                $row['buyer_phone'],
                $row['total_amount'],
                ucfirst($row['payment_status']),
                $row['created_at']
            ]);
        }
        fclose($file);
        exit;
    }

    public function export_participants()
    {
        $participantModel = new ParticipantModel();

        $categoryId = $this->request->getGet('category_id');
        $gender = $this->request->getGet('gender');

        $builder = $participantModel
            ->select('participants.*, categories.name as category_name, orders.order_code')
            ->join('categories', 'categories.id = participants.category_id')
            ->join('orders', 'orders.id = participants.order_id')
            ->orderBy('participants.created_at', 'DESC');

        if ($categoryId)
            $builder->where('participants.category_id', $categoryId);
        if ($gender)
            $builder->where('participants.gender', $gender);

        $participants = $builder->findAll();

        $filename = 'Laporan_Peserta_' . date('Ymd_His') . '.csv';

        header('Content-Description: File Transfer');
        header("Content-Disposition: attachment; filename=$filename");
        header('Content-Type: application/csv; ');

        $file = fopen('php://output', 'w');

        // Header
        fputcsv($file, ['No', 'BIB', 'Nama Peserta', 'Kategori', 'Gender', 'Tgl Lahir', 'Ukuran Jersey', 'Kode Pesanan', 'Status Check-in']);

        foreach ($participants as $key => $row) {
            fputcsv($file, [
                $key + 1,
                $row['bib_number'] ?? '-',
                $row['name'],
                $row['category_name'],
                $row['gender'],
                $row['dob'],
                $row['jersey_size'],
                $row['order_code'],
                $row['is_collected'] ? 'Sudah Diambil' : 'Belum'
            ]);
        }
        fclose($file);
        exit;
    }
}
