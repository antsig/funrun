<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CategoryModel;
use App\Models\EventModel;

class Events extends BaseController
{
    public function index()
    {
        $model = new EventModel();
        // Pagination
        $data['events'] = $model->paginate(10);
        $data['pager'] = $model->pager;
        $data['currentPage'] = $this->request->getVar('page') ? $this->request->getVar('page') : 1;

        return view('admin/events/index', $data);
    }

    public function create()
    {
        return view('admin/events/form');
    }

    public function store()
    {
        $model = new EventModel();
        $model->save($this->request->getPost());
        return redirect()->to('/admin/events')->with('success', 'Event created successfully');
    }

    public function edit($id)
    {
        $model = new EventModel();
        $data['event'] = $model->find($id);

        $categoryModel = new CategoryModel();
        $data['categories'] = $categoryModel->where('event_id', $id)->findAll();

        return view('admin/events/form', $data);
    }

    public function update($id)
    {
        $model = new EventModel();
        $model->update($id, $this->request->getPost());
        return redirect()->to('/admin/events')->with('success', 'Event updated successfully');
    }

    public function tickets($eventId)
    {
        $eventModel = new EventModel();
        $data['event'] = $eventModel->find($eventId);

        if (!$data['event']) {
            return redirect()->to('/admin/events')->with('error', 'Event not found');
        }

        $categoryModel = new CategoryModel();
        $data['categories'] = $categoryModel->where('event_id', $eventId)->findAll();

        return view('admin/events/tickets', $data);
    }

    // Category Management
    public function addCategory($eventId)
    {
        $categoryModel = new CategoryModel();
        $categoryModel->save([
            'event_id' => $eventId,
            'name' => $this->request->getPost('name'),
            'price' => $this->request->getPost('price'),
            'quota' => $this->request->getPost('quota'),
            'bib_prefix' => $this->request->getPost('bib_prefix'),
            'last_bib' => $this->request->getPost('last_bib')
        ]);
        return redirect()->back()->with('success', 'Category added');
    }

    public function updateCategory($id)
    {
        $categoryModel = new CategoryModel();

        $data = [
            'name' => $this->request->getPost('name'),
            'price' => $this->request->getPost('price'),
            'quota' => $this->request->getPost('quota'),
            'bib_prefix' => $this->request->getPost('bib_prefix'),
            'last_bib' => $this->request->getPost('last_bib'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
        ];

        $categoryModel->update($id, $data);
        return redirect()->back()->with('success', 'Category updated successfully');
    }

    public function deleteCategory($id)
    {
        $categoryModel = new CategoryModel();
        $categoryModel->delete($id);
        return redirect()->back()->with('success', 'Category deleted');
    }
}
