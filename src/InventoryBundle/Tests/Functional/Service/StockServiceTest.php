<?php

namespace App\InventoryBundle\Tests\Functional\Service;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\InventoryBundle\Service\StockService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;

/**
 * Functional tests for the StockService class.
 *
 * Tests the behavior of the StockService class methods by simulating various scenarios
 * and verifying that the expected results are obtained.
 */
class StockServiceTest extends KernelTestCase
{
    /**
     * Tests that saving stock data with zero stock generates a StockOutMessage.
     *
     * This method boots the Symfony kernel to initialize the container, sets up
     * necessary dependencies such as the entity manager and message bus mocks,
     * creates a mock for the validator interface, and then invokes the
     * saveStockData method of the StockService class with test data. Finally,
     * it asserts that the method returns the expected HTTP status code.
     */
    public function testSaveStockDataGeneratesStockOutMessage(): void
    {
        // Boot the Symfony kernel to initialize the container
        self::bootKernel();
        $entityManager = self::$container->get('doctrine.orm.entity_manager');

        if (!$entityManager instanceof \Doctrine\ORM\EntityManagerInterface) {
            throw new \LogicException('Entity manager not found');
        }

        // Drop and recreate the database schema
        $metadatas = $entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($entityManager);
        $schemaTool->dropDatabase();
        $schemaTool->createSchema($metadatas);

        // Create a mock for the MessageBusInterface
        $messageBusMock = $this->createMock(MessageBusInterface::class);
        // Configure the mock to expect the dispatch method exactly once
        $messageBusMock->expects($this->once())
            ->method('dispatch')
            ->willReturn(new Envelope(new \stdClass()));

        $validatorMock = $this->createMock(\Symfony\Component\Validator\Validator\ValidatorInterface::class);
        $validatorMock->expects($this->any())
            ->method('validate')
            ->willReturn(new \ArrayObject());

        // Get the StockService instance from the container
        $stockService = new StockService($entityManager, $messageBusMock, $validatorMock);

        // Create test data to save
        $data = [
            'sku' => 'TESTSKU123',
            'branch' => 'TESTBRANCH123',
            'stock' => 0 // Set zero stock
        ];

        // Call the saveStockData method, which should generate a stock out message
        $response = $stockService->saveStockData($data);

        // Check that the method returns the expected HTTP status
        $this->assertEquals(Response::HTTP_CREATED, $response['status']);
    }
}
