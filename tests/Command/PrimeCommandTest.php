<?php

namespace CodePrimer\Tests\Command;

use CodePrimer\Command\PrimeCommand;
use CodePrimer\Tests\Functional\TemplateTestCase;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class PrimeCommandTest extends TemplateTestCase
{
    /** @var CommandTester */
    private $commandTester;

    public function setUp(): void
    {
        $application = new Application();
        $application->add(new PrimeCommand());
        $command = $application->find('prime');
        $this->commandTester = new CommandTester($command);
    }

    /**
     * @dataProvider invalidArgumentsProvider
     * @param array $arguments
     * @param string $expectedError
     */
    public function testExecuteShouldFailOnInvalidArgument(array $arguments, array $inputs, string $expectedError)
    {
        $this->commandTester->setInputs($inputs);

        $this->commandTester->execute($arguments);

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
                "Destination directory fixtures/configuration/readonlydir is not writable. Please update its permissions and try again.\n"
            ],
        ];
    }
}
