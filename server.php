<?php
require 'vendor/autoload.php';
require 'ConnectionPool.php';

use React\Socket\ConnectionInterface;
use React\Stream\ReadableResourceStream;

$loop = React\EventLoop\Factory::create();
$socket = new React\Socket\Server('127.0.0.1:8000', $loop);
$pool = new ConnectionsPool();
$socket->on('connection', function (ConnectionInterface $connection) use ($pool) {
    $pool->add($connection);
});
echo "Listening on {$socket->getAddress()}\n";

$input = new ReadableResourceStream(STDIN, $loop);
$output = new \React\Stream\WritableResourceStream(STDOUT, $loop);

$connector = new Connector($loop);
$connector->connect('127.0.0.1:8000')
    ->then(
        function (ConnectionInterface $connection) use ($input, $output) {
            $input->pipe($connection)->pipe($output);
        },
        function (Exception $exception) {
            echo $exception->getMessage() . PHP_EOL;
        }
    );

$loop->run();
