<?php

namespace App\Controllers;

use App\Libraries\Uploads;
use App\Models\ProductModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Products extends PageController
{
    public function index()
    {
        $search = trim((string) $this->request->getGet('q'));
        $model = new ProductModel();
        if ($search !== '') {
            $model->like('name', $search);
        }
        return $this->page('resources/list', [
            'title' => 'Inventory', 'section' => 'products', 'kind' => 'products',
            'rows' => $model->orderBy('id', 'DESC')->findAll(), 'search' => $search,
        ]);
    }

    public function form(?int $id = null)
    {
        $row = $id ? (new ProductModel())->find($id) : null;
        if ($id && ! $row) throw PageNotFoundException::forPageNotFound();
        return $this->page('resources/form', [
            'title' => $id ? 'Edit car' : 'Add car', 'section' => 'products',
            'kind' => 'products', 'row' => $row,
        ]);
    }

    public function save(?int $id = null)
    {
        $model = new ProductModel();
        if ($id && ! $model->find($id)) throw PageNotFoundException::forPageNotFound();
        $data = $this->request->getPost();
        if (! $this->validateData($data, [
            'name' => 'required|max_length[100]',
            'price' => 'required|decimal|greater_than_equal_to[0]',
            'stock_quantity' => 'required|is_natural',
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        try {
            $image = Uploads::save($this->request->getFile('image'));
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
        $fields = [
            'name' => trim($data['name']),
            'price' => number_format((float) $data['price'], 2, '.', ''),
            'stock_quantity' => (int) $data['stock_quantity'],
        ];
        if ($image) $fields['image'] = $image;
        $model->save($id ? ['id' => $id] + $fields : $fields);
        return redirect()->to('/products')->with('success', $id ? 'Car updated.' : 'Car added to inventory.');
    }

    public function delete(int $id)
    {
        if (! (new ProductModel())->find($id)) throw PageNotFoundException::forPageNotFound();
        (new ProductModel())->delete($id);
        return redirect()->to('/products')->with('success', 'Car archived. Sales history remains intact.');
    }
}
