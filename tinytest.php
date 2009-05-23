#!/usr/bin/php
<?php
/**
 *    TinyTest - test runner
 *    Copyright (C) 2009 Juho Vepsäläinen
 *
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *   This program is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
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
