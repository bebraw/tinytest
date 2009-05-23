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

function findTests( $dir=NULL ) { # this can be run only once as TestFiles load functions!
    if( !$dir) {
        $dir = dirname(__FILE__);
    }

    $dirHandler = opendir($dir);
    $tests = array();

    while( $fileName = readdir($dirHandler) ) {
        if( $fileName != "." and $fileName != ".." and is_dir($fileName) ) {
            $tests = array_merge($tests, findTests($dir . "/" . $fileName));
        }
        else if( preg_match("/_test.php$/", $fileName) ) {
                $tests[] = new TestFile($dir . "/" . $fileName);
            }
    }

    closedir($dirHandler);

    return $tests;
}

function findFileInTests( $file, $tests ) {
    foreach( $tests as $test ) {
        if( $file == $test->getFilename() ) {
            return $test;
        }
    }
}

function runTests( $tests ) {
    $testsPassed = 0;
    $testsRun = 0;

    foreach( $tests as $test ) {
        $test->run();
        $testsRun += $test->testsRun;
        $testsPassed += $test->testsPassed;
        print "\n";
    }

    print "SUMMARY: " . $testsPassed . "/" . getTestAmount($testsRun)  . " passed.\n";
}

class File {
    public $file; /* whole path to file and the filename */
    private $lastModificationTime;

    public function __construct( $file ) {
        $this->file = $file;
        $this->lastModificationTime = $this->getModificationTime();
    }

    public function getFilename() {
        return substr(strrchr($this->file, "/"), 1);
    }

    public function hasBeenModified() {
        clearstatcache();
        return $this->lastModificationTime != $this->getModificationTime();
    }

    private function getModificationTime() {
        return filemtime($this->file);
    }
}

class TestFile extends File {
    public $testsPassed;
    public $testsRun;
    public $tests;

    public function __construct( $file ) {
        parent::__construct($file);
        $this->loadTests();
    }

    public function run() {
        $this->testsPassed = 0;
        $this->testsRun = 0;

        print $this->getFilename() . " tests:\n";

        foreach( $this->tests as $test ) {
            $testStr = "  " . $test->name . " ";
            print $testStr;

            $testStrLen = strlen($testStr);
            print getNchars(30-$testStrLen, " ");

            try {
                $test->run();
                print "OK";
                $this->testsPassed++;
            } catch (Exception $e) {
                print "FAILED " . $e->getMessage();
            }
            print "\n";
            $this->testsRun++;
        }

        print "  " . "Executed " . getTestAmount($this->testsRun) . ".\n";
    }

    private function loadTests() {
        $this->tests = array();
        list ($classes, $functions) = loadClassesAndFunctions($this->file);

        foreach( $classes as $class ) {
            if( preg_match("/^Test/", $class) ) {
                $methods = get_class_methods($class);
                foreach( $methods as $method ) {
                    if( preg_match("/^test_/", $method) ) {
                        $this->tests[] = new TestMethod($method, $class);
                    }
                }
            }
        }
        
        foreach( $functions as $function ) {
            if( preg_match("/^test_/", $function) ) {
                $this->tests[] = new TestFunction($function);
            }
        }
    }
}

abstract class Test {
    public $name;

    public function __construct( $name ) {
        $this->name = $name;
    }

    abstract public function run();
}

class TestFunction extends Test {
    public function run() {
        $function = $this->name;

        $function();
    }
}

class TestMethod extends Test {
    private $class;

    public function __construct( $name, $class ) {
        $this->name = $name;
        $this->class = $class;
    }

    public function run() {
        $instance = new $this->class();
        $method = $this->name;

        $instance->$method();
    }
}

function getTestAmount( $amount ) {
    if( $amount == 1) {
        return $amount . " test";
    }

    return $amount . " tests";
}

?>
