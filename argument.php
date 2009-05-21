<?php

$possibleArgs = array(new File(), new Help(), new Loop(), );

function findArguments( $args ) {
    $foundArguments = false;

    foreach( $args as $arg ) {
        global $possibleArgs;

        foreach( $possibleArgs as $possibleArg ) {
            if( $possibleArg->matches($arg) ) {
                $possibleArg->run();
                $foundArguments = true;
                break;
            }
        }
    }

    return $foundArguments;
}

abstract class Argument {
    protected $callName;
    public $helpText;

    abstract public function run();

    public function getCallNames() {
        return array("-" . $this->callName[0], "--" . $this->callName);
    }

    public function matches( $argument ) {
        if( !$this->callName ) {
            return false;
        }

        $callNames = $this->getCallNames();

        foreach( $callNames as $callName ) {
            if( $argument == $callName ) {
                return true;
            }
        }
    }
}

class File extends Argument {
    protected $callName = NULL;
    public $helpText = NULL;
    private $test;

    public function run() {
        $this->test->run(); # won't work with loop this way! rethink
    }

    public function matches( $argument ) {
        global $tests;

        $this->test = findFileInTests($argument, $tests);

        if( $this->test ) {
            return true;
        }
    }
}

class Help extends Argument {
    protected $callName = "help";
    public $helpText = "Shows all available arguments.";

    public function run() {
        global $author, $possibleArgs, $programName, $version;

        print $programName . " " . $version . " by " . $author . ".\n\n";
        print "Possible arguments:\n";
        foreach( $possibleArgs as $possibleArg ) {
            $callNames = $possibleArg->getCallNames();
            $callNameStr = str_pad($callNames[0] . ", " . $callNames[1], 16, " ", STR_PAD_LEFT);

            print $callNameStr . " - " . $possibleArg->helpText . "\n";
        }
    }
}

class Loop extends Argument {
    protected $callName = "loop";
    public $helpText = "Execute tests automatically as files are changed.";

    public function run() {
        $tests = findTests();
        runTests($tests);

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
