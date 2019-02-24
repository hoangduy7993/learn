<?php
    require 'vendor/autoload.php';
    $counter = 0 ;
    $loop = React\EventLoop\Factory::create();
    $loop->addPeriodicTimer(1, function(\React\EventLoop\TimerInterface $timer) use (&$counter, $loop){
        $counter ++;
        if($counter == 10){
            $loop->cancelTimer($timer);
        };
        echo "Hello";
    });
    $loop->addTimer(1, function () {
        sleep(5);
    });
    $loop->run();