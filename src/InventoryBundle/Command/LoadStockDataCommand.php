<?php

namespace App\InventoryBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class LoadStockDataCommand extends Command
{
    protected static $defaultName = 'app:load-stock-data';

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

        // Example of reading CSV data
        if (($handle = fopen($csvPath, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                // Process each row of the CSV here
                // For example, output the first column of each row
                $io->note('Processing item: ' . $data[0]);
            }
            fclose($handle);
        }

        $io->success('Stock data has been successfully loaded.');

        return Command::SUCCESS;
    }
}
