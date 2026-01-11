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
        $filters = [
            'start_date' => $this->request->getGet('start_date'),
            'end_date' => $this->request->getGet('end_date'),
            'status' => $this->request->getGet('status')
        ];

        $service = new \App\Services\ReportExportService();
        $content = $service->generateOrdersExcel($filters);

        return $this->downloadExcel('Laporan_Pesanan', $content);
    }

    public function export_participants()
    {
        $filters = [
            'category_id' => $this->request->getGet('category_id'),
            'gender' => $this->request->getGet('gender')
        ];

        $service = new \App\Services\ReportExportService();
        $content = $service->generateParticipantsExcel($filters);

        return $this->downloadExcel('Laporan_Peserta', $content);
    }

    private function downloadExcel($fileName, $content)
    {
        $fileName .= '_' . date('Ymd_His') . '.xls';

        return $this
            ->response
            ->setHeader('Content-Type', 'application/vnd.ms-excel; charset=utf-8')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $fileName . '"')
            ->setHeader('Expires', '0')
            ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0')
            ->setHeader('Cache-Control', 'private')
            ->setBody($content);
    }
}
