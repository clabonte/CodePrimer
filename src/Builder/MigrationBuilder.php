<?php

namespace CodePrimer\Builder;

use CodePrimer\Helper\BusinessBundleHelper;
use CodePrimer\Helper\BusinessModelHelper;
use CodePrimer\Helper\FieldHelper;
use CodePrimer\Model\BusinessBundle;
use CodePrimer\Renderer\TemplateRenderer;
use CodePrimer\Template\Template;

class MigrationBuilder implements ArtifactBuilder
{
    /**
     * @return string[]
     *
     * @throws \Exception
     */
    public function build(BusinessBundle $businessBundle, Template $template, TemplateRenderer $renderer): array
    {
        $artifact = $template->getArtifact();

        $files = [];
        switch (strtolower($artifact->getVariant())) {
            case 'doctrine':
                $files[] = $this->buildDoctrineMigration($businessBundle, $template, $renderer);
                break;
            default:
                $files[] = $this->buildDatabaseMigration($businessBundle, $template, $renderer);
        }

        return $files;
    }

    /**
     * @throws \Exception
     */
    private function buildDatabaseMigration(BusinessBundle $businessBundle, Template $template, TemplateRenderer $renderer): string
    {
        $context = [
            'package' => $businessBundle,
            'bundle' => $businessBundle,
            'packageHelper' => new BusinessBundleHelper(),
            'businessModelHelper' => new BusinessModelHelper(),
            'fieldHelper' => new FieldHelper(),
        ];

        return $renderer->renderToFile($template->getName(), $businessBundle, $template, $context);
    }

    /**
     * @throws \Exception
     */
    private function buildDoctrineMigration(BusinessBundle $businessBundle, Template $template, TemplateRenderer $renderer): string
    {
        // Doctrine Migration Version:
        // It is strongly recommended that the Version{date} migration class name format is used and that the various
        // tools for generating migrations are used.
        // Should some custom migration numbers be necessary, keeping the version number the same length as the date
        // format (14 total characters) and padding it to the left with zeros should work.
        // Source: https://www.doctrine-project.org/projects/doctrine-migrations/en/2.1/reference/version-numbers.html#version-numbers
        $filename = 'Version00000000000001';

        $context = [
            'package' => $businessBundle,
            'subpackage' => 'Migration',
            'model' => $filename,
            'packageHelper' => new BusinessBundleHelper(),
            'businessModelHelper' => new BusinessModelHelper(),
            'fieldHelper' => new FieldHelper(),
        ];

        return $renderer->renderToFile($filename, $businessBundle, $template, $context);
    }
}
