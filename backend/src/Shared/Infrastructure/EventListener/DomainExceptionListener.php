<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\EventListener;

use App\Shared\Domain\Exception\DomainException;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Messenger\Exception\HandlerFailedException;

#[AsEventListener(event: KernelEvents::EXCEPTION)]
final class DomainExceptionListener
{
    public function __invoke(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        // Jeśli wyjątek pochodzi z Messengera, musimy go "odpakować"
        if ($exception instanceof HandlerFailedException) {
            // Pobieramy pierwszy z zagnieżdżonych wyjątków
            $exception = $exception->getPrevious();
        }

        // Interesują nas wyłącznie nasze wyjątki domenowe.
        // Jeśli to np. 404 Not Found lub błąd bazy danych (500),
        // pozwalamy Symfony obsłużyć to domyślnie.
        if (!$exception instanceof DomainException) {
            return;
        }

        // Formujemy czystą, znormalizowaną odpowiedź JSON dla Frontendu
        $response = new JsonResponse(
            [
                'error' => [
                    'type' => 'DomainValidationError',
                    'message' => $exception->getMessage(),
                ]
            ],
            Response::HTTP_BAD_REQUEST // Kod 400 - winny jest użytkownik, a nie serwer
        );

        // Nadpisujemy domyślną odpowiedź frameworka
        $event->setResponse($response);
    }
}
