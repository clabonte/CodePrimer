<?php

namespace CodePrimer\Helper;

use CodePrimer\Model\BusinessBundle;
use CodePrimer\Template\Artifact;

class ArtifactHelper
{
    /**
     * @return string
     */
    public function getDirectory(BusinessBundle $businessBundle, Artifact $artifact)
    {
        $dir = '.';

        switch ($artifact->getCategory()) {
            case Artifact::CODE:
                $dir = 'src';
                break;
            case Artifact::TESTS:
                $dir = 'tests';
                break;
            case Artifact::DOCUMENTATION:
                $dir = 'docs';
                break;
        }

        switch ($artifact->getType()) {
            case 'model':
                switch ($artifact->getFormat()) {
                    case 'php':
                        $dir .= '/Model';
                        break;
                    case 'java':
                        $dir .= '/'.$this->getJavaBasePath($businessBundle).'/model';
                        break;
                    case 'markdown':
                        $dir .= '/DataModel';
                        break;
                }
                break;
            case 'process':
                switch ($artifact->getFormat()) {
                    case 'markdown':
                        $dir .= '/Process';
                        break;
                }
                break;
            case 'entity':
                switch ($artifact->getFormat()) {
                    case 'php':
                        $dir .= '/Entity';
                        break;
                    case 'java':
                        $dir .= '/'.$this->getJavaBasePath($businessBundle).'/entity';
                        break;
                }
                break;
            case 'repository':
                switch ($artifact->getFormat()) {
                    case 'php':
                        $dir .= '/Repository';
                        break;
                    case 'java':
                        $dir .= '/'.$this->getJavaBasePath($businessBundle).'/repository';
                        break;
                }
                break;
            case 'migration':
                switch ($artifact->getFormat()) {
                    case 'php':
                        $dir .= '/Migrations';
                        break;
                    case 'mysql':
                        $dir = 'migrations';
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
        }

        return $extension;
    }

    private function getJavaBasePath(BusinessBundle $businessBundle)
    {
        return str_replace(['.', '\\', ' '], '/', strtolower($businessBundle->getNamespace()));
    }
}
