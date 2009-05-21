<?php
$possibleArgs = array(new File(), new Help(), new Loop(), );

function findArguments( $args ) {
    $foundArgs = array();

    foreach( $args as $arg ) {
        global $possibleArgs;

        foreach( $possibleArgs as $possibleArg ) {
            if( $possibleArg->matches($arg) ) {
                $foundArgs[] = $possibleArg;
            }
        }
    }

    foreach( $foundArgs as $foundArg ) {
        $foundArg->run();
    }

    if( count($foundArgs) > 0 ) {
        return true;
    }
}

abstract class Argument {
    protected $callName;
    public $helpText;
    public $renderable = true;

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

# TODO: make this work if only test file is passed as arg!
class File extends Argument {
    public $renderable = false;

    public function run() {}

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
    protected $callName = "help";
    public $helpText = "Shows all available arguments.";

    public function run() {
        global $author, $possibleArgs, $programName, $version;

        print $programName . " " . $version . " by " . $author . ".\n\n";
        print "Possible arguments:\n";
        foreach( $possibleArgs as $possibleArg ) {
            if ( $possibleArg->renderable ) {
                $callNames = $possibleArg->getCallNames();
                $callNameStr = str_pad($callNames[0] . ", " . $callNames[1], 16, " ", STR_PAD_LEFT);

                print $callNameStr . " - " . $possibleArg->helpText . "\n";
            }
        }
    }
}

class Loop extends Argument {
    protected $callName = "loop";
    public $helpText = "Execute tests automatically as files are changed.";

    public function run() {
        global $tests;

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
