<?php

namespace CodePrimer\Command;

use CodePrimer\Model\BusinessBundle;
use CodePrimer\Template\Artifact;
use Symfony\Component\Yaml\Yaml;

/**
 * Class ProjectConfiguration.
 *
 * @codeCoverageIgnore
 */
class ProjectConfiguration
{
    private $configuration;

    public function load(string $filename): ProjectConfiguration
    {
        $this->configuration = Yaml::parseFile($filename);

        return $this;
    }

    public function save(string $filename): ProjectConfiguration
    {
        $yaml = Yaml::dump($this->configuration, 4);
        file_put_contents($filename, $yaml);

        return $this;
    }

    public function getPath(): string
    {
        return $this->configuration['bundle']['path'];
    }

    public function setPath(string $path): ProjectConfiguration
    {
        $this->configuration['bundle']['path'] = $path;

        return $this;
    }

    public function getBusinessBundle(): BusinessBundle {
        $bundle = $this->configuration['bundle'];
        return new BusinessBundle($bundle['namespace'], $bundle['name'], $bundle['description']);
    }

    public function setBusinessBundle(BusinessBundle $bundle): ProjectConfiguration
    {
        $this->configuration['bundle']['name'] = $bundle->getName();
        $this->configuration['bundle']['namespace'] = $bundle->getNamespace();
        $this->configuration['bundle']['description'] = $bundle->getDescription();

        return $this;
    }

    /**
     * @return Artifact[]
     */
    public function getAllArtifacts(): array
    {
        $artifacts = $this->getArtifacts(Artifact::CODE);
        $artifacts = array_merge($artifacts, $this->getArtifacts(Artifact::CONFIGURATION));
        $artifacts = array_merge($artifacts, $this->getArtifacts(Artifact::DOCUMENTATION));
        $artifacts = array_merge($artifacts, $this->getArtifacts(Artifact::PROJECT));
        $artifacts = array_merge($artifacts, $this->getArtifacts(Artifact::TESTS));

        return $artifacts;
    }

    /**
     * @param string $category
     * @return Artifact[]
     */
    public function getArtifacts(string $category): array
    {
        $results = [];

        if (isset($this->configuration['artifacts'][$category])) {
            foreach ($this->configuration['artifacts'][$category] as $format => $artifacts) {
                if (is_array($artifacts)) {
                    foreach ($artifacts as $index => $artifact) {
                        // Given the great flexibility offered by YAML to represent collections and how they are mapped
                        // by the YAML PHP component in memory, let's adapt our parsing accordingly
                        if (!is_array($artifact)) {
                            // Possible formats:
                            //  - <type> => <variant>
                            //  - index => <type>
                            if (!is_numeric($index)) {
                                //  - <type> => <variant>
                                $type = $index;
                                $variant = $artifact;
                                $results[] = new Artifact($category, $type, $format, $variant);
                            } else {
                                //  - index => <type>
                                $type = $artifact;
                                $results[] = new Artifact($category, $type, $format);
                            }
                        } else {
                            // Possible formats:
                            //  - <type> => [variants]
                            //  - index => [<type> => <variant>]
                            //  - index => [<type> => [variants]]
                            if (!is_numeric($index)) {
                                //  - <type> => [variants]
                                $type = $index;
                                foreach ($artifact as $variant) {
                                    $results[] = new Artifact($category, $type, $format, $variant);
                                }
                            } else {
                                //  - index => [<type> => <variant>]
                                //  - index => [<type> => [variants]]
                                foreach ($artifact as $type => $variant) {
                                    if (!is_array($variant)) {
                                        //  - index => [<type> => <variant>]
                                        $results[] = new Artifact($category, $type, $format, $variant);
                                    } else {
                                        //  - index => [<type> => [variants]]
                                        foreach ($variant as $name) {
                                            $results[] = new Artifact($category, $type, $format, $name);
                                        }
                                    }
                                }
                            }
                        }
                    }
                } else {
                    $type = $artifacts;
                    $results[] = new Artifact($category, $type, $format);
                }
            }
        }
        return $results;
    }

    public function addArtifact(Artifact $artifact): ProjectConfiguration
    {
        $category = $artifact->getCategory();
        if (!isset($this->configuration['artifacts'][$category])) {
            $this->configuration['artifacts'][$category] = [];
        }

        $format = $artifact->getFormat();
        if (!isset($this->configuration['artifacts'][$category][$format])) {
            $this->configuration['artifacts'][$category][$format] = [];
        }

        $type = $artifact->getType();
        $variant = $artifact->getVariant();
        if (!empty($variant)) {
            $this->configuration['artifacts'][$category][$format][] = [$type => $variant];
        } else {
            $this->configuration['artifacts'][$category][$format][] = $type;
        }

        return $this;
    }

    public function isRelationalDatabaseConfigured(): bool
    {
        $result = false;

        $artifacts = array_change_key_case($this->configuration['artifacts'][Artifact::CODE]);
        if (isset($artifacts['mysql'])) {
            $result = true;
        }

        return $result;
    }
}
