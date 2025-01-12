--TEST--
runkit7_method_remove() function
--SKIPIF--
<?php
if (!extension_loaded("runkit7") || !RUNKIT7_FEATURE_MANIPULATION) print "skip\n";
if (PHP_VERSION_ID >= 80000) { print "skip\n"; }
?>
--INI--
display_errors=on
--FILE--
<?php
ini_set('error_reporting', E_ALL & (~E_DEPRECATED) & (~E_STRICT));

class runkit_class {
	function runkit_method() {
		echo "Runkit Method\n";
	}
	function runkitMethod() {
		echo "Runkit Method\n";
	}
}

runkit_class::runkit_method();
runkit7_method_remove('runkit_class','runkit_method');
if (!method_exists('runkit_class','runkit_method')) {
	echo "Runkit Method Removed\n";
}
runkit_class::runkitMethod();
runkit7_method_remove('runkit_class','runkitMethod');
if (!method_exists('runkit_class','runkitMethod')) {
	echo "Runkit Method Removed\n";
}
runkit_class::runkitMethod();
?>
--EXPECTF--
Runkit Method
Runkit Method Removed
Runkit Method
Runkit Method Removed

Fatal error: Uncaught Error: Call to undefined method runkit_class::runkitMethod() in %s:%d
Stack trace:
#0 {main}
  thrown in %s on line %d
