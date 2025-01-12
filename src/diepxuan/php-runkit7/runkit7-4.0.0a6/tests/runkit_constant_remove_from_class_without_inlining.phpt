--TEST--
runkit7_constant_remove() function removes constant from class (Easier to do: no inlined constants)
--POST--
x=1
--SKIPIF--
<?php
	if(!extension_loaded("runkit7") || !RUNKIT7_FEATURE_MANIPULATION) print "skip";
?>
--FILE--
<?php
// If the compiler knows what TestClass will be, it would be able to
// optimize out the opcode FETCH_CONSTANT and replace it with SEND_VAL 'foo'
if ($_POST['x'] == 1) {
	class TestClass {
		const FOO = 'foo';
	}
} else {
	class TestClass {
		const FOO = 'bar';
	}
}

$const = 'TestClass::FOO';
var_dump($const);
runkit7_constant_remove($const);
var_dump($const);
var_dump(TestClass::FOO);
?>
--EXPECTF--
string(14) "TestClass::FOO"
string(14) "TestClass::FOO"

Fatal error: Uncaught Error: Undefined %Sconstant %sFOO%S in %s:%d
Stack trace:
#0 {main}
  thrown in %s on line %d
