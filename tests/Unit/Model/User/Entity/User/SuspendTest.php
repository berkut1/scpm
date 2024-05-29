<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\User\Entity\User;

use App\Tests\Builder\User\UserBuilder;
use PHPUnit\Framework\TestCase;

final class SuspendTest extends TestCase
{
    public function testSuccess(): void
    {
        $user = (new UserBuilder())->viaLogin()->build();

        $user->suspend();

        self::assertFalse($user->getStatus()->isActive());
        self::assertTrue($user->getStatus()->isSuspended());
    }

    public function testAlready(): void
    {
        $user = (new UserBuilder())->viaLogin()->build();

        $user->suspend();

        $this->expectExceptionMessage('User is already suspended.');
        $user->suspend();
    }
}
