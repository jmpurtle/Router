#!/usr/bin/env php
<?php

require_once 'Tenjin.php';

/// set action ('create' or 'edit')
$action = 'create';

/// set context data
if ($action == 'create') {
    $title = 'Create User';
    $params = array(
        'name'=>null, 'email'=>null, 'gender'=>null, 'id'=>null,
    );
}
else {
    $title = 'Edit User';
    $params = array(
        'name'   => 'Margalette',
        'email'  => 'meg@example.com',
        'gender' => 'f',
        'id'     => 123,
    );
}
$context = array('title'=>$title, 'params'=>$params);

/// create engine object
$layout = ':layout';   // or 'user_layout.phtml'
$properties = array('prefix'=>'user_', 'postfix'=>'.phtml', 'layout'=>$layout);
$engine = new Tenjin_Engine($properties);

/// evaluate template
$template_name = ':'.$action;   // ':create' or ':edit'
$output = $engine->render($template_name, $context);
echo $output;
