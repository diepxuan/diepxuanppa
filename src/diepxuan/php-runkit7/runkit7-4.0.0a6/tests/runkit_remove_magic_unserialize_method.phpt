--TEST--
removing magic unserialize method
--INI--
; Suppress Serializable deprecation
error_reporting=E_ALL&~E_DEPRECATED
--SKIPIF--
<?php if(!extension_loaded("runkit7") || !RUNKIT7_FEATURE_MANIPULATION) print "skip";
?>
--FILE--
<?php
class Test implements Serializable {
	function serialize() {return "";}
	function unserialize($s) {}
}

$a = new Test();
runkit7_method_remove("Test", "unserialize");
$s1 = serialize($a);
unserialize($s1);
?>
--EXPECTF--
Fatal error: Couldn't find implementation for method Test::unserialize in Unknown on line %d
