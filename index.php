<?php
include './vendor/autoload.php';

$content = vender("test", [
    'name' => "world",
    'msg' => "this is demo",
]);
echo $content . PHP_EOL;