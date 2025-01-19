<?php

ini_set('max_execution_time', 5400);
ini_set('memory_limit', '512M');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// List of files to include in order
$files = [
    'kpop.php',
    'optc.php',
    'pure.php',
    'goon.php',
    'made.php',
    'dove.php',
    'mr.php',
    'mush.php',
    'air.php',
    'cfe.php',
    'fbas.php',
    'sia.php',
    'naa.php',
    'sabl.php',
    'ndgo.php',
    'pory.php',
    'rsng.php',
    'boop.php',
    'rad.php',
    'soju.php',
    'xene.php',
    'br.php',
    'lem.php',
    'eon.php',
    'xmas.php',
    'lepa.php',
    'crow.php',
    'pkem.php',
    'krkn.php',
    'alya.php',
    'lgnd.php',
    'exi.php',
    'void.php',
    'uxie.php',
    'roo.php',
    'generate-users.php'
];

// Loop through the files and include them
foreach ($files as $file) {
    echo "Including file: $file\n";
    try {
        include $file;
    } catch (Throwable $e) {
        echo "Error including $file: " . $e->getMessage() . "\n";
    }
}

echo "All files have been executed.\n";
