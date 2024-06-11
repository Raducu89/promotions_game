<?php

namespace App\Command;

use App\Service\DataImportService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(name: 'app:import-data')]
class ImportDataCommand extends Command
{
    private DataImportService $dataImportService;

    public function __construct(DataImportService $dataImportService)
    {
        $this->dataImportService = $dataImportService;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Import data from CSV files')
            ->addOption('partners', null, InputOption::VALUE_REQUIRED, 'Path to the partners CSV file')
            ->addOption('prizes', null, InputOption::VALUE_REQUIRED, 'Path to the prizes CSV file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $partnersFile = $input->getOption('partners');
        $prizesFile = $input->getOption('prizes');

        if ($partnersFile) {
            $this->dataImportService->importPartners($partnersFile);
            $output->writeln('Partners imported successfully.');
        }

        if ($prizesFile) {
            $this->dataImportService->importPrizes($prizesFile);
            $output->writeln('Prizes imported successfully.');
        }

        return Command::SUCCESS;
    }
}
