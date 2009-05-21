#!/usr/bin/php
<?php

$author = "Juho Vepsäläinen";
$programName = "TinyTest";
$version = "0.02";

require("argument.php");
require("utils.php");

$application = new Application();
$application->run();

class Application {
    public function __construct() {
        setup_assert();
    }
    public function run() {
        $argumentChecker = new ArgumentChecker();
        $argumentChecker->checkArguments($argv);

        if( !$argumentChecker->foundArguments ) {
            runTests(findTests());
        }
    }
}
?>
