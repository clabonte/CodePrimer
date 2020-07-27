<?php


namespace CodePrimer\Builder;


use CodePrimer\Helper\EntityHelper;
use CodePrimer\Helper\FieldHelper;
use CodePrimer\Helper\PackageHelper;
use CodePrimer\Model\Package;
use CodePrimer\Renderer\TemplateRenderer;
use CodePrimer\Template\Template;

class MigrationBuilder implements ArtifactBuilder
{

    /**
     * @param Package $package
     * @param Template $template
     * @param TemplateRenderer $renderer
     * @return string[]
     * @throws \Exception
     */
    public function build(Package $package, Template $template, TemplateRenderer $renderer): array
    {
        $artifact = $template->getArtifact();

        $files = [];
        switch (strtolower($artifact->getVariant())) {
            case 'doctrine':
                $files[] = $this->buildDoctrineMigration($package, $template, $renderer);
                break;
            default:
                $files[] = $this->buildDatabaseMigration($package, $template, $renderer);
        }

        return $files;
    }

    /**
     * @param Package $package
     * @param Template $template
     * @param TemplateRenderer $renderer
     * @return string
     * @throws \Exception
     */
    private function buildDatabaseMigration(Package $package, Template $template, TemplateRenderer $renderer): string
    {
        $context = [
            'package' => $package,
            'packageHelper' => new PackageHelper(),
            'entityHelper' => new EntityHelper(),
            'fieldHelper' => new FieldHelper()
        ];

        return $renderer->renderToFile($template->getName(), $package, $template, $context);
    }

    /**
     * @param Package $package
     * @param Template $template
     * @param TemplateRenderer $renderer
     * @return string
     * @throws \Exception
     */
    private function buildDoctrineMigration(Package $package, Template $template, TemplateRenderer $renderer): string
    {
        // Doctrine Migration Version:
        // It is strongly recommended that the Version{date} migration class name format is used and that the various
        // tools for generating migrations are used.
        // Should some custom migration numbers be necessary, keeping the version number the same length as the date
        // format (14 total characters) and padding it to the left with zeros should work.
        // Source: https://www.doctrine-project.org/projects/doctrine-migrations/en/2.1/reference/version-numbers.html#version-numbers
        $filename = 'Version00000000000001';

        $context = [
            'package' => $package,
            'subpackage' => 'Migrations',
            'model' => $filename,
            'packageHelper' => new PackageHelper(),
            'entityHelper' => new EntityHelper(),
            'fieldHelper' => new FieldHelper()
        ];

        return $renderer->renderToFile($filename, $package, $template, $context);
    }
}
