<?php


namespace CodePrimer\Command;


/**
 * Class Configuration
 * @package CodePrimer\Command
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
     * @return bool
     */
    public function isInitProject(): bool
    {
        return $this->initProject;
    }

    /**
     * @codeCoverageIgnore
     * @param bool $initProject
     * @return Configuration
     */
    public function setInitProject(bool $initProject): Configuration
    {
        $this->initProject = $initProject;
        return $this;
    }

    /**
     * @codeCoverageIgnore
     * @return string
     */
    public function getProjectPath(): string
    {
        return $this->projectPath;
    }

    /**
     * @codeCoverageIgnore
     * @param string $projectPath
     * @return Configuration
     */
    public function setProjectPath(string $projectPath): Configuration
    {
        $this->projectPath = $projectPath;
        return $this;
    }

    /**
     * @codeCoverageIgnore
     * @return string
     */
    public function getBundleFile(): string
    {
        return $this->bundleFile;
    }

    /**
     * @codeCoverageIgnore
     * @param string $bundleFile
     * @return Configuration
     */
    public function setBundleFile(string $bundleFile): Configuration
    {
        $this->bundleFile = $bundleFile;
        return $this;
    }

    /**
     * @codeCoverageIgnore
     * @return bool
     */
    public function isPrimeMySql(): bool
    {
        return $this->primeMySql;
    }

    /**
     * @codeCoverageIgnore
     * @param bool $primeMySql
     * @return Configuration
     */
    public function setPrimeMySql(bool $primeMySql): Configuration
    {
        $this->primeMySql = $primeMySql;
        return $this;
    }

    /**
     * @codeCoverageIgnore
     * @return bool
     */
    public function isPrimePhp(): bool
    {
        return $this->primePhp;
    }

    /**
     * @codeCoverageIgnore
     * @param bool $primePhp
     * @return Configuration
     */
    public function setPrimePhp(bool $primePhp): Configuration
    {
        $this->primePhp = $primePhp;
        return $this;
    }

    /**
     * @codeCoverageIgnore
     * @return bool
     */
    public function isPrimeMarkdown(): bool
    {
        return $this->primeMarkdown;
    }

    /**
     * @codeCoverageIgnore
     * @param bool $primeMarkdown
     * @return Configuration
     */
    public function setPrimeMarkdown(bool $primeMarkdown): Configuration
    {
        $this->primeMarkdown = $primeMarkdown;
        return $this;
    }


}