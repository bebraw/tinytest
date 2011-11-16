<?php
function initializeAssert() {
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

function loadClassesAndFunctions( $file ) {
    $prev_classes = get_declared_classes();
    $prev_funcs = get_defined_functions();
    $prev_funcs = $prev_funcs["user"];

    require($file);

    $cur_classes = get_declared_classes();
    $cur_funcs = get_defined_functions();
    $cur_funcs = $cur_funcs["user"];

    $added_classes = array_diff($cur_classes, $prev_classes);
    $added_funcs = array_diff($cur_funcs, $prev_funcs);

    return array($added_classes, $added_funcs);
}
?>
