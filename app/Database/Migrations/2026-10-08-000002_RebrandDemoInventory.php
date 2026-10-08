<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RebrandDemoInventory extends Migration
{
    private const MODELS = [
        ['old' => 'Apex GT', 'new' => 'Mercedes-Benz S-Class', 'image' => 'mercedes-s-class.webp', 'hash' => '4c5e23ca83af60565bc429691202283f4afc81599c53304047af3be8ab6b31f3'],
        ['old' => 'Vortex S', 'new' => 'Mercedes-Benz E-Class', 'image' => 'mercedes-e-class.webp', 'hash' => '6af15a27bb0758263eb6456914b0f3f6f3bf7820af7d6965ea5ed8346d9ae497'],
        ['old' => 'Eclipse RS', 'new' => 'Mercedes-Benz G-Class', 'image' => 'mercedes-g-class.webp', 'hash' => 'c865ea5e3479fe90a3e5b8d6d3d00c49caafe2a88618713dc9f5d707827d2eea'],
    ];

    public function up()
    {
        $this->db->transStart();

        foreach (self::MODELS as $model) {
            $product = $this->db->table('products')
                ->where('name', $model['old'])
                ->where('deleted_at', null)
                ->get()->getRowArray();

            if (! $product || $this->db->table('products')->where('name', $model['new'])->countAllResults()) {
                continue;
            }

            $changes = ['name' => $model['new'], 'updated_at' => date('Y-m-d H:i:s')];
            if (preg_match('~^media/(\d+)$~', (string) ($product['image'] ?? ''), $matches)) {
                $media = $this->db->table('media')->select('content')->where('id', (int) $matches[1])->get()->getRowArray();
                $oldBytes = $media ? base64_decode((string) $media['content'], true) : false;

                // Preserve any image a staff member uploaded after the original seed.
                if ($oldBytes !== false && hash_equals($model['hash'], hash('sha256', $oldBytes))) {
                    $newBytes = file_get_contents(ROOTPATH . 'public/' . $model['image']);
                    if ($newBytes === false) {
                        throw new \RuntimeException('Missing Mercedes demo image: ' . $model['image']);
                    }
                    $this->db->table('media')->insert([
                        'mime_type' => 'image/webp',
                        'content' => base64_encode($newBytes),
                        'created_at' => date('Y-m-d H:i:s'),
                    ]);
                    $changes['image'] = 'media/' . $this->db->insertID();
                }
            }

            $this->db->table('products')->where('id', $product['id'])->update($changes);
        }

        $this->db->transComplete();
        if (! $this->db->transStatus()) {
            throw new \RuntimeException('Could not rebrand the demo inventory.');
        }
    }

    public function down()
    {
        // Keep product names and images as edited by staff after this migration.
    }
}
