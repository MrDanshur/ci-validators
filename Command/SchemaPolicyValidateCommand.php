<?php

namespace IS\CIValidatorsBundle\Command;

use IS\CIValidatorsBundle\Component\SchemaValidateProcessor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Parser;

/**
 * Class SchemaPolicyValidateCommand
 */
class SchemaPolicyValidateCommand extends Command
{
    /** @var SchemaValidateProcessor */
    private $processor;

    /** @var string */
    private $rootDir;

    public const CONFIG_FILE_NAME = 'entity_rules.yml';

    /**
     * SchemaPolicyValidateCommand constructor.
     *
     * @param SchemaValidateProcessor $processor
     * @param string                  $rootDir
     */
    public function __construct(
        SchemaValidateProcessor $processor,
        string $rootDir
    ) {
        $this->processor = $processor;
        $this->rootDir   = $rootDir;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('internal:civalidate:schema')
            ->addOption('em', null, InputOption::VALUE_REQUIRED, 'Entity Manager id');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$input->hasOption('em')) {
            $output->writeln('Option \'em\' is needed');

            return 1;
        }

        $path = $this->rootDir . DIRECTORY_SEPARATOR . $this::CONFIG_FILE_NAME;

        if (!\file_exists($path)) {
            $output->writeln('Configuration file doesn\'t exist');

            return 1;
        }

        $yml       = new Parser();
        $configArr = $yml->parse(\file_get_contents($path));

        $allViolations = $this->processor->process($input->getOption('em'), $configArr);

        if ($allViolations) {
            foreach ($allViolations as $table => $violations) {
                $output->writeln('<error>' . \mb_strtoupper($table) . ':</error>');
                foreach ($violations as $violation) {
                    $output->writeln('- ' . $violation);
                }
            }

            return 1;
        }

        $output->writeln('Validation Successful.');

        return 0;
    }
}
