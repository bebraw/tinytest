<?php
function getNchars( $n, $char ) {
    $ret = "";

    for($i=0; $i<$n; $i++) {
        $ret .= $char;
    }

    return $ret;
}
?>