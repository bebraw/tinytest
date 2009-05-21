#!/usr/bin/php
<?php

$author = "Juho Vepsäläinen";
$programName = "TinyTest";
$version = "0.07";

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
        $args = constructArguments($argv);

        $arg = inArguments("help", $args);
        if( $arg->callName == "help" ) {
            $arg->run();
        }
        else {
            $arg = inArguments("filename", $args);
            $arg->run();

            print "running tests\n";
            global $tests;
            runTests($tests);

            $arg = inArguments("loop", $args);
            $arg->run();
        }
    }
}
?>
