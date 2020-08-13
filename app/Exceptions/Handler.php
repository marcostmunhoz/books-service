<?php

namespace App\Exceptions;

use App\Traits\ApiResponserTrait;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

/**
 * Error Handler.
 */
class Handler extends ExceptionHandler
{
    use ApiResponserTrait;

    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Returns the exception handlers for each exception type.
     *
     * @return array
     */
    public function exceptionHandlers()
    {
        return [
            HttpException::class => function (HttpException $exception) {
                $code = $exception->getStatusCode();

                return [
                    Response::$statusTexts($code),
                    $code,
                ];
            },
            ModelNotFoundException::class => function (ModelNotFoundException $exception) {
                $model = strtolower(class_basename($exception->getModel()));

                return [
                    sprintf('There\'s no %s for the given ID.', $model),
                    Response::HTTP_NOT_FOUND,
                ];
            },
            AuthorizationException::class => function (AuthorizationException $exception) {
                return [
                    $exception->getMessage(),
                    Response::HTTP_FORBIDDEN,
                ];
            },
            AuthenticationException::class => function (AuthenticationException $exception) {
                return [
                    $exception->getMessage(),
                    Response::HTTP_UNAUTHORIZED,
                ];
            },
            ValidationException::class => function (ValidationException $exception) {
                return [
                    $exception->validator->errors()->getMessages(),
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                ];
            },
        ];
    }

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param \Throwable $exception
     *
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Throwable               $exception
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        if ($handler = $this->exceptionHandlers()[\get_class($exception)] ?? null) {
            $params = $handler($exception);

            return \call_user_func_array([$this, 'errorResponse'], $params);
        }

        if (env('APP_DEBUG', false)) {
            return parent::render($request, $exception);
        }

        return $this->errorResponse(
            'Unexpected error. Try again later.',
            Response::HTTP_INTERNAL_SERVER_ERROR
        );
    }
}
