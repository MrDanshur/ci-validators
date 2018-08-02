<?php

namespace InternalSite\CoreBundle\Command;

use IS\CIValidatorsBundle\Component\SchemaValidateProcessor;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Parser;

/**
 * Class SchemaPolicyValidateCommand
 */
class SchemaPolicyValidateCommand extends ContainerAwareCommand
{
    /** @var SchemaValidateProcessor */
    private $processor;

    public const CONFIG_FILE_NAME = 'entity_rules.yml';

    /**
     * SchemaPolicyValidateCommand constructor.
     *
     * @param SchemaValidateProcessor $processor
     */
    public function __construct(
        SchemaValidateProcessor $processor
    ) {
        $this->processor = $processor;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('internal:validate:schema')
            ->addOption('em', null, InputOption::VALUE_REQUIRED, 'Entity Manager id');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$input->hasOption('em')) {
            $output->writeln('Option \'em\' is needed');

            return 1;
        }

        $path = $this->getContainer()->getParameter('kernel.project_dir') . DIRECTORY_SEPARATOR . $this::CONFIG_FILE_NAME;

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
