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
     * Returns a list of status codes and messages.
     *
     * @var array
     */
    const STATUS_TEXTS = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',            // RFC2518
        103 => 'Early Hints',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',          // RFC4918
        208 => 'Already Reported',      // RFC5842
        226 => 'IM Used',               // RFC3229
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',    // RFC7238
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Payload Too Large',
        414 => 'URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',                                               // RFC2324
        421 => 'Misdirected Request',                                         // RFC7540
        422 => 'Unprocessable Entity',                                        // RFC4918
        423 => 'Locked',                                                      // RFC4918
        424 => 'Failed Dependency',                                           // RFC4918
        425 => 'Too Early',                                                   // RFC-ietf-httpbis-replay-04
        426 => 'Upgrade Required',                                            // RFC2817
        428 => 'Precondition Required',                                       // RFC6585
        429 => 'Too Many Requests',                                           // RFC6585
        431 => 'Request Header Fields Too Large',                             // RFC6585
        451 => 'Unavailable For Legal Reasons',                               // RFC7725
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',                                     // RFC2295
        507 => 'Insufficient Storage',                                        // RFC4918
        508 => 'Loop Detected',                                               // RFC5842
        510 => 'Not Extended',                                                // RFC2774
        511 => 'Network Authentication Required',                             // RFC6585
    ];

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
                    self::STATUS_TEXTS[$code],
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
        foreach ($this->exceptionHandlers() as $class => $handler) {
            if (is_subclass_of($exception, $class) || \get_class($exception) === $class) {
                $params = $handler($exception);

                return $params instanceof Response
                    ? $params
                    : \call_user_func_array([$this, 'errorResponse'], $params);
            }
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
