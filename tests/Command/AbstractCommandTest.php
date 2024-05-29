<?php
declare(strict_types=1);

namespace App\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

abstract class AbstractCommandTest extends KernelTestCase //https://github.com/symfony/demo/blob/main/tests/Command/AbstractCommandTest.php
{

    /**
     * This helper method abstracts the boilerplate code needed to test the
     * execution of a command.
     *
     * @param array<string, string|int> $arguments All the arguments passed when executing the command
     * @param array<int, mixed>         $inputs    The (optional) answers given to the command when it asks for the
     *                                             value of the missing arguments
     */
    protected function executeCommand(array $arguments, array $inputs = []): CommandTester
    {
        $kernel = self::bootKernel();

        // this uses a special testing container that allows you to fetch private services
        /** @var Command $command */
        $command = static::getContainer()->get($this->getCommandFqcn());
        $command->setApplication(new Application($kernel));

        $commandTester = new CommandTester($command);
        $commandTester->setInputs($inputs);
        $commandTester->execute($arguments);

        return $commandTester;
    }

    abstract protected function getCommandFqcn(): string;
}