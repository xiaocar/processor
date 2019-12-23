<?php

namespace processor;

/**
 * Class MultiJob
 * @package Processor
 */
abstract class MultiJob extends ProcessHandler
{
    /**
     * @param callable $data callback($index)
     * @return $this
     */
    final public function init(callable $data)
    {
        for ($i = 1; $i <= $this->maxProcess; $i++) {
            $this->setData($i, call_user_func($data, $i));
        }
        return $this;
    }

}
