#!/usr/bin/php
<?php

$author = "Juho Vepsäläinen";
$programName = "TinyTest";
$version = "0.04";

require("argument.php");
require("test.php");
require("utils.php");

$application = new Application();
$application->run();

class Application {
    public function __construct() {
        setup_assert();
    }
    public function run() {
        global $argv;

        if( !findArguments($argv) ) {
            $tests = findTests();
            runTests($tests);
        }
    }
}
?>
