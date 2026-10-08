<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class InitialSeeder extends Seeder
{
    public function run()
    {
        $password = getenv('SEED_ADMIN_PASSWORD');
        if (! $password) {
            throw new \RuntimeException('Set SEED_ADMIN_PASSWORD before seeding.');
        }

        if (! $this->db->table('users')->where('username', 'SirVon')->countAllResults()) {
            $this->db->table('users')->insert([
                'username' => 'SirVon',
                'full_name' => 'SirVon',
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }

        if ($this->db->table('products')->countAllResults() === 0) {
            $now = date('Y-m-d H:i:s');
            $this->db->table('products')->insertBatch([
                ['name' => 'Apex GT', 'price' => '128000.00', 'stock_quantity' => 4, 'created_at' => $now],
                ['name' => 'Vortex S', 'price' => '89500.00', 'stock_quantity' => 6, 'created_at' => $now],
                ['name' => 'Eclipse RS', 'price' => '164000.00', 'stock_quantity' => 2, 'created_at' => $now],
            ]);
        }

        foreach ([
            'Apex GT' => 'hero-car.webp',
            'Vortex S' => 'catalog-vortex.webp',
            'Eclipse RS' => 'catalog-eclipse.webp',
        ] as $name => $file) {
            $product = $this->db->table('products')->where('name', $name)->get()->getRowArray();
            if ($product && ! $product['image']) {
                $bytes = file_get_contents(ROOTPATH . 'public/' . $file);
                $this->db->table('media')->insert([
                    'mime_type' => 'image/webp', 'content' => $bytes,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
                $this->db->table('products')->where('id', $product['id'])
                    ->update(['image' => 'media/' . $this->db->insertID()]);
            }
        }
    }
}
