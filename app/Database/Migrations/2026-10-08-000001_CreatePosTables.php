<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreatePosTables extends Migration
{
    public function up()
    {
        $postgres = $this->db->DBDriver === 'Postgre';
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'name' => ['type' => 'VARCHAR', 'constraint' => 100],
            'price' => ['type' => 'DECIMAL', 'constraint' => '10,2'],
            'stock_quantity' => ['type' => 'INT', 'default' => 0],
            'image' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_at' => ['type' => 'DATETIME'],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('products');

        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'full_name' => ['type' => 'VARCHAR', 'constraint' => 100],
            'email' => ['type' => 'VARCHAR', 'constraint' => 100],
            'phone' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'created_at' => ['type' => 'DATETIME'],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('customers');

        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'username' => ['type' => 'VARCHAR', 'constraint' => 50],
            'full_name' => ['type' => 'VARCHAR', 'constraint' => 100],
            'password' => ['type' => 'VARCHAR', 'constraint' => 255],
            'avatar' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_at' => ['type' => 'DATETIME'],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('username');
        $this->forge->createTable('users');

        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'product_id' => ['type' => 'INT', 'unsigned' => true],
            'customer_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'sold_by' => ['type' => 'INT', 'unsigned' => true],
            'quantity' => ['type' => 'INT'],
            'total_price' => ['type' => 'DECIMAL', 'constraint' => '10,2'],
            'created_at' => ['type' => 'DATETIME'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('product_id', 'products', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('customer_id', 'customers', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('sold_by', 'users', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('sales');

        // Image bytes live in the database so uploads survive serverless deployments.
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'mime_type' => ['type' => 'VARCHAR', 'constraint' => 40],
            'content' => ['type' => $postgres ? 'TEXT' : 'LONGTEXT'],
            'created_at' => ['type' => 'DATETIME'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('media');

        $this->forge->addField([
            'id' => ['type' => 'VARCHAR', 'constraint' => 128],
            'ip_address' => $postgres ? ['type' => 'INET'] : ['type' => 'VARCHAR', 'constraint' => 45],
            'timestamp' => ['type' => $postgres ? 'TIMESTAMPTZ' : 'TIMESTAMP', 'default' => new RawSql('CURRENT_TIMESTAMP')],
            'data' => ['type' => 'BLOB'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('timestamp');
        $this->forge->createTable('ci_sessions');
    }

    public function down()
    {
        foreach (['ci_sessions', 'media', 'sales', 'users', 'customers', 'products'] as $table) {
            $this->forge->dropTable($table, true);
        }
    }
}
