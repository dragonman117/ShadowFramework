<?php
/*
 * Index.php
 *
 *   This is the main file that everything Goes through
 *   It simply calles the main director file that everything else will use.
*/
require_once('core/director.php');

//for testing purposes
/*error_reporting(E_ALL);
ini_set( 'display_errors','1');//*/


$app = new Director();


?>