<?php
require_once 'Tenjin.php';

class MyTemplate extends Tenjin_Template {

    /// return pattern string for embedded expressions
    function expr_pattern() {
      return '/\{=(=)?(.*?)=\}|<%=(.*?)%>/s';
    }
  
    // return expression string and flag whether escape or not from matched array
    function get_expr_and_escapeflag($match) {
        if (isset($match[3])) {
            $expr = trim($match[3][0]);
            $escapeflag = false;
        }
        else {
            $expr = $match[2][0];
            $escapeflag = @$match[1][0] == '=';
        }
        return array($expr, $escapeflag);
    }

}

## test program
$context = array('value' => 'AAA&BBB');
$properties = array('templateclass'=>'MyTemplate');
$engine = new Tenjin_Engine($properties);
$output = $engine->render('ex6-expr-pattern.phtml', $context);
echo $output;
?>
