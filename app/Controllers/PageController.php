<?php

namespace App\Controllers;

abstract class PageController extends BaseController
{
    protected function page(string $template, array $data = []): string
    {
        return view('layouts/app', [
            'title' => $data['title'] ?? 'Overview',
            'section' => $data['section'] ?? '',
            'content' => view($template, $data),
        ]);
    }
}
