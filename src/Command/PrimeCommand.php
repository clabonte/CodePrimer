<?php

namespace CodePrimer\Command;

use CodePrimer\Adapter\RelationalDatabaseAdapter;
use CodePrimer\Helper\BusinessBundleHelper;
use CodePrimer\Model\BusinessBundle;
use CodePrimer\Template\Artifact;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Throwable;

/**
 * 'prime' command line script.
 */
class PrimeCommand extends CodePrimerCommand
{
    const OPTION_CONFIGURATION = 'configuration';
    const OPTION_DESTINATION = 'destination';
    const DEFAULT_CONFIGURATION_FILE = './codeprimer/codeprimer.yaml';

    protected static $defaultName = 'prime';

    /** @var ProjectConfiguration */
    protected $configuration;

    /** @var string */
    protected $destination;

    /** @var BusinessBundle */
    protected $businessBundle;

    protected function getBusinessBundle(): BusinessBundle
    {
        return $this->businessBundle;
    }

    protected function configure()
    {
        $help = <<<'EOF'
The <info>codeprimer %command.name%</info> command primes (i.e. generates) a list of artifacts based on a YAML configuration file.
  Unless a configuration file is specified, it will use the default one located at <info>./codeprimer/codeprimer.yaml</info> 
  If you have no configuration file yet, you may generate one by running the <info>codeprimer init</info> command 
EOF;
        $this
            ->setDescription('Primes artifacts for your project based on the configuration file specified.')
            ->addOption(
                self::OPTION_CONFIGURATION,
                ['c'],
                InputOption::VALUE_REQUIRED,
                'The configuration file to use',
                self::DEFAULT_CONFIGURATION_FILE
            )
            ->addOption(
                self::OPTION_DESTINATION,
                ['d'],
                InputOption::VALUE_REQUIRED,
                'The directory where artifacts will be generated',
                '.'
            )
            ->setHelp($help);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->prepareStyles($output);

        if (!$this->parseArguments($input, $output)) {
            return self::FAILURE;
        }

        if (!$this->validateConfiguration($output)) {
            return self::FAILURE;
        }

        $this->templateRenderer->setBaseFolder($this->destination);

        require $this->configuration->getPath();

        $configBundle = $this->configuration->getBusinessBundle();
        $this->businessBundle = prepareBundle($configBundle->getNamespace(), $configBundle->getName(), $configBundle->getDescription());

        $this->finalizeBundle();

        try {
            $this->primeArtifacts(Artifact::DOCUMENTATION, $output);
            $this->primeArtifacts(Artifact::CONFIGURATION, $output);
            $this->primeArtifacts(Artifact::CODE, $output);
            $this->primeArtifacts(Artifact::TESTS, $output);
        } catch (Throwable $t) {
            $output->writeln("<error>Failed to prime artifacts: {$t->getMessage()}</error>");

            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    private function primeArtifacts(string $category, OutputInterface $output)
    {
        $artifacts = $this->configuration->getArtifacts($category);
        foreach ($artifacts as $artifact) {
            $output->writeln("Priming artifact(s) - category: <info>{$artifact->getCategory()}</info>, format: <info>{$artifact->getFormat()}</info>, type: <info>{$artifact->getType()}</info>, variant: <info>{$artifact->getVariant()}</info>");
            $this->primeArtifact($artifact);
        }
    }

    protected function finalizeBundle()
    {
        // Establish the relationships between the various BusinessModel objects
        $bundleHelper = new BusinessBundleHelper();
        $bundleHelper->buildRelationships($this->businessBundle);

        if ($this->configuration->isRelationalDatabaseConfigured()) {
            // Prepare the relationships for a relational database
            $adapter = new RelationalDatabaseAdapter();
            $adapter->generateRelationalFields($this->businessBundle);
        }
    }

    private function parseArguments(InputInterface $input, OutputInterface $output): bool
    {
        // Make sure the configuration file exists and is readable
        $filename = $input->getOption(self::OPTION_CONFIGURATION);
        if (!file_exists($filename)) {
            $output->writeln("<error>Cannot find configuration file <file>$filename</file></error>");
            if (self::DEFAULT_CONFIGURATION_FILE == $filename) {
                $output->writeln('<question>Did you forget to run the <info>codeprimer init</info> command ?</question>');
            }

            return false;
        }

        if (!is_readable($filename)) {
            $output->writeln("<error>Configuration file <file>$filename</file> is not readable. Please update its permissions and try again.</error>");

            return false;
        }

        // Check if the output folder is present and writable
        $destination = $input->getOption(self::OPTION_DESTINATION);
        if (!is_dir($destination)) {
            if (file_exists($destination)) {
                $output->writeln("<error>Destination <file>$destination</file> must be a directory.</error>");

                return false;
            } else {
                $question = new ConfirmationQuestion("The destination directory <file>$destination</file> does not exist. Do you want to create it? [Yes/No] [Yes]", true);
                $helper = $this->getHelper('question');
                if ($helper->ask($input, $output, $question)) {
                    mkdir($destination, 0755, true);
                }
            }
        } elseif (!is_writable($destination)) {
            $output->writeln("<error>Destination directory <file>$destination</file> is not writable. Please update its permissions and try again.</error>");

            return false;
        }

        if (strpos($destination, '/', -1) === FALSE) {
            $destination .= '/';
        }

        $this->destination = $destination;
        $output->writeln("Loading configuration from file <file>$filename</file>");
        try {
            $this->configuration = new ProjectConfiguration();
            $this->configuration->load($filename);
        } catch (Throwable $t) {
            $output->writeln("<error>Failed to load configuration file: {$t->getMessage()}</error>");

            return false;
        }

        return true;
    }

    private function validateConfiguration(OutputInterface $output): bool
    {
        // Check if the bundle definition is valid
        $bundleDefinitionFile = $this->configuration->getPath();
        if (!file_exists($bundleDefinitionFile)) {
            $output->writeln("<error>Cannot find bundle definition file <file>$bundleDefinitionFile</file></error>");

            return false;
        }

        if (!is_readable($bundleDefinitionFile)) {
            $output->writeln("<error>Bundle definition file <file>$bundleDefinitionFile</file> is not readable. Please update its permissions and try again.</error>");

            return false;
        }

        // Make sure all artifacts requested exists
        $unknownTemplates = [];
        $unknownBuilders = [];
        foreach ($this->configuration->getAllArtifacts() as $artifact) {
            // Check if we have a template available for this artifact...
            $template = $this->templateRegistry->getTemplateForArtifact($artifact);
            if (null === $template) {
                $unknownTemplates[] = $artifact;
            } else {
                // Check if we have a builder available for this artifact...
                $builder = $this->builderFactory->createBuilder($artifact);
                if (null === $builder) {
                    $unknownBuilders[] = $artifact;
                }
            }
        }

        $result = true;
        if (!empty($unknownTemplates)) {
            foreach ($unknownTemplates as $artifact) {
                $output->writeln("<error>No template available for artifact - category: <info>{$artifact->getCategory()}</info>, format: <info>{$artifact->getFormat()}</info>, type: <info>{$artifact->getType()}</info>, variant: <info>{$artifact->getVariant()}</info></error>");
            }
            $result = false;
        }
        if (!empty($unknownBuilders)) {
            foreach ($unknownBuilders as $artifact) {
                $output->writeln("<error>No builder available for artifact - category: <info>{$artifact->getCategory()}</info>, format: <info>{$artifact->getFormat()}</info>, type: <info>{$artifact->getType()}</info>, variant: <info>{$artifact->getVariant()}</info></error>");
            }
            $result = false;
        }

        return $result;
    }
}
