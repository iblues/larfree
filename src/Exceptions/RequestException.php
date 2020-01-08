<?php
/**
 * 用于参数异常的时候的报错
 * User: Blues
 * Date: 2020/1/8
 * Time: 11:22 AM
 */

namespace Larfree\Exceptions;


class RequestException extends ApiException
{
    public function __construct($message = "", $data = [], $code = 422, Throwable $previous = null)
    {
        parent::__construct($message, $data, $code, $previous);
    }

}
