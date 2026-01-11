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
            return false;  // Sudah punya BIB atau tidak ditemukan
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Kunci Kategori (Lock)
            $categoryModel = new CategoryModel();

            // Raw query untuk mengunci baris (lock for update)
            $category = $db->query('SELECT * FROM categories WHERE id = ? FOR UPDATE', [$participant['category_id']])->getRowArray();

            if (!$category) {
                throw new \Exception('Kategori tidak ditemukan');
            }

            $prefix = $category['bib_prefix'] ?? '';
            $currentLast = $category['last_bib'];

            // Jika last_bib adalah 0, mulai dari 1.
            $newSeq = $currentLast + 1;

            $bibNumber = $prefix . str_pad($newSeq, 3, '0', STR_PAD_LEFT);

            // Update Kategori
            $categoryModel->update($category['id'], ['last_bib' => $newSeq]);

            // Update Peserta
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
