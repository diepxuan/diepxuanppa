--TEST--
runkit7_function_add() function
--SKIPIF--
<?php if(!extension_loaded("runkit7") || !RUNKIT7_FEATURE_MANIPULATION) print "skip"; ?>
--FILE--
<?php
$name = 'runkitSample';
runkit7_function_add($name, '$a, $b, $c = "baz"', 'static $is="is"; for($i=0; $i<10; $i++) {} echo "a $is $a\nb $is $b\nc $is $c\n";');
runkitSample('foo','bar');
echo $name;
?>
--EXPECT--
a is foo
b is bar
c is baz
runkitSample
