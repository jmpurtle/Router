<?php
require_once 'Tenjin.php';
$properties = array('escapefunc'=>'escapeHTML');
$engine = new Tenjin_Engine($properties);
$template = $engine->get_template('ex3.phtml');
echo $template->script;
?>
