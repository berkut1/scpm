<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Controller\ErrorHandler;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final readonly class DomainExceptionFormatter implements EventSubscriberInterface
{
    public function __construct(private ErrorHandler $errors) {}

    #[ArrayShape([KernelEvents::EXCEPTION => "string"])]
    #[\Override]
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $request = $event->getRequest();

        if (!$exception instanceof \DomainException) {
            return;
        }

        if (!str_starts_with((string)$request->attributes->get('_route'), 'api.')) { //(strpos($request->attributes->get('_route'), 'api.') !== 0)
            return;
        }

        $this->errors->handle($exception);

        $event->setResponse(new JsonResponse([
            'error' => [
                'code' => 400,
                'message' => $exception->getMessage(),
            ],
        ], Response::HTTP_BAD_REQUEST));
    }
}
