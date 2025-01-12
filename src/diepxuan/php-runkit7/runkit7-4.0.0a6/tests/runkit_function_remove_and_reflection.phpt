--TEST--
runkit7_function_remove() function with reflection
--SKIPIF--
<?php if(!extension_loaded("runkit7") || !RUNKIT7_FEATURE_MANIPULATION) print "skip"; ?>
--FILE--
<?php
function runkitFunction($param) {
	echo "Runkit function\n";
}

$reflFunc = new ReflectionFunction('runkitFunction');

runkit7_function_remove('runkitFunction');

var_dump($reflFunc);
$reflFunc->invoke();
?>
--EXPECTF--
object(ReflectionFunction)#%d (1) {
  ["name"]=>
  string(30) "__function_removed_by_runkit__"
}

Fatal error: __function_removed_by_runkit__(): A function removed by runkit7 was somehow invoked in %s on line %d
