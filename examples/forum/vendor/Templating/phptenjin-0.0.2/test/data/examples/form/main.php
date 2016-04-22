<?php
// create Engine object
require_once('Tenjin.php');
$properties = array('postfix'=>'.phtml', 'layout'=>'layout.phtml');
$engine = new Tenjin_Engine($properties);

// render template with context data
$params = array('name'=>'Foo', 'gender'=>'M');
$context = array('params'=>$params);
$output = $engine->render(':update', $context);   # ':update' == 'update'+postfix
echo $output;
?>
