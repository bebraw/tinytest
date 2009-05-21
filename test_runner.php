#!/usr/bin/php
<?php

$author = "Juho Vepsäläinen";
$possibleArgs = array(new Help(), new Loop(), );
$programName = "TinyTest";
$version = "0.01";

setup_assert();

$fileLoader = new FileLoader();
$argumentChecker = new ArgumentChecker();
$argumentChecker->checkArguments($argv);

if( !$argumentChecker->foundArguments ) {
	$testFinder = new TestFinder();
	$testFinder->findTests();
	$testFinder->executeTests();
}

function setup_assert() {
	error_reporting(E_ALL | E_STRICT);
	
	function assert_callcack($file, $line, $message) {
    		throw new Exception();
	}
	
	assert_options(ASSERT_ACTIVE,     1);
	assert_options(ASSERT_WARNING,    0);
	assert_options(ASSERT_BAIL,       0);
	assert_options(ASSERT_QUIET_EVAL, 0);
	assert_options(ASSERT_CALLBACK,   'assert_callcack');
}

class FileLoader {
	public function loadFunctions( $file ) {
		$prev_funcs = get_defined_functions();
		$prev_funcs = $prev_funcs["user"];
		
		require($file);
		
		$cur_funcs = get_defined_functions();
		$cur_funcs = $cur_funcs["user"];
		
		$added_funcs = array_diff($cur_funcs, $prev_funcs);
		
		return $added_funcs;
	}
}

class TestFinder {
	private $tests = array();
	
	public function findTests() {
		$dir = dirname(__FILE__);
		$dirHandler = opendir($dir);
		
		while( $fileName = readdir($dirHandler) ) {
			if( preg_match("/_test.php$/", $fileName) ) {
				$this->tests[] = new TestFile($fileName);
			}
		}
		
		closedir($dirHandler);
	}
	
	public function executeTests() {
		$testsRun = 0;
		
		foreach( $this->tests as $test ) {
			print $test->fileName . " tests:\n";
			$testsRun += $test->execute();
		}
		
		print "Executed " . $testsRun . " tests in total.\n";
	}
}

class TestFile {
	public $fileName;
	private $tests;
	
	public function __construct( $fileName ) {
		$this->fileName = $fileName;
	}
	
	public function execute() {
		$this->loadTests();
		
		$testsRun = 0;
			
		foreach( $this->tests as $test ) {
			print "  " . $test . " ";
			try {
				$test();
				print "OK";
			} catch (Exception $e) {
				print "FAILED " . $e->getMessage();
			}
			print "\n";
			$testsRun++;
		}
		
		print "  " . "Executed " . $testsRun . " tests.\n";
		
		return $testsRun;
	}
	
	private function loadTests() {
		global $fileLoader;
		
		$functions = $fileLoader->loadFunctions($this->fileName);
		$tests = array();
		
		foreach( $functions as $function ) {
			if( preg_match("/^test_/", $function) ) {
				$tests[] = $function;
			}
		}
		
		$this->tests = $tests;
	}
}

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
