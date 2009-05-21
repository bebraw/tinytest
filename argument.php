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
    foreach( $args as $arg ) {
        if( $name == $arg->callName ) {
            return $arg;
        }
    }

    return new Argument();
}

class Argument {
    public $callName;
    public $helpText;
    public $renderable = true;

    public function run() {}

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

    public function matches( $argument ) {
        global $tests;

        $test = findFileInTests($argument, $tests);
        
        if( $test ) {
            $tests = array($test);
            return true;
        }
    }
}

class Help extends Argument {
    public $callName = "help";
    public $helpText = "Shows all available arguments.";

    public function run() {
        global $author, $possibleArgs, $programName, $version, $year;
        $emptyArea = getNchars(4, ' ');

        print $programName . " " . $version . " Copyright (C) " . $year . " " . $author . "\n\n";
        print "Usage:\n";
        print $emptyArea . "'tinytest.py <arguments> <filename>'\n";
        print $emptyArea . "'tinytest.py <filename>'\n";
        print $emptyArea . "'tinytest.py'\n\n";
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
        global $tests;

        while(true) {
            sleep(1);
            if( $this->testsHaveChanged( $tests ) ) {
                exit(); # tinytest.py makes sure that the script gets run again!
            }
        }
    }

    private function testsHaveChanged( $tests ) {
        $testsChanged = false;

        foreach( $tests as $test ) {
            if( $test->hasBeenModified() == true ) {
                $test->updateModificationTime();
                $testsChanged = true;
            }
        }

        return $testsChanged;
    }
}
?>
