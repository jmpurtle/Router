<?php
$languages = array(
    'en' => 'Engilish',
    'fr' => 'French',
    'de' => 'German',
    'es' => 'Spanish',
    'ch' => 'Chinese',
    'ja' => 'Japanese',
);

function link_to($label, $action=null, $id=null) {
    $url = '/app';
    if ($action) $url .= '/'.$action;
    if ($id)     $url .= '/'.$id;
    $url = preg_replace('/%2F/', '/', urlencode($url));
    return "<a href=\"".$url."\">$label</a>";
}
?>
