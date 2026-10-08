<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ExpandDemoInventory extends Migration
{
    /** Additional demonstration listings. Prices are sample values, not quotations. */
    private const CARS = [
        ['Mercedes-Benz S-Class', '128000.00', 4],
        ['Mercedes-Benz E-Class', '89500.00', 6],
        ['Mercedes-Benz G-Class', '164000.00', 2],
        ['Mercedes-Benz C-Class', '61200.00', 7],
        ['Mercedes-Benz A-Class', '43800.00', 8],
        ['Mercedes-Benz CLA', '55700.00', 5],
        ['Mercedes-Benz CLE', '73500.00', 3],
        ['Mercedes-Benz GLA', '54800.00', 6],
        ['Mercedes-Benz GLB', '58900.00', 4],
        ['Mercedes-Benz GLC', '71200.00', 5],
        ['Mercedes-Benz GLE', '98600.00', 3],
        ['Mercedes-Benz GLS', '121500.00', 2],
        ['Mercedes-Benz EQE', '92400.00', 4],
        ['Mercedes-Benz EQS', '137500.00', 2],
        ['Mercedes-AMG GT', '158900.00', 2],
    ];

    public function up()
    {
        $now = date('Y-m-d H:i:s');
        foreach (self::CARS as [$name, $price, $stock]) {
            // Existing staff-created listings, including archived ones, take precedence.
            if ($this->db->table('products')->where('name', $name)->countAllResults() > 0) {
                continue;
            }
            $this->db->table('products')->insert([
                'name' => $name,
                'price' => $price,
                'stock_quantity' => $stock,
                'created_at' => $now,
            ]);
        }
    }

    public function down()
    {
        // Keep stock edits and sales history intact if migrations are rolled back.
    }
}
