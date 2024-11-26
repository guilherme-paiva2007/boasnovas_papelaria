<?php
$scripts = true;

$dirbase = "boasnovas_papelaria";

/**
 * Create a link based on the base directory where the project is.
 * @param string $destination
 * @return string
 */
function createLink($destination) {
    global $dirbase;
    $base = $dirbase . '/';

    return '/' . $base . $destination;
}

/**
 * Create a link based on the current location
 * @param string $destination
 * @param int $backLevel
 * @return string
 */
function createRefLink($destination, $backLevel = 0) {
    if ($backLevel < 0) $backLevel *= -1;

    $dots = "";
    for ($i = 0; $i < $backLevel; $i++) {
        $dots = $dots . ".";
    }

    return "$dots" . "./" . "$destination";
}