<?php


namespace CodePrimer\Tests\Twig;

use CodePrimer\Model\Package;
use CodePrimer\Tests\Helper\TestHelper;
use PHPUnit\Framework\TestCase;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Twig\TwigTest;

abstract class TwigExtensionTest extends TestCase
{
    /** @var array */
    protected $context;

    public function setUp(): void
    {
        parent::setUp();
        $this->context = ['package' => TestHelper::getSamplePackage()];
    }

    /**
     * @param string $name
     * @param TwigFilter[] $filters
     *
     */
    protected function assertTwigFilter($name, array $filters)
    {
        $found = false;
        foreach ($filters as $filter) {
            if ($filter->getName() == $name) {
                $found = true;
                break;
            }
        }

        self::assertTrue($found, "Filter $name not found");
    }

    /**
     * @param string $name
     * @param TwigTest[] $tests
     *
     */
    protected function assertTwigTest($name, array $tests)
    {
        $found = false;
        foreach ($tests as $test) {
            if ($test->getName() == $name) {
                $found = true;
                break;
            }
        }

        self::assertTrue($found, "Test $name not found");
    }

    /**
     * @param string $name
     * @param TwigFunction[] $functions
     *
     */
    protected function assertTwigFunction($name, array $functions)
    {
        $found = false;
        foreach ($functions as $function) {
            if ($function->getName() == $name) {
                $found = true;
                break;
            }
        }

        self::assertTrue($found, "Function $name not found");
    }
}
