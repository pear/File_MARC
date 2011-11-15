<?php

// Stash the current error_reporting value
$error_reporting = error_reporting();

// Disable warnings for the include_once() test
error_reporting(E_ALL & ~E_WARNING);
if (!include_once('Structures/LinkedList/Double.php')) {
    print("skip - Structures_LinkedList PEAR package is not installed.");
}

// Restore the error reporting to previous value
error_reporting($error_reporting);

?>
