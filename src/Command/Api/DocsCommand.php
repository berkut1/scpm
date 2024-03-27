<?php
declare(strict_types=1);

namespace App\Command\Api;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

#[AsCommand('api:docs')]
final class DocsCommand extends Command
{
    #[\Override]
    protected function configure(): void
    {
        $this
            ->setDescription('Generate OpenAPI docs');
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $swagger = 'vendor/bin/openapi';
        $source = 'src/Controller/Api';
        $target = 'public/docs/openapi.json';

        $process = new Process([PHP_BINARY, $swagger, $source, '--output', $target]);
        $process->run(static function ($type, $buffer) use ($output) {
            $output->write($buffer);
        });

        $output->writeln('<info>Done!</info>');
        return 0;
    }
}
