<?php
require('mathutils.php');

function test_sum() {
    assert("sum(3, 5) == 8");
    assert("sum(1, 1) == 2");
}

function test_sum_and_fail() {
    assert("sum(1, 1) == 2");
}
?>
