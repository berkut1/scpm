<?php
declare(strict_types=1);

namespace App\Tests\Command\User;

use App\Command\User\CreateCommand;
use App\Tests\Command\AbstractCommandTest;

final class CreateCommandTest extends AbstractCommandTest
{
    public function testExecute(): void
    {
        $commandTester = $this->executeCommand([
            'command' => 'user:create',
        ], [
            'test_command_user', // Login
            'password123', // Password
            'n', // Is admin? No
        ]);

        // Check if the command output contains 'Done!'
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Done!', $output);
    }

    #[\Override]
    protected function getCommandFqcn(): string
    {
        return CreateCommand::class;
    }
}