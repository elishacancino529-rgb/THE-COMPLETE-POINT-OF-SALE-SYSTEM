<?php

namespace App\Controllers;

use App\Libraries\Uploads;
use App\Models\UserModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Staff extends PageController
{
    public function index()
    {
        $search = trim((string) $this->request->getGet('q'));
        $model = new UserModel();
        if ($search !== '') {
            $model->groupStart()->like('full_name', $search)->orLike('username', $search)->groupEnd();
        }
        return $this->page('resources/list', [
            'title' => 'Staff', 'section' => 'staff', 'kind' => 'staff',
            'rows' => $model->orderBy('id', 'DESC')->findAll(), 'search' => $search,
        ]);
    }

    public function form(?int $id = null)
    {
        $row = $id ? (new UserModel())->find($id) : null;
        if ($id && ! $row) throw PageNotFoundException::forPageNotFound();
        return $this->page('resources/form', [
            'title' => $id ? 'Edit staff member' : 'Add staff member', 'section' => 'staff',
            'kind' => 'staff', 'row' => $row,
        ]);
    }

    public function save(?int $id = null)
    {
        $model = new UserModel();
        if ($id && ! $model->find($id)) throw PageNotFoundException::forPageNotFound();
        $data = $this->request->getPost();
        $rules = [
            'username' => 'required|alpha_numeric|min_length[3]|max_length[50]',
            'full_name' => 'required|max_length[100]',
            'password' => $id ? 'permit_empty|min_length[10]' : 'required|min_length[10]',
        ];
        if (! $this->validateData($data, $rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $username = trim($data['username']);
        $existing = $model->withDeleted()->where('username', $username)->first();
        if ($existing && (int) $existing['id'] !== (int) $id) {
            return redirect()->back()->withInput()->with('error', 'This username is already taken.');
        }
        try {
            $avatar = Uploads::save($this->request->getFile('avatar'));
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
        $fields = ['username' => $username, 'full_name' => trim($data['full_name'])];
        if (! empty($data['password'])) $fields['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        if ($avatar) $fields['avatar'] = $avatar;
        $model->save($id ? ['id' => $id] + $fields : $fields);
        if ($id === (int) session('user_id')) session()->set('user_name', $fields['full_name']);
        return redirect()->to('/staff')->with('success', $id ? 'Staff member updated.' : 'Staff member added.');
    }

    public function delete(int $id)
    {
        if ($id === (int) session('user_id')) {
            return redirect()->to('/staff')->with('error', 'You cannot archive your own account.');
        }
        if (! (new UserModel())->find($id)) throw PageNotFoundException::forPageNotFound();
        (new UserModel())->delete($id);
        return redirect()->to('/staff')->with('success', 'Staff member archived. Sales history remains intact.');
    }
}
