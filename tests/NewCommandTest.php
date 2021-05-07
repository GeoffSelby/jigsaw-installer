<?php

namespace Jigsaw\Installer\Console\Tests;

use PHPUnit\Framework\TestCase;
use Jigsaw\Installer\Console\NewCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class NewCommandTest extends TestCase
{
    protected $scaffoldDirectory;
    protected $scaffoldDirectoryName;

    protected function setUp(): void
    {
        parent::setUp();

        $this->scaffoldDirectoryName = 'tests-output/my-test-site';
        $this->scaffoldDirectory = __DIR__ . '/../' . $this->scaffoldDirectoryName;
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        if (file_exists($this->scaffoldDirectory)) {
            if (PHP_OS_FAMILY == 'Windows') {
                exec("rd /s /q \"$this->scaffoldDirectory\"");
            } else {
                exec("rm -rf \"$this->scaffoldDirectory\"");
            }
        }
    }

    /** @test */
    public function it_can_scaffold_a_new_jigsaw_site()
    {
        $app = new Application('Jigsaw Installer');
        $app->add(new NewCommand);

        $tester = new CommandTester($app->find('new'));

        $statusCode = $tester->execute(['name' => $this->scaffoldDirectoryName]);

        $this->assertSame(0, $statusCode);
        $this->assertDirectoryExists($this->scaffoldDirectory . '/source');
        $this->assertFileExists($this->scaffoldDirectory . '/source/index.blade.php');
    }

    /** @test */
    public function it_initializes_git_by_default()
    {
        $app = new Application('Jigsaw Installer');
        $app->add(new NewCommand);

        $tester = new CommandTester($app->find('new'));

        $statusCode = $tester->execute(['name' => $this->scaffoldDirectoryName]);

        $this->assertSame(0, $statusCode);
        $this->assertDirectoryExists($this->scaffoldDirectory . '/source');
        $this->assertDirectoryExists($this->scaffoldDirectory . '/.git');
        $this->assertFileExists($this->scaffoldDirectory . '/source/index.blade.php');
    }

    /** @test */
    public function it_does_not_initialize_git_when_no_git_flag_is_passed()
    {
        $app = new Application('Jigsaw Installer');
        $app->add(new NewCommand);

        $tester = new CommandTester($app->find('new'));

        $statusCode = $tester->execute(['name' => $this->scaffoldDirectoryName, '--no-git' => 'true']);

        $this->assertSame(0, $statusCode);
        $this->assertDirectoryExists($this->scaffoldDirectory . '/source');
        $this->assertFileExists($this->scaffoldDirectory . '/source/index.blade.php');
        $this->assertFileDoesNotExist($this->scaffoldDirectory . '/.git');
    }

    /** @test */
    public function it_can_scaffold_a_new_jigsaw_site_from_dev_version()
    {
        $app = new Application('Jigsaw Installer');
        $app->add(new NewCommand);

        $tester = new CommandTester($app->find('new'));

        $statusCode = $tester->execute(['name' => $this->scaffoldDirectoryName, '--dev' => true]);

        $composerJson = file_get_contents($this->scaffoldDirectory . '/composer.json');

        $this->assertSame(0, $statusCode);
        $this->assertNotFalse(strpos($composerJson, '"tightenco/jigsaw": "dev-main"'));
        $this->assertDirectoryExists($this->scaffoldDirectory . '/source');
        $this->assertFileExists($this->scaffoldDirectory . '/source/index.blade.php');
    }

    /** @test */
    public function it_can_scaffold_a_new_site_from_a_starter_template()
    {
        $app = new Application('Jigsaw Installer');
        $app->add(new NewCommand);

        $tester = new CommandTester($app->find('new'));

        $statusCode = $tester->execute(['name' => $this->scaffoldDirectoryName, '--starter' => 'blog']);

        $this->assertSame(0, $statusCode);
        $this->assertDirectoryExists($this->scaffoldDirectory . '/source');
        $this->assertFileExists($this->scaffoldDirectory . '/source/blog.blade.php');
    }
}
