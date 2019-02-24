<?php
    require 'vendor/autoload.php';
    $loop = \React\EventLoop\Factory::create();
    $readable = new \React\Stream\ReadableResourceStream(STDIN, $loop, 8192);
    $readable->on('data', function ($chunk){
        echo $chunk . PHP_EDL;        
    });
    $loop->run();