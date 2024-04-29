<?php
declare(strict_types=1);

namespace App\Controller;

use App\Event\FormErrorEvent;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final readonly class FormErrorFlashBag implements EventSubscriberInterface
{
    public function __construct(
        private EventDispatcherInterface $dispatcher,
        private ErrorHandler $errors
    ) {}

    #[ArrayShape([KernelEvents::CONTROLLER => "string"])]
    #[\Override]
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }

    public function onKernelController(ControllerEvent $event): void
    {
        $request = $event->getRequest();

        if (!str_starts_with((string)$request->attributes->get('_route'), 'api.')) { //(strpos($request->attributes->get('_route'), 'api.') !== 0)
            $this->dispatcher->addListener(FormErrorEvent::class, function (FormErrorEvent $event) use ($request) {
                $request->getSession()->getFlashBag()->add('error', $event->getErrorMessage() . " --> " . $event->getException()->getMessage());
                $this->errors->handleError($event->getException());
//                $controller = $request->attributes->get('_controller');
//                if (is_array($controller) && is_object($controller[0])) {
//                    $controller[0]->addFlash('error', $event->getErrorMessage());
//                }
            });
        }
    }
}