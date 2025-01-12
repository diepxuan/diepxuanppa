--TEST--
runkit.superglobal setting creates superglobals that can be referenced multiple ways. (new function name) (<8.1)
--SKIPIF--
<?php
if(!extension_loaded("runkit7") || !RUNKIT7_FEATURE_MANIPULATION) print "skip\n";
if(!extension_loaded("session")) print "skip - This test assumes \$_SESSION will exist, but the session extension isn't enabled/installed\n";
if(PHP_VERSION_ID >= 80100) print "skip test php prior to 8.1 due to change to GLOBALS\n";
?>
--INI--
display_errors=on
runkit.superglobal=foo
error_reporting=E_ALL
--FILE--
<?php
ini_set('error_reporting', E_ALL);

function capture_runkit_superglobals_dump() {
    ob_start();
    debug_zval_dump(runkit_superglobals());
    return ob_get_clean();
}

class FooClass {
    public $prop;
    public function __construct($value) {
        $this->prop = $value;
    }

    public function bar() {
        debug_print_backtrace();
        var_dump($GLOBALS['foo']);
        var_dump($foo);
        // Verify that runkit_superglobals properly reference counts string keys, call it twice.
        $result = capture_runkit_superglobals_dump();
        echo $result;
        // In php 8.1, debug_zval_dump starts printing "interned" for internally interned strings.
        debug_zval_dump(array_keys($GLOBALS));
        $result2 = capture_runkit_superglobals_dump();
        echo "result === result2: ";
        $same = $result === $result2;
        var_dump($same);
        if (!$same) {
            echo "result2\n";
            var_dump($result2);
        }
    }
}

function initfoo() {
    $foo = new FooClass('value');
}

function usefoo() {
    $foo->bar();
}
initfoo();
usefoo();
?>
--EXPECTF--
#0  FooClass->bar() called at [%s:%d]
#1  usefoo() called at [%s:%d]
object(FooClass)#1 (1) {
  ["prop"]=>
  string(5) "value"
}
object(FooClass)#1 (1) {
  ["prop"]=>
  string(5) "value"
}

Deprecated: Function runkit_superglobals() is deprecated in %srunkit_superglobals_obj_alias.php on line 6
array(10) refcount(1){
  [0]=>
  string(7) "GLOBALS" %s
  [1]=>
  string(4) "_GET" %s
  [2]=>
  string(5) "_POST" %s
  [3]=>
  string(7) "_COOKIE" %s
  [4]=>
  string(7) "_SERVER" %s
  [5]=>
  string(4) "_ENV" %s
  [6]=>
  string(8) "_REQUEST" %s
  [7]=>
  string(6) "_FILES" %s
  [8]=>
  string(8) "_SESSION" %s
  [9]=>
  string(3) "foo" %s
}
array(9) refcount(%d){
  [0]=>
  string(4) "_GET" %s
  [1]=>
  string(5) "_POST" %s
  [2]=>
  string(7) "_COOKIE" %s
  [3]=>
  string(6) "_FILES" %s
  [4]=>
  string(4) "argv" %s
  [5]=>
  string(4) "argc" %s
  [6]=>
  string(7) "_SERVER" %s
  [7]=>
  string(7) "GLOBALS" %s
  [8]=>
  string(3) "foo" %s
}
result === result2: bool(true)
