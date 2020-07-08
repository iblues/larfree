<?php

namespace Larfree\Exceptions;

use Exception;
use Larfree\Resources\ApiResource;
use Throwable;

class ApiException extends Exception
{
    protected $data = [];

    public function __construct($message = "", $data = [], $code = 400, Throwable $previous = null)
    {
        $this->data = $data;
        parent::__construct($message, $code, $previous);
    }

    public function getData()
    {
        return $this->data;
    }

    public function render($request)
    {
        $msg    = $this->getMessage();
        $code   = $this->getCode();
        $status = 0;
        //后期会取消data中的内容
        return (new ApiResource(collect($this->data)))
            ->additional(['error' => $this->data, 'code' => $code, 'status' => $status, 'msg' => $msg]);
    }

}
