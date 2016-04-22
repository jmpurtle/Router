<?php
/// template file
$filename = 'example14.phtml';

/// convert into php code
require_once 'Tenjin.php';
$template = new Tenjin_Template($filename);
/// or
//$template = new Tenjin_Template();
//$script = $template->convert_file($filename);
/// or
//$template = new Tenjin_Template();
//$input = file_get_contents($filename);
//$script = $template->convert($input, $filename);  # filename is optional

/// show converted php code
echo "---- php code ----\n";
echo $template->script;

/// evaluate php code
$context = array('title'=>'phpTenjin Example', 'items'=>array('<AAA>','B&B','"CCC"'));
$output = $template->render($context);
echo "---- output ----\n";
echo $output;
