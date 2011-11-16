<?php
require("vector.php");

class TestVector {
    function testCreate() {
        $vector = new Vector(0.0, 0.0, 0.0, 0.0);

        assert("$vector->x == 0");
        assert("$vector->y == 0");
        assert("$vector->z == 0");
        assert("$vector->w == 0");
    }
}

?>
