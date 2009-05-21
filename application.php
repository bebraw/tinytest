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
$version = "0.15";
$year = 2009;

/**
 * TODO:
 * -make loop work with changes made to tested files too!
 */

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
