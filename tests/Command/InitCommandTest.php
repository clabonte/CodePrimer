<?php

namespace CodePrimer\Tests\Command;

use CodePrimer\Command\InitCommand;
use CodePrimer\Tests\Functional\TemplateTestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class InitCommandTest extends TemplateTestCase
{
    /** @var CommandTester */
    private $commandTester;

    public function setUp(): void
    {
        parent::setUp();
        $application = new Application();
        $application->add(new InitCommand());
        $command = $application->find('init');
        $this->commandTester = new CommandTester($command);
    }

    public function testInitPhpProjectShouldPass()
    {
        $destDir = __DIR__.'/../output/actual';
        $inputs = [
            'TestProject',
            'com.test',
            'This is a test',
            'PHP',
            $destDir,
            'No',
        ];

        $this->commandTester->setInputs($inputs);

        self::assertEquals(Command::SUCCESS, $this->commandTester->execute([]), "Command failed with display:\n{$this->commandTester->getDisplay()}");

        // Make sure all configuration files have been generated
        self::assertFileExists($destDir.'/.gitignore');
        self::assertFileExists($destDir.'/.php_cs.dist');
        self::assertFileExists($destDir.'/composer.json');
        self::assertFileExists($destDir.'/phpunit.xml.dist');
        self::assertFileExists($destDir.'/.github/workflows/validate-master.yml');
        self::assertFileExists($destDir.'/.github/workflows/validate-pr.yml');
        self::assertFileExists($destDir.'/codeprimer/codeprimer.yaml');
        self::assertFileExists($destDir.'/codeprimer/bundle.php');
        self::assertFileExists($destDir.'/codeprimer/BusinessModelFactory.php');
        self::assertFileExists($destDir.'/codeprimer/BusinessProcessFactory.php');
        self::assertFileExists($destDir.'/codeprimer/DatasetFactory.php');
    }
}
