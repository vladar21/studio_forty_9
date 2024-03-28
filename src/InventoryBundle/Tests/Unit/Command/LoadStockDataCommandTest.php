<?php

namespace App\InventoryBundle\Tests\Unit\Command;

use App\InventoryBundle\Command\LoadStockDataCommand;
use App\InventoryBundle\Service\StockService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use org\bovigo\vfs\vfsStream;

/**
 * Unit tests for the LoadStockDataCommand class.
 *
 * Tests stock data loading from a CSV file.
 */
class LoadStockDataCommandTest extends KernelTestCase
{
    private $commandTester;
    private $entityManager;
    private $stockService;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->stockService = static::getContainer()->get(StockService::class);

        $metadatas = $this->entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($this->entityManager);
        $schemaTool->dropDatabase();
        $schemaTool->createSchema($metadatas);

        // Настройка и запуск теста
        $application = new Application(static::$kernel);
        $command = new LoadStockDataCommand($this->entityManager, $this->stockService);
        $application->add($command);
        $this->commandTester = new CommandTester($application->find('app:load-stock-data'));

        vfsStream::setup('root');
    }

    public function testExecute(): void
    {
        $csvContent = "SKU,BRANCH,STOCK\nTESTSKU001,TESTBRANCH001,10";
        $vfsRoot = vfsStream::setup('root');
        $testCsvFile = vfsStream::newFile('test.csv')->at($vfsRoot)->setContent($csvContent);

        $this->commandTester->execute([
            'csvPath' => $testCsvFile->url(),
        ]);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('Stock data has been successfully loaded', $output);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null;
    }
}
