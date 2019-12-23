<?php

namespace processor;

use swoole_process;

class Processor
{
    private $workers = [];
    private $needReboot = false;
    private $newIndex = 1;

    /** @var string $processFormatName runner sprintf name */
    private $processFormatName = 'php-chk-ps:%s';

    /** @var ProcessHandler $handler */
    private $handler;

    /**
     * Processor constructor.
     * @param ProcessHandler $handler handler
     */
    public function __construct(ProcessHandler $handler)
    {
        $this->handler = $handler;
    }

    /**
     * @throws ProcessException
     */
    public function run()
    {
        $this->init();
        try {
            swoole_set_process_name(sprintf($this->processFormatName, 'master'));
            $this->work();
            $this->processWait();
        } catch (\Exception $e) {
            throw new ProcessException(500, 'ALL ERROR: ' . $e->getMessage());
        }
    }

    /**
     * @throws ProcessException
     */
    protected function init()
    {
        if (php_sapi_name() !== 'cli') {
            throw new ProcessException(500, 'must run in cli mode');
        }
        if (empty($this->handler)) {
            throw new ProcessException(500, 'config must exist');
        }
    }

    /**
     * @return void
     */
    protected function work()
    {
        for ($i = 1; $i <= $this->handler->getMaxProcess(); $i++) {
            $this->createProcess($i);
        }
    }

    /**
     * @param int $index workId
     * @return mixed
     */
    protected function createProcess($index = null)
    {
        $process = new swoole_process(function (swoole_process $worker) use ($index) {
            if (is_null($index)) {
                $index = $this->newIndex;
                $this->newIndex++;
            }
            swoole_set_process_name(sprintf($this->processFormatName, $index));
            $this->handler->setWorkIndex($index)->handle();
        }, false, false);
        $pid = $process->start();
        $this->workers[$index] = $pid;
        return $pid;
    }

    /**
     * @param array $ret wait result
     * @return void
     * @throws ProcessException
     */
    protected function rebootProcess(array $ret)
    {
        $pid = $ret['pid'];
        $index = array_search($pid, $this->workers);
        if ($index === false) {
            throw new ProcessException(500, 'rebootProcess Error: no pid');
        }
        $index = intval($index);
        $this->createProcess($index);
    }

    /**
     * 子进程异常退出时，自动重启
     * @return void
     * @throws ProcessException
     */
    protected function processWait()
    {
        while (true) {
            if (!count($this->workers)) {
                break;
            }
            $ret = swoole_process::wait();
            if ($ret && $this->needReboot) {
                $this->rebootProcess($ret);
            } else {
                break;
            }
        }
    }

    /**
     * 设置是否需要重启
     * @param bool $needReboot is need reboot run
     * @return Processor
     */
    public function setNeedReboot(bool $needReboot): Processor
    {
        $this->needReboot = $needReboot;
        return $this;
    }

}
