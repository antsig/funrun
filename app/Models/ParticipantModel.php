<?php

namespace App\Models;

use CodeIgniter\Model;

class ParticipantModel extends Model
{
    protected $table = 'participants';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['order_id', 'name', 'gender', 'dob', 'category_id', 'jersey_size', 'jersey_status', 'bib_number', 'is_collected', 'collected_at', 'collected_by', 'taker_name', 'taker_phone'];
    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = '';
    protected $deletedField = '';
    // Validation
    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;
    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    // Generate BIB Number
    public function generateBib($participantId)
    {
        $participant = $this->find($participantId);
        if (!$participant || !empty($participant['bib_number'])) {
            return false;  // Already has BIB or not found
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Lock Category
            $categoryModel = new CategoryModel();

            // Raw query to lock row for update
            $category = $db->query('SELECT * FROM categories WHERE id = ? FOR UPDATE', [$participant['category_id']])->getRowArray();

            if (!$category) {
                throw new \Exception('Category not found');
            }

            $prefix = $category['bib_prefix'] ?? '';
            $currentLast = $category['last_bib'];

            // If last_bib is 0, maybe set a default start like 100? Or just 1.
            // Let's assume start from 1 if 0.
            // But if user set inputs, last_bib might be 1000.
            $newSeq = $currentLast + 1;

            $bibNumber = $prefix . str_pad($newSeq, 3, '0', STR_PAD_LEFT);

            // Update Category
            $categoryModel->update($category['id'], ['last_bib' => $newSeq]);

            // Update Participant
            $this->update($participantId, ['bib_number' => $bibNumber]);

            $db->transComplete();
            return $bibNumber;
        } catch (\Exception $e) {
            $db->transRollback();
            return false;
        }
    }

    public function getParticipantsByOrder($orderId)
    {
        return $this
            ->select('participants.*, categories.name as category_name')
            ->join('categories', 'categories.id = participants.category_id')
            ->where('order_id', $orderId)
            ->findAll();
    }
}
