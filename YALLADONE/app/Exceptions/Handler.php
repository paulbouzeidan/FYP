<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
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

    public function render ($request, Throwable $exception)
    {
        if($exception instanceof HttpException){
        $code=$exception->getStatusCode();
        $message=Response::$statusTexts[$code];
        return $this->errorResponse($message, $code);
        }

        if($exception instanceof ModelNotFoundException){
        $model = strtolower (class_basename($exception->getModel()));
        return $this->errorResponse("Does not exist any instaes of {$model} with given id",Response::HTTP_NOT_FOUND) ;
        }

        if ($exception instanceof AuthorizationException){
            return $this->errorResponse($exception->getMessage(),Response::HTTP_FORBIDDEN);
        }
       

        if($exception instanceof AuthenticationException){
        return $this->errorResponse($exception->getMessage(), Response::HTTP_UNAUTHORIZED);
        }

        if($exception instanceof ValidationException){
            $error=$exception->validator->errors()->getMessages();
            return $this->errorResponse($error, Response:: HTTP_UNPROCESSABLE_ENTITY);
        }
        

        if(env( 'APP_DEBUG', false)){
        $this->errorResponse( ' Unexpected error, try later',  Response::HTTP_INTERNAL_SERVER_ERROR);

}


    }

    
}
