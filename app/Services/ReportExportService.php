<?php

namespace App\Services;

use App\Models\OrderModel;
use App\Models\ParticipantModel;

class ReportExportService
{
    public function generateOrdersExcel($filters)
    {
        $orderModel = new OrderModel();
        $builder = $orderModel->orderBy('created_at', 'DESC');

        if (!empty($filters['start_date'])) {
            $builder->where('created_at >=', $filters['start_date'] . ' 00:00:00');
        }
        if (!empty($filters['end_date'])) {
            $builder->where('created_at <=', $filters['end_date'] . ' 23:59:59');
        }
        if (!empty($filters['status'])) {
            $builder->where('payment_status', $filters['status']);
        }

        $orders = $builder->findAll();

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

        return $this->buildExcel('Laporan_Pesanan', $headers, $rows);
    }

    public function generateParticipantsExcel($filters)
    {
        $participantModel = new ParticipantModel();
        $builder = $participantModel
            ->select('participants.*, categories.name as category_name, orders.order_code')
            ->join('categories', 'categories.id = participants.category_id')
            ->join('orders', 'orders.id = participants.order_id')
            ->orderBy('participants.created_at', 'DESC');

        if (!empty($filters['category_id'])) {
            $builder->where('participants.category_id', $filters['category_id']);
        }
        if (!empty($filters['gender'])) {
            $builder->where('participants.gender', $filters['gender']);
        }

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

        return $this->buildExcel('Laporan_Peserta', $headers, $rows);
    }

    private function buildExcel($fileName, $headers, $data)
    {
        // ... (Logic from existing Reports controller)
        // Since we cannot return "exit" from a service easily without stopping execution flow,
        // we can return the string content, and let the controller handle headers.
        // OR we can keep the header output here if we intend to stream directly.
        // Let's return a generator or simply correct headers and output.
        // For cleaner separation, we should probably output the XML string.

        ob_start();

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
        // Auto-width columns
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
                echo '    <Cell ss:StyleID="sData"><Data ss:Type="String">' . htmlspecialchars((string) $cell) . '</Data></Cell>' . "\n";
            }
            echo '   </Row>' . "\n";
        }

        echo '  </Table>
 </Worksheet>
</Workbook>';

        return ob_get_clean();
    }
}
