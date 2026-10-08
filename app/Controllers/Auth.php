<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    public function login()
    {
        if (session('user_id')) {
            return redirect()->to('/');
        }
        return view('auth/login');
    }

    public function attempt()
    {
        $input = $this->request->getPost();
        if (! $this->validateData($input, [
            'username' => 'required|max_length[50]',
            'password' => 'required',
        ])) {
            return redirect()->back()->withInput()->with('error', 'Enter your username and password.');
        }
        $user = (new UserModel())->where('username', trim($input['username']))->first();
        if (! $user || ! password_verify($input['password'], $user['password'])) {
            return redirect()->back()->withInput()->with('error', 'Incorrect username or password.');
        }
        session()->regenerate(true);
        session()->set(['user_id' => $user['id'], 'user_name' => $user['full_name']]);
        return redirect()->to('/');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}
