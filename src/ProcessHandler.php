<?php


namespace processor;

/**
 * Class ProcessHandler
 * @package processor
 */
abstract class ProcessHandler
{
    protected $workIndex = 1;

    protected $maxProcess = 1;

    /** @var array $initData */
    private $initData = [];

    /**
     * ProcessHandler constructor.
     * @param int $maxProcess
     */
    public function __construct(int $maxProcess)
    {
        $this->maxProcess = $maxProcess;
    }

    /**
     * 获取最大配置数
     * @return int
     */
    public function getMaxProcess(): int
    {
        return $this->maxProcess;
    }

    /**
     * @param int $workIndex workIndex
     * @return $this
     */
    public function setWorkIndex(int $workIndex)
    {
        $this->workIndex = $workIndex;
        return $this;
    }

    /**
     * @param int $workIndex
     * @param mixed $data
     * @return void
     */
    final public function setData(int $workIndex, $data): void
    {
        $this->initData[$workIndex] = $data;
    }

    /**
     * @return mixed
     */
    final public function getData()
    {
        return $this->initData[$this->workIndex];
    }

    /**
     * @return mixed
     */
    abstract public function handle();

    /**
     * @return void
     * @throws ProcessException
     */
    final public function run()
    {
        (new Processor($this))->run();
    }
}
