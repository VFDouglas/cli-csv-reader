<?php

namespace App\Command;

use App\DTO\FileReaderConfigDTO;
use App\Exception\UnsupportedFileFormatException;
use App\Factory\FileReaderFactory;
use App\Repository\ProductRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportProductsCommand extends Command
{
    public const string PRODUCTS_IMPORTED = 'Products imported successfully';

    private const string INVALID_CHOICE = '🚨 Oops! "%s" is not a valid choice. Please try again.';

    private const string STORAGE_PATH = __DIR__ . '/../../storage/products';

    protected function configure(): void
    {
        $this->setName('import:products')->setDescription('Imports products from a CSV file');
    }

    /**
     * @throws UnsupportedFileFormatException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Product Import Wizard');
        $io->text(
            "Please make sure the files are in the storage/products directory and are enclosed between quotes" .
            " or single quotes."
        );

        $files = glob(self::STORAGE_PATH . '/*.csv');
        if (empty($files)) {
            $io->error('<info>No files found in storage.</info>');

            return Command::FAILURE;
        }

        $fileChoices      = array_map('basename', $files);
        $separators       = [',' => 'Comma', ';' => 'Semi-colon', "\t" => 'Tab'];
        $separatorChoices = array_values($separators);

        $file      = $this->askQuestion($io, 'Please select a CSV file to import:', $fileChoices);
        $separator = $this->askQuestion($io, "Please select the separator for the file $file:", $separatorChoices);
        $separator = array_search($separator, $separators, true);
        $enclosure = $this->askQuestion($io, "Please select the enclosure for the file $file:", ['"', "'"]);
        $escape    = $this->askQuestion($io, "Please select the escape character for the file $file:", ['\\', '/']);

        $filePath         = realpath(self::STORAGE_PATH . "/$file");
        $fileReaderConfig = (new FileReaderConfigDTO($filePath))
            ->setSeparator($separator)
            ->setEnclosure($enclosure)
            ->setEscape($escape);

        $io->writeln("Importing products from path: $filePath");

        $fileReader = FileReaderFactory::create($fileReaderConfig);
        $result     = $fileReader->save(new ProductRepository());

        if ($result) {
            $io->success(self::PRODUCTS_IMPORTED);

            return Command::SUCCESS;
        }
        $io->error('Import failed.');

        return Command::FAILURE;
    }

    private function askQuestion(SymfonyStyle $io, string $questionText, array $choices): string
    {
        $question = new ChoiceQuestion($questionText, $choices, 0);
        $question->setErrorMessage(self::INVALID_CHOICE);

        return $io->askQuestion($question);
    }
}
