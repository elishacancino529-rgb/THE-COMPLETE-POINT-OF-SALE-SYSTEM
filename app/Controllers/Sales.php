<?php

namespace App\Controllers;

use App\Models\CustomerModel;
use App\Models\ProductModel;
use App\Models\SaleModel;

class Sales extends PageController
{
    public function index()
    {
        $rows = (new SaleModel())
            ->select('sales.*, products.name AS product_name, customers.full_name AS customer_name, users.full_name AS staff_name')
            ->join('products', 'products.id = sales.product_id')
            ->join('customers', 'customers.id = sales.customer_id', 'left')
            ->join('users', 'users.id = sales.sold_by')
            ->orderBy('sales.id', 'DESC')->findAll();
        return $this->page('sales/list', ['title' => 'Sales history', 'section' => 'sales', 'rows' => $rows]);
    }

    public function form()
    {
        return $this->page('sales/form', [
            'title' => 'Record sale', 'section' => 'sales',
            'products' => (new ProductModel())->where('stock_quantity >', 0)->orderBy('name')->findAll(),
            'customers' => (new CustomerModel())->orderBy('full_name')->findAll(),
        ]);
    }

    public function record()
    {
        $data = $this->request->getPost();
        if (! $this->validateData($data, [
            'product_id' => 'required|is_natural_no_zero',
            'customer_id' => 'permit_empty|is_natural_no_zero',
            'quantity' => 'required|is_natural_no_zero',
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $customerId = empty($data['customer_id']) ? null : (int) $data['customer_id'];
        if ($customerId && ! (new CustomerModel())->find($customerId)) {
            return redirect()->back()->withInput()->with('error', 'Choose an active customer or leave the field blank.');
        }
        $productId = (int) $data['product_id'];
        $quantity = (int) $data['quantity'];
        $db = db_connect();
        $db->transBegin();
        $product = (new ProductModel())->find($productId);
        if (! $product) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'This car is no longer available.');
        }
        // The WHERE clause makes the stock check atomic under concurrent sales.
        $db->query(
            'UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ? AND deleted_at IS NULL AND stock_quantity >= ?',
            [$quantity, $productId, $quantity]
        );
        if ($db->affectedRows() !== 1) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Not enough stock is available for this sale. Refresh inventory and try again.');
        }
        $ok = $db->table('sales')->insert([
            'product_id' => $productId,
            'customer_id' => $customerId,
            'sold_by' => (int) session('user_id'),
            'quantity' => $quantity,
            'total_price' => number_format((float) $product['price'] * $quantity, 2, '.', ''),
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        if (! $ok || $db->transStatus() === false) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Sale could not be saved. No stock was changed.');
        }
        $db->transCommit();
        return redirect()->to('/sales')->with('success', 'Sale recorded and stock updated.');
    }
}
