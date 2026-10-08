<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class InitialSeeder extends Seeder
{
    public function run()
    {
        if (! $this->db->table('users')->where('username', 'SirVon')->countAllResults()) {
            $password = getenv('SEED_ADMIN_PASSWORD');
            if (! $password) {
                throw new \RuntimeException('Set SEED_ADMIN_PASSWORD before the first seed.');
            }
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
                ['name' => 'Mercedes-Benz S-Class', 'price' => '128000.00', 'stock_quantity' => 4, 'created_at' => $now],
                ['name' => 'Mercedes-Benz E-Class', 'price' => '89500.00', 'stock_quantity' => 6, 'created_at' => $now],
                ['name' => 'Mercedes-Benz G-Class', 'price' => '164000.00', 'stock_quantity' => 2, 'created_at' => $now],
                ['name' => 'Mercedes-Benz C-Class', 'price' => '61200.00', 'stock_quantity' => 7, 'created_at' => $now],
                ['name' => 'Mercedes-Benz A-Class', 'price' => '43800.00', 'stock_quantity' => 8, 'created_at' => $now],
                ['name' => 'Mercedes-Benz CLA', 'price' => '55700.00', 'stock_quantity' => 5, 'created_at' => $now],
                ['name' => 'Mercedes-Benz CLE', 'price' => '73500.00', 'stock_quantity' => 3, 'created_at' => $now],
                ['name' => 'Mercedes-Benz GLA', 'price' => '54800.00', 'stock_quantity' => 6, 'created_at' => $now],
                ['name' => 'Mercedes-Benz GLB', 'price' => '58900.00', 'stock_quantity' => 4, 'created_at' => $now],
                ['name' => 'Mercedes-Benz GLC', 'price' => '71200.00', 'stock_quantity' => 5, 'created_at' => $now],
                ['name' => 'Mercedes-Benz GLE', 'price' => '98600.00', 'stock_quantity' => 3, 'created_at' => $now],
                ['name' => 'Mercedes-Benz GLS', 'price' => '121500.00', 'stock_quantity' => 2, 'created_at' => $now],
                ['name' => 'Mercedes-Benz EQE', 'price' => '92400.00', 'stock_quantity' => 4, 'created_at' => $now],
                ['name' => 'Mercedes-Benz EQS', 'price' => '137500.00', 'stock_quantity' => 2, 'created_at' => $now],
                ['name' => 'Mercedes-AMG GT', 'price' => '158900.00', 'stock_quantity' => 2, 'created_at' => $now],
            ]);
        }

        foreach ([
            'Mercedes-Benz S-Class' => 'mercedes-s-class.webp',
            'Mercedes-Benz E-Class' => 'mercedes-e-class.webp',
            'Mercedes-Benz G-Class' => 'mercedes-g-class.webp',
        ] as $name => $file) {
            $product = $this->db->table('products')->where('name', $name)->get()->getRowArray();
            if ($product && ! $product['image']) {
                $bytes = file_get_contents(ROOTPATH . 'public/' . $file);
                $this->db->table('media')->insert([
                    'mime_type' => 'image/webp', 'content' => base64_encode($bytes),
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
                $this->db->table('products')->where('id', $product['id'])
                    ->update(['image' => 'media/' . $this->db->insertID()]);
            }
        }
    }
}
