<?php

namespace Command;

use App\Command\ImportProductsCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class ImportProductsCommandTest extends TestCase
{
    private string $csvFile;

    protected function setUp(): void
    {
        $random        = rand(1, 100) . '-' . time();
        $this->csvFile = __DIR__ . "/../../../storage/products/test_$random.csv";

        file_put_contents(
            $this->csvFile,
            "gtin,language,title,picture,description,price,stock\n1,en,Bar,Foo,Bar,1,2"
        );
    }

    protected function tearDown(): void
    {
        if (file_exists($this->csvFile)) {
            unlink($this->csvFile);
        }
    }

    public function testCommandImportsProductsSuccessfully(): void
    {
        $application = new Application();
        $application->add(new ImportProductsCommand());

        $command       = $application->find('import:products');
        $commandTester = new CommandTester($command);

        $fileName = basename($this->csvFile);
        $commandTester->setInputs([$fileName, ',', '"', '\\']);

        $exitCode = $commandTester->execute([]);

        $this->assertSame(0, $exitCode);
        $this->assertStringContainsString(ImportProductsCommand::PRODUCTS_IMPORTED, $commandTester->getDisplay());
    }
}
