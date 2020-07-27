<?php

namespace CodePrimer\Template;

use CodePrimer\Twig\DoctrineOrmTwigExtension;
use CodePrimer\Twig\JavaTwigExtension;
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
        $phpExtensions = [new PhpTwigExtension()];
        $doctrineOrmExtensions = [new DoctrineOrmTwigExtension()];

        // PHP Entity
        $this->addTemplate(new Template('PlainEntity', new Artifact(Artifact::CODE, 'entity', 'php'), $phpExtensions));
        $this->addTemplate(new Template('DoctrineOrmEntity', new Artifact(Artifact::CODE, 'entity', 'php', 'doctrineOrm'), $doctrineOrmExtensions));

        // PHP Repository
        $this->addTemplate(new Template('DoctrineOrmRepository', new Artifact(Artifact::CODE, 'repository', 'php', 'doctrineOrm'), $doctrineOrmExtensions));

        // PHP Migration
        $this->addTemplate(new Template('DoctrineMigration', new Artifact(Artifact::CODE, 'migration', 'php', 'doctrine'), $doctrineOrmExtensions));

        // Prepare Java code templates
        $javaExtensions = [new JavaTwigExtension()];

        $this->addTemplate(new Template('PlainEntity', new Artifact(Artifact::CODE, 'entity', 'java'), $javaExtensions));

        // Prepare PHP project templates
        $this->addTemplate(new Template('setup', new Artifact(Artifact::PROJECT, 'symfony', 'sh', 'setup')));

        // Prepare MySQL scripts templates
        $mysqlExtensions = [new MySqlTwigExtension()];
        $this->addTemplate(new Template('CreateDatabase', new Artifact(Artifact::CODE, 'migration', 'mysql', 'createDatabase'), $mysqlExtensions));
        $this->addTemplate(new Template('RevertDatabase', new Artifact(Artifact::CODE, 'migration', 'mysql', 'revertDatabase'), $mysqlExtensions));
        $this->addTemplate(new Template('CreateUser', new Artifact(Artifact::CODE, 'migration', 'mysql', 'createUser'), $mysqlExtensions));
    }

    public function addTemplate(Template $template)
    {
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
}
