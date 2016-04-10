<?php
// create Engine object
require_once('Tenjin.php');
$engine = new Tenjin_Engine();

// render template with context data
$context = array('title'=>'Bordered Table Example',
                 'items'=>array('<AAA>', 'B&B', '"CCC"'));
$output = $engine->render('table.phtml', $context);
echo $output;
?>
