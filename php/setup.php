<?php
if (!isset($dir_level)) $dir_level = 0;
$localconfig_href = str_repeat('.', $dir_level) . "./localconfig.json";

$localconfig = json_decode(file_get_contents($localconfig_href), true);

$setup = true;

$host = $localconfig['host'];
$base_dir = $localconfig['base_dir'];

function generateLink($destination) {
    global $base_dir;
    $link = $base_dir . $destination;
    return $link;
}