<?php

namespace Tests\Unit\Database;

use App\Database\Connection;
use PDO;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class ConnectionTest extends TestCase
{
    protected function setUp(): void
    {
        $ref  = new ReflectionClass(Connection::class);
        $prop = $ref->getProperty('instance');
        $prop->setValue(null, null);
    }

    public function testGetInstanceReturnsSameInstance(): void
    {
        $instance1 = Connection::getInstance();
        $instance2 = Connection::getInstance();

        $this->assertInstanceOf(Connection::class, $instance1);
        $this->assertSame($instance1, $instance2); // singleton check
    }

    public function testGetConnectionReturnsPDO(): void
    {
        $connection = Connection::getInstance();

        $this->assertInstanceOf(PDO::class, $connection->getConnection());
    }

    public function testUsesTestDatabaseWhenRunningUnderPhpunit(): void
    {
        if (!defined('PHPUNIT_COMPOSER_INSTALL')) {
            $this->markTestSkipped('Only meaningful under PHPUnit runtime');
        }

        $connection = Connection::getInstance();
        $pdo        = $connection->getConnection();

        $this->assertStringContainsString(
            'mysql via TCP/IP',
            $pdo->getAttribute(PDO::ATTR_CONNECTION_STATUS) ?? ''
        );
    }
}
