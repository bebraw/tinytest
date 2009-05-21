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
?>
