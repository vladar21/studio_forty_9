<?php

namespace App\InventoryBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\InventoryBundle\Entity\Stock;

class LoadStockDataCommand extends Command
{
    protected static $defaultName = 'app:load-stock-data';
    private $entityManager;

    public function __construct(\Doctrine\ORM\EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Loads stock data from a CSV file')
            ->addArgument('csvPath', InputArgument::REQUIRED, 'The path to the CSV file containing stock data');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $csvPath = $input->getArgument('csvPath');

        if (!file_exists($csvPath)) {
            $io->error('The file ' . $csvPath . ' does not exist.');
            return Command::FAILURE;
        }

        $io->success('Loading stock data from: ' . $csvPath);

        if (($handle = fopen($csvPath, "r")) !== FALSE) {
            fgetcsv($handle); // Skip header
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $stock = new Stock();
                $stock->setSku($data[0]);
                $stock->setBranch($data[1]);
                $stock->setStock((float)$data[2]);
                $this->entityManager->persist($stock);
            }
            $this->entityManager->flush();
            fclose($handle);
        }

        $io->success('Stock data has been successfully loaded.');

        return Command::SUCCESS;
    }
}
