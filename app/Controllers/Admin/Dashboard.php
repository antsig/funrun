<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\EventModel;
use App\Models\OrderModel;
use App\Models\ParticipantModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $eventModel = new EventModel();
        $orderModel = new OrderModel();
        $participantModel = new ParticipantModel();
        $categoryModel = new \App\Models\CategoryModel();

        // Basic Stats
        $data = [
            'total_events' => $eventModel->countAllResults(),
            'total_categories' => $categoryModel->countAllResults(),
            // Orders Breakdown
            'total_orders' => $orderModel->countAllResults(),
            'orders_paid' => $orderModel->where('payment_status', 'paid')->countAllResults(),
            'orders_failed' => $orderModel->whereIn('payment_status', ['expired', 'failed', 'cancel', 'deny'])->countAllResults(),
            'orders_pending' => $orderModel->where('payment_status', 'pending')->countAllResults(),
            // Participants Breakdown
            'total_participants' => $participantModel->countAllResults(),
            // Join with orders to check payment status
            'participants_paid' => $participantModel
                ->select('participants.id')
                ->join('orders', 'orders.id = participants.order_id')
                ->where('orders.payment_status', 'paid')
                ->countAllResults(),
            'participants_unpaid' => $participantModel
                ->select('participants.id')
                ->join('orders', 'orders.id = participants.order_id')
                ->where('orders.payment_status !=', 'paid')
                ->countAllResults(),
            'recent_orders' => $orderModel->orderBy('created_at', 'DESC')->limit(5)->find(),
            // Financials
            'total_revenue' => $orderModel->selectSum('total_amount')->where('payment_status', 'paid')->first()['total_amount'] ?? 0
        ];

        // Conversion Rate
        $data['conversion_rate'] = $data['total_orders'] > 0
            ? round(($data['orders_paid'] / $data['total_orders']) * 100, 1)
            : 0;

        // Daily Registrations (Last 7 Days)
        $db = \Config\Database::connect();
        $dailyQuery = $db
            ->table('participants')
            ->select('DATE(participants.created_at) as date, COUNT(participants.id) as count')
            ->join('orders', 'orders.id = participants.order_id')  // Ensure valid orders if needed, assuming participants created with orders
            ->groupBy('DATE(participants.created_at)')
            ->orderBy('date', 'DESC')
            ->limit(7)
            ->get()
            ->getResultArray();

        // Normalize for chart (ensure all 7 days exist or just show available data)
        // Let's just reverse to chronological order
        $data['daily_stats'] = array_reverse($dailyQuery);

        // Ticket Stats Logic
        $categories = $categoryModel->findAll();
        $ticketStats = [];

        // Pre-fetch counts to avoid N+1 queries ideally, but for now simple loop is fine for small scale
        // Or better: Use database builder to group by category and status
        $db = \Config\Database::connect();
        $query = $db
            ->table('participants')
            ->select('participants.category_id, orders.payment_status, COUNT(participants.id) as count')
            ->join('orders', 'orders.id = participants.order_id')
            ->groupBy('participants.category_id, orders.payment_status')
            ->get()
            ->getResultArray();

        // Process stats
        $statsMap = [];
        foreach ($query as $row) {
            $catId = $row['category_id'];
            $status = $row['payment_status'];
            if (!isset($statsMap[$catId]))
                $statsMap[$catId] = ['paid' => 0, 'pending' => 0];

            if ($status == 'paid')
                $statsMap[$catId]['paid'] += $row['count'];
            elseif ($status == 'pending')
                $statsMap[$catId]['pending'] += $row['count'];
        }

        foreach ($categories as $cat) {
            $paid = $statsMap[$cat['id']]['paid'] ?? 0;
            $pending = $statsMap[$cat['id']]['pending'] ?? 0;
            $ticketStats[] = [
                'id' => $cat['id'],
                'name' => $cat['name'],
                'quota' => $cat['quota'],
                'sold' => $paid,
                'pending' => $pending,
                'remaining' => $cat['quota'] - ($paid + $pending)
            ];
        }

        $data['ticket_stats'] = $ticketStats;

        return view('admin/dashboard', $data);
    }
}
