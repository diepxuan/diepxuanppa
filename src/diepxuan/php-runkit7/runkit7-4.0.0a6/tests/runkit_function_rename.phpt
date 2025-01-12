--TEST--
runkit7_function_rename() function
--SKIPIF--
<?php if(!extension_loaded("runkit7") || !RUNKIT7_FEATURE_MANIPULATION) print "skip"; ?>
--FILE--
<?php
function runkitSample($n) {
	echo "Runkit Sample: $n\n";
}

$oldName = 'runkitSample';
$newName = 'runkitNewName';
runkitSample(1);
runkit7_function_rename($oldName, $newName);
if (function_exists('runkitSample')) {
	echo "Old function name still exists!\n";
}
runkitNewName(2);
echo $oldName, "\n";
echo $newName, "\n";

runkitSample(2);
?>
--EXPECTF--
Runkit Sample: 1
Runkit Sample: 2
runkitSample
runkitNewName

Fatal error: Uncaught Error: Call to undefined function runkit%sample() in %s:%d
Stack trace:
#0 {main}
  thrown in %s on line %d
