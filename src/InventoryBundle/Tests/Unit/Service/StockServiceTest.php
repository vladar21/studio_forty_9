<?php
namespace App\InventoryBundle\Tests\Unit\Service;

use App\InventoryBundle\Entity\Stock;
use App\InventoryBundle\Service\StockService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class StockServiceTest extends TestCase
{
    private $messageBusMock;
    private $entityManagerMock;
    private $validatorMock;

    protected function setUp(): void
    {
        $this->messageBusMock = $this->createMock(MessageBusInterface::class);
        $this->entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $this->validatorMock = $this->createMock(ValidatorInterface::class);
    }

    public function testHandleStockChange()
    {
        $stockService = new StockService($this->entityManagerMock, $this->messageBusMock, $this->validatorMock);

        $stock = new Stock();
        $stock->setSku('TESTSKU');
        $stock->setBranch('TESTBRANCH');
        $stock->setStock(0);

        $this->messageBusMock
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->callback(function($message) {
                return $message->getSku() === 'TESTSKU' && $message->getBranch() === 'TESTBRANCH';
            }))
            ->willReturn(new Envelope(new \stdClass()));

        $stockService->handleStockChange($stock, 1.0);
    }
}
