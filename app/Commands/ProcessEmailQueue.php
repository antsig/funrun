<?php

namespace App\Commands;

use App\Models\EmailQueueModel;
use App\Models\SettingModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class ProcessEmailQueue extends BaseCommand
{
    protected $group = 'FunRun';
    protected $name = 'email:process';
    protected $description = 'Process pending emails from the queue';

    public function run(array $params)
    {
        $queueModel = new EmailQueueModel();
        $settingModel = new SettingModel();

        // Limit processing to prevent timeouts
        $limit = 10;

        // Fetch pending emails
        $emails = $queueModel
            ->where('status', 'pending')
            ->orderBy('created_at', 'ASC')
            ->findAll($limit);

        if (empty($emails)) {
            CLI::write('No pending emails found.', 'yellow');
            return;
        }

        // Configure Email Service
        $emailService = \Config\Services::email();
        $config = [
            'protocol' => 'smtp',
            'SMTPHost' => $settingModel->getValue('smtp_host'),
            'SMTPUser' => $settingModel->getValue('smtp_user'),
            'SMTPPass' => $settingModel->getValue('smtp_pass'),
            'SMTPPort' => (int) ($settingModel->getValue('smtp_port') ?? 465),
            'SMTPCrypto' => $settingModel->getValue('smtp_crypto') ?? 'ssl',
            'mailType' => 'html',
            'newline' => "\r\n"
        ];

        // Basic validation of config
        if (empty($config['SMTPHost']) || empty($config['SMTPUser'])) {
            CLI::error('SMTP Configuration missing. Please check Settings.');
            return;
        }

        $emailService->initialize($config);
        $fromName = $settingModel->getValue('app_name') ?? 'FunRun';

        foreach ($emails as $item) {
            CLI::write("Processing email ID: {$item['id']} to {$item['to_email']}...", 'white');

            // Mark as processing
            $queueModel->update($item['id'], ['status' => 'processing']);

            $emailService->setFrom($config['SMTPUser'], $fromName);
            $emailService->setTo($item['to_email']);
            $emailService->setSubject($item['subject']);
            $emailService->setMessage($item['message']);

            if ($emailService->send(false)) {  // false to not auto-clear to allow debugging if needed, but here we loop
                $queueModel->update($item['id'], [
                    'status' => 'sent',
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                CLI::write(' [SENT]', 'green');
            } else {
                $error = $emailService->printDebugger(['headers']);
                $attempts = $item['attempts'] + 1;
                $updateData = [
                    'attempts' => $attempts,
                    'error_message' => substr($error, 0, 1000),
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                if ($attempts >= 3) {
                    $updateData['status'] = 'failed';
                    $updateData['failed_at'] = date('Y-m-d H:i:s');
                    CLI::write(' [FAILED - Dead Letter]', 'red');
                } else {
                    $updateData['status'] = 'pending';  // Re-queue for next run
                    CLI::write(" [RETRY $attempts/3]", 'yellow');
                }

                $queueModel->update($item['id'], $updateData);
            }

            $emailService->clear();  // Clear for next loop
        }

        CLI::write('Batch processed.', 'green');
    }
}
