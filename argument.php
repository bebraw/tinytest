<?php

$possibleArgs = array(new Help(), new Loop(), );

class ArgumentChecker {
    public $foundArguments;
    
    public function checkArguments( $args ) {
        foreach( $args as $arg ) {
            global $possibleArgs;

            foreach( $possibleArgs as $possibleArg ) {
                if( $possibleArg->matches($arg) ) {
                    $this->foundArguments = true;
                    $possibleArg->execute();
                    break;
                }
            }
        }

        $this->foundArguments = false;
    }
}

abstract class Argument {
    protected $callName;
    public $helpText;

    abstract public function execute();

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

class Help extends Argument {
    protected $callName = "help";
    public $helpText = "Shows all available arguments.";

    public function execute() {
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

    public function execute() {
        print 'should do the loop thingy now';
    }
}
?>
