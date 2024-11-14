<?php
echo "\n\n// prototypes.js\n";
include './prototypes.js';

echo "\n\n// script.js\n";
include './script.js';

echo "\n\n// web_script.js\n";
include './web_script.js';

echo "\n\n// config.js\n";
include './config.js';

echo "\n\n// load.js\n";
echo "window.addEventListener('load', () => {\n";
include './load.js';
echo "\n});";