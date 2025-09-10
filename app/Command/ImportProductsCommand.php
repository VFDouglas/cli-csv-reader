<?php

namespace CliCsvReader\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportProductsCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('import:products')->setDescription('Imports products from a CSV file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->writeln('<info>Hello! Here you\'ll be able to import products from a CSV file interactively.</info>');
        $io->writeln("<info>Please make sure the files are in the storage/products directory.</info>");

        $files = glob(__DIR__ . '/../../storage/products/*.csv');
        if (empty($files)) {
            $io->error('<info>No products found.</info>');
            return Command::FAILURE;
        }

        $io->writeln("\n<info>Here are the files found:</info>");

        $choices  = array_map('basename', $files);
        $question = new ChoiceQuestion(
            'Please select a CSV file to import',
            $choices,
            0
        );
        $question->setErrorMessage('🚨 Oops! "%s" is not a valid choice. Please try again.');
        $selected = $io->askQuestion($question);

        $io->success("You selected: $selected");

        return Command::SUCCESS;
    }
}