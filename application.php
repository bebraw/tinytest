#!/usr/bin/php
<?php

$author = "Juho Vepsäläinen";
$programName = "TinyTest";
$version = "0.05";

require("argument.php");
require("stringutils.php");
require("test.php");
require("utils.php");

$tests = findTests();

$application = new Application();
$application->run();

class Application {
    public function __construct() {
        setup_assert();
    }
    public function run() {
        global $argv;

        if( !findArguments($argv) ) {
            global $tests;
            
            runTests($tests);
        }
    }
}
?>
