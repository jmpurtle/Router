<?php
require_once 'Tenjin.php';

##
## message catalog to translate message
##
$MESSAGE_CATALOG = array(
    'en' => array(
              'Hello'    => 'Hello',
              'Good bye' => 'Good bye',
            ),
    'fr' => array(
              'Hello'    => 'Bonjour',
              'Good bye' => 'Au revoir',
            ),
);

##
## language
##
$LANG = 'en';


##
## define translator function
##
function _($message_key) {
    global $MESSAGE_CATALOG, $LANG;
    $arr = $MESSAGE_CATALOG[$LANG];
    if (! $arr)
        return $message_key;
    return isset($arr[$message_key]) ? $arr[$message_key] : $message_key;
}


##
## engine class which supports M17N
##
class M17NEngine extends Tenjin_Engine {

    ## change cache filename to 'file.html.lang.cache'
    function cachename($filename) {
        global $LANG;
        return "$filename.$LANG.cache";
    }

}


##
## test program
##
$template_name = 'ex7-m18n.phtml';
$context = array('username' => 'World');

## english page
$properties = array('preprocess'=>true);
$engine = new M17NEngine($properties);
$output = $engine->render($template_name, $context);
echo "--- lang: $LANG ---\n";
echo $output, "\n";

## french page
$LANG = 'fr';
$engine = new M17NEngine($properties);
$output = $engine->render($template_name, $context);
echo "--- lang: $LANG ---\n";
echo $output;
?>
