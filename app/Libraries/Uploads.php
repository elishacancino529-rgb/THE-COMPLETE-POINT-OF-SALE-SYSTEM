<?php

namespace App\Libraries;

use CodeIgniter\HTTP\Files\UploadedFile;

class Uploads
{
    /** Save a verified image and return its display URL. */
    public static function save(?UploadedFile $file): ?string
    {
        if ($file === null || $file->getError() === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        if (! $file->isValid() || $file->getSize() > 3 * 1024 * 1024) {
            throw new \InvalidArgumentException('Choose an image smaller than 3 MB.');
        }
        $bytes = file_get_contents($file->getTempName());
        $info = $bytes === false ? false : @getimagesizefromstring($bytes);
        $mime = $info['mime'] ?? '';
        if (! in_array($mime, ['image/jpeg', 'image/png', 'image/webp'], true)) {
            throw new \InvalidArgumentException('Only JPEG, PNG, and WebP images are allowed.');
        }
        if ($info[0] > 5000 || $info[1] > 5000) {
            throw new \InvalidArgumentException('Image dimensions must be at most 5000 × 5000 pixels.');
        }
        $db = db_connect();
        $db->table('media')->insert([
            'mime_type' => $mime,
            'content' => $bytes,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        return 'media/' . $db->insertID();
    }
}
