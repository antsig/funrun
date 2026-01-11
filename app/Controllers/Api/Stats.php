<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\OrderModel;
use App\Models\ParticipantModel;
use CodeIgniter\API\ResponseTrait;

class Stats extends BaseController
{
    use ResponseTrait;

    protected $tokenRecord;

    public function __construct() {}

    private function validateToken()
    {
        $token = $this->request->getHeaderLine('X-API-TOKEN');
        if (empty($token)) {
            return false;
        }

        $model = new \App\Models\ApiTokenModel();
        $record = $model->isValid($token);

        if (!$record) {
            return false;
        }

        // Record usage
        $model->recordUsage($record['id']);
        $this->tokenRecord = $record;

        return true;
    }

    public function summary()
    {
        if (!$this->validateToken()) {
            return $this->failUnauthorized('Invalid API Token');
        }

        $orderModel = new OrderModel();
        $participantModel = new ParticipantModel();

        $data = [
            'orders' => [
                'total' => $orderModel->countAllResults(),
                'paid' => $orderModel->where('payment_status', 'paid')->countAllResults(),
            ],
            'participants' => [
                'total' => $participantModel->countAllResults(),
                'checked_in' => $participantModel->where('is_collected', 1)->countAllResults()
            ],
            'revenue' => (float) ($orderModel->selectSum('total_amount')->where('payment_status', 'paid')->first()['total_amount'] ?? 0),
            'generated_at' => date('Y-m-d H:i:s')
        ];

        return $this->respond($data);
    }

    public function participants()
    {
        if (!$this->validateToken()) {
            return $this->failUnauthorized('Invalid API Token');
        }

        $model = new ParticipantModel();

        $page = $this->request->getVar('page') ?? 1;
        $perPage = 50;

        $data = $model
            ->select('participants.id, participants.bib_number, participants.name, categories.name as category, participants.gender')
            ->join('categories', 'categories.id = participants.category_id')
            ->orderBy('id', 'DESC')
            ->paginate($perPage, 'default', $page);

        return $this->respond([
            'data' => $data,
            'pager' => $model->pager->getDetails()
        ]);
    }
}
