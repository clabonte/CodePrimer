<?php


namespace CodePrimer\Helper;

use CodePrimer\Model\Package;
use CodePrimer\Template\Artifact;

class ArtifactHelper
{
    /**
     * @param Artifact $artifact
     * @return string
     */
    public function getDirectory(Package $package, Artifact $artifact)
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
            case 'entity':
                switch ($artifact->getFormat()) {
                    case 'php':
                        $dir .= '/Entity';
                        break;
                    case 'java':
                        $dir .= '/'.$this->getJavaBasePath($package) .'/entity';
                        break;
                }
                break;
            case 'repository':
                switch ($artifact->getFormat()) {
                    case 'php':
                        $dir .= '/Repository';
                        break;
                    case 'java':
                        $dir .= '/'.$this->getJavaBasePath($package) .'/repository';
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
        }

        return $extension;
    }

    private function getJavaBasePath(Package $package)
    {
        return str_replace(['.', '\\', ' '], '/', strtolower($package->getNamespace()));
    }
}
