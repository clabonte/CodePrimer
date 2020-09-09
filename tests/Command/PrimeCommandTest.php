<?php

namespace CodePrimer\Tests\Command;

use CodePrimer\Command\PrimeCommand;
use CodePrimer\Tests\Functional\TemplateTestCase;
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

        chmod(__DIR__.'/../../fixtures/configuration/notreadable.yaml', 0222);
        chmod(__DIR__.'/../../fixtures/configuration/codeprimer/notreadable_bundle.php', 0222);
        if (is_writable(__DIR__.'/../../fixtures/configuration/readonlydir')) {
            chmod(__DIR__.'/../../fixtures/configuration/readonlydir', 0444);
        }
    }

    public function tearDown(): void
    {
        parent::tearDown();
        if (file_exists(__DIR__.'/../../fixtures/configuration/codeprimer/RenamedDatasetFactory.php')) {
            rename(__DIR__.'/../../fixtures/configuration/codeprimer/RenamedDatasetFactory.php', __DIR__.'/../../fixtures/configuration/codeprimer/DatasetFactory.php');
        }

        chmod(__DIR__.'/../../fixtures/configuration/notreadable.yaml', 0666);
        chmod(__DIR__.'/../../fixtures/configuration/codeprimer/notreadable_bundle.php', 0666);
    }

    /**
     * @dataProvider invalidArgumentsProvider
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
                "Cannot find configuration file ./codeprimer/codeprimer.yaml\nDid you forget to run the codeprimer init command ?\n",
            ],
            'Invalid configuration file' => [
                ['--configuration' => 'unknownfile.yaml'],
                [],
                "Cannot find configuration file unknownfile.yaml\n",
            ],
            'Not readable configuration file' => [
                ['--configuration' => 'fixtures/configuration/notreadable.yaml'],
                [],
                "Configuration file fixtures/configuration/notreadable.yaml is not readable. Please update its permissions and try again.\n",
            ],
            'Destination is file' => [
                [
                    '--configuration' => 'fixtures/configuration/codeprimer.yaml',
                    '--destination' => 'fixtures/configuration/notreadable.yaml',
                ],
                [],
                "Destination fixtures/configuration/notreadable.yaml must be a directory.\n",
            ],
            'Destination is read-only' => [
                [
                    '--configuration' => 'fixtures/configuration/codeprimer.yaml',
                    '--destination' => 'fixtures/configuration/readonlydir',
                ],
                [],
                "Destination directory fixtures/configuration/readonlydir is not writable. Please update its permissions and try again.\n",
            ],
            'Unknown destination - do not create' => [
                [
                    '--configuration' => 'fixtures/configuration/codeprimer.yaml',
                    '--destination' => 'tests/output/actual/newdir',
                ],
                ['No'],
                "The destination directory tests/output/actual/newdir does not exist. Do you want to create it? [Yes/No] [Yes]Select another destination and try again.\n",
            ],
            'Unknown destination - create - invalid config file' => [
                [
                    '--configuration' => 'fixtures/configuration/invalid.yaml',
                    '--destination' => 'tests/output/actual/newdir',
                ],
                ['Yes'],
                "The destination directory tests/output/actual/newdir does not exist. Do you want to create it? [Yes/No] [Yes]Loading configuration from file fixtures/configuration/invalid.yaml
Failed to load configuration file: You cannot define a mapping item when in a sequence in \"fixtures/configuration/invalid.yaml\" at line 6 (near \"artifacts:\").\n",
            ],
        ];
    }

    /**
     * @dataProvider invalidConfigurationProvider
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
                    '--destination' => 'tests/output/actual/',
                ],
                "Loading configuration from file fixtures/configuration/bundle_not_found.yaml
Cannot find bundle definition file codeprimer/bundle.php\n",
            ],
            'Bundle file not readable' => [
                [
                    '--configuration' => 'fixtures/configuration/bundle_not_readable.yaml',
                    '--destination' => 'tests/output/actual/',
                ],
                "Loading configuration from file fixtures/configuration/bundle_not_readable.yaml
Bundle definition file fixtures/configuration/codeprimer/notreadable_bundle.php is not readable. Please update its permissions and try again.\n",
            ],
            'Unknown template' => [
                [
                    '--configuration' => 'fixtures/configuration/unknown_template.yaml',
                    '--destination' => 'tests/output/actual/',
                ],
                "Loading configuration from file fixtures/configuration/unknown_template.yaml
No template available for category 'code', type 'unknown', format 'php', variant ''\n",
            ],
        ];
    }

    public function testExecuteShouldFailOnMissingFactory()
    {
        $arguments = [
            '--configuration' => 'fixtures/configuration/codeprimer.yaml',
            '--destination' => 'tests/output/actual/',
        ];
        $expectedError = "Loading configuration from file fixtures/configuration/codeprimer.yaml
Failed to load configured path 'fixtures/configuration/codeprimer/bundle.php': require(DatasetFactory.php): failed to open stream: No such file or directory\n";

        if (file_exists(__DIR__.'/../../fixtures/configuration/codeprimer/DatasetFactory.php')) {
            rename(__DIR__.'/../../fixtures/configuration/codeprimer/DatasetFactory.php', __DIR__.'/../../fixtures/configuration/codeprimer/RenamedDatasetFactory.php');
        }
        self::assertEquals(Command::FAILURE, $this->commandTester->execute($arguments));

        $output = $this->commandTester->getDisplay();
        self::assertEquals($expectedError, $output);
    }

    public function testExecuteShouldPass()
    {
        $destDir = 'tests/output/actual/';
        $arguments = [
            '--configuration' => 'fixtures/configuration/codeprimer.yaml',
            '--destination' => $destDir,
        ];

        if (file_exists(__DIR__.'/../../fixtures/configuration/codeprimer/RenamedDatasetFactory.php')) {
            rename(__DIR__.'/../../fixtures/configuration/codeprimer/RenamedDatasetFactory.php', __DIR__.'/../../fixtures/configuration/codeprimer/DatasetFactory.php');
        }

        self::assertEquals(Command::SUCCESS, $this->commandTester->execute($arguments), "Command failed with display:\n{$this->commandTester->getDisplay()}");

        // Make sure all documentation files have been generated
        self::assertFileExists($destDir.'docs/DataModel/Overview.md');
        self::assertFileExists($destDir.'docs/Dataset/Overview.md');
        self::assertFileExists($destDir.'docs/Process/Overview.md');
        self::assertFileExists($destDir.'docs/Process/UserLogin.md');
        self::assertFileExists($destDir.'docs/Process/UserLogout.md');
        self::assertFileExists($destDir.'docs/Process/UserRegistration.md');

        // Make sure all source code files have been generated
        self::assertFileExists($destDir.'gen-src/Dataset/UserRole.php');
        self::assertFileExists($destDir.'gen-src/Dataset/UserStatus.php');
        self::assertFileExists($destDir.'gen-src/Event/LoginRequest.php');
        self::assertFileExists($destDir.'gen-src/Event/LogoutRequest.php');
        self::assertFileExists($destDir.'gen-src/Event/RegistrationRequest.php');
        self::assertFileExists($destDir.'gen-src/Model/User.php');

        // Make sure all migration files have been generated
        self::assertFileExists($destDir.'migrations/CreateDatabase.sql');
        self::assertFileExists($destDir.'migrations/RevertDatabase.sql');
    }
}
