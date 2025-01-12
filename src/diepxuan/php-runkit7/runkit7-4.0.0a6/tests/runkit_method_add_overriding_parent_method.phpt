--TEST--
runkit7_method_add() function
--SKIPIF--
<?php
if(!extension_loaded("runkit7") || !RUNKIT7_FEATURE_MANIPULATION) print "skip\n";
if(PHP_VERSION_ID >= 80000) print "skip using parent in class with no parent\n";
?>
--INI--
display_errors=on
error_reporting = E_ALL & ~E_DEPRECATED
--FILE--
<?php
// TODO: Write an alternative test version that isn't an Error in php 8.0-dev
class Class0 {
    function method3() {
        parent::method3();
        echo "method3_new\n";
    }
}

class Class1 {
    function method1()
    {
        echo "method1\n";
    }
    function method2()
    {
        echo "method2\n";
    }
    function method3()
    {
        echo "method3\n";
    }
}
class Class2 extends Class1 {
}

$c = new Class2();
runkit7_method_add('Class2', 'method1', '', 'parent::method1(); echo "method1_new\n";');
$c->method1();
runkit7_method_rename('Class2', 'method1', 'method2');
$c->method2();
runkit7_method_copy('Class2', 'method3', 'Class0', 'method3');
$c->method3();
?>
--EXPECT--
method1
method1_new
method1
method1_new
method3
method3_new
