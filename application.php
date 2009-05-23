#!/usr/bin/php
<?php
/**
 *    TinyTest - test runner
 *    Copyright (C) 2009 Juho Vepsäläinen
 *
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *   This program is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

$author = "Juho Vepsäläinen";
$programName = "TinyTest";
$version = "0.25";
$year = 2009;

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
            $matchingArgs->run();
        }
        else {
            $matchingArgs = inArguments("filename", $args);

            if( $matchingArgs->found ) {
                $matchingArgs->run();
            }
            else {
                runTests($this->tests);
            }
            
            $matchingArgs = inArguments("loop", $args);
            $matchingArgs->run();
        }

        print "quit\n";
    }
}
?>
