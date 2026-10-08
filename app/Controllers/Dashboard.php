<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\CustomerModel;
use App\Models\SaleModel;

class Dashboard extends PageController
{
    public function index()
    {
        $db = db_connect();
        $products = new ProductModel();
        $recent = (new SaleModel())
            ->select('sales.*, products.name AS product_name, customers.full_name AS customer_name')
            ->join('products', 'products.id = sales.product_id')
            ->join('customers', 'customers.id = sales.customer_id', 'left')
            ->orderBy('sales.id', 'DESC')->findAll(5);
        return $this->page('dashboard', [
            'title' => 'Overview', 'section' => 'overview',
            'cars' => $products->countAllResults(),
            'customers' => (new CustomerModel())->countAllResults(),
            'salesCount' => (new SaleModel())->countAllResults(),
            'revenue' => $db->table('sales')->selectSum('total_price')->get()->getRow('total_price') ?: 0,
            'lowStock' => $products->where('stock_quantity <=', 2)->orderBy('stock_quantity')->findAll(4),
            'recent' => $recent,
        ]);
    }
}
