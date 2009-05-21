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
	$tests = $testFinder->findTests();
	
	$testRunner = new TestRunner($tests);
	$testRunner->runTests();
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
	public function findTests() {
		$dir = dirname(__FILE__);
		$dirHandler = opendir($dir);
		$tests = array();
		
		while( $fileName = readdir($dirHandler) ) {
			if( preg_match("/_test.php$/", $fileName) ) {
				$tests[] = new TestFile($fileName);
			}
		}
		
		closedir($dirHandler);
		
		return $tests;
	}
}

class TestRunner {
	private $tests;
	
	public function __construct( $tests ) {
		$this->tests = $tests;
	}
	
	public function runTests() {
		$testsPassed = 0;
		$testsRun = 0;
		
		foreach( $this->tests as $test ) {
			print $test->fileName . " tests:\n";
			$test->run();
			$testsRun += $test->testsRun;
			$testsPassed += $test->testsPassed;
		}
		
		print "SUMMARY: " . $testsPassed . "/" . $testsRun . " tests passed.\n";
	}
}

class TestFile {
	public $fileName;
	public $testsPassed;
	public $testsRun;
	private $tests;
	
	public function __construct( $fileName ) {
		$this->fileName = $fileName;
	}
	
	public function run() {
		$this->loadTests();
		$this->testsPassed = 0;
		$this->testsRun = 0;
		
		foreach( $this->tests as $test ) {
			$testStr = "  " . $test . " ";
			print $testStr;
			
			$testStrLen = strlen($testStr);
			print $this->getNchars(30-$testStrLen, " ");
			
			try {
				$test();
				print "OK";
				$this->testsPassed++;
			} catch (Exception $e) {
				print "FAILED " . $e->getMessage();
			}
			print "\n";
			$this->testsRun++;
		}
		
		print "  " . "Executed " . $this->testsRun . " tests.\n\n";
	}
	
	# XXX: to utils!
	private function getNchars( $n, $char ) {
		$ret = "";
		
		for($i=0; $i<$n; $i++) {
			$ret .= $char;
		}
		
		return $ret;
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
