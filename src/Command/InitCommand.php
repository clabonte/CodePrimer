<?php

namespace CodePrimer\Command;

use CodePrimer\Model\BusinessBundle;
use CodePrimer\Template\Artifact;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

class InitCommand extends CodePrimerCommand
{
    const PROJECT_PHP = 'PHP';

    protected static $defaultName = 'init';

    /** @var BusinessBundle */
    protected $businessBundle;

    protected function configure()
    {
        $help = <<<'EOF'
The <info>%command.name%</info> command initializes a new CodePrimer project by generating a set of configuration files for a given project type
EOF;
        $this
            ->setDescription('Initializes a CodePrimer project')
            ->setHelp($help);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->prepareStyles($output);

        $helper = $this->getHelper('question');

        $question = new Question('Name of your project: [MyProject] ', 'MyProject');
        $name = $helper->ask($input, $output, $question);

        $namespace = 'com.'.$name;
        $question = new Question("Namespace to use for your project: [$namespace] ", $namespace);
        $namespace = $helper->ask($input, $output, $question);

        $question = new Question('Description of your project: ', '');
        $description = $helper->ask($input, $output, $question);

        $question = new ChoiceQuestion('Project type:', ['PHP'], 'PHP');
        $question->setErrorMessage('Project type %s not supported');

        $type = $helper->ask($input, $output, $question);

        $question = new Question('Location of your project: [./] ', 'output');
        $projectPath = $helper->ask($input, $output, $question);
        if ('/' !== substr($projectPath, -1)) {
            $projectPath .= '/';
        }

        $question = new ConfirmationQuestion('Override your composer.json? [No] ', false);
        $overwrite = $helper->ask($input, $output, $question);

        $output->writeln("Creating $type project $name at $projectPath...");

        $this->businessBundle = new BusinessBundle($namespace, $name, $description);

        if (self::PROJECT_PHP == $type) {
            $this->primeNewPhpProject($output, $projectPath, $overwrite);
        }

        return self::SUCCESS;
    }

    private function primeNewPhpProject(OutputInterface $output, string $projectPath, bool $overwriteComposer)
    {
        $this->templateRenderer->setBaseFolder($projectPath);

        // 1. Prepare the composer.json file to use if it does not already exists
        $output->writeln("Priming <file>${projectPath}composer.json</> file (overwrite: ".($overwriteComposer ? 'yes' : 'no').')');
        $artifact = new Artifact(Artifact::CONFIGURATION, 'dependency manager', 'php', 'composer');
        $this->primeArtifact($artifact, $overwriteComposer);

        // 2. Prepare the .php_cs.dist file to use if it does not already exists
        $output->writeln("Priming <file>${projectPath}.php_cs.dist</file> file");
        $artifact = new Artifact(Artifact::CONFIGURATION, 'coding standards', 'php', 'PHP CS Fixer');
        $this->primeArtifact($artifact, false);

        // 3. Prepare the phpunit.xml.dist file to use if it does not already exists
        $output->writeln("Priming <file>${projectPath}phpunit.xml.dist</file> file");
        $artifact = new Artifact(Artifact::CONFIGURATION, 'tests', 'php', 'phpunit');
        $this->primeArtifact($artifact, false);

        // 4. Prepare the .gitgnore file to use if it does not already exists
        $output->writeln("Priming <file>${projectPath}.gitignore</file> file");
        $artifact = new Artifact(Artifact::CONFIGURATION, 'git', 'php', 'gitignore');
        $this->primeArtifact($artifact, false);

        // 5. Prepare the GitHub CI configuration files
        $output->writeln("Priming <file>${projectPath}.github/workflows/validate-master</file> file");
        $artifact = new Artifact(Artifact::CONFIGURATION, 'github', 'php', 'validate-master');
        $this->primeArtifact($artifact, false);

        $output->writeln("Priming <file>${projectPath}.github/workflows/validate-pr</file> file");
        $artifact = new Artifact(Artifact::CONFIGURATION, 'github', 'php', 'validate-pr');
        $this->primeArtifact($artifact, false);

        // 6. Prepare the CodePrimer configuration files
        $output->writeln("Priming <file>${projectPath}codeprimer/</file> configuration files");
        $artifact = new Artifact(Artifact::CONFIGURATION, 'codeprimer', 'php', 'bundle');
        $this->primeArtifact($artifact, false);

        $artifact = new Artifact(Artifact::CONFIGURATION, 'codeprimer', 'php', 'BusinessModelFactory');
        $this->primeArtifact($artifact, false);

        $artifact = new Artifact(Artifact::CONFIGURATION, 'codeprimer', 'php', 'BusinessProcessFactory');
        $this->primeArtifact($artifact, false);

        $artifact = new Artifact(Artifact::CONFIGURATION, 'codeprimer', 'php', 'DatasetFactory');
        $this->primeArtifact($artifact, false);

        // Prepare the configuration file
        $configuration = new ProjectConfiguration();
        $configuration
            ->setPath('codeprimer/bundle.php')
            ->setBusinessBundle($this->businessBundle)
            ->addArtifact(new Artifact(Artifact::CODE, 'dataset', 'php'))
            ->addArtifact(new Artifact(Artifact::CODE, 'model', 'php'))
            ->addArtifact(new Artifact(Artifact::CODE, 'event', 'php'))
            ->addArtifact(new Artifact(Artifact::CODE, 'Migration', 'mysql', 'CreateDatabase'))
            ->addArtifact(new Artifact(Artifact::CODE, 'Migration', 'mysql', 'RevertDatabase'))
            ->addArtifact(new Artifact(Artifact::DOCUMENTATION, 'dataset', 'markdown'))
            ->addArtifact(new Artifact(Artifact::DOCUMENTATION, 'model', 'markdown'))
            ->addArtifact(new Artifact(Artifact::DOCUMENTATION, 'process', 'markdown', 'index'))
            ->addArtifact(new Artifact(Artifact::DOCUMENTATION, 'process', 'markdown', 'details'));

        $configuration->save($projectPath.'/codeprimer/codeprimer.yaml');
    }

    protected function getBusinessBundle(): BusinessBundle
    {
        return $this->businessBundle;
    }
}
