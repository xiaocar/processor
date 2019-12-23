<?php

require_once "vendor/autoload.php";

use processor\MultiJob;

class TestJob extends MultiJob
{
    public function handle()
    {
        echo $this->getData();
        sleep(1);
        echo $this->workIndex . PHP_EOL;
        sleep(60);
        echo chr($this->workIndex + 97) . PHP_EOL;
    }
}

$job = new TestJob(100);
$job->init(function ($index){
    return "{$index}_abc " . random_int(100, 999) . PHP_EOL;
});
$job->run();

//初始化配置 决定用分页还是不分页
// loc