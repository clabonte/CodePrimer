<?php

namespace CodePrimer\Helper;

use CodePrimer\Model\BusinessBundle;
use CodePrimer\Template\Artifact;
use Doctrine\Inflector\InflectorFactory;

class ArtifactHelper
{
    /**
     * @return string
     */
    public function getDirectory(BusinessBundle $businessBundle, Artifact $artifact)
    {
        $dir = '.';
        $inflector = InflectorFactory::create()->build();

        switch ($artifact->getCategory()) {
            case Artifact::CODE:
                $dir = 'gen-src';
                break;
            case Artifact::TESTS:
                $dir = 'tests';
                break;
            case Artifact::DOCUMENTATION:
                $dir = 'docs';
                break;
        }

        switch ($artifact->getFormat()) {
            case 'php':
                if (Artifact::CODE == $artifact->getCategory()) {
                    $dir .= '/'.$inflector->classify($artifact->getType());
                    if (!empty($artifact->getVariant())) {
                        //$dir .= '/'.$inflector->classify($artifact->getVariant());
                    }
                }
                break;
            case 'java':
                if (Artifact::CODE == $artifact->getCategory()) {
                    $dir .= '/'.$this->getJavaBasePath($businessBundle).'/'.strtolower($artifact->getType());
                }
                break;
            case 'mysql':
                $dir = $inflector->pluralize(strtolower($artifact->getType()));
                break;
            case 'markdown':
                switch ($artifact->getType()) {
                    case 'model':
                        $dir .= '/DataModel';
                        break;
                    default:
                        $dir .= '/'.$inflector->classify($artifact->getType());
                        break;
                }
        }

        return $dir;
    }

    public function getFilenameExtension(Artifact $artifact)
    {
        $extension = '.txt';

        switch ($artifact->getFormat()) {
            case 'php':
                $extension = '.php';
                break;
            case 'java':
                $extension = '.java';
                break;
            case 'sh':
                $extension = '.sh';
                break;
            case 'mysql':
                $extension = '.sql';
                break;
            case 'markdown':
                $extension = '.md';
                break;
            case 'json':
                $extension = '.json';
                break;
        }

        return $extension;
    }

    private function getJavaBasePath(BusinessBundle $businessBundle)
    {
        return str_replace(['.', '\\', ' '], '/', strtolower($businessBundle->getNamespace()));
    }
}
