<?php

namespace CodePrimer\Command;

/**
 * Class Configuration.
 *
 * @codeCoverageIgnore
 */
class Configuration
{
    private $initProject = false;
    private $projectPath = './sample/output/';

    private $bundleFile = './sample/bundle.php';
    private $primeMySql = true;
    private $primePhp = true;
    private $primeMarkdown = true;

    public function __construct(array $argv)
    {
        if (in_array('--init', $argv)) {
            $this->initProject = true;
        }
    }

    /**
     * @codeCoverageIgnore
     */
    public function isInitProject(): bool
    {
        return $this->initProject;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setInitProject(bool $initProject): Configuration
    {
        $this->initProject = $initProject;

        return $this;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getProjectPath(): string
    {
        return $this->projectPath;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setProjectPath(string $projectPath): Configuration
    {
        $this->projectPath = $projectPath;

        return $this;
    }

    public function getCodePrimerConfigurationPath(): string
    {
        return $this->projectPath.'codeprimer/';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getBundleFile(): string
    {
        return $this->bundleFile;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setBundleFile(string $bundleFile): Configuration
    {
        $this->bundleFile = $bundleFile;

        return $this;
    }

    /**
     * @codeCoverageIgnore
     */
    public function isPrimeMySql(): bool
    {
        return $this->primeMySql;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setPrimeMySql(bool $primeMySql): Configuration
    {
        $this->primeMySql = $primeMySql;

        return $this;
    }

    /**
     * @codeCoverageIgnore
     */
    public function isPrimePhp(): bool
    {
        return $this->primePhp;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setPrimePhp(bool $primePhp): Configuration
    {
        $this->primePhp = $primePhp;

        return $this;
    }

    /**
     * @codeCoverageIgnore
     */
    public function isPrimeMarkdown(): bool
    {
        return $this->primeMarkdown;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setPrimeMarkdown(bool $primeMarkdown): Configuration
    {
        $this->primeMarkdown = $primeMarkdown;

        return $this;
    }
}
