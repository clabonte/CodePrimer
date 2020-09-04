<?php

namespace CodePrimer\Command;

use CodePrimer\Adapter\RelationalDatabaseAdapter;
use CodePrimer\Builder\ArtifactBuilderFactory;
use CodePrimer\Helper\BusinessBundleHelper;
use CodePrimer\Model\BusinessBundle;
use CodePrimer\Renderer\TemplateRenderer;
use CodePrimer\Template\Artifact;
use CodePrimer\Template\TemplateRegistry;
use Exception;
use Throwable;
use Twig\Loader\FilesystemLoader;

/**
 * 'prime' command line script.
 *
 * @codeCoverageIgnore
 */
class Prime
{
    // PATH CONSTANTS
    const BASE_PATH = __DIR__.'/../../';

    /** @var Configuration */
    protected $configuration;

    /** @var TemplateRegistry */
    private $templateRegistry;

    /** @var ArtifactBuilderFactory */
    private $builderFactory;

    /** @var TemplateRenderer */
    private $templateRenderer;

    /** @var BusinessBundle */
    private $businessBundle;

    /**
     * @throws Exception
     */
    public static function main(bool $exit = true): int
    {
        $argv = $_SERVER['argv'];

        try {
            if (in_array('-h', $argv) || in_array('--help', $argv)) {
                self::printHelp();
                if ($exit) {
                    exit(0);
                }

                return 0;
            }

            return (new static())->run($argv, $exit);
        } catch (Throwable $t) {
            throw new Exception($t->getMessage(), (int) $t->getCode(), $t);
        }
    }

    public static function printHelp()
    {
        echo 'prime [--init]';
    }

    /**
     * @throws Exception
     */
    public function run(array $argv, bool $exit = true): int
    {
        $this->configuration = new Configuration($argv);

        $this->initCodePrimer();

        require $this->configuration->getBundleFile();
        $this->businessBundle = prepareBundle();

        if ($this->configuration->isInitProject()) {
            $this->primeNewProject();
        } else {
            $this->primeArtifacts();
        }

        if ($exit) {
            exit(0);
        }

        return 0;
    }

    /**
     * Initializes the CodePrimer components used to generate artifacts.
     */
    private function initCodePrimer()
    {
        $this->templateRegistry = new TemplateRegistry();
        $this->builderFactory = new ArtifactBuilderFactory();
        $loader = new FilesystemLoader('templates', self::BASE_PATH);
        $this->templateRenderer = new TemplateRenderer($loader, $this->configuration->getProjectPath());
    }

    private function primeNewProject()
    {
        if ($this->configuration->isPrimePhp()) {
            $this->primeNewPhpProject();
        }
    }

    private function primeNewPhpProject()
    {
        $this->templateRenderer->setBaseFolder($this->configuration->getProjectPath());

        // 1. Prepare the composer.json file to use if it does not already exists
        $artifact = new Artifact(Artifact::CONFIGURATION, 'dependency manager', 'php', 'composer');
        $this->primeArtifact($artifact, false);

        // 2. Prepare the .php_cs.dist file to use if it does not already exists
        $artifact = new Artifact(Artifact::CONFIGURATION, 'coding standards', 'php', 'PHP CS Fixer');
        $this->primeArtifact($artifact, false);

        // 3. Prepare the phpunit.xml.dist file to use if it does not already exists
        $artifact = new Artifact(Artifact::CONFIGURATION, 'tests', 'php', 'phpunit');
        $this->primeArtifact($artifact, false);

        // 4. Prepare the .gitgnore file to use if it does not already exists
        $artifact = new Artifact(Artifact::CONFIGURATION, 'git', 'php', 'gitignore');
        $this->primeArtifact($artifact, false);

        // 5. Prepare the CodePrimer configuration files
        $this->templateRenderer->setBaseFolder($this->configuration->getCodePrimerConfigurationPath());
        $artifact = new Artifact(Artifact::CONFIGURATION, 'codeprimer', 'php', 'bundle');
        $this->primeArtifact($artifact, false);

        $artifact = new Artifact(Artifact::CONFIGURATION, 'codeprimer', 'php', 'BusinessModelFactory');
        $this->primeArtifact($artifact, false);

        $artifact = new Artifact(Artifact::CONFIGURATION, 'codeprimer', 'php', 'BusinessProcessFactory');
        $this->primeArtifact($artifact, false);

        $artifact = new Artifact(Artifact::CONFIGURATION, 'codeprimer', 'php', 'DatasetFactory');
        $this->primeArtifact($artifact, false);

    }

