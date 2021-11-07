<?php
declare(strict_types=1);

namespace App\Command\User;

use App\Model\User\Entity\User\Role;
use App\Model\User\UseCase\SignUp\Manual;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class CreateCommand extends Command
{
    private Manual\Handler $handler;

    public function __construct(Manual\Handler $handler)
    {
        $this->handler = $handler;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('user:create')
            ->setDescription('Create a new user');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');

        $login = $helper->ask($input, $output, new Question('Login: '));
        $password = $helper->ask($input, $output, new Question('Password: '));
        $role = $helper->ask($input, $output, new Question('Is admin? (y/n): '));
        $role = $role === 'y' ? Role::admin()->getName() : Role::user()->getName();

        $command = new Manual\Command($login, $password, $role);
        $this->handler->handle($command);

        $output->writeln('<info>Done!</info>');
        return 0;
    }
}