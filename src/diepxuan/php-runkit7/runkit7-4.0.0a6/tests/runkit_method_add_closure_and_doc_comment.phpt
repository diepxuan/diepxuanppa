--TEST--
runkit7_method_add() function with closure and doc_comment
--SKIPIF--
<?php
	if(!extension_loaded("runkit7") || !RUNKIT7_FEATURE_MANIPULATION) print "skip";
?>
--INI--
display_errors=on
--FILE--
<?php
class runkit_class {
}
runkit7_method_add('runkit_class','runkit_method',function() {}, NULL, 'new doc_comment1');
runkit7_method_add('runkit_class','runkitMethod',function() {}, NULL, 'new doc_comment2');
$r1 = new ReflectionMethod('runkit_class', 'runkit_method');
echo $r1->getDocComment(), "\n";
$r2 = new ReflectionMethod('runkit_class', 'runkitMethod');
echo $r2->getDocComment(), "\n";
?>
--EXPECT--
new doc_comment1
new doc_comment2
