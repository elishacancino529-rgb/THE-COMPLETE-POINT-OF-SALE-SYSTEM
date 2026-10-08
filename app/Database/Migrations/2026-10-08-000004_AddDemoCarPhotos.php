<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDemoCarPhotos extends Migration
{
    private const PHOTOS = [
        'Mercedes-Benz C-Class' => 'mercedes-c-class.webp',
        'Mercedes-Benz A-Class' => 'mercedes-a-class.webp',
        'Mercedes-Benz CLA' => 'mercedes-cla.webp',
        'Mercedes-Benz CLE' => 'mercedes-cle.webp',
        'Mercedes-Benz GLA' => 'mercedes-gla.webp',
        'Mercedes-Benz GLB' => 'mercedes-glb.webp',
        'Mercedes-Benz GLC' => 'mercedes-glc.webp',
        'Mercedes-Benz GLE' => 'mercedes-gle.webp',
        'Mercedes-Benz GLS' => 'mercedes-gls.webp',
        'Mercedes-Benz EQE' => 'mercedes-eqe.webp',
        'Mercedes-Benz EQS' => 'mercedes-eqs.webp',
        'Mercedes-AMG GT' => 'mercedes-amg-gt.webp',
    ];

    public function up()
    {
        foreach (self::PHOTOS as $name => $file) {
            if (! is_file(ROOTPATH . 'public/' . $file)) {
                throw new \RuntimeException('Missing demo car photo: ' . $file);
            }
            // Leave user-uploaded images and archived inventory untouched.
            $this->db->table('products')
                ->where('name', $name)
                ->where('image', null)
                ->where('deleted_at', null)
                ->update(['image' => $file, 'updated_at' => date('Y-m-d H:i:s')]);
        }
    }

    public function down()
    {
        // Retain images if a staff member has edited a listing.
    }
}
