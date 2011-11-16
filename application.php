#!/usr/bin/php
<?php
$author = "Juho Vepsäläinen";
$programName = "TinyTest";
$version = "0.26";
$year = 2011;

require("argument.php");
require("stringutils.php");
require("test.php");
require("utils.php");

$tests = findTests();
$application = new Application( $tests );
$application->run();

class Application {
    private $tests;

    public function __construct( $tests ) {
        $this->tests = $tests;
        initializeAssert();
        initializePossibleArgs( $this->tests );
    }

    public function run() {
        global $argv;

        $args = constructArguments($argv);
        $matchingArgs = inArguments("help", $args);
        if( $matchingArgs->found ) {
            $matchingArgs->runOnce();
        }
        else {
            $matchingArgs = inArguments("filename", $args);

            if( $matchingArgs->found ) {
                $matchingArgs->run();
            }
            else {
                $this->tests->run();
            }
            
            $matchingArgs = inArguments("loop", $args);
            $matchingArgs->runOnce();
        }

        print "quit\n";
    }
}
?>
