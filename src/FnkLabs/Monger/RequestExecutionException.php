<?php
/**
 * Created by PhpStorm.
 * User: ali
 * Date: 07.01.15
 * Time: 16:31
 */

namespace FnkLabs\Monger;

class RequestExecutionException extends MongerException
{
    public function __construct($message = "", $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}