    private function primeArtifacts()
    {
        $this->finalizeBundle();

        if ($this->configuration->isPrimePhp()) {
            $this->primePhpArtifacts();
        }

        if ($this->configuration->isPrimeMySql()) {
            $this->primeMySqlArtifacts();
        }

        if ($this->configuration->isPrimeMarkdown()) {
            $this->primeMarkdownArtifacts();
        }
    }

    protected function finalizeBundle()
    {
        // Establish the relationships between the various BusinessModel objects
        $bundleHelper = new BusinessBundleHelper();
        $bundleHelper->buildRelationships($this->businessBundle);

        if ($this->configuration->isPrimeMySql()) {
            // Prepare the relationships for a relational database
            $adapter = new RelationalDatabaseAdapter();
            $adapter->generateRelationalFields($this->businessBundle);
        }
    }

    protected function primePhpArtifacts()
    {
        // 1. Prime 'Dataset' source code
        $artifact = new Artifact(Artifact::CODE, 'dataset', 'php');
        $this->primeArtifact($artifact);

        // 2. Prime 'Business Model' source code
        $artifact = new Artifact(Artifact::CODE, 'model', 'php');
        $this->primeArtifact($artifact);

        // 3. Prime 'Event' source code
        $artifact = new Artifact(Artifact::CODE, 'event', 'php');
        $this->primeArtifact($artifact);
    }

    protected function primeMySqlArtifacts()
    {
        // 1. Prime the MySQL 'Create DB' script to create the initial database
        $artifact = new Artifact(Artifact::CODE, 'Migration', 'mysql', 'CreateDatabase');
        $this->primeArtifact($artifact);

        // 2. Prime the MySQL 'Revert DB' scripts to revert the initial database setup
        $artifact = new Artifact(Artifact::CODE, 'Migration', 'mysql', 'RevertDatabase');
        $this->primeArtifact($artifact);
    }

    protected function primeMarkdownArtifacts()
    {
        // 1. Prime 'Dataset' documentation in Markdown
        $artifact = new Artifact(Artifact::DOCUMENTATION, 'dataset', 'markdown');
        $this->primeArtifact($artifact);

        // 2. Prime 'Data Model' documentation in Markdown
        $artifact = new Artifact(Artifact::DOCUMENTATION, 'model', 'markdown');
        $this->primeArtifact($artifact);

        // 3. Prime 'Processing Model' overview documentation in Markdown
        $artifact = new Artifact(Artifact::DOCUMENTATION, 'process', 'markdown', 'index');
        $this->primeArtifact($artifact);

        // 4. Prime 'Processing Model' detailed documentation in Markdown
        $artifact = new Artifact(Artifact::DOCUMENTATION, 'process', 'markdown', 'details');
        $this->primeArtifact($artifact);
    }

    /**
     * @param Artifact $artifact Artifact to generate
     * @param bool $overwrite Whether we should overwrite the file if it exists
     *
     * @throws Exception
     */
    protected function primeArtifact(Artifact $artifact, bool $overwrite = true)
    {
        // Extract the template to use for this artifact
        $template = $this->templateRegistry->getTemplateForArtifact($artifact);

        // Extract the builder to use for this artifact
        $builder = $this->builderFactory->createBuilder($artifact);

        // Build the artifacts
        $this->templateRenderer->setOverwriteFiles($overwrite);
        $builder->build($this->businessBundle, $template, $this->templateRenderer);
    }
}
