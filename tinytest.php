#!/usr/bin/php
<?php
require("argument.php");

$argv = $argv or array();
$argsStr = argsToString($argv);

function argsToString( $argv ) {
    $argvSlice = array_slice($argv, 1);
    return implode(" ", $argvSlice);
}

$loopArgument = new LoopArgument();
if($loopArgument->inArguments($argv)) {
    while(true) {
        runScript($argsStr);
    }
}
else {
    runScript($argsStr);
}

function runScript( $argsStr) {
    system("./application.php " . $argsStr);
}
?>
