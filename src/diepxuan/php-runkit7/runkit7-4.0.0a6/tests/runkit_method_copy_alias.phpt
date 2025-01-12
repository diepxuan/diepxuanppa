--TEST--
runkit_method_copy() function
--SKIPIF--
<?php if(!extension_loaded("runkit7") || !RUNKIT7_FEATURE_MANIPULATION) print "skip"; ?>
--INI--
display_errors=on
error_reporting=E_ALL
--FILE--
<?php
ini_set('error_reporting', E_ALL);

class runkit_one {
	public static function runkit_method($n) {
		echo "Runkit Method: $n\n";
	}

	public static function runkitMethod($n) {
		echo "Runkit Method: $n\n";
	}
}

class runkit_two {
}

runkit_one::runkit_method(1);
runkit_method_copy('runkit_two','runkit_method','runkit_one');
runkit_method_copy('runkit_two','runkitMethod','runkit_one');
runkit_one::runkit_method(2);
runkit_two::runkit_method(3);
runkit_one::runkitMethod(4);
runkit_two::runkitmethod(5);
runkit_two::runkitMethod(6);
runkit_method_remove('runkit_one','runkit_method');
if (method_exists('runkit_one','runkit_method')) {
	echo "runkit_method still exists in Runkit One!\n";
}
runkit_method_remove('runkit_one','runkitMethod');
if (method_exists('runkit_one','runkitMethod')) {
	echo "runkitMethod still exists in Runkit One!\n";
}
runkit_two::runkit_method(7);
runkit_two::runkitMethod(8);
if (class_exists('ReflectionMethod')) {
	$reflMethod = new ReflectionMethod('runkit_two', 'runkitMethod');
	$declClass = $reflMethod->getDeclaringClass();
	echo $declClass->getName(), "\n";
	echo $reflMethod->getName(), "\n";
} else {
	echo "runkit_two\n";
	echo "runkitMethod\n";
}
?>
--EXPECTF--
Runkit Method: 1

Deprecated: Function runkit_method_copy() is deprecated in %srunkit_method_copy_alias.php on line 18

Deprecated: Function runkit_method_copy() is deprecated in %srunkit_method_copy_alias.php on line 19
Runkit Method: 2
Runkit Method: 3
Runkit Method: 4
Runkit Method: 5
Runkit Method: 6

Deprecated: Function runkit_method_remove() is deprecated in %srunkit_method_copy_alias.php on line 25

Deprecated: Function runkit_method_remove() is deprecated in %srunkit_method_copy_alias.php on line 29
Runkit Method: 7
Runkit Method: 8
runkit_two
runkitMethod