<?php

namespace Larfree\Exceptions;
use Exception;

class SchemasException extends Exception
{
    protected $data=[];
//
    public function __construct($message = "",$data=[], $code = 500,Throwable $previous = null)
    {
        $this->data=$data;
        parent::__construct($message, $code, $previous);
    }

//    public function render($request)
//    {
//        $msg = $this->getMessage();
//        $code = $this->getCode();
//        $status = 0;
//        if($code=='401'){
//            $status=-10;
//        }
//        return (new ApiResource(  collect($this->data)  ))
//             ->additional(['code' =>$code,'status'=>$status,'msg'=>$msg]);
//    }

}
