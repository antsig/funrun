<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CategoryModel;
use App\Models\EventModel;

class Home extends BaseController
{
    public function index()
    {
        return view('home/index', [
            'event' => (new EventModel())->first(),
            'categories' => (new CategoryModel())->findAll()
        ]);
    }
}
