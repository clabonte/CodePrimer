<?php

namespace CodePrimer\Tests\Command;

use CodePrimer\Command\PrimeCommand;
use CodePrimer\Tests\Functional\TemplateTestCase;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class PrimeCommandTest extends TemplateTestCase
{
    /** @var CommandTester */
    private $commandTester;

    public function setUp(): void
    {
        parent::setUp();
        $application = new Application();
        $application->add(new PrimeCommand());
        $command = $application->find('prime');
        $this->commandTester = new CommandTester($command);

        chmod('fixtures/configuration/notreadable.yaml', 0222);
        chmod('fixtures/configuration/codeprimer/notreadable_bundle.php', 0222);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        chmod('fixtures/configuration/notreadable.yaml', 0666);
        chmod('fixtures/configuration/codeprimer/notreadable_bundle.php', 0666);
    }

    /**
     * @dataProvider invalidArgumentsProvider
     * @param array $arguments
     * @param string $expectedError
     */
    public function testExecuteShouldFailOnInvalidArgument(array $arguments, array $inputs, string $expectedError)
    {
        $this->commandTester->setInputs($inputs);

        self::assertEquals(Command::FAILURE, $this->commandTester->execute($arguments));

        $output = $this->commandTester->getDisplay();
        self::assertEquals($expectedError, $output);
    }

    public function invalidArgumentsProvider()
    {
        return [
            'Missing default configuration file' => [
                [],
                [],
                "Cannot find configuration file ./codeprimer/codeprimer.yaml\nDid you forget to run the codeprimer init command ?\n"
            ],
            'Invalid configuration file' => [
                ['--configuration' => 'unknownfile.yaml'],
                [],
                "Cannot find configuration file unknownfile.yaml\n"
            ],
            'Not readable configuration file' => [
                ['--configuration' => 'fixtures/configuration/notreadable.yaml'],
                [],
                "Configuration file fixtures/configuration/notreadable.yaml is not readable. Please update its permissions and try again.\n"
            ],
            'Destination is file' => [
                [
                    '--configuration' => 'fixtures/configuration/codeprimer.yaml',
                    '--destination' => 'fixtures/configuration/notreadable.yaml'
                ],
                [],
                "Destination fixtures/configuration/notreadable.yaml must be a directory.\n"
            ],
            'Destination is read-only' => [
                [
                    '--configuration' => 'fixtures/configuration/codeprimer.yaml',
                    '--destination' => 'fixtures/configuration/readonlydir'
                ],
                [],
                "Destination directory fixtures/configuration/readonlydir is not writable. Please update its permissions and try again.\n"
            ],
            'Unknown destination - do not create' => [
                [
                    '--configuration' => 'fixtures/configuration/codeprimer.yaml',
                    '--destination' => 'tests/output/actual/newdir'
                ],
                ['No'],
                "The destination directory tests/output/actual/newdir does not exist. Do you want to create it? [Yes/No] [Yes]Select another destination and try again.\n"
            ],
            'Unknown destination - create - invalid config file' => [
                [
                    '--configuration' => 'fixtures/configuration/invalid.yaml',
                    '--destination' => 'tests/output/actual/newdir'
                ],
                ['Yes'],
                "The destination directory tests/output/actual/newdir does not exist. Do you want to create it? [Yes/No] [Yes]Loading configuration from file fixtures/configuration/invalid.yaml
Failed to load configuration file: You cannot define a mapping item when in a sequence in \"fixtures/configuration/invalid.yaml\" at line 6 (near \"artifacts:\").\n"
            ],
        ];
    }

    /**
     * @dataProvider invalidConfigurationProvider
     * @runInSeparateProcess
     * @param array $arguments
     * @param string $expectedError
     */
    public function testExecuteShouldFailOnInvalidConfiguration(array $arguments, string $expectedError)
    {
        self::assertEquals(Command::FAILURE, $this->commandTester->execute($arguments));

        $output = $this->commandTester->getDisplay();
        self::assertEquals($expectedError, $output);
    }

    public function invalidConfigurationProvider()
    {
        return [
            'Bundle file not found' => [
                [
                    '--configuration' => 'fixtures/configuration/bundle_not_found.yaml',
                    '--destination' => 'tests/output/actual/'
                ],
                "Loading configuration from file fixtures/configuration/bundle_not_found.yaml
Cannot find bundle definition file codeprimer/bundle.php\n"
            ],
            'Bundle file not readable' => [
                [
                    '--configuration' => 'fixtures/configuration/bundle_not_readable.yaml',
                    '--destination' => 'tests/output/actual/'
                ],
                "Loading configuration from file fixtures/configuration/bundle_not_readable.yaml
Bundle definition file fixtures/configuration/codeprimer/notreadable_bundle.php is not readable. Please update its permissions and try again.\n"
            ],
            'Unknown template' => [
                [
                    '--configuration' => 'fixtures/configuration/unknown_template.yaml',
                    '--destination' => 'tests/output/actual/'
                ],
                "Loading configuration from file fixtures/configuration/unknown_template.yaml
No template available for category 'code', type 'unknown', format 'php', variant ''\n"
            ],
            'Missing factory' => [
                [
                    '--configuration' => 'fixtures/configuration/missing_factories.yaml',
                    '--destination' => 'tests/output/actual/'
                ],
                "Loading configuration from file fixtures/configuration/missing_factories.yaml
Failed to load configured path 'fixtures/configuration/missing_factories/bundle.php': require(DatasetFactory.php): failed to open stream: No such file or directory\n"
            ],
        ];
    }

    /**
     * @runInSeparateProcess
     */
    public function testExecuteShouldPass()
    {
        $arguments = [
            '--configuration' => 'fixtures/configuration/codeprimer.yaml',
            '--destination' => 'tests/output/actual/'
        ];

        self::assertEquals(Command::SUCCESS, $this->commandTester->execute($arguments), "Command failed with display:\n{$this->commandTester->getDisplay()}");

    }
}
