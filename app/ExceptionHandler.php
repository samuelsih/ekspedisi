<?php

namespace App;

use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Session\TokenMismatchException;
use Inertia\Inertia;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class ExceptionHandler
{
    /**
     * Invoke the class instance.
     */
    public function __invoke(Exceptions $handler): void
    {
        $handler->render(
            fn (
                NotFoundHttpException
                |ServiceUnavailableHttpException
                |UnauthorizedHttpException
                |TokenMismatchException $e
            ) => Inertia::render('Error', [
                'status' => $e->getStatusCode(),
            ])
        );
    }
}
