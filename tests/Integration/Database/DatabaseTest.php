<?php

namespace Tests\Integration\Database;

use App\Database\Database;
use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase
{
    private const int DEFAULT_GTIN = 123;
    private Database $db;

    protected function setUp(): void
    {
        $this->db = new Database();
    }

    public function testExecuteInsertsRowSuccessfully(): void
    {
        $result = $this->db->execute(
            sql: "
                INSERT INTO tests.products (gtin, language, title, description, price, stock)
                VALUES (:gtin, :language, :title, :description, :price, :stock)
            ",
            params: [
                'gtin'        => '123',
                'language'    => 'es',
                'title'       => 'test',
                'description' => 'douglas testing',
                'price'       => 123,
                'stock'       => 10
            ]
        );

        $this->assertTrue($result->isSuccess());
        $this->assertGreaterThan(0, $result->getLastInsertId());

        $rows = $this->db->fetch(
            sql: "SELECT * FROM tests.products WHERE gtin = :gtin",
            params: ['gtin' => '123']
        );
        $this->assertCount(1, $rows);
        $this->assertSame(self::DEFAULT_GTIN, (int)$rows[0]['gtin']);
    }

    /** @noinspection SqlInsertValues */
    public function testExecuteHandlesSqlError(): void
    {
        $result = $this->db->execute("INSERT INTO tests.products (`nonexistent`) VALUES ('foo')");

        $this->assertFalse($result->isSuccess());
        $this->assertIsArray($result->getErrors());
        $this->assertStringContainsString('column not found', strtolower($result->getErrors()[1]));
    }

    public function testBatchInsertInsertsMultipleRows(): void
    {
        $rows = [
            [
                'gtin'  => '999', 'language' => 'it', 'title' => 'Foo', 'description' => 'Foo', 'price' => 123,
                'stock' => 10
            ],
            [
                'gtin'  => '111', 'language' => 'nz', 'title' => 'Bar', 'description' => 'Bar', 'price' => 321,
                'stock' => 11
            ],
        ];

        $result = $this->db->batchInsert('products', $rows);

        $this->assertTrue($result->isSuccess());
        $this->assertSame(2, $result->getAffectedRows());

        $all = $this->db->fetch("SELECT * FROM tests.products WHERE gtin in ('999','111')");
        $this->assertCount(2, $all);
    }

    public function testBatchInsertReturnsErrorOnEmptyRows(): void
    {
        $result = $this->db->batchInsert('products', []);

        $this->assertFalse($result->isSuccess());
        $this->assertSame('No rows to insert', $result->getErrors()[1]);
    }

    public function testBatchInsertOnDuplicateUpdatesRow(): void
    {
        $testData = ['gtin' => '678', 'language' => 'es', 'title' => 'a', 'price' => 123, 'stock' => 10];
        $this->db->execute(
            sql: "
                INSERT INTO tests.products (gtin, language, title, price, stock)
                VALUES (:gtin, :language, :title, :price, :stock)
            ",
            params: $testData
        );

        $rows   = [$testData];
        $result = $this->db->batchInsert('products', $rows, ['price']);

        $this->assertTrue($result->isSuccess());

        $all = $this->db->fetch("SELECT * FROM tests.products WHERE gtin = '678'");
        $this->assertCount(1, $all);
        $this->assertEquals('678', $all[0]['gtin']);
    }

    public function testFetchReturnsEmptyArrayIfNoResults(): void
    {
        $rows = $this->db->fetch("SELECT * FROM tests.products WHERE gtin = :gtin", ['gtin' => 'Unknown']);
        $this->assertSame([], $rows);
    }
}
