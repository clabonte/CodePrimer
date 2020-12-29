<?php


namespace CodePrimer\Builder;


use CodePrimer\Helper\DataBundleHelper;
use CodePrimer\Helper\EventHelper;
use CodePrimer\Helper\FieldHelper;
use CodePrimer\Model\BusinessBundle;
use CodePrimer\Model\BusinessProcess;
use CodePrimer\Model\Data\ReturnedDataBundle;
use CodePrimer\Renderer\TemplateRenderer;
use CodePrimer\Template\Template;
use CodePrimer\Twig\LanguageTwigExtension;
use Exception;

class DataBundleBuilder implements ArtifactBuilder
{

    /**
     * @inheritDoc
     */
    public function build(BusinessBundle $businessBundle, Template $template, TemplateRenderer $renderer): array
    {
        $files = [];

        foreach ($businessBundle->getBusinessProcesses() as $businessProcess) {
            if ($businessProcess->isDataReturned()) {
                $this->buildReturnedDataBundle($businessBundle, $businessProcess, $businessProcess->getReturnedData(), $template, $renderer);
            }
        }
        return $files;
    }

    private function buildReturnedDataBundle(BusinessBundle $businessBundle, BusinessProcess $businessProcess, ReturnedDataBundle $dataBundle, Template $template, TemplateRenderer $renderer)
    {
        $context = [
            'bundle' => $businessBundle,
            'subpackage' => 'Data',
            'model' => $dataBundle,
            'dataBundle' => $dataBundle,
            'process' => $businessProcess,
            'dataBundleHelper' => new DataBundleHelper($businessBundle),
            'fieldHelper' => new FieldHelper(),
        ];
        $languageExtension = new LanguageTwigExtension();
        $name = $dataBundle->getName();
        if (empty($name)) {
            $name = $businessProcess->getName().'Output';
        }
        $filename = $languageExtension->classFilter($name);

        return $renderer->renderToFile($filename, $businessBundle, $template, $context);
    }
}