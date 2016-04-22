<?php
// create engine
require 'Tenjin.php';
$properties = array('postfix'=>'.phtml', 'preprocess'=>true);
$engine = new Tenjin_Engine($properties);

// render template with context data
$params = array('id'=>1234, 'name'=>'Foo', 'lang'=>'ch');
$context = array('params'=>$params);
$output = $engine->render(':select', $context);
echo $output;
?>
