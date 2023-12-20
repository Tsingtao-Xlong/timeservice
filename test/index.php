<?php

require "../vendor/autoload.php";

$test = new \Times\TimeService();
$days = $test::getDaysList("2020-01-02", "2023-03-1");
var_dump($days);
exit();