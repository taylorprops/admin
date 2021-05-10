<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    // public function register()
    // {
    //     $this -> reportable(function (Throwable $e) {
    //         //
    //     });
    // }

    protected function context()
    {
        if(auth()) {
            return array_merge(parent::context(), [
                'user_id' => auth() -> user() -> id,
                'user_name' => auth() -> user() -> name,
                'user_email' => auth() -> user() -> email
            ]);
        }
    }

    public function report(Throwable $exception)
    {
        if ($this -> shouldReport($exception)) {
            $airbrakeNotifier = \App::make('Airbrake\Notifier');
            $airbrakeNotifier -> notify($exception);
        }

        parent::report($exception);
    }

    /* public function render($request, Throwable $exception) {
        if($this -> pageExpired($exception)) {
            return back(fallback: '/dashboard');
        }
        return parent::render($request, $exception);
    }

    private function pageExpired(Throwable $exception): bool {
        return $exception instanceof TokenMismatchException || ($exception instanceof HttpException && $exception -> getStatusCode() == 419);
    } */

}
