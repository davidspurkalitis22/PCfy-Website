<?php
// This script is created by cleanup.php to remove itself
if (isset($_POST['file']) && $_POST['file'] === 'cleanup.php') {
    if (file_exists('cleanup.php')) {
        unlink('cleanup.php');
    }
    // Delete this file too
    unlink(__FILE__);
}
?>