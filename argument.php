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

$possibleArgs = array(new File(), new Help(), new Loop(), );

function initializePossibleArgs( $tests ) {
    global $possibleArgs;

    foreach( $possibleArgs as $possibleArg ) {
        $possibleArg->tests = $tests;
    }
}

function constructArguments( $args ) {
    $foundArgs = array();

    foreach( $args as $arg ) {
        global $possibleArgs;
        
        foreach( $possibleArgs as $possibleArg ) {
            if( $possibleArg->matches($arg) ) {
                $foundArgs[] = $possibleArg;
            }
        }
    }

    return $foundArgs;
}

function inArguments( $name, $args ) {
    $matchingArgs = array();

    foreach( $args as $arg ) {
        if( $name == $arg->callName ) {
            $matchingArgs[] = $arg;
        }
    }

    return new MatchingArguments( $matchingArgs );
}

class MatchingArguments {
    public $found;
    private $args;

    public function __construct( $args ) {
        $this->args = $args;
        $this->found = count($this->args) > 0;
    }

    public function run() {
        foreach( $this->args as $arg ) {
            $arg->run();
        }
    }
}

abstract class Argument {
    public $callName;
    public $helpText;
    public $renderable = true;
    public $tests;

    abstract public function run();

    public function getCallNames() {
        return array("-" . $this->callName[0], "--" . $this->callName);
    }

    public function matches( $argument ) {
        $callNames = $this->getCallNames();

        foreach( $callNames as $callName ) {
            if( $argument == $callName ) {
                return true;
            }
        }
    }
}

class File extends Argument {
    public $callName = 'filename';
    public $renderable = false;

    public function run() {
        runTests($this->tests);
    }

    public function matches( $argument ) {
        $test = findFileInTests($argument, $this->tests);
        
        if( $test ) {
            $this->tests = array($test);
            return true;
        }
    }
}

class Help extends Argument {
    public $callName = "help";
    public $helpText = "Shows all available arguments.";

    public function run() {
        global $author, $programName, $version, $year, $possibleArgs;
        $emptyArea = getNchars(4, ' ');

        print $programName . " " . $version . " Copyright (C) " . $year . " " . $author . "\n\n";
        print "Usage:\n";
        print $emptyArea . "'tinytest.py <arguments> <filename>'\n";
        print $emptyArea . "'tinytest.py <filename>' (Passing multiple filenames works too.)\n";
        print $emptyArea . "'tinytest.py'\n\n";
        print "Note that test files must be named using 'test' suffix (ie. utils_test.php).\n";
        print "Test functions contained in the test files must have 'test' prefix (ie. test_sum).\n";
        print "Test classes must be named using 'Test' prefix (ie. TestVector). Methods must be \n";
        print "named in the same manner as functions.\n\n";
        
        print "Possible arguments:\n";
        foreach( $possibleArgs as $possibleArg ) {
            if ( $possibleArg->renderable ) {
                $callNames = $possibleArg->getCallNames();
                $callNameStr = $emptyArea . $callNames[0] . ", " . $callNames[1];

                print $callNameStr . " - " . $possibleArg->helpText . "\n";
            }
        }
    }
}

class Loop extends Argument {
    public $callName = "loop";
    public $helpText = "Executes tests automatically as tests are changed.";

    public function run() {
        while(true) {
            sleep(1);
            if( $this->testsHaveChanged( $this->tests ) ) {
                exit(); # tinytest.py makes sure that the script gets run again!
            }
        }
    }

    private function testsHaveChanged( $tests ) {
        foreach( $tests as $test ) {
            if( $test->hasBeenModified() == true ) {
                return true;
            }
        }
    }
}
?>
