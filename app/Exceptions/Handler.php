<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

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
     * A list of exceptions that denote something was not found.
     *
     * @var array
     */
    protected $notFoundExceptions = [
        ModelNotFoundException::class,
        NotFoundHttpException::class,
        RecordNotFoundException::class
    ];

    protected $clientInputExceptions = [
        \UnexpectedValueException::class,
        AuthorizationException::class
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];
    
    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception $exception
     * @return void
     * @throws Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Throwable $exception)
    {
        try {
            if (starts_with($request->path(), 'xhr')) {
                # we're dealing with an Ajax request
                $status = 500;
                # the status code
                $exceptionClass = get_class($exception);
                # we get the class name for the exception
                if (in_array($exceptionClass, $this->notFoundExceptions)) {
                    $status = 404;
                    $message = $exceptionClass === RecordNotFoundException::class ?
                        $exception->getMessage() : 'Could not find what you were looking for.';
                    # our response message
                    $response = [
                        'status' => $status,
                        'code' => 'not_found',
                        'title' => $message,
                        'source' => array_merge($request->all(), ['path' => $request->getPathInfo()])
                    ];

                } elseif ($exception instanceof MethodNotAllowedHttpException) {
                    $status = 405;
                    $response = [
                        'status' => $status,
                        'code' => 'http_error',
                        'title' => 'This method is not allowed for this endpoint.',
                        'source' => array_merge($request->all(),
                            ['path' => $request->getPathInfo(), 'method' => $request->getMethod()])
                    ];

                } elseif ($exception instanceof ValidationException) {
                    $status = 400;
                    $response = [
                        'status' => $status,
                        'code' => 'validation_failed',
                        'title' => 'Some validation errors were encountered while processing your request',
                        'source' => validation_errors_to_messages($exception)
                    ];

                } elseif (in_array($exceptionClass, $this->clientInputExceptions)) {
                    $status = 400;
                    $response = [
                        'status' => $status,
                        'code' => 'http_error',
                        'title' => $exception->getMessage(),
                        'source' => array_merge($request->all(),
                            ['path' => $request->getPathInfo(), 'method' => $request->getMethod()])
                    ];

                } elseif ($exception instanceof DeletingFailedException) {
                    $response = [
                        'status' => $status,
                        'code' => 'exception',
                        'title' => $exception->getMessage(),
                        'source' => array_merge($request->all(),
                            ['path' => $request->getPathInfo(), 'method' => $request->getMethod()])
                    ];

                } else {
                    $response = [
                        'status' => $status,
                        'code' => 'exception',
                        'title' => $exception->getMessage(),
                    ];
                }
                return response()->json(['errors' => [$response]], $status);
            }
            if ($exception instanceof UnauthorizedException) {
                $alarm = [
                    'title' => 'Access Denied',
                    'text' => 'You do not have the required permissions to use, or access this feature.'
                ];
                return response(view('errors.403', ['alarm' => $alarm])->render(), 403);
            }
        } catch (\Throwable $e) {}
        return parent::render($request, $exception);
    }
}
