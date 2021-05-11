<?php

namespace Jigsaw\Installer\Console;

use RuntimeException;
use Symfony\Component\Process\Process;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NewCommand extends Command
{
    /**
     * Command configuration
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('new')
            ->setDescription('Create a new Jigsaw site')
            ->addArgument('name', InputArgument::REQUIRED)
            ->addOption('starter', null, InputOption::VALUE_OPTIONAL, 'Starter template to initialize with')
            ->addOption('dev', 'd', InputOption::VALUE_NONE, 'Installs the latest "development" release')
            ->addOption('v', null, InputOption::VALUE_OPTIONAL, 'Installs the given version')
            ->addOption('no-git', null, InputOption::VALUE_NONE, 'Does not initialize a Git repository')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Forces install even if the directory already exists');
    }

    /**
     * Execute the command.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');

        $directory = $name !== '.' ? getcwd() . '/' . $name : '.';

        $version = $this->getVersion($input);

        $starter = $input->getOption('starter') ? $input->getOption('starter') : '';

        if (!$input->getOption('force')) {
            $this->verifyDirectoryDoesntExist($directory);

            if ($directory !== '.') {
                mkdir($directory);
            }
        }

        if ($input->getOption('force') && $directory === '.') {
            throw new RuntimeException('Cannot use --force option when using current directory for installation!');
        }

        $process = $this->installJigsaw($input, $output, $directory, $version, $starter);

        return $process->getExitCode();
    }

    /**
     * Install and initialize Jigsaw
     *
     * @param \Symfony\Component\Console\Input\InputInterface  $input
     * @param \Symfony\Component\Console\Output\OutputInterface  $output
     * @param String $directory
     * @param String $version
     * @param String $starter
     *
     * @return \Symfony\Component\Process\Process
     */
    protected function installJigsaw(InputInterface $input, OutputInterface $output, String $directory, String $version, String $starter)
    {
        $cwd = getcwd();
        chdir($directory);

        $composer = $this->getComposer();

        $commands = [
            $composer . " require tightenco/jigsaw{$version} --no-interaction",
        ];

        if (($process = $this->runCommands($commands, $output))->isSuccessful()) {
            $output->writeln(PHP_EOL . '<comment>Jigsaw Installed. Initializing now...</comment>');

            if ($this->runCommands(["./vendor/bin/jigsaw init " . $starter], $output)->isSuccessful()) {
                if (!$input->getOption('no-git')) {
                    $this->initGit($output);
                }

                chdir($cwd);
            }

            return $process;
        }
    }

    /**
     * Gets the version to install.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     *
     * @return String
     **/
    protected function getVersion($input)
    {
        if ($input->getOption('dev')) {
            return ':dev-main';
        };

        if ($input->getOption('v')) {
            return ':' . $input->getOption('v');
        };

        return '';
    }

    /**
     * Verify that the directory doesn't already exist.
     *
     * @param  string  $directory
     *
     * @return void
     */
    protected function verifyDirectoryDoesntExist($directory)
    {
        if (is_dir($directory) && $directory != getcwd()) {
            throw new RuntimeException('Directory already exists!');
        }
    }

    /**
     * Determine how to run Composer commands
     * (Borrowed from https://github.com/laravel/installer)
     *
     * @return string
     */
    protected function getComposer()
    {
        $composer = getcwd() . '/composer.phar';

        if (file_exists($composer)) {
            return '"' . PHP_BINARY . '" ' . $composer;
        }

        return 'composer';
    }

    /**
     * Run commands.
     * (Borrowed from https://github.com/laravel/installer)
     *
     * @param  array  $commands
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     * @param  array  $env
     *
     * @return \Symfony\Component\Process\Process
     */
    protected function runCommands($commands, OutputInterface $output, array $env = [])
    {
        $process = Process::fromShellCommandline(implode(' && ', $commands), null, $env, null, null);

        if ('\\' !== DIRECTORY_SEPARATOR && file_exists('/dev/tty') && is_readable('/dev/tty')) {
            try {
                $process->setTty(true);
            } catch (RuntimeException $e) {
                $output->writeln('Warning: ' . $e->getMessage());
            }
        }

        $process->run(function ($type, $line) use ($output) {
            $output->write('    ' . $line);
        });

        return $process;
    }

    /**
     * Initialize a git repository and make initial commit.
     * (Borrowed from https://github.com/laravel/installer)
     *
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     *
     * @return void
     */
    protected function initGit(OutputInterface $output)
    {
        $commands = [
            'git init -q',
            'git add .',
            'git commit -q -m "Install Jigsaw"',
            "git branch -M main",
        ];

        $this->runCommands($commands, $output);
    }
}
