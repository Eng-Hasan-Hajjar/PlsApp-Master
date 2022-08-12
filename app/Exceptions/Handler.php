<?php

namespace App\Exceptions;

use Exception;
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
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Exception
     */
    public function render($request, Exception $exception)
    {
        if($request->is('api/*')){

            if (
                  $request->is('api/VisLogIn')
                ||$request->is('api/VisRegister')
                ||$request->is('api/CategoryAll/*')
                ||$request->is('api/CategoryOne/*')
                ||$request->is('api/ServiceAll/*')
                ||$request->is('api/ServiceOne/*')
                ||$request->is('api/ServiceByCat/*')
                &&  $request->expectsJson()
            ){
            //Do Nothing
            } 
            
            else{
                if ($exception instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
                
                    return response()->json(['err',['err'=>'3','message' => 'TokenInvalidErr']],401);
                }
                else{
                
                    return response()->json(['err',['err'=>'0','message' => 'UnauthorizedErr']], 401);
                }
                if ($exception instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
            
                    return response()->json(['err',['err'=>'2','message' => 'TokenExpiredErr']],401);
                }
            }
        }
        return parent::render($request, $exception);
    }
}
