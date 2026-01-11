<?php

namespace App\Services;

use App\Models\CategoryModel;
use App\Models\ParticipantModel;
use Config\Database;

class BIBGeneratorService
{
    /**
     * Generate BIB for a single participant.
     *
     * @param int $participantId
     * @return string|false BIB Number or false on failure
     */
    public function generateForParticipant($participantId)
    {
        $participantModel = new ParticipantModel();
        $participant = $participantModel->find($participantId);

        if (!$participant) {
            return false;
        }

        // Check if already has BIB
        if (!empty($participant['bib_number'])) {
            return $participant['bib_number'];
        }

        $db = Database::connect();
        $db->transStart();

        try {
            // Lock Category
            $categoryModel = new CategoryModel();
            $category = $db->query(
                'SELECT * FROM categories WHERE id = ? FOR UPDATE',
                [$participant['category_id']]
            )->getRowArray();

            if (!$category) {
                throw new \Exception('Category not found for bib generation');
            }

            // Calculate new sequence
            $prefix = $category['bib_prefix'] ?? '';
            $currentLast = (int) $category['last_bib'];
            $newSeq = $currentLast + 1;

            $bibNumber = $prefix . str_pad($newSeq, 3, '0', STR_PAD_LEFT);

            // Update Category and Participant
            $categoryModel->update($category['id'], ['last_bib' => $newSeq]);
            $participantModel->update($participantId, ['bib_number' => $bibNumber]);

            $db->transComplete();

            return $bibNumber;
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'BIB Gen Failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Generate BIBs for all participants in an order.
     *
     * @param int $orderId
     * @return void
     */
    public function generateForOrder($orderId)
    {
        $participantModel = new ParticipantModel();
        $participants = $participantModel->where('order_id', $orderId)->findAll();

        foreach ($participants as $p) {
            $this->generateForParticipant($p['id']);
        }
    }
}
