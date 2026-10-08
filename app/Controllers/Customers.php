<?php

namespace App\Controllers;

use App\Models\CustomerModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Customers extends PageController
{
    public function index()
    {
        $search = trim((string) $this->request->getGet('q'));
        $model = new CustomerModel();
        if ($search !== '') {
            $model->groupStart()->like('full_name', $search)->orLike('email', $search)->groupEnd();
        }
        return $this->page('resources/list', [
            'title' => 'Customers', 'section' => 'customers', 'kind' => 'customers',
            'rows' => $model->orderBy('id', 'DESC')->findAll(), 'search' => $search,
        ]);
    }

    public function form(?int $id = null)
    {
        $row = $id ? (new CustomerModel())->find($id) : null;
        if ($id && ! $row) throw PageNotFoundException::forPageNotFound();
        return $this->page('resources/form', [
            'title' => $id ? 'Edit customer' : 'Add customer', 'section' => 'customers',
            'kind' => 'customers', 'row' => $row,
        ]);
    }

    public function save(?int $id = null)
    {
        $model = new CustomerModel();
        if ($id && ! $model->find($id)) throw PageNotFoundException::forPageNotFound();
        $data = $this->request->getPost();
        if (! $this->validateData($data, [
            'full_name' => 'required|max_length[100]',
            'email' => 'required|valid_email|max_length[100]',
            'phone' => 'permit_empty|max_length[20]',
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $fields = [
            'full_name' => trim($data['full_name']),
            'email' => trim($data['email']),
            'phone' => trim($data['phone'] ?? ''),
        ];
        $model->save($id ? ['id' => $id] + $fields : $fields);
        return redirect()->to('/customers')->with('success', $id ? 'Customer updated.' : 'Customer added.');
    }

    public function delete(int $id)
    {
        if (! (new CustomerModel())->find($id)) throw PageNotFoundException::forPageNotFound();
        (new CustomerModel())->delete($id);
        return redirect()->to('/customers')->with('success', 'Customer archived. Sales history remains intact.');
    }
}
