--TEST--
Bug#56662 - Wrong access level with RUNKIT7_ACC_PUBLIC
--SKIPIF--
<?php if(!extension_loaded("runkit7")) print "skip"; ?>
--FILE--
<?php
class A {}
runkit7_method_add('A', 'x', '', '', RUNKIT7_ACC_PUBLIC);
echo new ReflectionMethod('A', 'x') . "\n";

class B extends A { public function x() {} }
echo new ReflectionMethod('B', 'x') . "\n";

eval("class C extends A { public function x() {} }");
echo new ReflectionMethod('C', 'x');

--EXPECTF--
Method [ <user%S> public method x ] {
  @@ %sbug56662.php(3) : runkit runtime-created method 1 - 1
}

Method [ <user, overwrites A%S> public method x ] {
  @@ %sbug56662.php 6 - 6
}

Method [ <user, overwrites A, prototype A> public method x ] {
  @@ %sbug56662.php(9) : eval()'d code 1 - 1
}
