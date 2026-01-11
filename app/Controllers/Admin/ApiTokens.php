<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ActivityLogModel;
use App\Models\ApiTokenModel;

class ApiTokens extends BaseController
{
    public function index()
    {
        $model = new ApiTokenModel();

        $data = [
            'title' => 'API Tokens',
            'tokens' => $model->orderBy('created_at', 'DESC')->findAll(),
        ];

        return view('admin/api_tokens/index', $data);
    }

    public function create()
    {
        $name = $this->request->getPost('name');

        if (empty($name)) {
            return redirect()->back()->with('error', 'Token Name is required');
        }

        $token = bin2hex(random_bytes(32));  // 64 chars

        $model = new ApiTokenModel();
        $model->insert([
            'name' => $name,
            'token' => $token,
            'scopes' => 'stats:read',  // Default scope for now
            'created_at' => date('Y-m-d H:i:s')
        ]);

        // Log action
        (new ActivityLogModel())->log('api_token_created', null, "Created token '$name'", 'info');

        // Flash the token to show it ONCE
        return redirect()->to('/admin/api-tokens')->with('new_token', $token)->with('success', "Token created successfully. Copy it now, you won't see it again!");
    }

    public function revoke($id)
    {
        $model = new ApiTokenModel();
        $model->update($id, ['revoked_at' => date('Y-m-d H:i:s')]);

        (new ActivityLogModel())->log('api_token_revoked', $id, 'Token revoked', 'warning');

        return redirect()->back()->with('success', 'Token revoked.');
    }
}
