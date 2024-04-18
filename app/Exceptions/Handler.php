<?php

namespace App\Exceptions;

use Exception;
use Throwable;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    // /**
    //  * Render an exception into an HTTP response.
    //  *
    //  * @param  \Illuminate\Http\Request  $request
    //  * @param  \Exception  $exception
    //  * @return \Illuminate\Http\Response
    //  */
    // public function render($request, Throwable $exception)
    // {
    //     if ($exception instanceof \Exception)
    //     {
    //         return Redirect::back()->withErrors(['server_error' => __('notifications.500_description')]);
    //     }

    //     return parent::render($request, $exception);
    // }

    /**
     * Display a JSON message if the API user has not authenticated
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        $is_api_request = $request->route()->getPrefix() == 'api';

        if ($is_api_request == false) {
            return response()->view('auth.login');
        }

        return response()->json(['message' => __('notifications.401_description')], 401);
    }
}
