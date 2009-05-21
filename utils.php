<?php
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

function loadFunctions( $file ) {
    $prev_funcs = get_defined_functions();
    $prev_funcs = $prev_funcs["user"];

    require($file);

    $cur_funcs = get_defined_functions();
    $cur_funcs = $cur_funcs["user"];

    $added_funcs = array_diff($cur_funcs, $prev_funcs);

    return $added_funcs;
}

function findTests() {
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

function runTests( $tests ) {
    $testsPassed = 0;
    $testsRun = 0;

    foreach( $tests as $test ) {
        print $test->fileName . " tests:\n";
        $test->run();
        $testsRun += $test->testsRun;
        $testsPassed += $test->testsPassed;
    }

    print "SUMMARY: " . $testsPassed . "/" . $testsRun . " tests passed.\n";
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
            print getNchars(30-$testStrLen, " ");

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

    private function loadTests() {
        $functions = loadFunctions($this->fileName);
        $tests = array();

        foreach( $functions as $function ) {
            if( preg_match("/^test_/", $function) ) {
                $tests[] = $function;
            }
        }

        $this->tests = $tests;
    }
}

function getNchars( $n, $char ) {
    $ret = "";

    for($i=0; $i<$n; $i++) {
        $ret .= $char;
    }

    return $ret;
}
?>
