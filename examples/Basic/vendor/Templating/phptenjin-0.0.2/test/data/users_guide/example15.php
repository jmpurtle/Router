<?php
require_once 'tenjin.php';
$filename = 'example14.phtml';
$template = new Tenjin_Template($filename, array('escapefunc'=>'escapeHTML'));
echo $template->script;

function escapeHTML($s) {
    $s = preg_replace('/\&/', '&amp;', $s);
    $s = preg_replace('/</',  '&lt;',  $s);
    $s = preg_replace('/>/',  '&gt;',  $s);
    $s = preg_replace('/"/',  '&quot;', $s);
    $s = preg_replace('/\'/', '&#039;', $s);
    return $s;
}
$context = array(
    'title' => 'phpTenjin Example',
    'items' => array('<foo>', '&bar', '"baz"'),
);
$output = $template->render($context);
echo $output;
?>
