<?php

namespace App\InventoryBundle\Service;

use App\InventoryBundle\Entity\Stock;
use App\InventoryBundle\Message\StockOutMessage;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Service for handling stock data changes.
 */
class StockService
{
    private MessageBusInterface $messageBus;

    /**
     * Constructor.
     *
     * @param MessageBusInterface $messageBus The message bus for dispatching messages.
     */
    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    /**
     * Handles the change in stock data.
     *
     * If the stock changes from a positive value to 0, dispatches a StockOutMessage.
     *
     * @param Stock $stock The stock entity that was changed.
     * @param float|null $previousStock The previous stock value before the change.
     */
    public function handleStockChange(Stock $stock, ?float $previousStock): void
    {
        if ($previousStock > 0 && $stock->getStock() === 0.0) {
            // The item is out of stock at the location, dispatch a message
            $message = new StockOutMessage($stock->getSku(), $stock->getBranch());
            $this->messageBus->dispatch($message);
        }
    }
}
