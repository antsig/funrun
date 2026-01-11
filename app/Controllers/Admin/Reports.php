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

        // Data Preparation
        $headers = ['No', 'Kode Pesanan', 'Nama Pemesan', 'Email', 'No HP', 'Total Bayar', 'Status', 'Tanggal'];
        $rows = [];
        foreach ($orders as $key => $row) {
            $rows[] = [
                $key + 1,
                $row['order_code'],
                $row['buyer_name'],
                $row['buyer_email'],
                $row['buyer_phone'],
                number_format($row['total_amount'], 0, ',', '.'),
                ucfirst($row['payment_status']),
                $row['created_at']
            ];
        }

        return $this->sendExcel('Laporan_Pesanan', $headers, $rows);
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

        $headers = ['No', 'BIB', 'Nama Peserta', 'Kategori', 'Gender', 'Tgl Lahir', 'Ukuran Jersey', 'Kode Pesanan', 'Status Check-in'];
        $rows = [];
        foreach ($participants as $key => $row) {
            $rows[] = [
                $key + 1,
                $row['bib_number'] ?? '-',
                $row['name'],
                $row['category_name'],
                $row['gender'],
                $row['dob'],
                $row['jersey_size'],
                $row['order_code'],
                $row['is_collected'] ? 'Sudah Diambil' : 'Belum'
            ];
        }

        return $this->sendExcel('Laporan_Peserta', $headers, $rows);
    }

    private function sendExcel($fileName, $headers, $data)
    {
        $fileName .= '_' . date('Ymd_His') . '.xls';

        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header("Content-Disposition: attachment; filename=\"$fileName\"");
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: private', false);

        echo '<?xml version="1.0"?>
<?mso-application progid="Excel.Sheet"?>
<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:o="urn:schemas-microsoft-com:office:office"
 xmlns:x="urn:schemas-microsoft-com:office:excel"
 xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:html="http://www.w3.org/TR/REC-html40">
 <Styles>
  <Style ss:ID="Default" ss:Name="Normal">
   <Alignment ss:Vertical="Center"/>
   <Borders/>
   <Font ss:FontName="Calibri" x:Family="Swiss" ss:Size="11" ss:Color="#000000"/>
   <Interior/>
   <NumberFormat/>
   <Protection/>
  </Style>
  <Style ss:ID="sHeader">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
   <Font ss:FontName="Calibri" ss:Size="11" ss:Color="#FFFFFF" ss:Bold="1"/>
   <Interior ss:Color="#4472C4" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="sData">
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
  </Style>
 </Styles>
 <Worksheet ss:Name="Sheet1">
  <Table>
';
        // Auto-width columns (simple approximation)
        foreach ($headers as $h) {
            echo '   <Column ss:Width="100"/>' . "\n";
        }

        // Header Row
        echo '   <Row>' . "\n";
        foreach ($headers as $h) {
            echo '    <Cell ss:StyleID="sHeader"><Data ss:Type="String">' . htmlspecialchars($h) . '</Data></Cell>' . "\n";
        }
        echo '   </Row>' . "\n";

        // Data Rows
        foreach ($data as $row) {
            echo '   <Row>' . "\n";
            foreach ($row as $cell) {
                // Force string type
                echo '    <Cell ss:StyleID="sData"><Data ss:Type="String">' . htmlspecialchars((string) $cell) . '</Data></Cell>' . "\n";
            }
            echo '   </Row>' . "\n";
        }

        echo '  </Table>
 </Worksheet>
</Workbook>';
        exit;
    }
}
