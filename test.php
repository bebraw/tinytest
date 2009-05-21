<?php
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
            $tests[] = new TestFile($dir, $fileName);
        }
    }

    closedir($dirHandler);

    return $tests;
}

function findFileInTests( $file, $tests ) {
    foreach( $tests as $test ) {
        if( $file == $test->fileName ) {
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

    print "SUMMARY: " . $testsPassed . "/" . $testsRun . " tests passed.\n";
}

class TestFile {
    public $filePath;
    public $fileName;
    public $testsPassed;
    public $testsRun;
    private $lastModificationTime;
    private $tests;

    public function __construct( $filePath, $fileName ) {
        $this->filePath = $filePath;
        $this->fileName = $fileName;
        $this->updateModificationTime();
        $this->loadTests();
    }

    public function hasBeenModified() {
        clearstatcache();
        return $this->lastModificationTime != $this->getModificationTime();
    }

    public function updateModificationTime() {
        $this->lastModificationTime = $this->getModificationTime();
    }

    private function getModificationTime() {
        return filemtime($this->getWholeName());
    }

    private function getWholeName() {
        return $this->filePath . "/" . $this->fileName;
    }

    public function run() {
        $this->testsPassed = 0;
        $this->testsRun = 0;

        print $this->fileName . " tests:\n";

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

        print "  " . "Executed " . $this->testsRun . " tests.\n";
    }

    private function loadTests() {
        $functions = loadFunctions($this->getWholeName());
        $tests = array();

        foreach( $functions as $function ) {
            if( preg_match("/^test_/", $function) ) {
                $tests[] = $function;
            }
        }

        $this->tests = $tests;
    }
}
?>
