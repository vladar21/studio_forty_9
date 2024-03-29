<?php

namespace App\InventoryBundle\Service;

use App\InventoryBundle\Entity\Stock;
use App\InventoryBundle\Message\StockOutMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Service for handling stock data changes.
 */
class StockService
{
    private EntityManagerInterface $entityManager;
    private MessageBusInterface $messageBus;
    private ValidatorInterface $validator;

    /**
     * Constructor.
     *
     * @param MessageBusInterface $messageBus The message bus for dispatching messages.
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        MessageBusInterface $messageBus,
        ValidatorInterface $validator
    ) {
        $this->entityManager = $entityManager;
        $this->messageBus = $messageBus;
        $this->validator = $validator;
    }

    public function getAllStocks()
    {
        return $this->entityManager->getRepository(Stock::class)->findAll();
    }

    public function saveStockData(array $data): array
    {
        $stock = new Stock();
        $stock->setSku($data['sku']);
        $stock->setBranch($data['branch']);
        $stock->setStock($data['stock']);

        // Validate the stock object
        $errors = $this->validator->validate($stock);
        if (count($errors) > 0) {
            $errorsString = (string) $errors;
            return ['status' => Response::HTTP_BAD_REQUEST, 'message' => $errorsString];
        }

        // Check for stock out condition before saving
        $existingStock = $this->entityManager->getRepository(Stock::class)->findOneBy([
            'sku' => $stock->getSku(),
            'branch' => $stock->getBranch(),
        ]);

        $previousStock = $existingStock ? $existingStock->getStock() : null;
        $this->handleStockChange($stock, $previousStock);

        // Here we dispatch the message
        $message = new StockOutMessage($stock->getSku(), $stock->getBranch());
        $this->messageBus->dispatch($message);

        $this->entityManager->persist($stock);
        $this->entityManager->flush();

        return ['status' => Response::HTTP_CREATED, 'message' => 'Stock data saved successfully'];
    }

    /**
     * Handles the change in stock data.
     */
    public function handleStockChange(Stock $stock, ?float $previousStock): void
    {
        if ($previousStock > 0 && $stock->getStock() == 0) {
            $message = new StockOutMessage($stock->getSku(), $stock->getBranch());
            $this->messageBus->dispatch($message);
        }
    }
}
