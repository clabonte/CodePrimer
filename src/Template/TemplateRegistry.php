<?php

namespace CodePrimer\Template;

use CodePrimer\Twig\DoctrineOrmTwigExtension;
use CodePrimer\Twig\JavaTwigExtension;
use CodePrimer\Twig\LanguageTwigExtension;
use CodePrimer\Twig\MySqlTwigExtension;
use CodePrimer\Twig\PhpTwigExtension;

class TemplateRegistry
{
    /** @var 4-dimension array containing the list of templates available */
    private $templates;

    public function __construct()
    {
        $this->templates = [];

        // Prepare PHP code templates
        $this->initPhpTemplates();

        // Prepare Java code templates
        $this->initJavaTemplates();

        // Prepare MySQL scripts templates
        $this->initMySqlTemplates();

        // Prepare documentation templates
        $this->initDocumentationTemplates();
    }

    public function addTemplate(Template $template, string $description, string $status = 'stable')
    {
        // Update the template's description and status
        $template
            ->setDescription($description)
            ->setStatus($status);

        $artifact = $template->getArtifact();

        $category = strtolower($artifact->getCategory());
        $type = strtolower($artifact->getType());
        $format = strtolower($artifact->getFormat());
        $variant = strtolower($artifact->getVariant());

        $this->templates[$category][$type][$format][$variant] = $template;
    }

    /**
     * @return Template
     *
     * @throws \Exception
     */
    public function getTemplateForArtifact(Artifact $artifact)
    {
        return $this->getTemplate($artifact->getCategory(), $artifact->getType(), $artifact->getFormat(), $artifact->getVariant());
    }

    /**
     * @throws \Exception
     */
    public function getTemplate(string $category, string $type, string $format, string $variant = ''): Template
    {
        $category = strtolower($category);
        $type = strtolower($type);
        $format = strtolower($format);
        $variant = strtolower($variant);

        if (!empty($this->templates[$category][$type][$format][$variant])) {
            return $this->templates[$category][$type][$format][$variant];
        }

        throw new \Exception("No template available for category $category, type $type, format $format, variant $variant");
    }

    /**
     * @return Template[]
     */
    public function listTemplates(string $category, string $type = null, string $format = null): array
    {
        $results = [];

        $category = strtolower($category);

        if (isset($type)) {
            $type = strtolower($type);

            if (isset($format)) {
                $format = strtolower($format);

                if (isset($this->templates[$category][$type][$format])) {
                    foreach ($this->templates[$category][$type][$format] as $template) {
                        $results[] = $template;
                    }
                }
            } else {
                if (isset($this->templates[$category][$type])) {
                    foreach ($this->templates[$category][$type] as $format => $list) {
                        foreach ($list as $template) {
                            $results[] = $template;
                        }
                    }
                }
            }
        } else {
            if (isset($this->templates[$category])) {
                foreach ($this->templates[$category] as $type => $typeList) {
                    foreach ($typeList as $format => $list) {
                        foreach ($list as $template) {
                            $results[] = $template;
                        }
                    }
                }
            }
        }

        return $results;
    }

    protected function initPhpTemplates()
    {
        $phpExtensions = [new PhpTwigExtension()];
        $doctrineOrmExtensions = [new DoctrineOrmTwigExtension()];

        // PHP BusinessModel
        $this->addTemplate(new Template('BusinessModel', new Artifact(Artifact::CODE, 'model', 'php'), $phpExtensions),
            'Generate plain PHP BusinessModel classes');

        // PHP Event
        $this->addTemplate(new Template('Event', new Artifact(Artifact::CODE, 'event', 'php'), $phpExtensions),
            'Generate plain PHP Event classes');

        // PHP Entity
        $this->addTemplate(new Template('PlainEntity', new Artifact(Artifact::CODE, 'entity', 'php'), $phpExtensions),
            'Generate plain PHP Entity classes', 'alpha');
        $this->addTemplate(new Template('DoctrineOrmEntity', new Artifact(Artifact::CODE, 'entity', 'php', 'doctrineOrm'), $doctrineOrmExtensions),
            'Generate Entity PHP classes using Doctrine ORM annotations', 'alpha');

        // PHP Repository
        $this->addTemplate(new Template('DoctrineOrmRepository', new Artifact(Artifact::CODE, 'repository', 'php', 'doctrineOrm'), $doctrineOrmExtensions),
            'Generate PHP Repository implementation using Doctrine ORM', 'alpha');

        // PHP Migration
        $this->addTemplate(new Template('DoctrineMigration', new Artifact(Artifact::CODE, 'migration', 'php', 'doctrine'), $doctrineOrmExtensions),
            'Generate Doctrine migration to setup and rollback initial database in PHP', 'alpha');

        // Prepare PHP project templates
        $this->addTemplate(new Template('setup', new Artifact(Artifact::PROJECT, 'symfony', 'sh', 'setup')),
            'Generate setup.sh script to quick create a PHP project using the Symfony Flex framework with the right libraries', 'beta');
    }

    protected function initJavaTemplates()
    {
        $javaExtensions = [new JavaTwigExtension()];

        $this->addTemplate(new Template('PlainEntity', new Artifact(Artifact::CODE, 'entity', 'java'), $javaExtensions),
            'Generate plain Java Entity classes', 'alpha');
    }

    protected function initMySqlTemplates()
    {
        $mysqlExtensions = [new MySqlTwigExtension()];
        $this->addTemplate(new Template('CreateDatabase', new Artifact(Artifact::CODE, 'migration', 'mysql', 'createDatabase'), $mysqlExtensions),
            'Generate MySQL script to create a database based on the configured data model', 'beta');
        $this->addTemplate(new Template('RevertDatabase', new Artifact(Artifact::CODE, 'migration', 'mysql', 'revertDatabase'), $mysqlExtensions),
            "Generate MySQL script to revert the creation of the database created by the 'CreateDatabase' template", 'beta');
        $this->addTemplate(new Template('CreateUser', new Artifact(Artifact::CODE, 'migration', 'mysql', 'createUser'), $mysqlExtensions),
            'Simple MySQL script to create a database user to use for the application', 'beta');
    }

    protected function initDocumentationTemplates()
    {
        $extensions = [new LanguageTwigExtension()];
        $this->addTemplate(new Template('Overview', new Artifact(Artifact::DOCUMENTATION, 'model', 'markdown'), $extensions),
            'Generates Markdown documentation of your data model to include in your repository');
        $this->addTemplate(new Template('Overview', new Artifact(Artifact::DOCUMENTATION, 'process', 'markdown', 'index'), $extensions),
            'Generates Markdown index file of your business processes to include in your repository');
        $this->addTemplate(new Template('BusinessProcess', new Artifact(Artifact::DOCUMENTATION, 'process', 'markdown', 'details'), $extensions),
            'Generates Markdown detailed documentation of your business processes to include in your repository');
    }
}
