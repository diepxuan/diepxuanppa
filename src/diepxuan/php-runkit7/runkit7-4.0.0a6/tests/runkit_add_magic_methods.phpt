--TEST--
adding and removing magic methods
--INI--
; Serializable interface is deprecated
error_reporting=E_ALL & ~E_DEPRECATED
--SKIPIF--
<?php if(!extension_loaded("runkit7") || !RUNKIT7_FEATURE_MANIPULATION) print "skip";
?>
--FILE--
<?php
class Test implements Serializable {
	function serialize() {}
	function unserialize($s) {}
}

class FOO_test extends test {
}

class FOO_test_Child extends FOO_test {
}

runkit7_method_add("Test", "__construct", "", 'echo "__construct\n";');
runkit7_method_add("Test", "__destruct", "", 'echo "__destruct\n";');
runkit7_method_add("Test", "__get", "", 'echo "__get\n";');
runkit7_method_add("Test", "__set", "", 'echo "__set\n";');
runkit7_method_add("Test", "__call", "", 'echo "__call\n";');
runkit7_method_add("Test", "__unset", "", 'echo "__unset\n";');
runkit7_method_add("Test", "__isset", "", 'echo "__isset\n";');
runkit7_method_add("Test", "__callStatic", "", 'echo "__callstatic\n";', RUNKIT7_ACC_STATIC);
runkit7_method_add("Test", "__clone", "", 'echo "__clone\n";');
runkit7_method_add("Test", "__tostring", "", 'return "__tostring\n";');
runkit7_method_add("Test", "__debuginfo", "", 'echo "__debuginfo\n"; ob_start(); return array();');
runkit7_method_redefine("Test", "serialize", "", 'echo "serialize\n";return "";');
runkit7_method_redefine("Test", "unserialize", "", 'echo "unserialize\n";');
$a = new test;
$b = new foo_test;
$c = new FOO_test_Child;
$a->test;
$b->test;
$c->test;
$a->test = 1;
$b->test = 2;
$c->test = 3;
$a->method();
$b->method();
$c->method();
unset($a->test);
unset($b->test);
unset($c->test);
isset($a->test);
isset($b->test);
isset($c->test);
$s1 = serialize($a);
$s2 = serialize($b);
$s3 = serialize($c);
$ua = unserialize($s1);
$ub = unserialize($s2);
$uc = unserialize($s3);
$ca = clone $a;
$cb = clone $b;
$cc = clone $c;
Test::method();
FOO_Test::method();
FOO_Test_child::method();

echo $a;
echo $b;
echo $c;

var_dump($a);
ob_end_clean();
var_dump($b);
ob_end_clean();
var_dump($c);
ob_end_clean();
$a = NULL;
$b = NULL;
$c = NULL;


runkit7_method_remove("Test", "__construct");
runkit7_method_remove("Test", "__destruct");
runkit7_method_remove("Test", "__get");
runkit7_method_remove("Test", "__set");
runkit7_method_remove("Test", "__unset");
runkit7_method_remove("Test", "__isset");
runkit7_method_remove("Test", "__clone");
runkit7_method_remove("Test", "__tostring");
runkit7_method_remove("Test", "__debuginfo");
echo "after removing\n";

$a = new test;
$b = new foo_test;
$c = new FOO_test_Child;
$a->test;
$b->test;
$c->test;
$a->test = 1;
$b->test = 2;
$c->test = 3;
unset($a->test);
unset($b->test);
unset($c->test);
isset($a->test);
isset($b->test);
isset($c->test);
$ca = clone $a;
$cb = clone $b;
$cc = clone $c;
var_dump($a);
var_dump($b);
var_dump($c);
$a = NULL;
$b = NULL;
$c = NULL;
?>
--EXPECTF--
__construct
__construct
__construct
__get
__get
__get
__set
__set
__set
__call
__call
__call
__unset
__unset
__unset
__isset
__isset
__isset
serialize
serialize
serialize
unserialize
unserialize
unserialize
__clone
__clone
__clone
__callstatic
__callstatic
__callstatic
__tostring
__tostring
__tostring
__debuginfo
__debuginfo
__debuginfo
__destruct
__destruct
__destruct
after removing

%s: Undefined property: %s in %s on line %d

%s: Undefined property: %s in %s on line %d

%s: Undefined property: %s in %s on line %d
object(Test)#3 (0) {
}
object(FOO_test)#2 (0) {
}
object(FOO_test_Child)#1 (0) {
}
