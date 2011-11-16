<?php
require("mathutils.php");

function testSum() {
    assert("sum(3, 5) == 8");
    assert("sum(1, 1) == 2");
}

function testSumAndFail() {
    assert("sum(1, 1) == 3");
}
?>
