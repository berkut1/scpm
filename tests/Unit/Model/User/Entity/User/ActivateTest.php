<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\User\Entity\User;

use App\Tests\Builder\User\UserBuilder;
use PHPUnit\Framework\TestCase;

final class ActivateTest extends TestCase
{
    public function testSuccess(): void
    {
        $user = (new UserBuilder())->viaLogin()->build();

        $user->suspend();

        $user->activate();

        self::assertTrue($user->getStatus()->isActive());
        self::assertFalse($user->getStatus()->isSuspended());
    }

    public function testAlready(): void
    {
        $user = (new UserBuilder())->viaLogin()->build();


        $this->expectExceptionMessage('User is already active.');
        $user->activate();
    }
}
