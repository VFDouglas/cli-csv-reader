<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateProductsTable extends AbstractMigration
{
    public function change(): void
    {
        $products = $this->table('products');
        $products
            ->addColumn('gtin', 'string', ['limit' => 50, 'null' => false, 'comment' => 'Global Trade Item Number'])
            ->addColumn('language', 'string', ['limit' => 5, 'null' => false])
            ->addColumn('title', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('picture', 'string', ['limit' => 255, 'null' => true, 'comment' => 'Picture link or path'])
            ->addColumn('description', 'text', ['null' => true])
            ->addColumn('price', 'decimal', ['precision' => 10, 'scale' => 2, 'null' => false])
            ->addColumn('stock', 'integer', ['null' => false])
            ->addTimestamps()
            ->addIndex(['gtin', 'language'], ['unique' => true])
            ->create();
    }
}
