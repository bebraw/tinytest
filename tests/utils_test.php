<?php

function test_initializeAssert() {
    # note that the test runner has already ran this function!

    $e = NULL;
    try {
        assert("false");
    }
    catch (Exception $e) {}

    if( !$e ) {
        assert("false");
    }
}

?>