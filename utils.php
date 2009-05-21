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
