<?php
declare(strict_types=1);

namespace App\Event;

use Symfony\Contracts\EventDispatcher\Event;

final class FormErrorEvent extends Event
{

    public function __construct(
        private readonly \Exception $exception,
        private readonly string     $errorMessage
    ) {}

    public function getException(): \Exception
    {
        return $this->exception;
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }
}