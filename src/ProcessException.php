<?php

namespace processor;

use Exception;
use Throwable;

class ProcessException extends Exception
{
    /**
     * ProcessException constructor.
     * @param int $code code
     * @param string $message message
     * @param Throwable|null $previous throwable
     */
    public function __construct($code = 0, $message = "", Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
