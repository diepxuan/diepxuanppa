--TEST--
runkit7_constant_redefine() function
--SKIPIF--
<?php if(!extension_loaded("runkit7") || !RUNKIT7_FEATURE_MANIPULATION) print "skip"; ?>
--FILE--
<?php
define('FOO', "The FOO constant\n");
echo FOO;
runkit7_constant_redefine('FOO', "The constant known as FOO\n");
echo FOO;
?>
--EXPECT--
The FOO constant
The constant known as FOO
