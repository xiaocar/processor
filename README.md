# 基于Swoole的任务并行处理

## Why

在数据量很大的情况下，我们项目运行脚本很容易出现数据库链接超时，而影响项目的正常导出
所以我们有时候需要并行运行一些脚本，可以减少脚本运行时间

## Requirements

- php >= 7.1
- php-swoole >= 4.2.8

# Install
Via Composer

$ composer require multi/processor


# Usage

example

> 参考 test/test.php 文件

```php
<?php

use processor\MultiJob;

class TestJob extends MultiJob
{
    public function handle()
    {
        echo $this->getData(); // getData获取 每个workId设置的data数据
        sleep(1);
        echo $this->workIndex . PHP_EOL;
        sleep(60);
        echo chr($this->workIndex + 97) . PHP_EOL;
    }
}

$job = new TestJob(100);
$job->init(function ($index){ //每个work进程对应的处理方法
    return "{$index}_abc " . random_int(100, 999) . PHP_EOL;
});
$job->run();


```