<?php

namespace CodePrimer\Renderer;

use CodePrimer\Helper\ArtifactHelper;
use CodePrimer\Model\BusinessBundle;
use CodePrimer\Template\Artifact;
use CodePrimer\Template\Template;
use Twig\Environment;
use Twig\Loader\LoaderInterface;
use Twig\TemplateWrapper;

class TemplateRenderer
{
    /** @var LoaderInterface */
    private $loader;

    /** @var string */
    private $baseFolder;

    /** @var ArtifactHelper */
    private $helper;

    /** @var bool Whether we should overwrite files if they already exists */
    private $overwriteFiles = true;

    /**
     * TemplateHelper constructor.
     *
     * @param ArtifactHelper $helper
     */
    public function __construct(LoaderInterface $loader, string $baseFolder, ArtifactHelper $helper = null)
    {
        $this->loader = $loader;
        $this->baseFolder = $baseFolder;
        if (!isset($helper)) {
            $this->helper = new ArtifactHelper();
        } else {
            $this->helper = $helper;
        }
    }

    /**
     * @codeCoverageIgnore
     */
    public function getBaseFolder(): string
    {
        return $this->baseFolder;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setBaseFolder(string $baseFolder): TemplateRenderer
    {
        $this->baseFolder = $baseFolder;

        return $this;
    }

    /**
     * @codeCoverageIgnore
     */
    public function isOverwriteFiles(): bool
    {
        return $this->overwriteFiles;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setOverwriteFiles(bool $overwriteFiles): TemplateRenderer
    {
        $this->overwriteFiles = $overwriteFiles;

        return $this;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getHelper(): ArtifactHelper
    {
        return $this->helper;
    }

    /**
     * Loads a Twig template based on the following logic:
     * If an extended version of this template exists in the ext/ folder, this one will be returned by this method.
     * Otherwise, the original template requested will be returned.
     *
     * Example:
     * If 'php/Model.php.twig' is requested, this method will try to load the 'ext/php/Model.php.twig' template if it
     * exists. Otherwise, it will try to load 'php/Model.php.twig'
     *
     * @param string $filename the name of the template to load
     *
     * @return TemplateWrapper
     *
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function loadTemplate(Environment $twig, string $filename)
    {
        if ($twig->getLoader()->exists('ext'.DIRECTORY_SEPARATOR.$filename)) {
            $filename = 'ext/'.$filename;
        }

        return $twig->load($filename);
    }

    /**
     * Renders a template and returns its content as a string.
     *
     * @param Template $template The template to render
     * @param array    $context  The context to pass to the rendering
     *
     * @return string The rendered content
     *
     * @throws \Exception
     */
    public function renderTemplate(Template $template, $context = [])
    {
        $templateFile = $this->getTemplateFilename($template);

        /** @var Environment */
        $twig = new Environment($this->loader);

        $twig->setExtensions($template->getExtensions());

        $twigTemplate = $this->loadTemplate($twig, $templateFile);

        $content = $twigTemplate->render($context);

        return $content;
    }

    /**
     * @param array $context
     *
     * @return string The name of the file rendered
     *
     * @throws \Exception
     */
    public function renderToFile(string $filename, BusinessBundle $businessBundle, Template $template, $context = []): string
    {
        $dir = $this->helper->getDirectory($businessBundle, $template->getArtifact());
        $extension = $this->helper->getFilenameExtension($template->getArtifact());

        $file = $this->baseFolder.$dir.'/'.$filename.$extension;

        if ($this->overwriteFiles || !file_exists($file)) {
            // Make sure the folder requested exist. If not, create it
            $dir = dirname($file);
            if (!file_exists($dir)) {
                mkdir($dir, 0777, true);
            }

            $content = $this->renderTemplate($template, $context);
            file_put_contents($file, $content);
        } else {
            fprintf(STDERR, "Skipping rendering of existing file $file...".PHP_EOL);
        }

        return $file;
    }

    /**
     * @return string
     */
    public function getTemplateFilename(Template $template)
    {
        $artifact = $template->getArtifact();

        $path = '';
        if (!empty($artifact->getCategory())) {
            $path .= $artifact->getCategory().DIRECTORY_SEPARATOR;
        }

        if (Artifact::PROJECT != $artifact->getCategory()) {
            $format = $artifact->getFormat();
            if (!empty($format)) {
                $path .= $format.DIRECTORY_SEPARATOR;
            }
        }

        if (!empty($artifact->getType())) {
            $path .= $artifact->getType().DIRECTORY_SEPARATOR;
        }

        $path = strtolower($path);
        $extension = $this->helper->getFilenameExtension($artifact).'.twig';

        return $path.$template->getName().$extension;
    }
}
