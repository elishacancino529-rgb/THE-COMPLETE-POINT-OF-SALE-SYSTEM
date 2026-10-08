<?php

namespace App\Controllers;

use CodeIgniter\Exceptions\PageNotFoundException;

class Media extends BaseController
{
    public function show(int $id)
    {
        $row = db_connect()->table('media')->where('id', $id)->get()->getRowArray();
        if (! $row) {
            throw PageNotFoundException::forPageNotFound();
        }
        return $this->response
            ->setHeader('Content-Type', $row['mime_type'])
            ->setHeader('Content-Disposition', 'inline')
            ->setHeader('X-Content-Type-Options', 'nosniff')
            ->setHeader('Cache-Control', 'private, max-age=3600')
            ->setBody($row['content']);
    }
}
