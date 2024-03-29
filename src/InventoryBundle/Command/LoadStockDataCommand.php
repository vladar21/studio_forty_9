<?php

namespace App\InventoryBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\InventoryBundle\Entity\Stock;
use Doctrine\ORM\EntityManagerInterface;
use App\InventoryBundle\Service\StockService;

/**
 * Command to load stock data from a CSV file into the database.
 */
class LoadStockDataCommand extends Command
{
    protected static $defaultName = 'app:load-stock-data';
    private EntityManagerInterface $entityManager;
    private StockService $stockService;

    /**
     * LoadStockDataCommand constructor.
     *
     * @param EntityManagerInterface $entityManager The entity manager.
     * @param StockService $stockService The stock service.
     */
    public function __construct(EntityManagerInterface $entityManager, StockService $stockService)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->stockService = $stockService;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Loads stock data from a CSV file into the database.')
            ->addArgument('csvPath', InputArgument::REQUIRED, 'The path to the CSV file containing stock data');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $csvPath = $input->getArgument('csvPath');

        // Check if the CSV file exists
        if (!file_exists($csvPath)) {
            $io->error('The file ' . $csvPath . ' does not exist.');
            return Command::FAILURE;
        }

        $io->success('Loading stock data from: ' . $csvPath);

        // Open the CSV file
        if (($handle = fopen($csvPath, "r")) !== FALSE) {
            fgetcsv($handle); // Skip header
            // Read each line of the CSV file
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                // Check if stock with the given SKU and branch exists
                $stock = $this->entityManager->getRepository(Stock::class)->findOneBy([
                    'sku' => $data[0],
                    'branch' => $data[1],
                ]);

                $previousStock = null;
                // If stock exists, get its previous stock value
                if ($stock) {
                    $previousStock = $stock->getStock();
                } else {
                    // If stock does not exist, create a new stock object
                    $stock = new Stock();
                    $stock->setSku($data[0]);
                    $stock->setBranch($data[1]);
                }

                // Set the stock value from the CSV file
                $stock->setStock((float) $data[2]);
                $this->entityManager->persist($stock);

                // Handle stock change
                $this->stockService->handleStockChange($stock, $previousStock);
            }
            fclose($handle);
            $this->entityManager->flush();
        }

        $io->success('Stock data has been successfully loaded.');

        return Command::SUCCESS;
    }
}
