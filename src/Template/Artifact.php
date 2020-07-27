<?php

namespace CodePrimer\Template;

/**
 * Class Artifact
 * Defines the kind of artifact that is associated with a template to help a user select the right one to use for her
 * project.
 * It is composed of 4 levels:
 * - Level 1: Category: Defines the kind of artifact that will be generated for templates of this type. (e.g. code)
 * - Level 2: Type: Defines the type of artifact that will be generated for templates of this type. (e.g. Entity)
 * - Level 3: Format: Defines the format in which the artifact will be generated (e.g. programming language, e.g. php)
 * - Level 4: Variant: Defines the format's variant to apply when generating the template (e.g. Doctrine)
 * @package App\Template
 */
class Artifact
{
    /** @var string Artifacts of this category are expected to generate source code */
    public const CODE = 'code';

    /** @var string Artifacts of this category are expected to generate documentation */
    public const DOCUMENTATION = 'documentation';

    /** @var string Artifacts of this category are expected to generate tests */
    public const TESTS = 'tests';

    /** @var string Artifacts of this category are expected to generate project files */
    public const PROJECT = 'project';

    /** @var string Artifacts of this category are expected to generate configuration files */
    public const CONFIGURATION = 'configuration';

    /** @var string The artifact's category. One of the constants defined in this class */
    private $category;

    /** @var string The artifact's type */
    private $type;

    /** @var string The artifact's format to generate (e.g. programming language) */
    private $format;

    /** @var string The artifact's format variant to generate (e.g. framework) */
    private $variant;

    /**
     * TemplateType constructor.
     * @param string $category
     * @param string $type
     * @param string $format
     * @param string $variant
     */
    public function __construct(string $category, string $type, string $format, string $variant = '')
    {
        $this->category = $category;
        $this->type = $type;
        $this->format = $format;
        $this->variant = $variant;
    }

    /**
     * @return string
     */
    public function getCategory(): string
    {
        return $this->category;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getFormat(): string
    {
        return $this->format;
    }

    /**
     * @return string
     */
    public function getVariant(): string
    {
        return $this->variant;
    }
}
