<?php

namespace App\Exceptions;

use Logging;
use Throwable;
use Illuminate\Database\QueryException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

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
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof ModelNotFoundException) {
            $model = class_basename($exception->getModel());

            Logging::exception(
                $exception,
                4,
                0
            );
            return response()->json([
                'success' => false,
                'message' => "{$model} model not found."
            ], 404);
        }

        if ($exception instanceof ValidationException) {
            Logging::exception(
                $exception,
                4,
                0
            );
            return $this->convertValidationExceptionToResponse($exception, $request);
        }

        if ($exception instanceof AuthenticationException) {
            Logging::exception(
                $exception,
                4,
                0
            );
            return $this->unauthenticated($request, $exception);
        }

        if ($exception instanceof AuthorizationException) {
            Logging::exception(
                $exception,
                1,
                0
            );
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized access.'
            ], 403);
        }

        if ($exception instanceof NotFoundHttpException) {
            Logging::exception(
                $exception,
                4,
                0
            );
            return response()->json([
                'status' => false,
                'message' => 'The specified URL cannot be found.'
            ], 404);
        }

        if ($exception instanceof MethodNotAllowedHttpException) {
            Logging::exception(
                $exception,
                4,
                0
            );
            return response()->json([
                'status' => false,
                'message' => 'The specified method is invalid.'
            ], 405);
        }

        if ($exception instanceof HttpException) {
            Logging::exception(
                $exception,
                1,
                0
            );
            return response()->json(
                [
                    'status' => false,
                    'message' => $exception->getMessage()
                ],
                $exception->getStatusCode()
            );
        }

        if ($exception instanceof QueryException) {
            $errorCode = $exception->errorInfo[1];

            if ($errorCode == 1451) {
                Logging::exception(
                    $exception,
                    4,
                    0
                );
                return response()->json([
                    'status' => false,
                    'message' => 'Cannot remove this resource permanently. It is related with any other resource.'
                ], 409);
            }
        }

        if (config('app.debug')) {
            return parent::render($request, $exception);
        }

        Logging::exception(
            $exception,
            4,
            0
        );
        return response()->json([
            'status' => false,
            'message' => 'Unexpected Exception.'
        ], 500);
    }

    /**
     * Create a response object from the given validation exception
     *
     * @param \Illuminate\Validation\ValidationException $exception
     * @param \Illuminate\Http\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function convertValidationExceptionToResponse(ValidationException $exception, $request)
    {
        $errors = $exception->validator->errors()->getMessages();

        $log['request_error'] = $errors;
        $log['reference_url'] = \Request::fullUrl();
        Logging::error('Validation Error', $log, 4, 0);

        if (is_array($errors)) {
            $err = "";
            foreach ($errors as $k => $vs) {
                foreach ($vs as $v) {
                    // NOTE: disabled [input_name: error_message]
                    // $err .= $k . ': ' . $v . "\r\n";
                    $err .= $v . "\r\n";
                }
            }

            return response()->json([
                'status' => false,
                'message' => $err
            ], 422);
        } else {
            return response()->json($errors, 422);
        }
    }

    /**
     *
     *
     * @param \Illuminate\Http\Request $request
     * @param $exception
     *
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        Logging::warning(
            'Unauthenticated.',
            [
                'request' => $request,
                'error_stacktrace' => $exception->getTraceAsString()
            ],
            4,
            0
        );
        return response()->json([
            'success' => false,
            'message' => 'Unauthenticated.',
        ], 401);
    }
}
